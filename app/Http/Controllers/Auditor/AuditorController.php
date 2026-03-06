<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Log, Response};
use App\Models\{Submission, SubmissionItem, Audit, Asset, User};
use App\Models\{Faculty, Office, Institute, Department, Unit, Category, Subcategory};

class AuditorController extends Controller
{
    private static $lastGeneratedNumber = null;

    // ============================================
    // DASHBOARD
    // ============================================
    public function dashboard()
{
    $stats = $this->getStats();
    $entityBreakdown = [
        'faculties'  => $this->getEntityStats(['faculty_id', 'dept_id']),
        'offices'    => $this->getEntityStats(['office_id', 'unit_id']),
        'institutes' => $this->getEntityStats(['institute_id']),
    ];

    $recentSubmissions = Submission::with([
        'items', 'items.audit.auditor.profile', 'submittedBy.profile', 
        'submittedBy.faculty', 'submittedBy.department', 'submittedBy.office', 
        'submittedBy.unit', 'submittedBy.institute'
    ])
    ->latest('submitted_at')
    ->take(10)
    ->get();

    foreach ($recentSubmissions as $sub) {
        $firstItem = $sub->items->first();
        $user = $sub->submittedBy;

        // 1. NEW: Structural Origin Logic (College Hierarchy)
        // We check the user's relations to see where the item is coming from
        if ($user->unit) {
            $sub->origin_name = $user->unit->unit_name;
            $sub->origin_color = 'bg-indigo-100 text-indigo-700';
        } elseif ($user->office) {
            $sub->origin_name = $user->office->office_name;
            $sub->origin_color = 'bg-amber-100 text-amber-700';
        } elseif ($user->department) {
            $sub->origin_name = $user->department->dept_name;
            $sub->origin_type = 'Dept';
            $sub->origin_color = 'bg-emerald-100 text-emerald-700';
        } elseif ($user->faculty) {
            $sub->origin_name = $user->faculty->faculty_name;
            $sub->origin_color = 'bg-blue-100 text-blue-700';
        } elseif ($user->institute) {
            $sub->origin_name = $user->institute->institute_name;
            $sub->origin_color = 'bg-purple-100 text-purple-700';
        } else {
            $sub->origin_name = 'General / College';
            $sub->origin_type = 'N/A';
            $sub->origin_color = 'bg-slate-100 text-slate-600';
        }

        // 2. Auditor Display Name Logic
        $sub->auditor_display_name = $firstItem && $firstItem->audit && $firstItem->audit->auditor 
            ? $firstItem->audit->auditor->full_name 
            : 'Pending';
        $sub->submission_date_formatted = $sub->submitted_at ? $sub->submitted_at->format('M d, Y') : 'N/A';
        // 3. Routing Logic
        if ($sub->status === 'pending') {
            $sub->routeName = 'auditor.submissions.show';
            $sub->routeParam = ['id' => $sub->submission_id]; 
            $sub->label = 'Audit Batch';
            $sub->btnClass = 'bg-indigo-600 text-white';
        } else {
            $sub->routeName = 'auditor.approved_items.show';
            $sub->routeParam = ['submission_id' => $sub->submission_id]; 
            $sub->label = 'View Details';
            $sub->btnClass = 'bg-slate-100 text-slate-600';
        }
    }

    return view('auditor.dashboard', compact('stats', 'recentSubmissions', 'entityBreakdown'));
}

    // ============================================
    // AUDIT STORE (Approve/Reject batch)
    // ============================================
    public function store(Request $request, $submission_id)
    {
        $data = $request->validate([
            'overall_decision' => 'required|in:approved,rejected',
            'comments'         => 'required_if:overall_decision,rejected|nullable|string|max:2000',
            'items'            => 'required|array',
            'items.*.status'   => 'required|in:approved,rejected,pending',
            'items.*.item_notes'  => 'nullable|string|max:1000',
        ]);

        $submission = Submission::with(['items', 'submittedBy'])->findOrFail($submission_id);

        if ($submission->status !== 'pending') {
            return back()->with('error', 'This submission has already been processed.');
        }

        try {
            return $this->processAudit($submission, $data);
        } catch (\Exception $e) {
            Log::error("Audit failed for #{$submission_id}: " . $e->getMessage());
            return back()->with('error', 'Critical Error: ' . $e->getMessage());
        }
    }

