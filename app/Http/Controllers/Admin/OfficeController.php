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
        if ($q = $request->input('q')) {
            $query->where('office_name', 'LIKE', "%{$q}%");
        }

        $offices = $query->with('officeHead')
                         ->orderBy('office_name', 'asc')
                         ->paginate(15);

        return view('admin.manage_offices.offices', compact('offices'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'office_name'    => 'required|string|max:255|unique:offices,office_name',
            'office_head_id' => 'nullable|exists:users,user_id',
            'is_active'      => 'required|in:active,inactive',
        ]);

        Office::create($request->all());
        return redirect()->route('admin.offices.index')->with('success', 'Office created.');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'office_name' => 'required|string|max:255|unique:offices,office_name,' . $id . ',office_id',
            'is_active'   => 'required|in:active,inactive',
        ]);

        $office = Office::findOrFail($id);
        $office->update($request->all());
        return redirect()->route('admin.offices.index')->with('success', 'Office updated.');
    }

    public function destroy($id): RedirectResponse
    {
        $office = Office::findOrFail($id);
        if ($office->users()->exists()) {
            return back()->with('error', "Office has users assigned to it.");
        }
        $office->delete();
        return redirect()->route('admin.offices.index')->with('success', 'Office deleted.');
    }
}