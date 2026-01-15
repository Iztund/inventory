<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Log};
use App\Models\{Submission,SubmissionItem, Audit, Asset, User, Faculty, Office, Institute, Department, Unit};

class AuditorController extends Controller
{
    /**
     * Display the auditor dashboard with entity breakdowns
     */
    /**
 * Display the auditor dashboard with item-level breakdowns
 */
    public function dashboard()
        {
            // 1. Overall Statistics (Now counting individual items)
            $stats = [
                'total'    => SubmissionItem::count(),
                'pending'  => SubmissionItem::where('status', 'pending')->count(),
                'approved' => SubmissionItem::where('status', 'approved')->count(),
                'rejected' => SubmissionItem::where('status', 'rejected')->count(),
            ];

            // 2. Entity Breakdown - Item stats grouped by College structure
            $entityBreakdown = [
                'faculties'  => $this->getEntityStats(['faculty_id', 'dept_id']),
                'offices'    => $this->getEntityStats(['office_id', 'unit_id']),
                'institutes' => $this->getEntityStats(['institute_id']),
            ];

            // 3. Recent Activity (Still useful to see recent batch submissions)
            $recentSubmissions = Submission::with([
                'items',
                'submittedBy.profile',
                'submittedBy.faculty',
                'submittedBy.department',
                'submittedBy.office',
                'submittedBy.unit',
                'submittedBy.institute'
            ])
            ->latest('submitted_at')
            ->take(10)
            ->get();

            return view('auditor.dashboard', compact('stats', 'entityBreakdown', 'recentSubmissions'));
        }

