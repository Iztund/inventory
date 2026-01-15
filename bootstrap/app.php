<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Configuration\Exceptions;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SessionTimeout;
use Illuminate\Auth\Middleware\Authenticate as AuthMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withMiddleware(function (Middleware $middleware) {

        // ✅ Middleware aliases must be passed as a single associative array
        $middleware->alias([
            'auth' => AuthMiddleware::class,
            'role' => RoleMiddleware::class,
            'session.timeout' => SessionTimeout::class,
        ]);

    })
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