    // ============================================
    // RE-EVALUATE (Correct single item)
    // ============================================
    public function reEvaluate(Request $request, $submission_id)
    {
        $request->validate([
            'item_id'           => 'required|exists:submission_items,submission_item_id',
            'new_status'        => 'required|in:approved,rejected',
            'correction_remarks'=> 'required|string|min:10|max:2000',
        ]);

        $submission = Submission::with(['items', 'submittedBy'])->findOrFail($submission_id);
        $item = SubmissionItem::where('submission_id', $submission_id)
                              ->findOrFail($request->item_id);

        try {
            return DB::transaction(function () use ($submission, $item, $request) {
                $newStatus = $request->new_status;

                $item->update([
                    'status'  => $newStatus,
                    'remarks' => "Individual Correction: " . $request->correction_remarks
                ]);

                Audit::create([
                    'submission_id'      => $submission->submission_id,
                    'submission_item_id' => $item->submission_item_id,
                    'auditor_id'         => Auth::id(),
                    'decision'           => $newStatus,
                    'comments'           => "CORRECTION AUDIT: " . $request->correction_remarks,
                    'audited_at'         => now(),
                ]);

                $this->processAsset($item, $submission->submittedBy, $newStatus);

                $counts = $this->getItemCounts($submission);
                $submission->update([
                    'summary'             => "Batch Results: {$counts['approved']} Approved, {$counts['rejected']} Rejected out of {$counts['total']} total. (Last change: Item #{$item->submission_item_id} corrected to {$newStatus})",
                    'reviewed_by_user_id' => Auth::id(),
                    'audited_at'          => now(),
                ]);

                return back()->with('success', "Item updated. Batch currently has {$counts['approved']} approved and {$counts['rejected']} rejected items.");
            });
        } catch (\Exception $e) {
            Log::error("Correction Failed: " . $e->getMessage());
            return back()->with('error', 'Correction Error: ' . $e->getMessage());
        }
    }

    // ============================================
    // REGISTRY INDEX (Full Central Registry)
    // ============================================
   public function registryIndex(Request $request)
{
    $query = SubmissionItem::with([
        'submission.submittedBy.unit',
        'submission.submittedBy.faculty',
        'submission.submittedBy.department',
        'submission.submittedBy.office',
        'submission.submittedBy.institute',
        'submission.submittedBy.profile',
        'category',
        'subcategory',
    ]);

    $this->applyFilters($query, $request, 'item');

    // Search Logic
    if ($request->filled('search')) {
        $search = trim($request->search);
        $query->where(function($q) use ($search) {
            $q->where('item_name', 'like', "%{$search}%")
              ->orWhere('serial_number', 'like', "%{$search}%")
              ->orWhereHas('submission', function($sq) use ($search) {
                  $sq->where('submission_id', 'like', "%{$search}%")
                     ->orWhereHas('submittedBy.profile', function($pq) use ($search) {
                         $pq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                     });
              });
        });
    }

    // Date Range Filter
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereHas('submission', function($q) use ($request) {
            $q->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        });
    }

    // Clone and get raw results
    $pending_raw  = (clone $query)->where('status', 'pending')->get();
    $approved_raw = (clone $query)->where('status', 'approved')->get();
    $rejected_raw = (clone $query)->where('status', 'rejected')->get();

