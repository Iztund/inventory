<?php

namespace App\Http\Controllers;

use App\Models\{Asset, Category, Subcategory, Faculty, Department, Office, Unit, Institute, Audit, Submission};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Storage};
use Barryvdh\DomPDF\Facade\Pdf;

class AssetController extends Controller
{
    // ============================================
    // INDEX - Universal (role-based views + scoping)
    // ============================================
    public function index(Request $request)
{
    $user = Auth::user();
    $dropdownData = $this->getOrganizationalDropdownData();
    $extraData = [];

    // --- 1. DEFINE COMMON FILTER LOGIC ---
    $applyFilters = function ($query) use ($request) {
        if ($request->filled('search')) {
            $search = trim($request->search);
            // Detect which model/table we are currently filtering
            $modelTable = $query->getModel()->getTable();
            $query->where(function ($q) use ($search, $modelTable) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('submission_id', 'like', "%{$search}%");
                if ($modelTable === 'assets') {
                $q->orWhere('asset_tag', 'like', "%{$search}%");
            }
            });
        }

        if ($request->filled('faculty_id'))   $query->where('faculty_id', $request->faculty_id);
        if ($request->filled('dept_id'))      $query->where('dept_id', $request->dept_id);
        if ($request->filled('office_id'))    $query->where('office_id', $request->office_id);
        if ($request->filled('unit_id'))      $query->where('unit_id', $request->unit_id);
        if ($request->filled('institute_id')) $query->where('institute_id', $request->institute_id);
        if ($request->filled('category_id'))  $query->where('category_id', $request->category_id);
        if ($request->filled('subcategory_id')) $query->where('subcategory_id', $request->subcategory_id);
        
        return $query;
    };

    // --- 2. AUDITOR & ADMIN APPROVED ITEMS (Submission-Centric) ---
    if (
        ((int)$user->role_id === 3) || 
        ((int)$user->role_id === 1 && request()->routeIs('admin.approved_items.index'))
    ) {
        $query = Submission::with(['items', 'submittedBy.profile', 'reviewedBy.profile'])
            ->whereIn('status', ['approved', 'rejected']);

        if ($request->filled('search') && is_numeric($request->search)) {
            $query->where('submission_id', $request->search);
        }

        $query->whereHas('items', function ($iq) use ($applyFilters) {
            $applyFilters($iq);
        });

        // --- START: GLOBAL STATS CALCULATION ---
        // We clone the query so we don't mess up the pagination later
        $statsQuery = clone $query;
        $fullCollection = $statsQuery->get(); // Get all filtered records for math

        $extraData['totalItemsCount'] = $fullCollection->sum(fn($s) => $s->items->count());
        $extraData['totalValue'] = $fullCollection->sum(fn($s) => $s->items->sum(fn($i) => ($i->cost ?? 0) * ($i->quantity ?? 1)));
        
        // Breakdown counts across ALL pages
        $extraData['countNew'] = $fullCollection->where('submission_type', 'new_purchase')->count();
        $extraData['countTransfer'] = $fullCollection->where('submission_type', 'transfer')->count();
        $extraData['countRepair'] = $fullCollection->where('submission_type', 'maintenance')->count();
        $extraData['countDisposal'] = $fullCollection->where('submission_type', 'disposal')->count();

        $submissions = $query->latest('submission_id')->paginate(15)->withQueryString();
        // --- END: GLOBAL STATS CALCULATION ---

        $submissions = $query->latest('submission_id')->paginate(15)->withQueryString();

        $view = match((int)$user->role_id) {
            1 => 'admin.approved_items.index',
            3 => 'auditor.approved_items.index',
            default => abort(403),
        };
        
        // Pass totalItems and totalValue to the view
        return view($view, array_merge(
            compact('submissions'), $extraData, $dropdownData));
            }

            // --- 3. ADMIN & STAFF ROLES (Asset-Centric, for /assets routes) ---
            $assetQuery = Asset::with([
                'category', 'subcategory', 'faculty', 'department', 'office', 'unit', 'institute'
            ]);

