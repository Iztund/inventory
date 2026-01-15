<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr; // Helper for array operations

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
    public function Index(Request $request)
    {
        // ADDED: Eager-load institute.faculty for the index blade display logic
        $query = User::with([
            'role', 
            'faculty', 
            'department', 
            'unit', 
            'office', 
            'institute.faculty', // <-- Essential for the index page display
            'profile'
        ]);

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $searchTerm = "%{$search}%";

                $q->where('username', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm)
                  ->orWhereHas('profile', function ($qProfile) use ($searchTerm) {
                      $qProfile->where('full_name', 'like', $searchTerm);
                  });
            });
        }

        $users = $query->latest('user_id')->paginate(20);
        return view('admin.manage_users.users', compact('users'));
    }

    // Show create form
    public function Create()
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
    public function Store(Request $request)
    {
        $data = $request->all();
        $data = array_map(function ($value) {
        return $value === '' ? null : $value;
                            }, $data);
        // 1. Validation Logic
        $validated = $request->validate([
            'full_name'     => 'nullable|string|max:255',
            'username'      => 'required|string|max:255|unique:users,username',
            'email'         => 'required|string|email|max:255|unique:users,email',
            // REMOVED 'password' validation since we use a default
            'role_id'       => 'required|exists:roles,role_id',
            
            // Primary Affiliation (Check your table primary keys: 'id' or 'faculty_id', etc.)
            'faculty_id'    => 'nullable|exists:faculties,faculty_id',     // Assuming 'id' is the PK for faculties
            'institute_id'  => 'nullable|exists:institutes,institute_id',    // Assuming 'id' is the PK for institutes
            'office_id'     => 'nullable|exists:offices,office_id',       // Assuming 'id' is the PK for offices
            
            // Secondary Affiliation
            'dept_id'       => 'nullable|exists:departments,dept_id',
            'unit_id'       => 'nullable|exists:units,unit_id',
            
            // Profile/Contact
            'status'        => 'required|in:active,inactive',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:500',
        ]);

        // 2. Custom Validation Check: Ensure only ONE primary affiliation is set.
        $primaryAffiliationKeys = ['faculty_id', 'institute_id', 'office_id'];
        
        // Filter out null values to count how many primary affiliations were selected
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
            // 3. Create the User Record
            $user = User::create([
                'username'      => $validated['username'],
                'email'         => $validated['email'],
                
                // --- DEFAULT PASSWORD & FORCED CHANGE ---
                'password'      => Hash::make($defaultPasswordString), // Set a strong default password
                'must_change_password' => true,             // IMPORTANT: Requires this column in the users table
                
                'role_id'       => $validated['role_id'],
                
                // Organizational IDs
                'faculty_id'    => $validated['faculty_id'] ?? null,
                'institute_id'  => $validated['institute_id'] ?? null,
                'office_id'     => $validated['office_id'] ?? null,
                'dept_id'       => $validated['dept_id'] ?? null,
                'unit_id'       => $validated['unit_id'] ?? null,
                'status'        => $validated['status'],
            ]);

            // 4. Create/Update Profile
            UserProfile::create([
                'user_id'      => $user->user_id,
                'full_name'    => $validated['full_name'],
                // Note: It's generally redundant to store Org IDs in UserProfile if they are in the User model,
                // but kept for compatibility with your existing structure:
                'phone'        => $validated['phone'] ?? null,
                'address'      => $validated['address'] ?? null,
            ]);

            DB::commit();
            
            $successMessage = "User **{$validated['username']}** created successfully. A default password has been assigned, and they must change it upon first login.";
            return redirect()->route('admin.users.index')
            ->with('success', $successMessage)
            ->with('default_password', $defaultPasswordString);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', "User creation failed: {$e->getMessage()}");
        }
    }

    // Show edit form
    public function Edit($id)
    {
        // ADDED: Eager-load institute.faculty for potential display in edit form
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
   // Update user
public function Update(Request $request, $id)
{
    $user = User::findOrFail($id);

    // 1. Validation Logic
    $validated = $request->validate([
        'full_name'    => 'required|string|max:255',
        'username'     => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->user_id, 'user_id')],
        'email'        => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->user_id, 'user_id')],
        
        'reset_password' => 'nullable|boolean', 

        'role_id'      => 'required|exists:roles,role_id',
        
        // Organizational Validations (Matching your Store logic)
        'faculty_id'   => 'nullable|exists:faculties,faculty_id',
        'institute_id' => 'nullable|exists:institutes,institute_id',
        'office_id'    => 'nullable|exists:offices,office_id',
        'dept_id'      => 'nullable|exists:departments,dept_id',
        'unit_id'      => 'nullable|exists:units,unit_id',
        
        'status'       => 'required|in:active,inactive',
        'phone'        => 'nullable|string|max:20',
        'address'      => 'nullable|string|max:500',
    ]);

    // 2. Custom Validation: Ensure only ONE primary affiliation
    $primaryAffiliationKeys = ['faculty_id', 'institute_id', 'office_id'];
    $setAffiliations = array_filter(Arr::only($validated, $primaryAffiliationKeys));

    if (count($setAffiliations) > 1) {
        return back()->withErrors([
            'primary_affiliation_error' => 'Please select only ONE primary organizational affiliation (Faculty, Institute, OR Office).'
        ])->withInput();
    }

    try {
        DB::beginTransaction();

        // Initialize variables to avoid "Undefined variable" errors
        $defaultPasswordString = null;
        
        $updateData = [
            'username'     => $validated['username'],
            'email'        => $validated['email'],
            'role_id'      => $validated['role_id'],
            'status'       => $validated['status'],
            
            // Organizational IDs (Explicitly matching Store logic)
            'faculty_id'   => $validated['faculty_id'] ?? null,
            'institute_id' => $validated['institute_id'] ?? null,
            'office_id'    => $validated['office_id'] ?? null,
            'dept_id'      => $validated['dept_id'] ?? null,
            'unit_id'      => $validated['unit_id'] ?? null,
        ];

        // 3. Logic for Password Reset
        if ($request->has('reset_password')) {
            $currentYear = date('Y');
            $defaultPasswordString = $validated['username'] . '@' . $currentYear;
            
            $updateData['password'] = Hash::make($defaultPasswordString);
            $updateData['must_change_password'] = true; 
        }

        $user->update($updateData);

        // 4. Update Profile
        $user->profile()->updateOrCreate(
            ['user_id' => $user->user_id],
            [
                'full_name'    => $validated['full_name'],
                'phone'        => $validated['phone'] ?? null,
                'address'      => $validated['address'] ?? null,
                // Keeping profile IDs consistent with User table
                'faculty_id'   => $validated['faculty_id'] ?? null,
                'dept_id'      => $validated['dept_id'] ?? null,
                'unit_id'      => $validated['unit_id'] ?? null,
                'office_id'    => $validated['office_id'] ?? null,
                'institute_id' => $validated['institute_id'] ?? null,
            ]
        );

        DB::commit();

        $msg = $request->has('reset_password') 
            ? "User updated and password reset to default." 
            : "User details updated successfully.";

        // Create the redirect instance
        $redirect = redirect()->route('admin.users.index')->with('success', $msg);

        // ONLY add 'default_password' to session if it was actually generated
        if ($defaultPasswordString) {
            $redirect->with('default_password', $defaultPasswordString);
        }

        return $redirect;

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()->with('error', "Update failed: " . $e->getMessage());
    }
}

    // Delete user
    public function Destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            if (Auth::id() == $user->user_id) {
                return back()->with('error', 'You cannot delete your own account.');
            }

            if ($user->profile) $user->profile->delete();
            $user->delete();

            return redirect()->route('admin.users.index')->with('success', "User {$user->username} deleted successfully.");

        } catch (\Exception $e) {
            return back()->with('error', "User deletion failed: {$e->getMessage()}");
        }
    }
}