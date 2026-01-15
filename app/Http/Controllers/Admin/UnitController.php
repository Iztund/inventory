<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;   
use App\Models\Office; 
use App\Models\User;      
use Illuminate\Http\RedirectResponse; 
use Illuminate\View\View;

class UnitController extends Controller
{
    public function index(Request $request): View
    {
        $query = Unit::query();

        if ($q = $request->input('q')) {
            $query->where('unit_name', 'LIKE', '%' . $q . '%')
                  ->orWhere('unit_code', 'LIKE', '%' . $q . '%');
        }

        // Eager load 'office' and 'supervisor' as defined in your Model
        $units = $query->with(['office', 'supervisor.profile'])
                       ->orderBy('unit_name', 'asc')
                       ->paginate(15) 
                       ->appends($request->query()); 

        return view('admin.manage_units.units', compact('units'));
    }

    public function create(): View
    {
        $unit = new Unit();
        $offices = Office::where('is_active', 'active')->orderBy('office_name')->get();

        return view('admin.manage_units.units_create', compact('unit', 'offices'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'unit_name' => 'required|string|max:255|unique:units,unit_name',
            'office_id' => 'required|exists:offices,office_id', 
            'unit_code' => 'nullable|string|max:50|unique:units,unit_code',
            'unit_head_id' => 'nullable|exists:users,user_id', // Matches your fillable
            'is_active' => 'required|in:active,inactive',
        ]);

        try {
            Unit::create([
                'unit_name' => $request->unit_name,
                'office_id' => $request->office_id, 
                'unit_code' => $request->unit_code, 
                'unit_head_id' => $request->unit_head_id,
                'is_active' => $request->is_active,
            ]);

            return redirect()->route('admin.units.index')
                             ->with('success', 'Unit created successfully!');

        } catch (\Exception $e) {
            return back()->withInput()
                         ->with('error', 'Failed to create unit: ' . $e->getMessage());
        }
    }

    public function edit(string $unit_id): View
    {
        $unit = Unit::findOrFail($unit_id);
        $offices = Office::orderBy('office_name')->get(); 
        
        $existingHead = null;
        // Using 'supervisor' relationship from your model
        if ($unit->supervisor) {
            $existingHead = [
                'id'   => $unit->supervisor->user_id,
                'text' => ($unit->supervisor->profile->full_name 
                        ?? $unit->supervisor->username)
                        . " (" . $unit->supervisor->email . ")",
            ];
        }

        return view('admin.manage_units.units_edit', compact('unit', 'offices', 'existingHead'));
    }

    public function update(Request $request, string $unit_id): RedirectResponse
    {
        $request->validate([
            'unit_name' => 'required|string|max:255|unique:units,unit_name,' . $unit_id . ',unit_id',
            'office_id' => 'required|exists:offices,office_id', 
            'unit_code' => 'nullable|string|max:50|unique:units,unit_code,' . $unit_id . ',unit_id',
            'unit_head_id' => 'nullable|exists:users,user_id',
            'is_active' => 'required|in:active,inactive',
        ]);

        try {
            $unit = Unit::findOrFail($unit_id);

            $unit->update([
                'unit_name' => $request->unit_name,
                'office_id' => $request->office_id, 
                'unit_code' => $request->unit_code,
                'unit_head_id' => $request->unit_head_id,
                'is_active' => $request->is_active,
            ]);

            return redirect()->route('admin.units.index')
                             ->with('success', "Unit updated successfully!");

        } catch (\Exception $e) {
            return back()->withInput()
                         ->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function destroy(string $unit_id): RedirectResponse
    {
        try {
            $unit = Unit::findOrFail($unit_id);
            
            if ($unit->users()->exists()) {
                return back()->with('error', "Cannot delete unit. It still has associated users.");
            }

            $unit->delete();
            return redirect()->route('admin.units.index')->with('success', "Unit deleted.");

        } catch (\Exception $e) {
            return back()->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }

    public function searchUnits(Request $request)
        {
            $q = $request->query('q');

            $unitsQuery = Unit::query()
                ->where('is_active', true); // optional, if you track active units

            if ($q) {
                $unitsQuery->where(function ($query) use ($q) {
                    $query->where('unit_name', 'like', "%{$q}%")
                        ->orWhere('unit_code', 'like', "%{$q}%");
                });
            }

            $units = $unitsQuery->take(20)->get();

            return response()->json(
                $units->map(function ($unit) {
                    return [
                        'id'   => $unit->unit_id,
                        'text' => "{$unit->unit_name} ({$unit->unit_code})",
                    ];
                })
            );
        }
}