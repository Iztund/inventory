<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource (Manage Departments).
     */
    public function index()
    {
        // Fetch and display all departments
        $departments = []; // Replace with actual Department Model query
        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new department.
     */
    public function create()
    {
        return view('admin.departments.create');
    }

    /**
     * Store a newly created department in storage.
     */
    public function store(Request $request)
    {
        // Validation and creation logic
        return redirect()->route('admin.departments.index')->with('success', 'Department created successfully.');
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit(string $id)
    {
        // Fetch department and return edit view
        return view('admin.departments.edit', compact('id'));
    }

    /**
     * Update the specified department in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validation and update logic
        return redirect()->route('admin.departments.index')->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified department from storage.
     */
    public function destroy(string $id)
    {
        // Deletion logic
        return redirect()->route('admin.departments.index')->with('success', 'Department deleted successfully.');
    }
}