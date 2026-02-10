<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\View;
use App\Models\Submission; // Adjust based on your model name
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
   // app/Providers/AppServiceProvider.php



public function boot()
{
    View::composer('layouts.auditor', function ($view) {
        // This ensures $stats is ALWAYS available in your sidebar
        $stats = [
            'pending' => Submission::where('status', 'pending')->count(),
            // You can add 'approved' => Submission::where('status', 'approved')->count() here too
        ];
        
        $view->with('stats', $stats);
    });
}
}
