<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Schema};
use Illuminate\Support\Str;
use App\Models\{
    Submission, SubmissionItem, Asset, User,
    Department, Unit, Faculty, Institute, Office,
    Category, Subcategory
};
use App\Exports\OrganizationalStructureExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class AdminController extends Controller
{
    // ============================================
    // ADMIN DASHBOARD
    // ============================================
    public function index()
    {
        $stats = $this->getStats();
        $topAssets = Asset::with(['category', 'subcategory', 'department', 'unit'])
        ->orderBy('created_at', 'desc') 
        ->limit(10)
        ->get();

        $recentSubmissions = Submission::with([
            'items','reviewedby.profile','audits.auditor.profile', 'submittedBy.profile', 'submittedBy.faculty',
            'submittedBy.department', 'submittedBy.office', 'submittedBy.unit'
        ])
        ->latest('submitted_at')
        ->take(10)
        ->get();

        return view('admin.dashboard', [
            'totalSubmissions'    => $stats['total_requests'],
            'totalAssets'         => $stats['total_assets'],
            'pendingSubmissions'  => $stats['pending'],
            'approvedSubmissions' => $stats['approved'],
            'rejectedSubmissions' => $stats['rejected'],
            'totalActiveUsers'    => User::where('status', 'active')->count(),
            'totalInactiveUsers'  => User::where('status', '!=', 'active')->count(),
            'totalFaculties'      => Faculty::count(),
            'totalDepartments'    => Department::count(),
            'totalUnits'          => Unit::count(),
            'totalInstitutes'     => Institute::count(),
            'totalOffices'        => Office::count(),
            'totalCategories'     => Category::count(),
            'recentSubmissions'   => $recentSubmissions,
            'topAssets'           => $topAssets,
        ]);
    }

    // ============================================
    // UNITS / ACADEMIC STRUCTURE MANAGEMENT
    // ============================================
    public function unitsManagementIndex()
    {
        $activeFacultyIds = Faculty::where('is_active', 'active')->pluck('faculty_id');
        $allActiveDepts   = Department::where('is_active', 'active')->get();
        $dropdownData = [
        'categories' => Category::orderBy('category_name', 'asc')->get(),
        'subcategories' => Subcategory::orderBy('subcategory_name', 'asc')->get()
        ];
        $summary = [
            'total_faculties'      => $activeFacultyIds->count(),
            'inactive_faculties'   => Faculty::where('is_active', '!=', 'active')->count(),
            'total_departments'    => $allActiveDepts->count(),
            'inactive_departments' => Department::where('is_active', '!=', 'active')->count(),
            'total_offices'        => Office::where('is_active', 'active')->count(),
            'inactive_offices'     => Office::where('is_active', '!=', 'active')->count(),
            'total_units'          => Unit::where('is_active', 'active')->count(),
            'inactive_units'       => Unit::where('is_active', '!=', 'active')->count(),
            'total_institutes'     => Institute::where('is_active', 'active')->count(),
            'inactive_institutes'  => Institute::where('is_active', '!=', 'active')->count(),
            'total_categories' => Category::count(),
            'total_subcategories' => Subcategory::count(),
            // More accurate asset counts
            'assets_in_faculties'   => Asset::whereIn('current_faculty_id', $activeFacultyIds)->sum('quantity'),
            'assets_in_departments' => Asset::whereIn('current_dept_id', $allActiveDepts->pluck('dept_id'))->sum('quantity'),
            'assets_in_offices'     => Asset::whereIn('current_office_id', Office::where('is_active', 'active')->pluck('office_id'))->sum('quantity'),
            'assets_in_units'       => Asset::whereIn('current_unit_id', Unit::where('is_active', 'active')->pluck('unit_id'))->sum('quantity'),
            'assets_in_institutes'  => Asset::whereIn('current_institute_id', Institute::where('is_active', 'active')->pluck('institute_id'))->sum('quantity'),
        ];
        // CALCULATE GLOBAL TOTAL
        $summary['total_global_assets'] = $summary['assets_in_faculties'] + 
                                        $summary['assets_in_departments'] + 
                                        $summary['assets_in_offices'] + 
                                        $summary['assets_in_units'] + 
                                        $summary['assets_in_institutes'];

        return view('admin.Academic.academic_admin', compact('summary', 'dropdownData'));
    }

    // ============================================
    // COMPREHENSIVE REPORTS
    // ============================================
    public function reportsIndex(Request $request)
{
    $entityType = $request->input('entity_type');
    $startDate  = $request->input('start_date', now()->subMonth()->startOfDay());
    $endDate    = $request->input('end_date', now()->endOfDay());

    // 1. Base Query (Filtered by Date & Global Standing)
    $assetQuery = Asset::query();

    // --- APPLY EXCLUSIVE FILTERING FOR SUMMARY METRICS ---
    // This ensures the "Total Assets" and "Total Value" cards update correctly
    if ($entityType) {
        switch ($entityType) {
            case 'faculty':
                $assetQuery->whereNotNull('current_faculty_id')->whereNull('current_dept_id');
                break;
            case 'department':
                $assetQuery->whereNotNull('current_dept_id');
                break;
            case 'office':
                $assetQuery->whereNotNull('current_office_id')->whereNull('current_unit_id');
                break;
            case 'unit':
                $assetQuery->whereNotNull('current_unit_id');
                break;
            case 'institute':
                $assetQuery->whereNotNull('current_institute_id');
                break;
        }
    }
    
    
   // 2. Summary Metrics (Filtered)
    $submissionQuery = Submission::whereBetween('submitted_at', [$startDate, $endDate]);

    if ($entityType) {
        // Filter submissions based on the profile of the user who submitted it
        $submissionQuery->whereHas('submittedBy', function($q) use ($entityType) {
            switch ($entityType) {
                case 'faculty':
                    // Submissions by users in a Faculty but not in a Dept
                    $q->whereNotNull('faculty_id')->whereNull('dept_id');
                    break;
                case 'department':
                    $q->whereNotNull('dept_id');
                    break;
                case 'office':
                    // Submissions by users in an Office but not in a Unit
                    $q->whereNotNull('office_id')->whereNull('unit_id');
                    break;
                case 'unit':
                    $q->whereNotNull('unit_id');
                    break;
                case 'institute':
                    $q->whereNotNull('institute_id');
                    break;
            }
        });
    }

    $summary = [
        'total_assets' => (clone $assetQuery)->count(),
        'total_value'  => (clone $assetQuery)->sum(DB::raw('quantity * purchase_price')),
        'submissions_period' => $submissionQuery->count(),
    ];

    // 3. Dynamic Entity Loop (For the Bar Chart)
    $queryData = collect();
    $typesToFetch = $entityType ? [$entityType] : ['faculty', 'department', 'office', 'unit', 'institute'];

    foreach ($typesToFetch as $type) {
        $table = Str::plural($type);
        $prefix = ($type === 'department') ? 'dept' : $type;
        $pk = $prefix . '_id';
        $nameAttr = $prefix . '_name';
        $foreignKey = 'current_' . $prefix . '_id';

        if (Schema::hasTable($table)) {
            $data = Asset::join($table, "assets.{$foreignKey}", '=', "{$table}.{$pk}")
                // Apply the branch exclusion logic
                ->when($type === 'faculty', fn($q) => $q->whereNull('assets.current_dept_id'))
                ->when($type === 'office', fn($q) => $q->whereNull('assets.current_unit_id'))
                ->select("{$table}.{$nameAttr} as label", DB::raw('SUM(assets.quantity * assets.purchase_price) as total'))
                ->groupBy("{$table}.{$nameAttr}")
                ->get();
                
            $queryData = $queryData->concat($data);
        }
    }

    $allEntities = $queryData->sortByDesc('total')->take(10);

    // 4. Chart Data (Logic is now consistent with steps above)
    $chartData = [
        'enrollment_labels' => $allEntities->pluck('label')->toArray(),
        'enrollment_data'   => $allEntities->pluck('total')->map(fn($v) => round($v / 1000000, 2))->toArray(),
        'status_labels'     => ['Available', 'Assigned', 'Maintenance', 'Retired'],
        'status_data'       => [
            (clone $assetQuery)->where('status', 'available')->count(),
            (clone $assetQuery)->where('status', 'assigned')->count(),
            (clone $assetQuery)->where('status', 'maintenance')->count(),
            (clone $assetQuery)->where('status', 'retired')->count(),
        ]
    ];

    // Top 10 High-Value Assets for the Table
    $topAssets = (clone $assetQuery)->with(['category', 'subcategory', 'department', 'unit', 'office', 'faculty', 'institute'])
        ->select('*', DB::raw('quantity * purchase_price as total_value'))
        ->orderBy('total_value', 'desc')
        ->take(10)
        ->get();

    return view('admin.comprehensive_reports', compact('summary', 'chartData', 'topAssets', 'allEntities'));
}
    // ============================================
    // CSV REPORT EXPORT
    // ============================================
    public function exportReport(Request $request)
    {
        $fileName = 'COM_Inventory_Report_' . now()->format('d_m_Y') . '.csv';

        $query = SubmissionItem::with([
            'submission.submittedBy.profile',
            'submission.submittedBy.faculty',
            'submission.submittedBy.department',
            'submission.submittedBy.office',
            'submission.submittedBy.unit',
            'submission.submittedBy.institute',
            'category',
            'subcategory',
            'asset'
        ]);

        // Apply filters (consistent with your previous logic)
        if ($request->filled('faculty_id')) {
            $query->whereHas('submission.submittedBy', fn($q) => $q->where('faculty_id', $request->faculty_id));
        }
        if ($request->filled('department_id')) {
            $query->whereHas('submission.submittedBy', fn($q) => $q->where('dept_id', $request->department_id));
        }
        if ($request->filled('office_id')) {
            $query->whereHas('submission.submittedBy', fn($q) => $q->where('office_id', $request->office_id));
        }
        if ($request->filled('unit_id')) {
            $query->whereHas('submission.submittedBy', fn($q) => $q->where('unit_id', $request->unit_id));
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $items = $query->get();

        return response()->stream(function() use ($items) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Ref #', 'Item Name', 'Category', 'Subcategory', 'Quantity',
                'Unit Cost', 'Total Value', 'Submitted By', 'Entity',
                'Department/Unit', 'Status', 'Date Submitted'
            ]);

            foreach ($items as $item) {
                $user = $item->submission->submittedBy;
                $entity = $user->faculty->faculty_name ?? $user->office->office_name ?? $user->institute->institute_name ?? 'N/A';
                $subEntity = $user->department->dept_name ?? $user->unit->unit_name ?? 'General';

                fputcsv($file, [
                    '#' . str_pad($item->submission_id, 5, '0', STR_PAD_LEFT),
                    $item->item_name ?? 'N/A',
                    $item->category->category_name ?? 'N/A',
                    $item->subcategory->subcategory_name ?? 'N/A',
                    $item->quantity ?? 0,
                    $item->unit_cost ?? $item->cost ?? 0,
                    ($item->unit_cost ?? $item->cost ?? 0) * ($item->quantity ?? 0),
                    $user->profile->full_name ?? $user->username,
                    $entity,
                    $subEntity,
                    ucfirst($item->status ?? 'pending'),
                    $item->submission->submitted_at ? $item->submission->submitted_at->format('M d, Y') : 'N/A'
                ]);
            }
            fclose($file);
        }, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        ]);
    }

    // ============================================
    // EXCEL EXPORT - ORGANIZATIONAL STRUCTURE
    // ============================================
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

    // ============================================
    // HELPERS
    // ============================================

    private function getStats(): array
    {
        return [
            'total_requests' => SubmissionItem::count(),
            'total_assets'   => Asset::count(),
            'pending'        => SubmissionItem::where('status', 'pending')->count(),
            'approved'       => SubmissionItem::where('status', 'approved')->count(),
            'rejected'       => SubmissionItem::where('status', 'rejected')->count(),
        ];
    }

    public function searchStaff(Request $request)
{
    $q = $request->query('q');
    // These values will be passed from your Select2 AJAX data
    $parentId = $request->query('parent_id'); 
    $parentType = $request->query('parent_type'); // 'faculty' or 'office'

    if (!$q) {
        return response()->json([]); 
    }

    $users = User::with('profile')
        ->where('status', 'active')
        ->where(function ($query) use ($q) {
            $query->where('username', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orWhereHas('profile', function ($subQuery) use ($q) {
                    $subQuery->where('first_name', 'like', "%{$q}%")
                            ->orWhere('last_name', 'like', "%{$q}%")
                            ->orWhere('middle_name', 'like', "%{$q}%");
                });
        })
        // Filter logic: Restrict results based on the hierarchy level
        ->when($parentId && $parentType, function ($query) use ($parentId, $parentType) {
            if ($parentType === 'faculty') {
                return $query->where('faculty_id', $parentId);
            } elseif ($parentType === 'office') {
                return $query->where('office_id', $parentId);
            }
        })
        ->take(20)
        ->get();

    $formattedUsers = $users->map(function($user) {
        return [
            'id' => $user->user_id,
            // formatted via your User model's full_name attribute
            'text' => $user->full_name . ' (' . $user->email . ')',
        ];
    });

    return response()->json($formattedUsers);
}
}