        /**
         * Helper function to get item-level statistics for specific organizational branches
         */
        private function getEntityStats(array $entityFields)
        {
            // Start from SubmissionItem to get granular counts
            $baseQuery = SubmissionItem::whereHas('submission.submittedBy', function($q) use ($entityFields) {
                $q->where(function($subQuery) use ($entityFields) {
                    foreach ($entityFields as $field) {
                        // Check if the user belongs to any of the requested organizational fields
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

    /**
     * List submissions that require auditor attention
     */
   public function index(Request $request)
        {
            /**
             * 1. Initialize Query with comprehensive eager loading.
             * Unlike the Staff controller, the Auditor sees all submissions,
             * so we include deep relationships to display Entity names clearly.
             */
            $query = Submission::with([
                'items',
                'submittedBy.profile',
                'submittedBy.faculty',
                'submittedBy.department',
                'submittedBy.office',
                'submittedBy.unit',
                'submittedBy.institute'
            ]);

            // 2. Filter by Status (Defaults to 'pending' for the Auditor's workflow)
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            } else {
                $query->where('status', 'pending');
            }

            // 3. Filter by Entity Type (Faculty, Office, Institute)
            if ($request->filled('entity_type')) {
                $entityType = $request->entity_type;
                $query->whereHas('submittedBy', function($q) use ($entityType) {
                    switch($entityType) {
                        case 'faculty':
                            // Shows records belonging to a Faculty or its child Departments
                            $q->whereNotNull('faculty_id');
                            break;
                        case 'office':
                            // Shows records belonging to an Office or its child Units
                            $q->whereNotNull('office_id');
                            break;
                        case 'institute':
                            $q->whereNotNull('institute_id');
                            break;
                    }
                });
            }

            // 4. Robust Search Logic (Matches your SubmissionController logic)
            if ($request->filled('search')) {
                $search = $request->search;
                
                // Clean ID: Extracts "45" from "#AUD-00045"
                $cleanId = preg_replace('/[^0-9]/', '', $search);

                $query->where(function($q) use ($search, $cleanId) {
                    if(!empty($cleanId)) {
                        // Adjust this column name if your Primary Key is 'submission_id'
                        $q->where('submission_id', $cleanId);
                    }
                    
                    $q->orWhereHas('items', function($itemQuery) use ($search) {
                        $itemQuery->where('item_name', 'like', "%{$search}%")
                                ->orWhere('serial_number', 'like', "%{$search}%");
                    });

                    // Added: Ability to search by the name of the person who submitted it
                    $q->orWhereHas('submittedBy.profile', function($profileQuery) use ($search) {
                        $profileQuery->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%");
                    });
                });
            }

            /**
             * 5. Finalizing results
             * Auditors usually deal with high volume, so we use latest('submitted_at').
             */
            $submissions = $query->latest('submitted_at')->paginate(20);
            $faculties = Faculty::orderBy('faculty_name')->get();
            $departments = Department::orderBy('dept_name')->get();
            $offices = Office::orderBy('office_name')->get();
            $units = Unit::orderBy('unit_name')->get();
            $institutes = Institute::orderBy('institute_name')->get();
            // Maintain filter state in pagination links
            $submissions->appends($request->all());

            return view('auditor.submissions.index', compact('submissions',
                'faculties', 'departments', 'offices', 'units', 'institutes'));
        }
    /**
     * Show a submission and audit form
     */
   public function show($submission_id)
{
    // 1. Load the Submission with all staff and item details
    // Added asset nested relationships to handle the Deployment Location hierarchy
    $submission = Submission::with([
        'items.audit',

        'items.asset.faculty',
        'items.asset.department',
        'items.asset.office',
        'items.asset.unit',
        'items.asset.institute',
        'items.category',
        'items.subcategory',
        'submittedBy.profile',
        'submittedBy.faculty',
        'submittedBy.department',
        'submittedBy.office',
        'submittedBy.unit',
        'submittedBy.institute',
        'reviewedBy.profile'
    ])->where('submission_id', $submission_id)->firstOrFail();

    // 2. Extract both Asset IDs AND Submission Item IDs
    // We need both because REJECTED items won't have an asset_id in the history table
    $assetIds = $submission->items->pluck('asset_id')->filter(); // Only get non-null IDs
    $itemIds = $submission->items->pluck('submission_item_id');

    // 3. Get full Audit Trail 
    // We query by asset_id (for history) OR by the specific submission_item_id (for the current batch)
    $history = SubmissionItem::where(function($query) use ($assetIds, $itemIds) {
            $query->whereIn('asset_id', $assetIds)
                  ->orWhereIn('submission_item_id', $itemIds);
        })
        ->with([
            'submission.reviewedBy.profile', 
            'submission.submittedBy.profile',
            'asset.category'
        ])
        ->latest()
        ->get();

    // 4. Get the latest condition snapshots
    $latestSnapshots = SubmissionItem::whereIn('asset_id', $assetIds)
        ->latest()
        ->get()
        ->groupBy('asset_id')
        ->map(fn($group) => $group->first());

    // --- LOGIC GATE ---

    // Scenario A: Submission is still PENDING
    if ($submission->status === 'pending') {
        return view('auditor.submissions.pending', compact('submission', 'history', 'latestSnapshots'));
    }

    // Scenario B: Submission is PROCESSED (Audit Log)
    return view('auditor.approved_items.show', compact('submission', 'history', 'latestSnapshots'));
}

public function updateItemStatus(Request $request, $id)
{
    $item = SubmissionItem::findOrFail($id);
    // 'status' here refers to the audit progress (verified/flagged)
    $item->update(['audit_status' => $request->status]); 

    return response()->json(['success' => true, 'message' => 'Item marked as ' . $request->status]);
}
    /**
     * Store an audit decision (Approve or Reject)
     */
    public function store(Request $request, $submission_id)
{
    // 1. Updated Validation
    // We now validate 'overall_decision' (from the nav buttons) 
    // and the 'items' array (from the hidden inputs in each card)
    $data = $request->validate([
        'overall_decision' => 'required|in:approved,rejected',
        'comments'         => 'required_if:overall_decision,rejected|nullable|string|max:2000',
        'items'            => 'required|array', // Item-level decisions
        'items.*.status'   => 'required|in:approved,rejected,pending',
    ]);

    $user = Auth::user();
    $submission = Submission::with(['items', 'submittedBy'])->findOrFail($submission_id);

    if ($submission->status !== 'pending') {
        return back()->with('error', 'This submission has already been processed.');
    }

    try {
        return DB::transaction(function () use ($submission, $data, $user) {
            
            $approvedCount = 0;
            $rejectedCount = 0;

            // 2. Loop through individual item decisions sent from the form
            foreach ($data['items'] as $itemId => $itemData) {
                $item = $submission->items->where('submission_item_id', $itemId)->first();
                
                if (!$item) continue;

                $itemDecision = $itemData['status'];
                
                // If user didn't mark an item but clicked "Finalize Approval", 
                // we treat 'pending' as 'approved' for that item.
                if ($itemDecision === 'pending') {
                    $itemDecision = ($data['overall_decision'] === 'approved') ? 'approved' : 'rejected';
                }

                // 3. Create individual Audit Logs for each item
                Audit::create([
                    'submission_id'      => $submission->submission_id,
                    'submission_item_id' => $item->submission_item_id,
                    'auditor_id'         => $user->user_id,
                    'decision'           => $itemDecision,
                    'comments'           => ($itemDecision === 'rejected') ? ($data['comments'] ?? 'Item rejected by auditor.') : 'Item verified.',
                    'audited_at'         => now(),
                ]);

                // 4. Update the individual item status
                $item->update([
                    'status'  => $itemDecision,
                    'remarks' => ($itemDecision === 'rejected') ? $data['comments'] : 'Verified and logged.',
                ]);

                // 5. Handle Asset Logic per item
                if ($itemDecision === 'approved') {
                    $this->processSingleAsset($item, $submission->submittedBy);
                    $approvedCount++;
                } else {
                    $this->handleSingleItemRejection($item, $submission->submittedBy);
                    $rejectedCount++;
                }
            }

            // 6. Update the Main Submission status
            // If the whole batch was rejected, status is rejected.
            // If some were approved and some rejected, we still mark the batch as 'approved' 
            // (meaning 'processed'), but the items hold their true status.
            $submission->update([
                'status'              => $data['overall_decision'],
                'reviewed_by_user_id' => $user->user_id,
                'reviewed_at'         => now(),
                'audited_at'          => now(),
                'summary'             => "Audit completed: $approvedCount items approved, $rejectedCount items rejected. " . ($data['comments'] ?? ''),
            ]);

            return redirect()->route('auditor.submissions.index')
                ->with('success', "Audit complete. Approved: $approvedCount, Rejected: $rejectedCount.");
        });
    } catch (\Exception $e) {
        $idForLog = $submission->submission_id ?? $submission_id ?? 'unknown';
        Log::error("Audit failed for #{$idForLog}: " . $e->getMessage());
        return back()->with('error', 'Critical Error: ' . $e->getMessage());
    }
}

/**
 * Refactored helper to process one asset at a time
 */
protected function processSingleAsset($item, $staff) {
    $asset = Asset::find($item->asset_id);
    if (!$asset) return;

    // Build the tag prefix based on staff location
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
    $finalPrefix = implode('/', array_filter($prefixParts)) ?: 'COM';

    $updateData = [
        'status'               => 'available',
        'last_audited_at'      => now(),
        'current_faculty_id'   => $staff->faculty_id,
        'current_dept_id'      => $staff->department_id,
        'current_office_id'    => $staff->office_id,
        'current_unit_id'      => $staff->unit_id,
        'current_institute_id' => $staff->institute_id,
    ];

    if (empty($asset->asset_tag)) {
        $updateData['asset_tag'] = $this->generateHierarchicalTag($finalPrefix);
    }

    $asset->update($updateData);
}

/**
 * Refactored helper to handle one rejection at a time
 */
protected function handleSingleItemRejection($item, $staff) {
    $asset = Asset::find($item->asset_id);
    if ($asset) {
        $asset->update([
            'status'               => 'available', // Revert to available so others can try again
            'last_audited_at'      => now(),
            'current_faculty_id'   => $staff->faculty_id,
            'current_dept_id'      => $staff->department_id,
            'current_office_id'    => $staff->office_id,
            'current_unit_id'      => $staff->unit_id,
            'current_institute_id' => $staff->institute_id,
        ]);
    }
}
    /**
     * Process asset logic upon approval
     */
    protected function processAssetLogic($submission)
    {
        $staff = $submission->submittedBy;
        $prefixParts = [];

        // Logic for hierarchical prefixing
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

        $finalPrefix = implode('/', array_filter($prefixParts)) ?: 'COM';

        foreach ($submission->items as $item) {
            $asset = Asset::find($item->asset_id);
            if ($asset) {
                $updateData = [
                    'status'                => 'available',
                    'last_audited_at'       => now(),
                    'current_faculty_id'    => $staff->faculty_id,
                    'current_dept_id'       => $staff->department_id,
                    'current_office_id'     => $staff->office_id,
                    'current_unit_id'       => $staff->unit_id,
                    'current_institute_id'  => $staff->institute_id,
                ];

                if (empty($asset->asset_tag)) {
                    $updateData['asset_tag'] = $this->generateHierarchicalTag($finalPrefix);
                }

                $asset->update($updateData);
            }
        }
    }

    /**
 * Handle asset cleanup when a submission is rejected
 */
protected function handleRejectionLogic($submission)
{
    $staff = $submission->submittedBy()->with([
                'unit.office', 
                'department.faculty', 
                'institute', 
                'office', 
                'faculty'
            ])->first();
    // Ensure we have the items
    if (!$submission->items || $submission->items->isEmpty()) {
        throw new \Exception("No items found in this submission to reject.");
    }

    foreach ($submission->items as $item) {
        // Find the asset associated with the item
        $asset = Asset::find($item->asset_id);
        
        if ($asset) {
            // We force the update and check if it actually saved
             $updated = [

                    'status'                  => 'available',
                    'last_audited_at'         => now(),
                ];
                // Assign Ownership to the Asset based on where the Staff belongs
                $updated['current_faculty_id']    = $staff->faculty_id;
                $updated['current_dept_id']       = $staff->department_id;
                $updated['current_office_id']     = $staff->office_id;
                $updated['current_unit_id']       = $staff->unit_id;
                $updated['current_institute_id']  = $staff->institute_id;
            if (!$updated) {
                throw new \Exception("Failed to update status for Asset ID: {$item->asset_id}");
            }
        } else {
            Log::warning("Rejection: Asset record missing for item ID {$item->id}");
        }
        $asset->update($updated);
    }
}
    /**
     * Generate hierarchical asset tag
     */
    /**
 * Track the highest used number during this specific request to prevent 
 * duplicate tags when processing multiple items in one submission.
 */
    private static $lastGeneratedNumber = null;

    private function generateHierarchicalTag($prefix)
        {
            $prefix = !empty($prefix) ? $prefix : 'COM';

            // 1. If this is the first item in the loop, check the database
            if (self::$lastGeneratedNumber === null) {
                $lastAsset = Asset::where('asset_tag', 'like', $prefix . '/%')
                    // Using raw order to ensure 000000010 comes after 000000009 correctly
                    ->orderByRaw('CAST(SUBSTRING_INDEX(asset_tag, "/", -1) AS UNSIGNED) DESC')
                    ->first();

                if ($lastAsset) {
                    $segments = explode('/', $lastAsset->asset_tag);
                    $lastNumString = end($segments);
                    self::$lastGeneratedNumber = is_numeric($lastNumString) ? (int)$lastNumString : 0;
                } else {
                    self::$lastGeneratedNumber = 0;
                }
            }

            // 2. Increment the tracker
            self::$lastGeneratedNumber++;

            // 3. Return the formatted tag (e.g., CHS/ANAT/000000001)
            return $prefix . '/' . str_pad(self::$lastGeneratedNumber, 9, '0', STR_PAD_LEFT);
        }
public function assetsIndex(Request $request)
        {
            // Fetch all organizational entities for the filter dropdowns
            $faculties = Faculty::orderBy('faculty_name')->get();
            $departments = Department::orderBy('dept_name')->get();
            $offices = Office::orderBy('office_name')->get();
            $units = Unit::orderBy('unit_name')->get();
            $institutes = Institute::orderBy('institute_name')->get();
            $categories = \App\Models\Category::orderBy('category_name')->get();

            // Start query: Get assets that belong to an APPROVED submission
            $query = Asset::whereHas('submissionItems.submission', function($q) {
                $q->where('status', 'approved');
            });

            // Apply Hierarchical Filters
            if ($request->filled('faculty_id')) $query->where('current_faculty_id', $request->faculty_id);
            if ($request->filled('dept_id')) $query->where('current_dept_id', $request->dept_id);
            if ($request->filled('office_id')) $query->where('current_office_id', $request->office_id);
            if ($request->filled('unit_id')) $query->where('current_unit_id', $request->unit_id);
            if ($request->filled('institute_id')) $query->where('current_institute_id', $request->institute_id);
            if ($request->filled('category_id')) $query->where('category_id', $request->category_id);

            // Search Logic
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('item_name', 'LIKE', "%{$search}%")
                    ->orWhere('asset_tag', 'LIKE', "%{$search}%")
                    ->orWhere('serial_number', 'LIKE', "%{$search}%");
                });
            }

            // In AuditorController.php
            $assets = $query->with(['category', 'unit', 'department', 'office', 'institute', 'faculty'])
                            ->orderByRaw("FIELD(status, 'available', 'in_use', 'under_maintenance', 'disposed')")
                            ->latest('updated_at')
                            ->paginate(25);

            $assets->appends($request->all());

            return view('auditor.approved_items.index', compact(
                'assets', 'faculties', 'departments', 'offices', 'units', 'institutes', 'categories'
            ));
        }
public function assetsShow($asset_id)
{
    // Eager load everything needed for a comprehensive view
    $asset = Asset::with([
        'faculty', 'department', 'office', 'unit', 'institute', 'category',
        'submissionItems.submission.reviewedBy', // Historical audit links
        'submissionItems.submission.submittedBy'
    ])->findOrFail($asset_id);

    // Get the most recent submission item to show current condition
    $latestSnapshot = $asset->submissionItems()->latest()->first();
    
    // Get all previous audit records for this specific asset
    $history = $asset->submissionItems()
        ->with('submission')
        ->latest()
        ->get();

    return view('auditor.approved_items.show', compact('asset', 'latestSnapshot', 'history'));
}

   public function registryIndex(Request $request)
{
    // Fetch all hierarchical data for the dropdowns
    $faculties = Faculty::orderBy('faculty_name')->get();
    $departments = Department::orderBy('dept_name')->get();
    $offices = Office::orderBy('office_name')->get();
    $units = Unit::orderBy('unit_name')->get();
    $institutes = Institute::orderBy('institute_name')->get();

    $query = Submission::with(['submittedBy.unit', 'submittedBy.faculty', 'submittedBy.department', 'items']);

    // --- HIERARCHY 1: Faculty -> Department ---
    if ($request->filled('faculty_id')) {
        $query->whereHas('submittedBy', fn($q) => $q->where('faculty_id', $request->faculty_id));
    }
    if ($request->filled('department_id')) {
        $query->whereHas('submittedBy', fn($q) => $q->where('dept_id', $request->department_id));
    }

    // --- HIERARCHY 2: Office -> Unit ---
    if ($request->filled('office_id')) {
        $query->whereHas('submittedBy', fn($q) => $q->where('office_id', $request->office_id));
    }
    if ($request->filled('unit_id')) {
        $query->whereHas('submittedBy', fn($q) => $q->where('unit_id', $request->unit_id));
    }

    // --- HIERARCHY 3: Institute ---
    if ($request->filled('institute_id')) {
        $query->whereHas('submittedBy', fn($q) => $q->where('institute_id', $request->institute_id));
    }

    // Search Logic
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('submission_id', 'like', "%$search%")
              ->orWhereHas('submittedBy', fn($sq) => $sq->where('username', 'like', "%$search%"));
        });
    }
    $rejected_submissions = (clone $query)->where('status', 'rejected')->get();
    $pending_submissions = (clone $query)->where('status', 'pending')->get();
    $approved_submissions = (clone $query)->where('status', 'approved')->get();
    
    $total_registry_value = Submission::where('status', 'approved')->with('items')->get()
        ->sum(fn($s) => $s->items->sum(fn($i) => ($i->unit_cost ?? $i->cost ?? 0) * $i->quantity));

    return view('auditor.central_registory.index', compact(
        'pending_submissions', 'rejected_submissions','approved_submissions', 'total_registry_value',
        'faculties', 'departments', 'offices', 'units', 'institutes'
    ));
}
}