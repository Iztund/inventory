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
            'totalOffices'        => Office::count(),
            'totalDepartments'    => Department::count(),
            'totalUnits'          => Unit::count(),
            'totalInstitutes'     => Institute::count(),
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
    // Extract filters
    $entityType = $request->input('entity_type');
    $startDate = $request->input('start_date', now()->subMonth()->startOfDay());
    $endDate = $request->input('end_date', now()->endOfDay());

    // Build base asset query with entity filtering
    $assetQuery = $this->buildAssetQuery($entityType);

    // Get summary metrics
    $summary = [
        'total_assets' => (clone $assetQuery)->sum('quantity'),
        'total_value' => (clone $assetQuery)->sum(DB::raw('quantity * purchase_price')),
        'submissions_period' => $this->getSubmissionsCount($entityType, $startDate, $endDate),
    ];

    // Get chart data
    $chartData = [
        'enrollment_labels' => [],
        'enrollment_data' => [],
        'status_labels' => ['Available', 'Assigned', 'Maintenance', 'Retired'],
        'status_data' => $this->getStatusCounts($assetQuery),
    ];

    // Get entity breakdown for bar chart
    $allEntities = $this->getEntityBreakdown($entityType);
    
    $chartData['enrollment_labels'] = $allEntities->pluck('label')->toArray();
    $chartData['enrollment_data'] = $allEntities->pluck('total')
        ->map(fn($v) => round($v / 1000000, 2))
        ->toArray();

    // Get top 10 high-value assets
    $topAssets = (clone $assetQuery)
        ->with(['category', 'subcategory', 'department', 'unit', 'faculty', 'office','institute'])
        ->select('*', DB::raw('quantity * purchase_price as total_value'))
        ->orderByDesc('total_value')
        ->take(10)
        ->get();

    return view('admin.comprehensive_reports', compact('summary', 'chartData', 'topAssets', 'allEntities'));
}

/**
 * Build asset query with entity type filtering
 */
private function buildAssetQuery(?string $entityType)
{
    $query = Asset::query();

    if (!$entityType) {
        return $query;
    }

    $filters = [
        // Case A: Asset is in a Faculty but NOT assigned to a Dept/Office/etc.
        'faculty' => fn($q) => $q->whereNotNull('current_faculty_id')
                                 ->whereNull('current_dept_id')
                                 ->whereNull('current_institute_id'),

        // Case B: Asset is in a Department but NOT in a sub-office or unit
        'department' => fn($q) => $q->whereNotNull('current_dept_id')
                                    ->whereNull('current_unit_id'),

        // Case C: Asset belongs to a specific Office
        'office' => fn($q) => $q->whereNotNull('current_office_id')
                                        ->whereNull('current_unit_id'),
        // Case D: Asset belongs to a specific Unit
        'unit' => fn($q) => $q->whereNotNull('current_unit_id'),

        // Case E: Asset belongs to an Institute
        'institute' => fn($q) => $q->whereNotNull('current_institute_id'),
    ];

    return isset($filters[$entityType]) ? $filters[$entityType]($query) : $query;
}

/**
 * Get submissions count for date range and entity type
 */
private function getSubmissionsCount(?string $entityType, $startDate, $endDate): int
{
    $query = Submission::whereBetween('submitted_at', [$startDate, $endDate]);

    if (!$entityType) {
        return $query->count();
    }

    $profileFilters = [
        'faculty' => fn($q) => $q->whereNotNull('faculty_id')->whereNull('dept_id'),
        'office' => fn($q) => $q->whereNotNull('office_id')->whereNull('unit_id'),
        'department' => fn($q) => $q->whereNotNull('dept_id'),
        'unit' => fn($q) => $q->whereNotNull('unit_id'),
        'institute' => fn($q) => $q->whereNotNull('institute_id'),
    ];

    return $query->whereHas('submittedBy', $profileFilters[$entityType] ?? fn($q) => $q)->count();
}

/**
 * Get status counts for pie chart
 */
private function getStatusCounts($assetQuery): array
{
    $statuses = ['available', 'assigned', 'maintenance', 'retired'];
    
    return collect($statuses)
        ->map(fn($status) => (clone $assetQuery)->where('status', $status)->count())
        ->toArray();
}

/**
 * Get entity breakdown with total values
 */
private function getEntityBreakdown(?string $entityType)
{
    $types = $entityType ? [$entityType] : ['faculty', 'department','office', 'unit', 'institute'];
    
    return collect($types)
        ->flatMap(fn($type) => $this->getEntitiesOfType($type))
        ->sortByDesc('total')
        ->take(10);
}

/**
 * Get all entities of a specific type with their totals
 */
private function getEntitiesOfType(string $type)
{
    $config = [
        'faculty' => ['table' => 'faculties', 'prefix' => 'faculty'],
        'department' => ['table' => 'departments', 'prefix' => 'dept'],
        'office' => ['table' => 'offices', 'prefix' => 'office'],
        'unit' => ['table' => 'units', 'prefix' => 'unit'],
        'institute' => ['table' => 'institutes', 'prefix' => 'institute'],
    ];

    if (!isset($config[$type]) || !Schema::hasTable($config[$type]['table'])) {
        return collect();
    }

    $cfg = $config[$type];
    $table = $cfg['table'];
    $prefix = $cfg['prefix'];
    $pk = $prefix . '_id';
    $nameAttr = $prefix . '_name';
    $foreignKey = 'current_' . $prefix . '_id';

    $query = Asset::join($table, "assets.{$foreignKey}", '=', "{$table}.{$pk}")
        ->select("{$table}.{$nameAttr} as label", DB::raw('SUM(assets.quantity * assets.purchase_price) as total'))
        ->groupBy("{$table}.{$nameAttr}");

    // Apply branch exclusion
    if ($type === 'faculty') {
        $query->whereNull('assets.current_dept_id');
    } elseif ($type === 'office') {
        $query->whereNull('assets.current_unit_id');
    }

    return $query->get();
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
                $entity = $user->faculty->faculty_name ?? $user->institute->institute_name ?? $user->office->office_name ?? 'N/A';
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
    $parentId = $request->query('parent_id'); 
    $parentType = $request->query('parent_type'); // 'faculty', 'office', or 'institute'

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
        // Apply filtering based on entity type and parent
        ->when($parentType, function ($query) use ($parentType, $parentId) {
            switch ($parentType) {
                case 'faculty':
                    // For Faculty Dean selection OR Department Head selection
                    if ($parentId) {
                        // This is a Department - filter by faculty_id
                        return $query->where('faculty_id', $parentId);
                    } else {
                        // This is a Faculty - show users belonging to ANY faculty
                        return $query->whereNotNull('faculty_id');
                    }
                    
                case 'office':
                    // For Office Head selection OR Unit Head selection
                    if ($parentId) {
                        // This is a Unit - filter by office_id
                        return $query->where('office_id', $parentId);
                    } else {
                        // This is an Office - show users belonging to ANY office
                        return $query->whereNotNull('office_id');
                    }
                    
                case 'institute':
                    // For Institute Director selection - show users belonging to ANY institute
                    return $query->whereNotNull('institute_id');
                    
                default:
                    return $query;
            }
        })
        ->take(20)
        ->get();

    $formattedUsers = $users->map(function($user) {
        return [
            'id' => $user->user_id,
            'text' => $user->full_name . ' (' . $user->email . ')',
        ];
    });

    return response()->json($formattedUsers);
}
}