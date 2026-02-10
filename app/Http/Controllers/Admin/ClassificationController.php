<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Asset;

class ClassificationController extends Controller
{
    public function index()
        {
            // Fetch all categories and subcategories with their parent relationships
            $categories = Category::withCount('subcategories')
                                    ->orderBy('category_name')
                                    ->take(10)
                                    ->get();
            $subcategories = Subcategory::with('category')
                                        ->orderBy('category_name')
                                        ->take(10)
                                        ->get();
            $dropdownData = [
        'categories' => Category::orderBy('category_name')->get(),
        
         ];
            return view('admin.Academic.classifications_index', compact('categories', 'subcategories', 'dropdownData'));
        }
    // --- CATEGORIES ---
    public function storeCategory(Request $request) {
        $request->validate(['category_name' => 'required|unique:categories,category_name']);
        Category::create($request->all());
        return back()->with('success', 'Category added to registry.');
    }

    public function updateCategory(Request $request, $id) {
        $category = Category::findOrFail($id);
        $category->update($request->all());
        return back()->with('success', 'Category updated.');
    }

    public function destroyCategory($id) {
        $category = Category::findOrFail($id);
        // Safety Check: Don't delete if assets exist
        if (Asset::where('category_id', $id)->exists()) {
            return back()->with('error', 'Cannot delete: Medical assets are currently linked to this category.');
        }
        $category->delete();
        return back()->with('success', 'Category removed.');
    }

    // --- SUBCATEGORIES ---
    public function storeSubcategory(Request $request) {
        Subcategory::create($request->all());
        return back()->with('success', 'Sub-classification added.');
    }

    public function updateSubcategory(Request $request, $id) {
        $sub = Subcategory::findOrFail($id);
        $sub->update($request->all());
        return back()->with('success', 'Sub-classification updated.');
    }

    public function destroySubcategory($id) {
        if (Asset::where('subcategory_id', $id)->exists()) {
            return back()->with('error', 'Cannot delete: Items are currently classified under this type.');
        }
        Subcategory::destroy($id);
        return back()->with('success', 'Sub-classification removed.');
    }
}
