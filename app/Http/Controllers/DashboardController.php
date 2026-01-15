<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
 * DashboardController
 *
 * This version uses numeric role_id only (no role_name) and routes users by role_id.
 * - Admin role_id => redirect to admin dashboard route so AdminController prepares metrics.
 * - Auditor role_id => show auditor dashboard view.
 * - Default (any other role_id) => show staff dashboard view.
 *
 * Adjust the numeric constants below if your roles table uses different ids.
 */
class DashboardController extends Controller
{
    public function index()
    {
        // Retrieve the currently authenticated user
        $user = Auth::user();

        // If there is no authenticated user, send them to the login page (defensive)
        if (! $user) {
            return redirect()->route('login');
        }

        // Use the numeric role_id directly. Cast to int for safety.
        $roleId = (int) ($user->role_id ?? 0);

        switch ($roleId) {
            case 1: // admin
                // Redirect to the admin dashboard route (AdminController@index should populate the view data)
                return redirect()->route('admin.dashboard');

            case 3: // auditor
                // Return auditor dashboard view and pass the user object
                return redirect()->route('auditor.dashboard');

            default: // staff and any other roles
                return redirect()->route('staff.dashboard');
        }
    }
}