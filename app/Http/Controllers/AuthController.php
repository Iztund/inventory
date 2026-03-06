<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Failed;
use Illuminate\Validation\Rules\Password;
use App\Models\LoginActivity;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if ($request->query('reason') === 'timeout') {
            session()->flash('info', 'Your session expired due to inactivity for security reasons.');
        }
        return view('auth.login');
    }

    public function doLogin(Request $request)
    {
        $data = $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $data['identifier'])
                    ->orWhere('email', $data['identifier'])
                    ->first();

        // 2. Validate Credentials
        if (!$user || !Hash::check($data['password'], $user->password)) {
            
            // Fire the Failed event
            event(new Failed('web', $user, ['email' => $data['identifier']]));

            return redirect()->back()
                ->withErrors(['identifier' => 'Invalid credentials.'])
                ->withInput();
        }

        // 4. Log the user in
        Auth::login($user);
        LoginActivity::create([
            'user_id'    => $user->user_id,
            'last_login_ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'last_login_at' => now(),
        ]);

        // 5. Handle Mandatory Password Change (For Default Passwords)
        if ($user->must_change_password) {
            return redirect()->route('password.change')
                ->with('info', 'For security reasons, you must change your default password before proceeding.');
        }

        return redirect()->route('dashboard');
    }

    /**
     * Show the mandatory password change form
     */
    public function showChangePassword()
    {
        return view('auth.reset-password');
    }

    /**
     * Process the password update
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/^[A-Z]/',  // Must start with uppercase
                'regex:/[a-z]/',   // Must contain lowercase
                'regex:/[0-9]/',   // Must contain number
            ],
        ], [
            'password.regex' => 'The password must start with a capital letter and contain at least one number and one lowercase letter.',
        ]);

        // Get the authenticated user
        $user = User::find(Auth::id());

        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // Update password and clear the flag
        $user->password = Hash::make($request->password);
        $user->must_change_password = false; 
        $user->save(); 

        // ✅ FIX: Logout the user BEFORE redirecting to login
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Flash success message
        session()->flash('success', 'Password updated successfully! Please log in with your new password.');

        return redirect()->route('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}