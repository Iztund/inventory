<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Office;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OfficeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Office::query();

        // Handle Search Query (Name or Code)
        if ($q = $request->input('q')) {
            $query->where(function($sub) use ($q) {
                $sub->where('office_name', 'LIKE', "%{$q}%")
                    ->orWhere('office_code', 'LIKE', "%{$q}%");
            });
        }

        // Handle Status Filter
        if ($status = $request->input('status')) {
            $query->where('is_active', $status);
        }

        $offices = $query->with(['head', 'head.profile']) 
                         ->withCount('units')
                         ->orderBy('office_name', 'asc')
                         ->paginate(15)
                         ->appends($request->query());

        return view('admin.manage_offices.offices', compact('offices'));
    }

    public function create(): View
    {
        $office = new Office();
        $activeOfficeStaff = User::where('status', 'active')
        ->whereHas('office') // Assuming the relationship name is 'office'
        ->count();
        return view('admin.manage_offices.office_create', compact('office', 'activeUsers', 'activeOfficeStaff'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'office_name'    => 'required|string|max:255|unique:offices,office_name',
            'office_code'    => 'nullable|string|max:50|unique:offices,office_code',
            'office_head_id' => 'nullable|exists:users,user_id',
            'is_active'      => 'required|in:active,inactive,hidden', 
        ]);

        try {
            Office::create($request->all());
            return redirect()->route('admin.offices.index')->with('success', 'Office created successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id): View
    {
        $office = Office::with('head.profile')->findOrFail($id);
        $unit_count = $office->units()->count();
        $activeUsers = User::where('status', 'active')->get();
        
        return view('admin.manage_offices.office_edit', compact('office', 'unit_count', 'activeUsers'));
    }

    public function update(Request $request, $office_id): RedirectResponse
    {
        $request->validate([
            'office_name'    => 'required|string|max:255|unique:offices,office_name,' . $office_id . ',office_id',
            'office_code'    => 'nullable|string|max:50|unique:offices,office_code,' . $office_id . ',office_id',
            'office_head_id' => 'nullable|exists:users,user_id',
            'is_active'      => 'required|in:active,inactive,hidden',
        ]);

        try {
            $office = Office::findOrFail($office_id);
            $office->update($request->all());
            return redirect()->route('admin.offices.index')->with('success', 'Office updated successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function destroy($id): RedirectResponse
    {
        $office = Office::findOrFail($id);
        
        // Prevent deletion if children exist (Units or Users)
        if ($office->units()->exists() || $office->users()->exists()) {
            return back()->with('error', "Cannot delete office. It has associated units or staff members.");
        }
        
        $office->delete();
        return redirect()->route('admin.offices.index')->with('success', 'Office deleted successfully.');
    }

    /**
     * Reusable search for AJAX dropdowns (like Select2)
     */
    public function searchHeads(Request $request)
{
    $q = $request->query('q');

    if (!$q) return response()->json([]);

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
            'text' => ($user->full_name ? $user->full_name : $user->username) . ' (' . $user->email . ')',
        ];
    });

    return response()->json($formattedUsers);
}
}