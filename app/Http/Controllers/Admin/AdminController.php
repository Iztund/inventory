<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};
use App\Models\{
    Submission, Asset, User, Department, Unit, Faculty, Institute, Office
};
use App\Exports\OrganizationalStructureExport;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    /**
     * Display the Admin dashboard overview.
     * Focuses on high-level counts and pending actions.
     */
    public function index()
    {
        return view('admin.dashboard', [
            // Submission Stats
            'totalSubmissions'    => Submission::count(),
            'pendingSubmissions'  => Submission::where('status', 'pending')->count(),
            'approvedSubmissions' => Submission::where('status', 'approved')->count(),
            'rejectedSubmissions' => Submission::where('status', 'rejected')->count(),

            // General Inventory & User Stats
            'totalAssets'         => Asset::count(),
            'totalActiveUsers'    => User::where('status', 'active')->count(),
            'totalInactiveUsers'  => User::where('status', '!=', 'active')->count(),

            // College Structure Metrics
            'totalFaculties'      => Faculty::count(),
            'totalDepartments'    => Department::count(),
            'totalUnits'          => Unit::count(),
            'totalInstitutes'     => Institute::count(),
            'totalOffices'        => Office::count(),

            // Recent Activity
            'recentSubmissions'   => Submission::with(['submittedBy.profile'])
                ->where('status', 'pending')
                ->orderByDesc('submitted_at')
                ->take(10)
                ->get()
        ]);
    }

    /**
     * Units Management Index.
     * Keeps track of the organizational hierarchy health.
     */
    public function unitsManagementIndex()
    {
        $activeFacultyIds = Faculty::where('is_active', 'active')->pluck('faculty_id');
        $allActiveDepts   = Department::where('is_active', 'active')->get();
        
        $summary = [
            'total_faculties'      => $activeFacultyIds->count(),
            'inactive_faculties'   => Faculty::where('is_active', 'inactive')->count(),
            'total_departments'    => $allActiveDepts->count(),
            'inactive_departments' => Department::where('is_active', 'inactive')->count(),
            'total_offices'        => Office::where('is_active', 'active')->count(),
            'total_units'          => Unit::where('is_active', 'active')->count(),
            'total_institutes'     => Institute::where('is_active', 'active')->count(),

            // Distribution metrics (Sum of quantities)
            'assets_in_faculties'   => Asset::whereIn('current_faculty_id', $activeFacultyIds)->sum('quantity'),
            'assets_in_departments' => Asset::whereIn('current_dept_id', $allActiveDepts->pluck('dept_id'))->sum('quantity'),
            'assets_in_offices'     => Asset::whereIn('current_office_id', Office::where('is_active', 'active')->pluck('office_id'))->sum('quantity'),
            'assets_in_units'       => Asset::whereIn('current_unit_id', Unit::where('is_active', 'active')->pluck('unit_id'))->sum('quantity'),
            'assets_in_institutes'  => Asset::whereIn('current_institute_id', Institute::where('is_active', 'active')->pluck('institute_id'))->sum('quantity'),
        ];

        return view('admin.Academic.academic_admin', compact('summary'));
    }

    /**
     * Comprehensive Reports.
     * Uses standardized naming (item_name, unit_cost).
     */
    public function reportsIndex(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate   = $request->input('end_date', now()->format('Y-m-d'));

        // Standardized Summary
        $summary = [
            'total_assets'       => Asset::sum('quantity'),
            'total_value'        => Asset::selectRaw('SUM(quantity * unit_cost) as total')->value('total') ?? 0,
            'submissions_period' => Submission::whereBetween('submitted_at', [$startDate, $endDate . ' 23:59:59'])->count(),
        ];

        // Chart: Asset Distribution (Money Value) by Department
        $assetsByDept = Asset::join('departments', 'assets.current_dept_id', '=', 'departments.dept_id')
            ->select('departments.dept_name as label', DB::raw('SUM(assets.quantity * assets.unit_cost) as value'))
            ->groupBy('departments.dept_name')
            ->orderBy('value', 'desc')
            ->get();

        $chartData = [
            'status_labels'         => ['Available', 'Assigned', 'Maintenance', 'Retired'],
            'status_data'           => [
                Asset::where('status', 'available')->sum('quantity'),
                Asset::where('status', 'assigned')->sum('quantity'),
                Asset::where('status', 'maintenance')->sum('quantity'),
                Asset::where('status', 'retired')->sum('quantity'),
            ],
            'department_labels'     => $assetsByDept->pluck('label')->toArray(),
            'department_value_data' => $assetsByDept->pluck('value')->map(fn($v) => round($v / 1000000, 2))->toArray(),
        ];

        $topAssets = Asset::with(['department', 'category'])
            ->select('asset_id', 'item_name', 'unit_cost', 'quantity', 'category_id', 'current_dept_id', DB::raw('quantity * unit_cost as total_value'))
            ->orderBy('total_value', 'desc')
            ->take(10)
            ->get();

        return view('admin.comprehensive_reports', compact('summary', 'chartData', 'topAssets'));
    }

    /**
     * CSV Report Export.
     * Fixed the 'product_name' bug to use 'item_name'.
     */
    public function exportReport(Request $request)
    {
        $fileName = 'COM_Inventory_Report_' . now()->format('d_m_Y') . '.csv';
        $query    = Asset::with(['department', 'unit', 'institute', 'category']);

        // Filtering Logic
        if ($request->filled('unit_id'))    $query->where('current_unit_id', $request->unit_id);
        if ($request->filled('department')) $query->where('current_dept_id', $request->department);

        $assets = $query->get();

        return response()->stream(function() use($assets) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Asset ID', 'Item Name', 'Category', 'Assigned To', 'Status', 'Unit Cost', 'Qty', 'Total Value']);

            foreach ($assets as $asset) {
                $location = $asset->department->dept_name ?? $asset->unit->unit_name ?? $asset->institute->institute_name ?? 'General';
                fputcsv($file, [
                    $asset->asset_id, 
                    $asset->item_name, // Fixed naming
                    $asset->category->category_name ?? 'N/A',
                    $location, 
                    ucfirst($asset->status), 
                    $asset->unit_cost, 
                    $asset->quantity,
                    $asset->unit_cost * $asset->quantity
                ]);
            }
            fclose($file);
        }, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
        ]);
    }

    /**
     * Excel Export for Organizational Structure.
     */
    public function export()
    {
        $summary = [
            'total_faculties'   => Faculty::count(),
            'total_departments' => Department::count(),
            'total_offices'     => Office::count(),
            'total_units'       => Unit::count(),
            'total_institutes'  => Institute::count(),
        ];
        return Excel::download(new OrganizationalStructureExport($summary), 'College_Structure_Report.xlsx');
    }
}