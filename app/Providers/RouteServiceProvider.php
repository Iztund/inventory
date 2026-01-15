<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        // Register custom middleware aliases
        Route::aliasMiddleware('role', \App\Http\Middleware\RoleMiddleware::class);

        // Always use framework auth middleware
        Route::aliasMiddleware('auth', \Illuminate\Auth\Middleware\Authenticate::class);
        Route::aliasMiddleware('session.timeout', \App\Http\Middleware\SessionTimeout::class);
    }
}
