<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Institute;
use App\Models\User;
use App\Models\Asset;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InstituteController extends Controller
{
    public function index(Request $request): View
    {
        $query = Institute::query();

        if ($q = $request->input('q')) {
            $query->where(function($sub) use ($q) {
                $sub->where('institute_name', 'LIKE', "%{$q}%")
                    ->orWhere('institute_code', 'LIKE', "%{$q}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('is_active', $status);
        }

        $activeStaffCount =User::where('status', 'active')
                        ->whereHas('institute')
                             ->count();
        $activeInstituteCount = Institute::where('is_active', 'active')
                          ->where('is_active', 'active')
                          ->count();
        $assignedHeadsCount = Institute::whereNotNull('institute_director_id')->count();
        $institutes = $query->with(['director', 'director.profile']) 
                           ->orderBy('institute_name', 'asc')
                           ->paginate(15)
                           ->appends($request->query());
        return view('admin.manage_institutes.institutes', compact('institutes', 'activeStaffCount', 'assignedHeadsCount','activeInstituteCount'));
    }

    public function create(): View
    {
        $institute = new Institute();
        $activeInstituteStaff = User::where('status', 'active')
        ->whereHas('institute') // Assuming the relationship name is 'office'
        ->count();
        return view('admin.manage_institutes.institutes_create', compact('institute', 'activeInstituteStaff'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'institute_name'        => 'required|string|max:255|unique:institutes,institute_name',
            'institute_code'        => 'nullable|string|max:50|unique:institutes,institute_code',
            'institute_director_id' => 'nullable|exists:users,user_id',
            'institute_address'     => 'nullable|string|max:255|unique:institutes,institute_address',
            'is_active'             => 'required|in:active,inactive', 
        ]);

        try {
            Institute::create($request->all());
            return redirect()->route('admin.institutes.index')->with('success', 'Institute created successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id): View
    {
        $institute = Institute::with('director.profile')->findOrFail($id);
        $activeStaffs = User::where('status', 'active')
        ->whereHas('institute', function($q) use ($id) {
                $q->where('institute_id', $id);
            })
        ->get();
        $ItemCount = Asset::where('current_institute_id', $id)->count();
        return view('admin.manage_institutes.institutes_edit', compact('institute', 'activeStaffs', 'ItemCount'));
    }

    public function update(Request $request, $institute_id): RedirectResponse
    {
        $request->validate([
            'institute_name'        => 'required|string|max:255|unique:institutes,institute_name,' . $institute_id . ',institute_id',
            'institute_code'        => 'nullable|string|max:50|unique:institutes,institute_code,' . $institute_id . ',institute_id',
            'institute_director_id' => 'nullable|exists:users,user_id',
            'is_active'             => 'required|in:active,inactive,hidden',
        ]);

        try {
            $institute = Institute::findOrFail($institute_id);
            $institute->update($request->all());
            return redirect()->route('admin.institutes.index')->with('success', 'Institute updated successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function destroy($id): RedirectResponse
    {
        $institute = Institute::findOrFail($id);
        
        if ($institute->users()->exists()) {
            return back()->with('error', "Cannot delete institute. Researchers or staff are still assigned to it.");
        }
        
        $institute->delete();
        return redirect()->route('admin.institutes.index')->with('success', 'Institute deleted successfully.');
    }


    public function searchInstitutes(Request $request)
            {
                $search = $request->input('term', '');

                $query = Institute::query()
                    ->where('is_active', true);

                if ($search) {
                    $query->where(function($q) use ($search) {
                        $q->where('institute_name', 'like', "%{$search}%")
                        ->orWhere('institute_code', 'like', "%{$search}%");
                    });
                }

                // Paginate results (Select2 expects pagination)
                $institutes = $query->paginate(20);

                // Format for Select2
                $formatted = $institutes->getCollection()->map(function ($institute) {
                    return [
                        'id' => $institute->institute_id,
                        'text' => "{$institute->institute_name} ({$institute->institute_code})"
                    ];
                });

                return response()->json([
                    'results' => $formatted,
                    'pagination' => [
                        'more' => $institutes->hasMorePages()
                    ]
                ]);
            }
}