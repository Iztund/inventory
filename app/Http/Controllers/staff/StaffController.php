<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};
use App\Models\{Submission, Asset, Category};

class StaffController extends Controller
{
    // ============================================
    // STAFF DASHBOARD
    // ============================================
   // app/Http/Controllers/Staff/StaffController.php

public function index()
{
    $user = Auth::user();

    $stats = [
        'total'    => Submission::where('submitted_by_user_id', $user->user_id)->count(),
        'pending'  => Submission::where('submitted_by_user_id', $user->user_id)->where('status', 'pending')->count(),
        'approved' => Submission::where('submitted_by_user_id', $user->user_id)->where('status', 'approved')->count(),
        'rejected' => Submission::where('submitted_by_user_id', $user->user_id)->where('status', 'rejected')->count(),
    ];

    // CLEANER CALL: Use the scope we just created
    $totalUnitAssets = Asset::applyScopeForUser($user)->sum('quantity');

    $recentSubmissions = Submission::where('submitted_by_user_id', $user->user_id)
        ->with('items.category', 'items.subcategory')
        ->orderByDesc('submitted_at')
        ->take(5)
        ->get();

    return view('staff.dashboard', compact('stats', 'totalUnitAssets', 'recentSubmissions'));
}

}