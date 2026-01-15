<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

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

        // 1. Find the user
        $user = User::where('username', $data['identifier'])
                    ->orWhere('email', $data['identifier'])
                    ->first();

        // 2. Validate Credentials
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return redirect()->back()
                ->withErrors(['identifier' => 'Invalid credentials.'])
                ->withInput();
        }

        // 3. Check if Account is Active
        if ($user->status !== 'active') {
            return redirect()->back()
                ->withErrors(['identifier' => 'Your account is inactive. Please contact the administrator.'])
                ->withInput();
        }

        // 4. Log the user in
        Auth::login($user);

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
                        // 1. Must contain at least one uppercase and one lowercase letter
                        // 2. Must contain at least one number
                        // 3. Regex: The very first character (^) must be an uppercase letter [A-Z]
                        'regex:/^[A-Z]/', 
                        'regex:/[a-z]/',
                        'regex:/[0-9]/',
                    ],
                ], [
                    // Custom error messages to make it clear for the user
                    'password.regex' => 'The password must start with a capital letter and contain at least one number and one lowercase letter.',
                ]);

                // Explicitly find the user by ID to ensure it's a Model instance
                $user = \App\Models\User::find(Auth::id());

                if (!$user) {
                    return redirect()->route('login')->with('error', 'User not found.');
                }

                // Update password and clear the flag
                $user->password = Hash::make($request->password);
                $user->must_change_password = false; 
                
                $user->save(); 

                return redirect()->route('dashboard')->with('success', 'Password updated successfully. Welcome!');
            }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}