<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/*
 * AssetController handles the full CRUD lifecycle for assets/inventory items.
 * These routes are typically protected by an 'admin' or 'staff' role middleware.
 */
class AssetController extends Controller
{
    /**
     * Display a listing of the assets (The Admin Inventory Index).
     * The `admin.assets.index` route will hit this method.
     */
    public function index(Request $request)
        {
            $user = Auth::user();
            $query = Asset::with(['unit.office', 'department.faculty', 'institute']);

            // --- 1. ROLE-BASED SCOPING ---
            if ($user->role_id == 2) { // Staff
                if ($user->unit_id) {
                    $query->where('current_unit_id', $user->unit_id);
                } elseif ($user->department_id) {
                    $query->where('current_dept_id', $user->department_id);
                } elseif ($user->institute_id) {
                    $query->where('current_institute_id', $user->institute_id);
                }
            } else {
                // --- 2. ADMIN-ONLY FILTERS ---
                // Only admins get to use these specific filters
                if ($request->filled('unit_id')) $query->where('current_unit_id', $request->unit_id);
                if ($request->filled('dept_id')) $query->where('current_dept_id', $request->dept_id);
                if ($request->filled('institute_id')) $query->where('current_institute_id', $request->institute_id);
            }

            // --- 3. SHARED SEARCH ---
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('item_name', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
                });
            }

            $assets = $query->latest()->paginate(20)->withQueryString();

            // Decide which view to return
            $view = ($user->role_id == 2) ? 'staff.assets.index' : 'admin.manage_assets.assets';

            // Calculate stats ONLY if needed for the view
            return view($view, compact('assets'));
        }
    /**
     * Show the form for creating a new asset.
     */
    public function create()
    {
        // Fetch necessary data for form dropdowns
        $categories = Category::all();
        $departments = Department::all();

        return view('admin.manage_assets.assets_create', compact('categories', 'departments'));
    }

    /**
     * Store a newly created asset in storage.
     */
    public function store(Request $request)
    {
        // Basic validation
        $validated = $request->validate([
            'category_id'     => 'required|exists:categories,category_id',
            'subcategory_id'  => 'nullable|exists:subcategories,subcategory_id',
            'current_dept_id' => 'required|exists:departments,dept_id',
            'serial_number'   => 'nullable|string|max:255|unique:assets,serial_number',
            'item_name'       => 'required|string|max:255', // Changed from product_name
            'purchase_price'  => 'required|numeric|min:0', // Changed from unit_cost
            'quantity'        => 'required|integer|min:1',
            'status'          => 'required|in:available,assigned,maintenance,retired',
        ]);

        Asset::create($validated);

        return redirect()->route('admin.assets.index')->with('success', 'Asset created successfully.');
    }

    /**
     * Display the specified asset.
     */
    public function show(Asset $asset)
    {
        return view('admin.assets.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified asset.
     */
    public function edit(Asset $asset)
    {
        $categories = Category::all();
        $departments = Department::all();

        return view('admin.assets.edit', compact('asset', 'categories', 'departments'));
    }

    /**
     * Update the specified asset in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        // Basic validation
        $validated = $request->validate([
            'category_id'       => 'required|exists:categories,category_id',
            'subcategory_id'    => 'nullable|exists:subcategories,subcategory_id',
            'current_dept_id'   => 'required|exists:departments,dept_id',
            // Allow serial number to be null or unique, ignoring the current asset's serial
            'serial_number'     => 'nullable|string|max:255|unique:assets,serial_number,' . $asset->asset_id . ',asset_id',
            'item_name'         => 'required|string|max:255',
            'description'       => 'nullable|string',
            'purchase_date'     => 'nullable|date',
            'unit_cost'         => 'required|numeric|min:0',
            'quantity'          => 'required|integer|min:1',
            'status'            => 'required|in:available,assigned,maintenance,retired',
        ]);

        $asset->update($validated);

        return redirect()->route('admin.assets.index')->with('success', 'Asset updated successfully.');
    }

    /**
     * Remove the specified asset from storage.
     */
    public function destroy(Asset $asset)
            {
                $asset->delete();

                return redirect()->route('admin.assets.index')->with('success', 'Asset deleted successfully.');
            }
    
}