    $prepareItems = function ($collection) {
        return $collection->map(function ($item) {
            $item->total_value = ($item->unit_cost ?? $item->cost ?? 0) * $item->quantity;

            $u = $item->submission->submittedBy;
            $item->source_name = $u->unit->unit_name ?? $u->department->dept_name ?? $u->institute->institute_name ?? 'General';
            $item->parent_branch_name = $u->faculty->faculty_name ?? $u->office->office_name ?? 'COMUI CENTRAL';

            // ADOPTING DASHBOARD ROUTING LOGIC
            if ($item->status === 'pending') {
                // Matches Dashboard: auditor.submissions.show with ['id' => ...]
                $item->action_route = route('auditor.submissions.show', ['id' => $item->submission_id]);
                $item->action_label = 'Process';
                $item->action_class = 'border-slate-900 text-slate-900 hover:bg-slate-900';
                $item->action_btn_bg = 'bg-slate-900';
            } else {
                // Matches Dashboard: auditor.approved_items.show with ['submission_id' => ...]
                // Note: We pass the parent submission_id as per your Dashboard "CRITICAL FIX"
                $item->action_route = route('auditor.approved_items.show', ['submission_id' => $item->submission_id]);
                
                if ($item->status === 'approved') {
                    $item->action_label = 'View Asset';
                    $item->action_class = 'border-emerald-600 text-emerald-600 hover:bg-emerald-600';
                    $item->action_btn_bg = 'bg-emerald-600';
                } else {
                    $item->action_label = 'Review Rejection';
                    $item->action_class = 'border-rose-600 text-rose-600 hover:bg-rose-600';
                    $item->action_btn_bg = 'bg-rose-600';
                }
            }

            return $item;
        });
    };

    $pending_items  = $prepareItems($pending_raw);
    $approved_items = $prepareItems($approved_raw);
    $rejected_items = $prepareItems($rejected_raw);

    $tabbed_data = [
        'pending'  => ['data' => $pending_items,  'theme' => 'bg-slate-900'],
        'rejected' => ['data' => $rejected_items, 'theme' => 'bg-rose-600'],
        'approved' => ['data' => $approved_items, 'theme' => 'bg-emerald-600'],
        'all'      => ['data' => $pending_items->concat($approved_items)->concat($rejected_items), 'theme' => 'bg-slate-700']
    ];

    $statusCounts = [
        'pending'  => $pending_items->count(),
        'rejected' => $rejected_items->count(),
        'approved' => $approved_items->count(),
        'all'      => $tabbed_data['all']['data']->count()
    ];

    $total_registry_value = $approved_items->sum('total_value');
    $dropdownData = $this->getOrganizationalData();

