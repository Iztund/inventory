<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param mixed ...$roles Role IDs (can be comma-separated string or multiple args)
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        // Ensure the user is authenticated
        if (! $user) {
            return redirect()->route('login');
        }

        // Normalize roles: support "1,2" string or multiple arguments
        if (count($roles) === 1 && is_string($roles[0]) && strpos($roles[0], ',') !== false) {
            $roles = array_map('trim', explode(',', $roles[0]));
        }

        // Convert all role identifiers to integers
        $allowedRoleIds = array_map('intval', $roles);

        $userRoleId = (int) $user->role_id;
        // Check if user's role is allowed
        if (! in_array($userRoleId, $allowedRoleIds, true)) {
            abort(403, "Unauthorized: your role_id = {$userRoleId}. Allowed role_ids = " . implode(',', $allowedRoleIds));
        }

        return $next($request);
    }
}
