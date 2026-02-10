<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Role;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Unit;
use App\Models\Office;
use App\Models\Institute;

class UserController extends Controller
{
    // List users with search
    public function index(Request $request)
    {
        $query = User::with([
            'role', 

            'faculty', 
            'department', 
            'unit', 
            'office', 
            'institute.faculty',
            'profile'
        ]);

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $searchTerm = "%{$search}%";

                $q->where('username', 'like', $searchTerm)
                ->orWhere('email', 'like', $searchTerm)
                ->orWhereHas('profile', function ($qProfile) use ($searchTerm) {
                    // Instead of searching one 'full_name' column, search the three parts
                    $qProfile->where('first_name', 'like', $searchTerm)
                            ->orWhere('middle_name', 'like', $searchTerm)
                            ->orWhere('last_name', 'like', $searchTerm);
                });
            });
        }
        $activeCount = (clone $query)->where('status', 'active')->count();
        $inactiveCount = (clone $query)->where('status', 'inactive')->count();
        $users = $query->latest('user_id')->paginate(20);
        return view('admin.manage_users.users', compact('users', 'activeCount', 'inactiveCount'));
    }

    // Show create form
    public function create()
    {
        $roles = Role::all();
        $faculties = Faculty::all();
        $departments = Department::all();
        $units = Unit::all();
        $offices = Office::all();
        $institutes = Institute::all();

        return view('admin.manage_users.users_create', compact('roles', 'faculties', 'departments', 'units', 'offices', 'institutes'));
    }

    // Store new user
    public function store(Request $request)
    {
        $data = $request->all();
        $data = array_map(function ($value) {
            return $value === '' ? null : $value;
        }, $data);

        $validated = $request->validate([
            'full_name'     => 'nullable|string|max:255',
            'username'      => 'required|string|max:255|unique:users,username',
            'email'         => 'required|string|email|max:255|unique:users,email',
            'role_id'       => 'required|exists:roles,role_id',
            
            'faculty_id'    => 'nullable|exists:faculties,faculty_id',
            'institute_id'  => 'nullable|exists:institutes,institute_id',
            'office_id'     => 'nullable|exists:offices,office_id',
            
            'dept_id'       => 'nullable|exists:departments,dept_id',
            'unit_id'       => 'nullable|exists:units,unit_id',
            
            'status'        => 'required|in:active,inactive',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:500',
        ]);

        $primaryAffiliationKeys = ['faculty_id', 'institute_id', 'office_id'];
        $setAffiliations = array_filter(Arr::only($validated, $primaryAffiliationKeys));

        if (count($setAffiliations) > 1) {
             return back()->withErrors([
                'primary_affiliation_error' => 'Please select only ONE primary organizational affiliation (Faculty, Institute, OR Office).'
            ])->withInput();
        }

        try {
            DB::beginTransaction();
            $currentYear = date('Y');
            $defaultPasswordString = $validated['username'] . '@' . $currentYear;

            $user = User::create([
                'username'      => $validated['username'],
                'email'         => $validated['email'],
                'password'      => Hash::make($defaultPasswordString),
                'must_change_password' => true,
                'role_id'       => $validated['role_id'],
                'faculty_id'    => $validated['faculty_id'] ?? null,
                'institute_id'  => $validated['institute_id'] ?? null,
                'office_id'     => $validated['office_id'] ?? null,
                'dept_id'       => $validated['dept_id'] ?? null,
                'unit_id'       => $validated['unit_id'] ?? null,
                'status'        => $validated['status'],
            ]);

            $fullName = trim($validated['full_name']);
            $nameParts = explode(' ', $fullName);

            $firstName = $nameParts[0] ?? '';
            $lastName = count($nameParts) > 1 ? end($nameParts) : '';
            $middleName = '';

            if (count($nameParts) > 2) {
                // Collect everything between the first and last name as the middle name
                $middleName = implode(' ', array_slice($nameParts, 1, -1));
            }

            UserProfile::create([
                'user_id'      => $user->user_id,
                'first_name'   => $firstName,
                'middle_name'  => $middleName,
                'last_name'    => $lastName,
                'phone'        => $validated['phone'] ?? null,
                'address'      => $validated['address'] ?? null,
            ]);

            DB::commit();
            
            return redirect()->route('admin.users.index')
                ->with('success', "User '{$validated['username']}' created successfully.")
                ->with('default_password', $defaultPasswordString);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', "User creation failed: {$e->getMessage()}");
        }
    }

    // Show edit form
    public function edit($id)
    {
        $user = User::with(['role', 'faculty', 'department', 'unit', 'office', 'institute.faculty', 'profile'])->findOrFail($id);

        $roles = Role::all();
        $faculties = Faculty::all();
        $departments = Department::all();
        $units = Unit::all();
        $offices = Office::all();
        $institutes = Institute::all();

        return view('admin.manage_users.users_edit', compact('user', 'roles', 'faculties', 'departments', 'units', 'offices', 'institutes'));
    }

    // Update user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'full_name'    => 'required|string|max:255',
            'username'     => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->user_id, 'user_id')],
            'email'        => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->user_id, 'user_id')],
            
            'reset_password' => 'nullable|boolean', 

            'role_id'      => 'required|exists:roles,role_id',
            
            'faculty_id'   => 'nullable|exists:faculties,faculty_id',
            'institute_id' => 'nullable|exists:institutes,institute_id',
            'office_id'    => 'nullable|exists:offices,office_id',
            'dept_id'      => 'nullable|exists:departments,dept_id',
            'unit_id'      => 'nullable|exists:units,unit_id',
            
            'status'       => 'required|in:active,inactive',
            'phone'        => 'nullable|string|max:20',
            'address'      => 'nullable|string|max:500',
        ]);

        $primaryAffiliationKeys = ['faculty_id', 'institute_id', 'office_id'];
        $setAffiliations = array_filter(Arr::only($validated, $primaryAffiliationKeys));

        if (count($setAffiliations) > 1) {
            return back()->withErrors([
                'primary_affiliation_error' => 'Please select only ONE primary organizational affiliation (Faculty, Institute, OR Office).'
            ])->withInput();
        }

        try {
            DB::beginTransaction();

            $defaultPasswordString = null;
            $passwordWasReset = false;
            
            $updateData = [
                'username'     => $validated['username'],
                'email'        => $validated['email'],
                'role_id'      => $validated['role_id'],
                'status'       => $validated['status'],
                'faculty_id'   => $validated['faculty_id'] ?? null,
                'institute_id' => $validated['institute_id'] ?? null,
                'office_id'    => $validated['office_id'] ?? null,
                'dept_id'      => $validated['dept_id'] ?? null,
                'unit_id'      => $validated['unit_id'] ?? null,
                
            ];

            if ($request->has('reset_password')) {
                $currentYear = date('Y');
                $defaultPasswordString = $validated['username'] . '@' . $currentYear;
                
                $updateData['password'] = Hash::make($defaultPasswordString);
                $updateData['must_change_password'] = true;
                $passwordWasReset = true;
            }

            $user->update($updateData);

            // Inside your update function, before updateOrCreate:
            // Inside your update function...

            $fullName = trim($validated['full_name']);
            $nameParts = explode(' ', $fullName);

            $firstName = $nameParts[0] ?? '';
            $lastName = count($nameParts) > 1 ? end($nameParts) : '';
            $middleName = '';

            if (count($nameParts) > 2) {
                // Collect everything between the first and last name as the middle name
                $middleName = implode(' ', array_slice($nameParts, 1, -1));
            }

            $user->profile()->updateOrCreate(
                ['user_id' => $user->user_id],
                [
                    'first_name'  => $firstName,
                    'middle_name' => $middleName,
                    'last_name'   => $lastName,
                    'phone'       => $validated['phone'] ?? null,
                    'address'     => $validated['address'] ?? null,
                ]
            );

            DB::commit();

            $redirect = redirect()->route('admin.users.index');

            if ($passwordWasReset) {
                $redirect->with('success', "User '{$validated['username']}' updated and password reset to default.")
                         ->with('default_password', $defaultPasswordString)
                         ->with('password_reset', true);
            } else {
                $redirect->with('success', "User '{$validated['username']}' updated successfully.");
            }

            return $redirect;

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', "Update failed: " . $e->getMessage());
        }
    }

    // Delete user
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            if (Auth::id() == $user->user_id) {
                return back()->with('error', 'You cannot delete your own account.');
            }

            $username = $user->username;
            if ($user->profile) $user->profile->delete();
            $user->delete();

            return redirect()->route('admin.manage_users.users')->with('success', "User '{$username}' deleted successfully.");

        } catch (\Exception $e) {
            return back()->with('error', "User deletion failed: {$e->getMessage()}");
        }
    }
}