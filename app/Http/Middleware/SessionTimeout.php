<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
{
    if (Auth::check()) {
        // 1. Get timeout from config (convert minutes to seconds)
        // This ensures JS and PHP are looking at the same number
        $timeout = config('session.lifetime') * 60; 
        
        $lastActivity = session('last_activity_time');

        // 2. ONLY check for timeout if we are NOT hitting the heartbeat route
        if ($request->routeIs('session.heartbeat')) {
            session(['last_activity_time' => time()]);
            return $next($request);
        }

        if ($lastActivity && (time() - $lastActivity > $timeout)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            // Match the reason parameter we used for the login alert
            return redirect()->route('login', ['reason' => 'timeout']);
        }
        
        session(['last_activity_time' => time()]);
    }

    return $next($request);
}
}