            // --- 3. ADMIN & STAFF ROLES (Asset-Centric) ---
            $assetQuery = Asset::with([
                'category', 'subcategory', 'faculty', 'department', 'office', 'unit', 'institute'
            ])->applyScopeForUser($user); // Updated: Chain directly to the query

            $applyFilters($assetQuery);

            $assets = $assetQuery->latest('updated_at')->paginate(20)->withQueryString();

            // Staff Extras
            if ((int)$user->role_id === 2) {
                $extraData['stats'] = $this->getAssetStats($user);
                
                // Updated: Call scope directly from the Model class
                $extraData['totalItems'] = Asset::applyScopeForUser($user)->sum('quantity');
            }

    $view = match ((int)$user->role_id) {
        1 => 'admin.manage_assets.assets',
        2 => 'staff.assets.index',
        default => abort(403),
    };

    return view($view, array_merge(compact('assets'), $dropdownData, $extraData));
}

    // ============================================
    // SHOW - Universal (role-based views)
    // ============================================
  public function show($submission_id)
{
    $user = Auth::user();

    // 1. Fetch the Submission first - this is the "Source of Truth"
    $submission = Submission::with([
        'submittedBy.profile',
        'reviewedBy.profile',
        'items.category',
        'items.subcategory',
        'items.asset.faculty',
        'items.asset.department',
        'items.asset.office',
        'items.asset.unit',
        'items.asset.institute'
    ])->findOrFail($submission_id);

    // 2. Extract the items
    $items = $submission->items;

    // 3. Handle cases where there are no items/assets
    if ($items->isEmpty()) {
        // Option A: Redirect back with an error
        return redirect()->back()->with('error', 'This submission contains no items.');
        
        // Option B: Initialize a blank Asset object so the view doesn't crash
        // $asset = new \App\Models\Asset();
    }

    // 4. Set $asset to the first item's asset for the sidebar details
    // We use optional() or null coalescing to prevent "Trying to get property of non-object"
    $asset = $items->first()->asset;

    // 5. Get Audit History
    $history = Audit::whereIn('submission_item_id', $items->pluck('submission_item_id'))
        ->with('auditor.profile')
        ->latest()
        ->get();

    // 6. Role-based View Switching
    $view = match((int)$user->role_id) {
        1 => 'admin.assets.show',
        2 => 'staff.assets.show',
        3 => 'auditor.approved_items.show',
        default => abort(403),
    };

    return view($view, compact('asset', 'history', 'submission', 'items'));
}

    // ============================================
    // CREATE - Admin Only
    // ============================================
    public function create()
    {
        $this->authorizeRole([1]);

        $categories    = Category::orderBy('category_name')->get();
        $subcategories = Subcategory::orderBy('subcategory_name')->get();
        $faculties     = Faculty::orderBy('faculty_name')->get();
        $departments   = Department::orderBy('dept_name')->get();
        $offices       = Office::orderBy('office_name')->get();
        $units         = Unit::orderBy('unit_name')->get();
        $institutes    = Institute::orderBy('institute_name')->get();

        return view('admin.assets.create', compact(
            'categories', 'subcategories', 'faculties', 'departments',
            'offices', 'units', 'institutes'
        ));
    }

    // ============================================
    // STORE - Admin Only
    // ============================================
    public function store(Request $request)
    {
        $this->authorizeRole([1]);

        $validated = $request->validate([
            'item_name'         => 'required|string|max:255',
            'serial_number'     => 'nullable|string|max:255|unique:assets',
            'asset_tag'         => 'nullable|string|max:100|unique:assets',
            'category_id'       => 'required|exists:categories,category_id',
            'subcategory_id'    => 'nullable|exists:subcategories,subcategory_id',
            'quantity'          => 'required|integer|min:1',
            'unit_cost'         => 'required|numeric|min:0',
            'purchase_date'     => 'nullable|date',
            'status'            => 'required|in:available,assigned,maintenance,retired',
            'current_faculty_id'   => 'nullable|exists:faculties,faculty_id',
            'current_dept_id'      => 'nullable|exists:departments,dept_id',
            'current_office_id'    => 'nullable|exists:offices,office_id',
            'current_unit_id'      => 'nullable|exists:units,unit_id',
            'current_institute_id' => 'nullable|exists:institutes,institute_id',
        ]);

        Asset::create($validated);

        return redirect()->route('admin.assets.index')
            ->with('success', 'Asset created successfully.');
    }

    // ============================================
    // EDIT - Admin Only
    // ============================================
    public function edit($asset_id)
    {
        $this->authorizeRole([1]);

        $asset = Asset::findOrFail($asset_id);

        $categories    = Category::orderBy('category_name')->get();
        $subcategories = Subcategory::orderBy('subcategory_name')->get();
        $faculties     = Faculty::orderBy('faculty_name')->get();
        $departments   = Department::orderBy('dept_name')->get();
        $offices       = Office::orderBy('office_name')->get();
        $units         = Unit::orderBy('unit_name')->get();
        $institutes    = Institute::orderBy('institute_name')->get();

        return view('admin.assets.edit', compact(
            'asset', 'categories', 'subcategories', 'faculties', 'departments',
            'offices', 'units', 'institutes'
        ));
    }

    // ============================================
    // UPDATE - Admin Only
    // ============================================
    public function update(Request $request, $asset_id)
    {
        $this->authorizeRole([1]);

        $asset = Asset::findOrFail($asset_id);

        $validated = $request->validate([
            'item_name'         => 'required|string|max:255',
            'serial_number'     => 'nullable|string|max:255|unique:assets,serial_number,'.$asset->asset_id.',asset_id',
            'asset_tag'         => 'nullable|string|max:100|unique:assets,asset_tag,'.$asset->asset_id.',asset_id',
            'category_id'       => 'required|exists:categories,category_id',
            'subcategory_id'    => 'nullable|exists:subcategories,subcategory_id',
            'quantity'          => 'required|integer|min:1',
            'unit_cost'         => 'required|numeric|min:0',
            'purchase_date'     => 'nullable|date',
            'status'            => 'required|in:available,assigned,maintenance,retired',
            'current_faculty_id'   => 'nullable|exists:faculties,faculty_id',
            'current_dept_id'      => 'nullable|exists:departments,dept_id',
            'current_office_id'    => 'nullable|exists:offices,office_id',
            'current_unit_id'      => 'nullable|exists:units,unit_id',
            'current_institute_id' => 'nullable|exists:institutes,institute_id',
        ]);

        $asset->update($validated);

        return redirect()->route('admin.assets.index')
            ->with('success', 'Asset updated successfully.');
    }

    // ============================================
    // DESTROY - Admin Only
    // ============================================
    public function destroy($asset_id)
    {
        $this->authorizeRole([1]);

        $asset = Asset::findOrFail($asset_id);
        $asset->delete();

        return redirect()->route('admin.assets.index')
            ->with('success', 'Asset deleted successfully.');
    }

    // ============================================
    // EXPORT PDF - Staff Only
    // ============================================
    public function exportPdf(Request $request)
        {
            $this->authorizeRole([2]);

            $user = Auth::user();

            // Updated: Calling the scope directly on the Asset model query
            $query = Asset::with(['category', 'subcategory', 'unit', 'department'])
                ->applyScopeForUser($user);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('item_name', 'LIKE', "%{$search}%")
                    ->orWhere('asset_tag', 'LIKE', "%{$search}%")
                    ->orWhere('serial_number', 'LIKE', "%{$search}%");
                });
            }

            $assets = $query->latest()->get();
            $totalItems = $assets->sum('quantity');

            $pdf = Pdf::loadView('staff.assets.export-pdf', compact('assets', 'totalItems', 'user'));

            return $pdf->setPaper('a4', 'landscape')
                    ->download('Inventory_Report_' . now()->format('Y-m-d') . '.pdf');
        }
    // ============================================
    // EXPORT CSV - Staff Only
    // ============================================
    public function exportCsv(Request $request)
    {
        $this->authorizeRole([2]);

        $user = Auth::user();
        $fileName = 'Inventory_Assets_' . now()->format('d_m_Y') . '.csv';

        $query = Asset::applyScopeForUser($user)->with(['category', 'subcategory', 'unit', 'department', 'office']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('item_name', 'LIKE', "%{$search}%")
                  ->orWhere('asset_tag', 'LIKE', "%{$search}%")
                  ->orWhere('serial_number', 'LIKE', "%{$search}%");
            });
        }

        $assets = $query->get();

        return response()->stream(function() use ($assets) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Asset ID', 'Item Name', 'Category', 'Sub Category',
                'Serial Number', 'Asset Tag', 'Location', 'Status', 'Qty'
            ]);

            foreach ($assets as $asset) {
                $location = $asset->unit?->unit_name
                    ?? $asset->department?->dept_name
                    ?? $asset->office?->office_name
                    ?? $asset->institute?->institute_name
                    ?? $asset->faculty?->faculty_name
                    ?? 'Central Registry';

                fputcsv($file, [
                    $asset->asset_id,
                    $asset->item_name,
                    $asset->category?->category_name ?? 'N/A',
                    $asset->subcategory?->subcategory_name ?? 'N/A',
                    $asset->serial_number ?? '---',
                    $asset->asset_tag ?? 'TAG PENDING',
                    $location,
                    ucfirst($asset->status),
                    $asset->quantity
                ]);
            }
            fclose($file);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0'
        ]);
    }

    // ============================================
    // HELPERS
    // ============================================


    private function applyHierarchyFilters($query, Request $request): void
    {
        if ($request->filled('faculty_id'))   $query->where('current_faculty_id', $request->faculty_id);
        if ($request->filled('dept_id'))      $query->where('current_dept_id', $request->dept_id);
        if ($request->filled('office_id'))    $query->where('current_office_id', $request->office_id);
        if ($request->filled('unit_id'))      $query->where('current_unit_id', $request->unit_id);
        if ($request->filled('institute_id')) $query->where('current_institute_id', $request->institute_id);
    }

    private function getOrganizationalDropdownData(): array
    {
        return [
            'faculties'     => Faculty::orderBy('faculty_name')->get(),
            'departments'   => Department::orderBy('dept_name')->get(),
            'offices'       => Office::orderBy('office_name')->get(),
            'units'         => Unit::orderBy('unit_name')->get(),
            'institutes'    => Institute::orderBy('institute_name')->get(),
            'categories'    => Category::orderBy('category_name')->get(),
            'subcategories' => Subcategory::orderBy('subcategory_name')->get(),
        ];
    }

    private function getAssetStats($user)
        {
            // Call the scope directly from the Asset model
            return Asset::applyScopeForUser($user)
                ->select('assets.status', DB::raw('count(*) as count'))
                ->groupBy('assets.status')
                ->pluck('count', 'status')
                ->toArray();
        }

    private function getAuditStats($user)
    {
        return Submission::where('submitted_by_user_id', $user->user_id)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    private function getSubcategorySummary($user)
        {
            // Use the new scope directly on the Model
            return Asset::applyScopeForUser($user)
                ->leftJoin('subcategories', 'assets.subcategory_id', '=', 'subcategories.subcategory_id')
                ->select(
                    DB::raw('COALESCE(subcategories.subcategory_name, "Uncategorized") as name'),
                    DB::raw('SUM(assets.quantity) as total_qty')
                )
                ->groupBy('subcategories.subcategory_name')
                ->orderBy('total_qty', 'desc')
                ->get();
        }
    private function authorizeRole(array $allowedRoles)
    {
        if (!in_array((int)Auth::user()->role_id, $allowedRoles)) {
            abort(403, 'Unauthorized action.');
        }
    }
}