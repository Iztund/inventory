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
        'items', 'items.audit.auditor.profile', 'submittedBy.profile', 'submittedBy.faculty', 
        'submittedBy.department', 'submittedBy.office', 'submittedBy.unit', 
        'submittedBy.institute'
    ])
    ->latest('submitted_at')
    ->take(10)
    ->get();

    foreach ($recentSubmissions as $sub) {
        $firstItem = $sub->items->first();
        
        // 1. Auditor Display Name Logic
        $sub->auditor_display_name = $firstItem && $firstItem->audit && $firstItem->audit->auditor 
            ? $firstItem->audit->auditor->full_name 
            : 'Pending';

        // 2. Routing Logic
        if ($sub->status === 'pending') {
            // This route uses {id} usually in SubmissionController
            $sub->routeName = 'auditor.submissions.show';
            $sub->routeParam = ['id' => $sub->submission_id]; 
            $sub->label = 'Audit Batch';
            $sub->btnClass = 'bg-indigo-600 text-white';
        } else {
            // CRITICAL FIX: This route now strictly expects {submission_id}
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
            'items.*.remarks'  => 'nullable|string|max:1000',
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
        $query = Submission::with([
            'items.category', 'items.subcategory',
            'submittedBy.profile', 'submittedBy.faculty', 'submittedBy.department',
            'submittedBy.office', 'submittedBy.unit', 'submittedBy.institute'
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $submissions = $query->latest('submitted_at')->get();

        return Response::stream(function() use ($submissions) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Ref #', 'Item Name', 'Cost', 'Quantity', 'Funding Source',
                'Category', 'Subcategory', 'Submitted By', 'Entity',
                'Department/Unit', 'Date Submitted'
            ]);

            foreach ($submissions as $s) {
                $user = $s->submittedBy;
                $entity = $user->faculty->faculty_name ?? $user->office->office_name ?? $user->institute->institute_name ?? 'N/A';
                $subEntity = $user->department->dept_name ?? $user->unit->unit_name ?? 'General';

                foreach ($s->items as $item) {
                    fputcsv($file, [
                        '#' . str_pad($s->submission_id, 5, '0', STR_PAD_LEFT),
                        $item->item_name ?? 'N/A',
                        $item->cost ?? '0.00',
                        $item->quantity ?? '0',
                        $item->funding_source_per_item ?? $s->funding_source ?? 'N/A',
                        $item->category->category_name ?? 'N/A',
                        $item->subcategory->subcategory_name ?? 'N/A',
                        $user->profile->full_name ?? $user->username,
                        $entity,
                        $subEntity,
                        $s->submitted_at ? $s->submitted_at->format('M d, Y') : 'N/A'
                    ]);
                }
            }
            fclose($file);
        }, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=Inventory_Report_" . now()->format('Ymd_His') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
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
        return DB::transaction(function () use ($submission, $data) {
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

            return redirect()->route('auditor.submissions.index')
                ->with('success', "Audit complete for #{$submission->submission_id}. Approved: $approvedCount, Rejected: $rejectedCount.");
        });
    }

    private function processAsset($item, $staff, $status): void
    {
        $asset = Asset::find($item->asset_id);
        if (!$asset) return;

        $updateData = [
            'status'               => 'available',
            'last_audited_at'      => now(),
            'current_faculty_id'   => $staff->faculty_id,
            'current_dept_id'      => $staff->department_id,
            'current_office_id'    => $staff->office_id,
            'current_unit_id'      => $staff->unit_id,
            'current_institute_id' => $staff->institute_id,
        ];

        if ($status === 'approved' && empty($asset->asset_tag)) {
            $updateData['asset_tag'] = $this->generateHierarchicalTag($this->buildAssetPrefix($staff));
        }

        $asset->update($updateData);
    }

    private function buildAssetPrefix($staff): string
    {
        $prefixParts = [];

        if ($staff->unit_id) {
            $prefixParts[] = $staff->unit->office->office_code ?? 'OFF';
            $prefixParts[] = $staff->unit->unit_code;
        } elseif ($staff->department_id) {
            $prefixParts[] = $staff->department->faculty->faculty_code ?? 'FAC';
            $prefixParts[] = $staff->department->dept_code;
        } elseif ($staff->institute_id) {
            $prefixParts[] = 'INST';
            $prefixParts[] = $staff->institute->institute_code;
        }

        return implode('/', array_filter($prefixParts)) ?: 'COM';
    }

    private function generateHierarchicalTag($prefix): string
    {
        $prefix = !empty($prefix) ? $prefix : 'COM';

        if (self::$lastGeneratedNumber === null) {
            $lastAsset = Asset::where('asset_tag', 'like', $prefix . '/%')
                ->orderByRaw('CAST(SUBSTRING_INDEX(asset_tag, "/", -1) AS UNSIGNED) DESC')
                ->first();

            self::$lastGeneratedNumber = $lastAsset 
                ? (int)explode('/', $lastAsset->asset_tag)[count(explode('/', $lastAsset->asset_tag)) - 1]
                : 0;
        }

        self::$lastGeneratedNumber++;
        return $prefix . '/' . str_pad(self::$lastGeneratedNumber, 9, '0', STR_PAD_LEFT);
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