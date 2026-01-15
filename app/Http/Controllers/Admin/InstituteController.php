<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Institute;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InstituteController extends Controller
{
    public function index(Request $request): View
    {
        $query = Institute::query();
        if ($q = $request->input('q')) {
            $query->where('institute_name', 'LIKE', "%{$q}%");
        }

        $institutes = $query->with('director')
                            ->orderBy('institute_name', 'asc')
                            ->paginate(15);

        return view('admin.manage_institutes.institutes', compact('institutes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'institute_name' => 'required|string|max:255|unique:institutes,institute_name',
            'director_id'    => 'nullable|exists:users,user_id',
            'is_active'      => 'required|in:active,inactive',
        ]);

        Institute::create($request->all());
        return redirect()->route('admin.institutes.index')->with('success', 'Institute created.');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'institute_name' => 'required|string|max:255|unique:institutes,institute_name,' . $id . ',institute_id',
        ]);

        $institute = Institute::findOrFail($id);
        $institute->update($request->all());
        return redirect()->route('admin.institutes.index')->with('success', 'Institute updated.');
    }

    public function destroy($id): RedirectResponse
    {
        $institute = Institute::findOrFail($id);
        if ($institute->users()->exists()) {
            return back()->with('error', "Institute has active staff members.");
        }
        $institute->delete();
        return redirect()->route('admin.institutes.index')->with('success', 'Institute deleted.');
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