<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\{Auth, DB};
use App\Models\{Submission, Asset, Category};

class StaffController extends Controller
{
    /**
     * Display the Staff dashboard overview.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Personal Submission Metrics
        $stats = [
            'total'    => Submission::where('submitted_by_user_id', $user->user_id)->count(),
            'pending'  => Submission::where('submitted_by_user_id', $user->user_id)->where('status', 'pending')->count(),
            'approved' => Submission::where('submitted_by_user_id', $user->user_id)->where('status', 'approved')->count(),
            'rejected' => Submission::where('submitted_by_user_id', $user->user_id)->where('status', 'rejected')->count(),
        ];

        // 2. Multi-Level Asset Count
        $totalUnitAssets = $this->getScopedAssetsQuery($user)->sum('quantity');

        // 3. Recent Submissions
        $recentSubmissions = Submission::where('submitted_by_user_id', $user->user_id)
            ->orderByDesc('submitted_at')
            ->take(5)
            ->get();

        return view('staff.dashboard', compact('stats', 'totalUnitAssets', 'recentSubmissions'));
    }

    /**
     * Display assets assigned to the staff's organizational level.
     */
    public function assetsIndex(Request $request)
{
    $user = Auth::user();
    $searchTerm = $request->input('search');

    // 1. Scoped query for the main list (with correct relationship naming)
    $query = $this->getScopedAssetsQuery($user)->with(['category', 'subcategory', 'unit', 'department', 'office', 'institute','faculty']);

    // Apply Search Filter for the table only
    if ($searchTerm) {
        $query->where(function($q) use ($searchTerm) {
            $q->where('item_name', 'LIKE', "%{$searchTerm}%")
              ->orWhere('asset_tag', 'LIKE', "%{$searchTerm}%")
              ->orWhere('serial_number', 'LIKE', "%{$searchTerm}%");
        });
    }

    // 2. Lifecycle Chart Data (Assets Status)
    $stats = $this->getScopedAssetsQuery($user)
        ->select('status', DB::raw('count(*) as count'))
        ->groupBy('status')
        ->pluck('count', 'status')
        ->toArray();

    // 3. Audit Stats (Personal submissions for the logged-in staff)
    $auditStats = Submission::where('submitted_by_user_id', $user->user_id)
        ->select('status', DB::raw('count(*) as count'))
        ->groupBy('status')
        ->pluck('count', 'status')
        ->toArray();

    // 4. Asset Info Summary: Grouped by Subcategory
    // We join the subcategories table to get the name for display
    $subcategorySummary = $this->getScopedAssetsQuery($user)
        ->leftJoin('subcategories', 'assets.subcategory_id', '=', 'subcategories.subcategory_id')
        ->select(DB::raw('COALESCE(subcategories.subcategory_name, "Uncategorized") as name'),DB::raw('SUM(assets.quantity) as total_qty'))
        ->groupBy('subcategories.subcategory_name')
        ->orderBy('total_qty', 'desc')
        ->get();

    $totalItems = $this->getScopedAssetsQuery($user)->sum('quantity');
    $assets = $query->latest()->paginate(15);

    return view('staff.assets.index', compact(
        'assets', 
        'totalItems', 
        'stats', 
        'auditStats', 
        'subcategorySummary'
    ));
}

    /**
     * Helper to scope queries based on College hierarchy.
     */
    private function getScopedAssetsQuery($user)
    {
        $query = Asset::query();

        // Priority 1: Specific Unit (e.g., Lab Unit)
        if ($user->unit_id) {
            $query->where('current_unit_id', $user->unit_id);
        } 
        // Priority 2: Department
        elseif ($user->department_id) {
            $query->where('current_dept_id', $user->department_id);
        }
        // Priority 3: Institute
        elseif ($user->institute_id) {
            $query->where('current_institute_id', $user->institute_id);
        }
        // Priority 4: Office
        elseif ($user->office_id) {
            $query->where('current_office_id', $user->office_id);
        }
        // Priority 5: Faculty
        elseif ($user->faculty_id) {
            $query->where('current_faculty_id', $user->faculty_id);
        }
        else {
            // Safety: Show nothing if user has no assigned location
            $query->whereRaw('1 = 0');
        }

        return $query;
    }

    /**
     * Export the filtered asset list to PDF.
     */
    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        $searchTerm = $request->input('search');

        $query = $this->getScopedAssetsQuery($user)->with(['category', 'unit', 'department']);

        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('item_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('asset_tag', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('serial_number', 'LIKE', "%{$searchTerm}%");
            });
        }

        $assets = $query->latest()->get(); 
        $totalItems = $assets->sum('quantity');

        $pdf = Pdf::loadView('staff.assets.export-pdf', compact('assets', 'totalItems', 'user'));
        
        return $pdf->setPaper('a4', 'landscape')->download('Inventory_Report_'.now()->format('Y-m-d').'.pdf');
    }

    public function exportCsv(Request $request)
{
    $user = Auth::user();
    $fileName = 'Inventory Assets' . now()->format('d_m_Y') . '.csv';
    
    // Use your existing helper to get scoped assets
    $query = $this->getScopedAssetsQuery($user)->with(['category','subcategory', 'unit', 'department']);

    // Apply Search Filter if one was active
    if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where(function($q) use ($searchTerm) {
            $q->where('item_name', 'LIKE', "%{$searchTerm}%")
              ->orWhere('asset_tag', 'LIKE', "%{$searchTerm}%")
              ->orWhere('serial_number', 'LIKE', "%{$searchTerm}%");
        });
    }

    $assets = $query->get();

    return response()->stream(function() use($assets) {
        $file = fopen('php://output', 'w');
        
        // CSV Headers - Standardized for College of Medicine
        fputcsv($file, [
            'Asset ID', 
            'Item Name', 
            'Category', 
            'Sub Category',
            'Serial Number', 
            'Asset Tag', 
            'Location', 
            'Item Status', 
            'Qty'
        ]);

        foreach ($assets as $asset) {
            // Determine location based on the hierarchy
            $location = $asset->unit->unit_name 
                ?? $asset->department->dept_name 
                ?? $asset->office->office_name 
                ?? 'Main Registry';

            fputcsv($file, [
                $asset->asset_id,
                $asset->item_name,
                $asset->category->name ?? 'N/A',
                $asset->serial_number ?? '---',
                $asset->asset_tag ?? 'TAG PENDING',
                $location,
                ucfirst($asset->status),
                $asset->quantity
            ]);
        }
        fclose($file);
    }, 200, [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ]);
}
}