    return view('auditor.central_registory.index', array_merge(
        compact('statusCounts', 'tabbed_data', 'total_registry_value'),
        $dropdownData
    ));
}

    // ============================================
    // EXPORT (CSV of submissions)
    // ============================================
   public function export(Request $request)
{
    // Eager load with the exact relationship names from your models
    $query = Asset::with([
        'category', 'subcategory',
        'faculty', 'department', 'office', 'unit', 'institute',
        'submissionItem.submission.submittedBy.profile',
        'submissionItem',
        'submission.submittedSubmissions' // Loading the submission chain
    ]);

    if ($request->filled('status')) {
        $query->whereHas('submissionItem', function($q) use ($request) {
            $q->where('status', $request->status);
        });
    }

    $assets = $query->latest()->get();

    return response()->stream(function() use ($assets) {
        $file = fopen('php://output', 'w');
        
        fputcsv($file, [
            'Asset Tag #', 'Item Name', 'Current Status', 'Procurement Status', 
            'Cost', 'Qty', 'Category', 'Origin Structure', 'Location', 
            'Submitted By', 'Date Submitted'
        ]);

        foreach ($assets as $asset) {
            // 1. Determine Location safely
            $originName = 'College of Medicine';
            if ($asset->unit) $originName = $asset->unit->unit_name;
            elseif ($asset->office) $originName = $asset->office->office_name;
            elseif ($asset->department) $originName = $asset->department->dept_name;
            elseif ($asset->faculty) $originName = $asset->faculty->faculty_name;
            elseif ($asset->institute) $originName = $asset->institute->institute_name;

            // 2. SAFE USER LOADING
            // Note: We use the submittedBy relationship you defined in the Asset Model
            // Or fallback to the Submission's user.
            $submittedBy = $asset->submissionItem?->submission?->submittedBy?->full_name 
               ?? $asset->submission?->submittedBy?->full_name 
               ?? 'System';

            // 3. SAFE DATE LOADING
            $dateSubmitted = $asset->submissionItem?->created_at 
                ? $asset->submissionItem->created_at->format('Y-m-d') 
                : ($asset->created_at ? $asset->created_at->format('Y-m-d') : 'N/A');

            fputcsv($file, [
                $asset->asset_tag ?? 'N/A',
                $asset->item_name,
                strtoupper($asset->status),
                ucfirst($asset->submissionItem?->status ?? 'Approved'),
                $asset->purchase_price ?? $asset->cost ?? 0,
                $asset->quantity,
                $asset->category?->category_name ?? 'N/A',
                $asset->origin_type ?? 'N/A',
                $originName,
                $submittedBy,
                $dateSubmitted
            ]);
        }
        fclose($file);
    }, 200, [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=College_Asset_Registry_" . now()->format('Ymd') . ".csv",
    ]);
}
    // ============================================
    // INTERNAL HELPERS (Auditor-Specific)
    // ============================================

    private function getStats(): array
    {
        return [
            'total'    => SubmissionItem::count(),
            'pending'  => SubmissionItem::where('status', 'pending')->count(),
            'approved' => SubmissionItem::where('status', 'approved')->count(),
            'rejected' => SubmissionItem::where('status', 'rejected')->count(),
        ];
    }

    private function getEntityStats(array $entityFields): array
    {
        $baseQuery = SubmissionItem::whereHas('submission.submittedBy', function($q) use ($entityFields) {
            $q->where(function($subQuery) use ($entityFields) {
                foreach ($entityFields as $field) {
                    $subQuery->orWhereNotNull($field);
                }
            });
        });

        return [
            'total'    => (clone $baseQuery)->count(),
            'pending'  => (clone $baseQuery)->where('status', 'pending')->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
        ];
    }

    private function getOrganizationalData(): array
    {
        return [
            'faculties'     => Faculty::orderBy('faculty_name')->get(),
            'departments'   => Department::orderBy('dept_name')->get(),
            'offices'       => Office::orderBy('office_name')->get(),
            'units'         => Unit::orderBy('unit_name')->get(),
            'institutes'    => Institute::orderBy('institute_name')->get(),
            'categories'    => Category::orderBy('category_name')->get(),
            'subcategories' => Subcategory::orderBy('subcategory_name')->get(),
        ];
    }

    private function processAudit($submission, $data)
{
    // 1. Run the database logic
    DB::transaction(function () use ($submission, $data, &$approvedCount, &$rejectedCount) {
        $approvedCount = 0;
        $rejectedCount = 0;

        foreach ($data['items'] as $itemId => $itemData) {
            $item = $submission->items->where('submission_item_id', $itemId)->first();
            if (!$item) continue;

            $itemDecision = $itemData['status'] === 'pending' 
                ? $data['overall_decision'] 
                : $itemData['status'];

            $specificRemark = $itemData['remarks'] 
                ?? $data['comments'] 
                ?? ($itemDecision === 'approved' ? 'Verified.' : 'Rejected by auditor.');

            Audit::create([
                'submission_id'      => $submission->submission_id,
                'submission_item_id' => $item->submission_item_id,
                'auditor_id'         => Auth::id(),
                'decision'           => $itemDecision,
                'comments'           => $specificRemark,
                'audited_at'         => now(),
            ]);

            $item->update([
                'status'  => $itemDecision,
                'remarks' => $specificRemark,
            ]);

            $this->processAsset($item, $submission->submittedBy, $itemDecision);
            
            $itemDecision === 'approved' ? $approvedCount++ : $rejectedCount++;
        }

        $submission->update([
            'status'              => $data['overall_decision'],
            'reviewed_by_user_id' => Auth::id(),
            'reviewed_at'         => now(),
            'audited_at'          => now(),
            'summary'             => "Audit completed: $approvedCount approved, $rejectedCount rejected. " . ($data['comments'] ?? ''),
        ]);
    });

    // 2. NOW perform the redirect outside the transaction block
    // We add the 'status' parameter to the URL so the view switcher knows to show the Registry

    $targetStatus = ($data['overall_decision'] === 'approved') ? 'approved' : 'pending';
    $location = ($targetStatus === 'approved') ? 'Verified Registry' : 'Pending Worklist';
    $resultMessage = $approvedCount > 0 
    ? "Successfully verified $approvedCount medical assets." 
    : "Audit completed.";

if ($rejectedCount > 0) {
    $resultMessage .= " $rejectedCount items were marked for correction.";
}
return redirect()->route('auditor.submissions.index', ['status' => $targetStatus])
    ->with('success', "{$resultMessage} You are now viewing the {$location}.");
    }

    private function processAsset($item, $staff, $status): void
{
    if ($status !== 'approved') return;

    // 1. Handle NEW PURCHASES
    if ($item->submission->submission_type === 'new_purchase' && !$item->asset_id) {
        
        // We temporarily update status so the accessor knows it's okay to generate
        $item->status = 'approved'; 
        $item->submission->status = 'approved';

        $asset = Asset::create([
            'item_name'            => $item->item_name,
            'category_id'          => $item->category_id,
            'subcategory_id'       => $item->subcategory_id,
            'funding_source'       => $item->funding_source_per_item ?? $item->submission->funding_source,
            'status'               => 'available',
            'asset_tag'            => $item->generated_tag, // USES YOUR ACCESSOR HERE
            'current_faculty_id'   => $staff->faculty_id,
            'current_dept_id'      => $staff->department_id,
            'current_office_id'    => $staff->office_id,
            'current_unit_id'      => $staff->unit_id,
            'current_institute_id' => $staff->institute_id,
            'last_audited_at'      => now(),
        ]);
        
        // Link back to the item
        $item->update(['asset_id' => $asset->asset_id]);
        return; 
    }

    // 2. Handle EXISTING ASSETS
    $asset = Asset::find($item->asset_id);
    if ($asset) {
        $asset->update([
            'status'               => 'available',
            'last_audited_at'      => now(),
            'current_faculty_id'   => $staff->faculty_id,
            'current_dept_id'      => $staff->department_id,
            'current_office_id'    => $staff->office_id,
            'current_unit_id'      => $staff->unit_id,
            'current_institute_id' => $staff->institute_id,
        ]);
    }
}

    private function getItemCounts($submission): array
    {
        return [
            'total'    => $submission->items()->count(),
            'approved' => $submission->items()->where('status', 'approved')->count(),
            'rejected' => $submission->items()->where('status', 'rejected')->count(),
        ];
    }

    private function applyFilters(&$query, Request $request, string $type = 'submission'): void
    {
        // Filter by faculty
        if ($request->filled('faculty_id')) {
            $query->whereHas('submission.submittedBy', function($q) use ($request) {
                $q->where('faculty_id', $request->faculty_id);
            });
        }

        // Filter by department
        if ($request->filled('dept_id')) {
            $query->whereHas('submission.submittedBy', function($q) use ($request) {
                $q->where('department_id', $request->dept_id);
            });
        }

        // Filter by office
        if ($request->filled('office_id')) {
            $query->whereHas('submission.submittedBy', function($q) use ($request) {
                $q->where('office_id', $request->office_id);
            });
        }

        // Filter by unit
        if ($request->filled('unit_id')) {
            $query->whereHas('submission.submittedBy', function($q) use ($request) {
                $q->where('unit_id', $request->unit_id);
            });
        }

        // Filter by institute
        if ($request->filled('institute_id')) {
            $query->whereHas('submission.submittedBy', function($q) use ($request) {
                $q->where('institute_id', $request->institute_id);
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by subcategory
        if ($request->filled('subcategory_id')) {
            $query->where('subcategory_id', $request->subcategory_id);
        }
    }
}