<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FacultyController extends Controller
{
   public function index(Request $request): View
    {
        $query = Faculty::query();

        // Handle Search Query (Name or Code)
        if ($q = $request->input('q')) {
            $query->where(function($sub) use ($q) {
                $sub->where('faculty_name', 'LIKE', "%{$q}%")
                    ->orWhere('faculty_code', 'LIKE', "%{$q}%");
            });
        }

        // Handle Visibility Status Filter
        if ($status = $request->input('status')) {
            $query->where('is_active', $status);
        }

        $faculties = $query->with(['dean','dean.profile']) 
                           ->withCount('departments')
                           ->orderBy('faculty_name', 'asc')
                           ->paginate(15)
                           ->appends($request->query());

        return view('admin.manage_faculties.faculty', compact('faculties'));
    }

    

    

    public function create(): View
    {
        $faculty = new Faculty();
        $activeFacultyUsers = User::where('status', 'active')
            ->where('faculty_id', null)
            ->get();
        return view('admin.manage_faculties.faculty_create', compact('faculty', 'activeFacultyUsers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'faculty_name'    => 'required|string|max:255|unique:faculties,faculty_name',
            'faculty_code'    => 'nullable|string|max:50|unique:faculties,faculty_code',
            'faculty_dean_id' => 'nullable|exists:users,user_id',
            'faculty_address' => 'nullable|string',
            // Updated to include 'hidden' to match your UI
            'is_active'       => 'required|in:active,inactive,hidden', 
        ]);

        try {
            Faculty::create($request->all());
            return redirect()->route('admin.faculties.index')->with('success', 'Faculty created successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id): View
    {
        $faculty = Faculty::with('dean.profile')->findOrFail($id);
        $department_count = $faculty->departments()->count();
        $activeUsers = User::where('status', 'active')->get();
        return view('admin.manage_faculties.faculty_edit', compact('faculty', 'department_count', 'activeUsers'));
    }

    public function update(Request $request, $faculty_id): RedirectResponse
    {
        $request->validate([
            'faculty_name'    => 'required|string|max:255|unique:faculties,faculty_name,'. $faculty_id . ',faculty_id',
            'faculty_code'    => 'nullable|string|max:50|unique:faculties,faculty_code,'. $faculty_id . ',faculty_id',
            'faculty_dean_id' => 'nullable|exists:users,user_id',
            'faculty_address' => 'nullable|string',
            // Updated to include 'hidden' to match your UI
            'is_active'       => 'required|in:active,inactive,hidden',
        ]);

        try {
            $faculty = Faculty::findOrFail($faculty_id);
            $faculty->update($request->all());
            return redirect()->route('admin.faculties.index')->with('success', 'Faculty updated successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function destroy($id): RedirectResponse
    {
        $faculty = Faculty::findOrFail($id);
        if ($faculty->departments()->exists() || $faculty->users()->exists()) {
            return back()->with('error', "Cannot delete faculty. It has associated departments or users.");
        }
        $faculty->delete();
        return redirect()->route('admin.faculties.index')->with('success', 'Faculty deleted.');
    }

    public function searchDeans(Request $request)
{
    $q = $request->query('q');

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