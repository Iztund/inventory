<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department; 
use App\Models\User;       
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse; 
use Illuminate\View\View;
use App\Models\Faculty;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments with smart filtering for Hidden/Orphaned units.
     */
    public function index(Request $request): View
{
    // 1. Fetch faculties for the filter dropdown
    // We only want active faculties to show in the filter list
    $faculties = Faculty::where('is_active', 'active')
                    ->orderBy('faculty_name', 'asc')
                    ->get();

    // 2. Build the base query
    $query = Department::with(['faculty', 'deptHead.profile'])
             ->withCount('assets'); 

    // 3. Handle Search with Logical Grouping
    if ($request->filled('q')) {
        $search = $request->q;
        $query->where(function ($q) use ($search) {
            $q->where('dept_name', 'like', "%{$search}%")
              ->orWhere('dept_code', 'like', "%{$search}%");
        });
    }

    // 4. Handle Faculty Filtering
    if ($request->filled('faculty_id')) {
        $query->where('faculty_id', $request->faculty_id);
    }

    // 5. Handle Status Filtering (The "Hidden" logic)
    if ($request->status == 'hidden') {
        $query->where('is_active', 'active')
              ->whereHas('faculty', function($q) {
                  $q->where('is_active', '!=', 'active');
              });
    } elseif ($request->status == 'active') {
        $query->where('is_active', 'active')
              ->whereHas('faculty', function($q) {
                  $q->where('is_active', 'active');
              });
    } elseif ($request->status == 'inactive') {
        $query->where('is_active', 'inactive');
    }

    // 6. Calculate counts for the dashboard cards
    $orphanCount = Department::where('is_active', 'active')
        ->whereHas('faculty', function($q) {
            $q->where('is_active', '!=', 'active');
        })->count();

    $department_active_count = Department::where('is_active', 'active')
        ->whereHas('faculty', function($q) {
            $q->where('is_active', 'active');
        })->count();

    $assignedHeadsCount = Department::whereNotNull('dept_head_id')->count();

    // 7. Finalize pagination
    $departments = $query->orderBy('dept_name', 'asc')
                         ->paginate(15)
                         ->withQueryString();

    return view('admin.manage_depts.depts', compact(
        'departments', 
        'faculties', 
        'orphanCount', 
        'department_active_count',
        'assignedHeadsCount'
    ));
}
    public function create(): View
    {
        $department = new Department();
        // Only show active faculties when creating new depts
        $faculties = Faculty::where('is_active', 'active')->orderBy('faculty_name')->get();
        $activeUsers = User::where('status', 'active')
                        ->WhereHas('faculty')
                        ->get();

        return view('admin.manage_depts.depts_create', compact('department', 'faculties', 'activeUsers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'faculty_id'   => 'required|exists:faculties,faculty_id',
            'dept_name'    => 'required|string|max:255|unique:departments,dept_name',
            'dept_code'    => 'nullable|string|max:50|unique:departments,dept_code',
            'dept_head_id' => 'nullable|exists:users,user_id',
            'is_active'    => 'required|in:active,inactive',
        ]);

        try {
            Department::create($request->only([
                'faculty_id', 'dept_name', 'dept_code', 'dept_head_id', 'is_active'
            ]));

            return redirect()->route('admin.departments.index')
                             ->with('success', 'Department created successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create: ' . $e->getMessage());
        }
    }

    public function edit($dept_id): View
    {
        $department = Department::with(['deptHead.profile', 'faculty'])->findOrFail($dept_id);
        
        // Show all faculties in edit mode so an admin can fix an orphaned dept
        $faculties = Faculty::orderBy('faculty_name')->get();
        $activeUsers = User::where('status', 'active')->get();

        $existingHead = null;
        if ($department->deptHead) {
            $existingHead = [
                'id'   => $department->deptHead->user_id,
                'text' => ($department->deptHead->profile->full_name ?? $department->deptHead->username)
                          . " (" . $department->deptHead->email . ")",
            ];
        }

        return view('admin.manage_depts.depts_edit', compact('department', 'faculties', 'existingHead', 'activeUsers'));
    }

    public function update(Request $request, $dept_id): RedirectResponse
    {
        $request->validate([
            'faculty_id'   => 'required|exists:faculties,faculty_id',
            'dept_name'    => 'required|string|max:255|unique:departments,dept_name,' . $dept_id . ',dept_id',
            'dept_code'    => 'nullable|string|max:50|unique:departments,dept_code,' . $dept_id . ',dept_id',
            'dept_head_id' => 'nullable|exists:users,user_id',
            'is_active'    => 'required|in:active,inactive',
        ]);

        try {
            $department = Department::findOrFail($dept_id);
            $department->update($request->all());
            
            return redirect()->route('admin.departments.index')
                             ->with('success', "Department '{$department->dept_name}' updated.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function destroy(string $dept_id): RedirectResponse
    {
        try {
            $department = Department::findOrFail($dept_id);
            
            // Critical: Check for assets before deleting in an inventory system
            if ($department->assets()->exists()) {
                return back()->with('error', "Cannot delete. This department still has inventory assets assigned to it.");
            }

            if ($department->users()->exists()) {
                return back()->with('error', "Cannot delete. Staff members are still assigned here.");
            }

            $department->delete();
            return redirect()->route('admin.departments.index')->with('success', "Department deleted.");
        } catch (\Exception $e) {
            return back()->with('error', 'Database restriction prevents deletion.');
        }
    }

    // In DepartmentController.php

public function searchDepartments(Request $request)
{
    $search = $request->input('term', ''); 

    $query = Department::query()
        ->where('is_active', true);
    
    if ($search) {
        // Wrap search in a function to maintain the is_active constraint
        $query->where(function($q) use ($search) {
            $q->where('dept_name', 'like', "%{$search}%")
              ->orWhere('dept_code', 'like', "%{$search}%");
        });
    }

    $departments = $query->paginate(30);

    $formatted_departments = $departments->getCollection()->map(function ($department) {
        return [
            'id' => $department->dept_id,
            'text' => "{$department->dept_name} ({$department->dept_code})" 
        ];
    });

    return response()->json([
        'results' => $formatted_departments,
        'pagination' => [
            'more' => $departments->hasMorePages() 
        ]
    ]);
}



}
