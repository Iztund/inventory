<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};
use App\Models\{Submission, SubmissionItem,Asset, Category, User};


class StaffController extends Controller
{
    // ============================================
    // STAFF DASHBOARD
    // ============================================
   // app/Http/Controllers/Staff/StaffController.php

public function index()
{
    $user = User::with(['unit', 'department', 'institute', 'office', 'faculty'])
                ->find(Auth::id());

    // 1. Get a base query for items belonging to this user's submissions
    $baseQuery = SubmissionItem::whereHas('submission', function($query) use ($user) {
        $query->where('submitted_by_user_id', $user->user_id);
    });

    // 2. Fetch counts grouped by status in one go
    $counts = $baseQuery->selectRaw('status, count(*) as total')
        ->groupBy('status')
        ->pluck('total', 'status')
        ->toArray();

    // 3. Build the final stats array with defaults (in case a status has 0 items)
    $stats = [
        'total'    => array_sum($counts),
        'pending'  => $counts['pending'] ?? 0,
        'approved' => $counts['approved'] ?? 0,
        'rejected' => $counts['rejected'] ?? 0,
    ];
    $unitQuery = Asset::applyScopeForUser($user);

    // CLEANER CALL: Use the scope we just created
    $totalUnitAssets = (clone $unitQuery)->sum('quantity');
    $totalValue = (clone $unitQuery)->sum(DB::raw('purchase_price * quantity'));
    $recentSubmissions = Submission::where('submitted_by_user_id', $user->user_id)
        ->with(['items', 'reviewedBy'])
        ->orderByDesc('submitted_at')
        ->take(5)
        ->get();

    return view('staff.dashboard', compact('stats', 'totalUnitAssets', 'recentSubmissions', 'totalValue'));
}

}