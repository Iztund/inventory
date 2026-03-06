<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use App\Models\FailedLoginAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogFailedLoginAttempt
{
    protected $request;

    // The Request object is automatically injected here by Laravel
    public function __construct(Request $request) 
    {
        $this->request = $request;
    }

    public function handle(Failed $event): void
    {
        try {
            $email = $event->credentials['email'] ?? 'unknown';
            
            // Manual lookup to avoid the Authenticatable interface error
            $user = User::where('email', $email)->first();

            FailedLoginAttempt::create([
                'user_id'      => $user ? $user->user_id : null,
                'email'        => $email,
                'ip_address'   => $this->request->ip() ?? '0.0.0.0', // Capture IP
                'user_agent'   => $this->request->userAgent(),
                'attempted_at' => now(),
            ]);
            
        } catch (\Exception $e) {
            // Check storage/logs/laravel.log if this fires
            Log::error('Failed to log login attempt: ' . $e->getMessage());
        }
    }
}