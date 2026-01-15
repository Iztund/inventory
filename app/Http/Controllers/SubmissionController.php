<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Storage, Log};
use App\Models\{Submission, SubmissionItem, Asset, Category, Subcategory};

class SubmissionController extends Controller
{
    /**
     * INDEX: View Submissions with Role-Based Filtering
     */
  public function index(Request $request)
        {
            /**
             * 1. Initialize Query with eager loading.
             * We load 'items' and 'submittedBy' to prevent N+1 issues when 
             * listing assets and staff names in the table.
             */
            $query = Submission::where('submitted_by_user_id', Auth::id()) 
                ->with(['items', 'submittedBy.profile']);

            // 2. Filter by Status (Pending, Approved, Rejected)
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // 3. Robust Search Logic
            if ($request->filled('search')) {
                $search = $request->search;
                
                /**
                 * Cleaning the ID: 
                 * If a user searches for "#AUD-00045", this extracts "45".
                 */
                $cleanId = preg_replace('/[^0-9]/', '', $search);

                $query->where(function($q) use ($search, $cleanId) {
                    // Search by Primary ID
                    if(!empty($cleanId)) {
                        $q->where('submission_id', $cleanId);
                    }
                    
                    // Search by Item Name or Serial Number in the nested items
                    $q->orWhereHas('items', function($itemQuery) use ($search) {
                        $itemQuery->where('item_name', 'like', "%{$search}%")
                                ->orWhere('serial_number', 'like', "%{$search}%");
                    });
                });
            }

            /**
             * 4. Results Ordering
             * latest() ensures the most recent medical audits appear at the top.
             */
            $submissions = $query->latest('submitted_at')->paginate(10);

            // Append current search/filters to pagination links
            $submissions->appends($request->all());

            return view('staff.submissions.my_submissions', compact('submissions'));
        }

    /**
     * CREATE: Show the Audit Form with Category mapping
     */
public function create()
        {
            $user = Auth::user();
            
            /**
             * 1. Hierarchical Asset Filtering
             * Fetches Assets owned by the user's specific College entity.
             * Priority: Institute > Unit > Office > Department > Faculty.
             */
            $myAssets = Asset::query()
                ->where('status', '!=', 'retired')
                ->where(function($q) use ($user) {
                    if ($user->institute_id) {
                        $q->where('current_institute_id', $user->institute_id);
                    } elseif ($user->unit_id) {
                        $q->where('current_unit_id', $user->unit_id);
                    } elseif ($user->office_id) {
                        $q->where('current_office_id', $user->office_id);
                    } elseif ($user->department_id) {
                        $q->where('current_dept_id', $user->department_id);
                    } elseif ($user->faculty_id) {
                        $q->where('current_faculty_id', $user->faculty_id);
                    } else {
                        // Safety: If user has no entity assigned, return nothing to prevent data leaks
                        $q->whereRaw('1 = 0');
                    }
                })
                ->orderBy('item_name')
                ->get();

            // 2. Load active Categories for the dropdown
            $categories = Category::whereIn('is_active', ['active', 1])
                ->orderBy('category_name')
                ->get();

            /**
             * 3. Subcategory Mapping for JavaScript
             * We group by category_id and use values() to ensure sequential arrays [0,1,2...].
             * This prevents JavaScript from treating the object as an associative array.
             */
            $subcategoryMap = Subcategory::whereIn('is_active', ['active', 1])
                ->get()
                ->groupBy('category_id')
                ->map(fn($items) => $items->values())
                ->toArray();

            return view('staff.submissions.new_submission', compact(
                'myAssets', 
                'categories', 
                'subcategoryMap'
            ));
        }

    /**
     * STORE: Staff submits the audit to Internal Audit
     */
public function store(Request $request)
{
    $userId = Auth::id();
    if (!$userId) {
        return back()->withInput()->with('error', 'Session expired. Please log in again.');
    }

    $request->validate([
        'items' => 'required|array|min:1',
        'items.*.submission_type' => 'required|string',
        'items.*.category_id'     => 'required|exists:categories,category_id',
        'items.*.subcategory_id'  => 'required|exists:subcategories,subcategory_id',
        'items.*.item_name'       => 'required|string',
        'items.*.quantity'        => 'required|integer|min:1',
        'items.*.cost'            => 'nullable|numeric',
        'items.*.funding_source_per_item' => 'nullable|string',
        // Update: Validation for the array of documents sent by JS
        'items.*.documents'       => 'nullable|array',
        'items.*.documents.*'     => 'file|mimes:pdf,jpg,jpeg,png|max:8048', 
        'funding_source'          => 'nullable|string',
        'notes'                   => 'nullable|string',
        'summary'                 => 'nullable|string',
        'items.*.serial_number'   => 'nullable|string',
        'items.*.condition'       => 'nullable|string',
        'items.*.item_notes'      => 'nullable|string',
        'items.*.asset_id'        => 'nullable|exists:assets,asset_id',
    ]);

    try {
        return DB::transaction(function () use ($request, $userId) {
            $user = Auth::user();

            $submission = Submission::create([
                'submitted_by_user_id' => $userId,
                'submission_type'      => $request->items[0]['submission_type'] ?? 'audit',
                'funding_source'       => $request->funding_source, 
                'status'               => 'pending',
                'notes'                => $request->notes,
                'summary'              => $request->summary,
                'submitted_at'         => now(),
            ]);

            foreach ($request->items as $index => $itemData) {
                $assetId = $itemData['asset_id'] ?? null;
                $itemFilePaths = []; // Array to store multiple file paths

                // Handle File Upload: Loop through the 'documents' array from the request
                if ($request->hasFile("items.$index.documents")) {
                    foreach ($request->file("items.$index.documents") as $file) {
                        $path = $file->store('audit_docs/' . $submission->submission_id, 'public');
                        $itemFilePaths[] = $path;
                    }
                }

                // Handle Asset Logic for New Purchases
                if ($itemData['submission_type'] === 'new_purchase') {
                    $serial = $itemData['serial_number'] ?? 'TEMP-' . strtoupper(uniqid());
                    // Check if this serial exists and is ALREADY approved
                    $existingAsset = Asset::where('serial_number', $serial)
                        ->where('status', '!=', 'rejected') // Or whatever status indicates a "failed" asset
                        ->first();

                    if ($existingAsset) {
                        // If it exists and is valid, link to it
                        $assetId = $existingAsset->asset_id;
                    } else {
                            $asset = Asset::Create(
                                [
                                    'serial_number' => $serial,
                                    'item_name'            => $itemData['item_name'],
                                    'purchase_price'       => $itemData['cost'] ?? 0,
                                    'status'               => 'available', 
                                    'category_id'          => $itemData['category_id'],
                                    'subcategory_id'       => $itemData['subcategory_id'],
                                    'quantity'             => $itemData['quantity'],
                                    'current_faculty_id'   => $user->faculty_id,
                                    'current_dept_id'      => $user->department_id,
                                    'current_office_id'    => $user->office_id,
                                    'current_unit_id'      => $user->unit_id,
                                    'current_institute_id' => $user->institute_id,
                                ]
                            );
                            $assetId = $asset->asset_id;
                }
            }

                $submission->items()->create([
                    'asset_id'                => $assetId,
                    'category_id'             => $itemData['category_id'],
                    'subcategory_id'          => $itemData['subcategory_id'],
                    'condition'               => $itemData['condition'] ?? null,
                    'item_notes'              => $itemData['item_notes'] ?? null,
                    'item_name'               => $itemData['item_name'],
                    'cost'                    => $itemData['cost'] ?? 0,
                    'serial_number'           => $itemData['serial_number'] ?? null,
                    'quantity'                => $itemData['quantity'],
                    'funding_source_per_item' => $itemData['funding_source_per_item'] ?? $request->funding_source, 
                    'document_path'           => json_encode($itemFilePaths), // Saves as ["path\/1.jpg", "path\/2.pdf"]
                ]);
            }

            return redirect()->route('staff.submissions.index')
                ->with('success', 'Inventory submission created successfully.');
        });
    } catch (\Exception $e) {
        Log::error("Store failed: " . $e->getMessage());
        return back()->withInput()->with('error', 'Database Error: ' . $e->getMessage());
    }
}
    /**
     * SHOW: View specific submission details
     */
    // The variable name $submission_id MUST match {submission_id} in web.php
public function show($submission_id)
{
    $user = Auth::user();

    // 1. Fetch the submission with all necessary relations
    $submission = Submission::with([
        'items.asset', 
        'items.category', 
        'items.subcategory', 
        'items.asset',
        'submittedBy.profile', 
        'reviewedBy.profile'
    ])->where('submission_id', $submission_id)->firstOrFail();
        
    // 2. Corrected Security Check
    $isAdminOrAuditor = in_array((int)$user->role_id, [1, 3]);
    
    // FIX: Compare user_id to user_id, NOT role_id
    $isOwner = (int)$submission->submitted_by_user_id === (int)$user->user_id;

    if (!$isAdminOrAuditor && !$isOwner) {
        abort(403, 'Unauthorized Access to this Inventory Record.');
    }

    // 3. Determine view folder based on role
    $folder = match ((int)$user->role_id) {
        1       => 'admin',
        3       => 'auditor',
        default => 'staff',
    };

    return view("$folder.submissions.show", compact('submission'));
}
// ... other methods ...

/**
 * Show the form for editing the specified audit submission.
 */
// Change $id to $submission_id to match web.php parameter
public function edit($submission_id)
{
    $user = Auth::user();

    // 1. Fetch using the correct primary key column: 'submission_id'
    $submission = Submission::where('submission_id', $submission_id)
        ->where('submitted_by_user_id', $user->user_id)
        ->where('status', 'pending')
        ->with(['items.category', 'items.subcategory']) 
        ->firstOrFail(); // Use firstOrFail with where() for custom primary keys

    // ... (rest of your existing logic for categories, subcategories, and assets)

    // 2. Fetch Active Categories
    $categories = Category::whereIn('is_active', ['active', 1])
        ->orderBy('category_name')
        ->get();
    
    // 3. Re-map subcategories
    $subcategoryMap = Subcategory::whereIn('is_active', ['active', 1])
        ->get()
        ->groupBy('category_id')
        ->map(fn($items) => $items->values())
        ->toArray();

    // 4. Asset Filtering
    $myAssets = Asset::query()
        ->where('status', '!=', 'retired')
        ->where(function($q) use ($user) {
            if ($user->institute_id) { $q->where('current_institute_id', $user->institute_id); } 
            elseif ($user->unit_id) { $q->where('current_unit_id', $user->unit_id); } 
            elseif ($user->office_id) { $q->where('current_office_id', $user->office_id); } 
            elseif ($user->department_id) { $q->where('current_dept_id', $user->department_id); } 
            elseif ($user->faculty_id) { $q->where('current_faculty_id', $user->faculty_id); }
        })
        ->orderBy('item_name')
        ->get();
    if (!$submission) {
        // This will trigger if: ID is wrong, OR it's not yours, OR it's not pending.
        abort(404, "Record not found, unauthorized, or no longer pending.");
    }
    return view('staff.submissions.edit_submission', compact(
        'submission', 
        'categories', 
        'subcategoryMap',
        'myAssets'
    ));
}
/**
 * Update the specified audit submission in storage.
 */
public function update(Request $request, $submission_id)
{
    $user = Auth::user();
    $submission = Submission::where('submission_id', $submission_id)
        ->where('submitted_by_user_id', $user->user_id)
        ->where('status', 'pending')
        ->firstOrFail();

    $request->validate([
        'funding_source' => 'nullable|string|max:255',
        'notes'          => 'nullable|string',
        'summary'        => 'nullable|string',
        'items'          => 'required|array|min:1',
        'items.*.item_name' => 'required|string',
        'items.*.new_evidence.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:8048',
    ]);

    try {
        return DB::transaction(function () use ($request, $submission) {
            
            $updatedItemIds = [];

            foreach ($request->items as $index => $itemData) {
                // 1. Find existing item or prepare to create new one
                $item = null;
                if (isset($itemData['submission_item_id'])) {
                    $item = SubmissionItem::find($itemData['submission_item_id']);
                }

                // 2. Manage Documents (Merge existing and new)
                // Get files the user kept in the UI
                $filesToKeep = $itemData['existing_files'] ?? []; 
                $newFiles = [];

                // Store new uploads
                if ($request->hasFile("items.$index.new_evidence")) {
                    foreach ($request->file("items.$index.new_evidence") as $file) {
                        $path = $file->store('audit_docs/' . $submission->submission_id, 'public');
                        $newFiles[] = $path;
                    }
                }

                // Combine both lists
                $finalFiles = array_merge($filesToKeep, $newFiles);

                // Cleanup: If an old file was removed from the UI, delete it from storage
                if ($item) {
                        // Laravel already cast this to an array for you!
                        // We just use a null coalescing operator to ensure it's at least an empty array.
                        $oldFiles = $item->document_path ?? []; 

                        // Calculate which files were removed in the UI
                        $deletedFiles = array_diff($oldFiles, $filesToKeep);

                        foreach ($deletedFiles as $fileToDelete) {
                            // Only attempt delete if the path is not empty
                            if ($fileToDelete) {
                                Storage::disk('public')->delete($fileToDelete);
                            }
                        }
                    }

                // 3. Update or Create the Item
                $newItem = $submission->items()->updateOrCreate(
                    ['submission_item_id' => $itemData['submission_item_id'] ?? null],
                    [
                        'submission_type'         => $itemData['submission_type'] ?? 'new_purchase',
                        'category_id'             => $itemData['category_id'],
                        'subcategory_id'          => $itemData['subcategory_id'],
                        'item_name'               => $itemData['item_name'],
                        'quantity'                => $itemData['quantity'],
                        'cost'                    => $itemData['cost'] ?? 0,
                        'serial_number'           => $itemData['serial_number'] ?? null,
                        'funding_source_per_item' => $itemData['funding_source_per_item'] ?? null,
                        'item_notes'              => $itemData['item_notes'] ?? null, // Match UI name
                        'document_path'           => $finalFiles, // Laravel casts to JSON if defined in Model
                    ]
                );
                
                $updatedItemIds[] = $newItem->submission_item_id;
            }

            // 4. Cleanup: Remove items deleted in the UI
            $itemsToDelete = $submission->items()->whereNotIn('submission_item_id', $updatedItemIds)->get();
            foreach ($itemsToDelete as $oldItem) {
                $paths = is_string($oldItem->document_path) ? json_decode($oldItem->document_path, true) : ($oldItem->document_path ?? []);
                foreach ($paths as $p) {
                    Storage::disk('public')->delete($p);
                }
                $oldItem->delete();
            }

            // 5. Update Submission Header
            $submission->update([
                'funding_source' => $request->funding_source,
                'notes'          => $request->notes,
                
                'summary'        => $request->summary, // Added summary
            ]);

            return redirect()->route('staff.submissions.index')
                ->with('success', 'Submission #' . $submission->submission_id . ' updated successfully.');
        });
    } catch (\Exception $e) {
        Log::error("Update failed for ID " . $submission->submission_id . ": " . $e->getMessage());
        return back()->withInput()->with('error', 'Update failed: ' . $e->getMessage());
    }
}
public function destroy($id)
        {
            $submission = Submission::findOrFail($id);

            // Security Check: Only allow deletion of PENDING records
            if ($submission->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending submissions can be deleted.');
            }

            // Optional: Delete related items first if your DB doesn't have cascade delete
            $submission->items()->delete();
            $submission->delete();

            return redirect()->route('staff.submissions.index')->with('success', 'Submission deleted successfully.');
        }
    /**
     * HANDLE ACTION: Auditor Approval/Rejection
     */
    /**
 * HANDLE ACTION: Auditor Approval/Rejection
 * Moves data from Submission to Asset inventory on approval.
 */
public function handleAction(Request $request, $id)
        {
            // 1. Fetch the submission with items to prevent N+1 issues during processing
            $submission = Submission::with('items')->findOrFail($id);
            $action = $request->input('action');

            // 2. Validate current state: Only 'pending' can be processed
            if ($submission->status !== 'pending') {
                return back()->with('error', 'This submission has already been processed and is currently ' . $submission->status . '.');
            }

            // 3. Validate input: Ensure review notes are provided if rejected
            if ($action === 'reject' && !$request->filled('review_notes')) {
                return back()->with('error', 'Please provide a reason for rejection in the review notes.');
            }

            try {
                return DB::transaction(function () use ($submission, $action, $request) {
                    
                    // 4. Update the Submission Header
                    $submission->update([
                        'status'              => ($action === 'approve') ? 'approved' : 'rejected',
                        'reviewed_by_user_id' => Auth::id(),
                        'reviewed_at'         => now(),
                        'audited_at'          => now(),
                        'summary'             => $request->review_notes // Ensure this matches your DB column name
                    ]);

                    // 5. Trigger Inventory Update logic only on approval
                    if ($action === 'approve') {
                        // This will now handle the hierarchical tags and funding transfers
                        $this->processAssetLogic($submission);
                    }

                    Log::info("Submission #{$submission->id} was {$action}d by User ID: " . Auth::id());

                    // 6. Redirect to the index/list view instead of just 'back' for better UX after processing
                    return redirect()->route('auditor.submissions.index')
                        ->with('success', "Submission #{$submission->id} has been " . ucfirst($action) . "d successfully.");
                });
            } catch (\Exception $e) {
                Log::error("Failed to process submission #{$id}: " . $e->getMessage());
                return back()->with('error', 'An error occurred while processing the submission. Please try again.');
            }
        }

    /**
     * INTERNAL LOGIC: Update Inventory upon approval
     */
    protected function processAssetLogic($submission)
        {
            $staff = $submission->submittedBy;
            $prefixParts = [];

            // 1. Build the dynamic prefix based on the hierarchy
            if ($staff->unit_id && $staff->unit) {
                $prefixParts[] = $staff->unit->office->office_code ?? 'OFFICE';
                $prefixParts[] = $staff->unit->unit_code;
            } elseif ($staff->department_id && $staff->department) {
                $prefixParts[] = $staff->department->faculty->faculty_code ?? 'FAC';
                $prefixParts[] = $staff->department->dept_code;
            } elseif ($staff->institute_id && $staff->institute) {
                $prefixParts[] = 'INST';
                $prefixParts[] = $staff->institute->institute_code;
            } elseif ($staff->faculty_id && $staff->faculty) {
                $prefixParts[] = $staff->faculty->faculty_code;
            }

            $finalPrefix = implode('/', $prefixParts);

            // 2. Loop through each item in the submission
            foreach ($submission->items as $item) {
                $asset = Asset::find($item->asset_id);
                if (!$asset) continue;

                // Prepare the base update data (Funding transfer)
                $updateData = [
                    'funding_source'          => $submission->funding_source,
                    'funding_source_per_item' => $item->funding_source_per_item,
                ];

                // 3. Specific Logic for New Purchases (Tagging & Availability)
                if ($submission->submission_type === 'new_purchase') {
                    $updateData['status'] = 'available';
                    
                    // Only generate a new tag if the asset doesn't have one yet
                    if (empty($asset->asset_tag)) {
                        $updateData['asset_tag'] = $this->generateHierarchicalTag($finalPrefix);
                    }
                }

                // 4. Handle other types (Disposal/Transfer logic would go here)
                if ($submission->submission_type === 'disposal') {
                    $updateData['status'] = 'disposed';
                }

                // Apply all updates to the Asset record
                $asset->update($updateData);
            }
        }

private function generateHierarchicalTag($prefix)
        {
            // 1. Safety check: If prefix is empty, default to 'GEN' (General)
            $prefix = !empty($prefix) ? $prefix : 'GEN';

            /** * 2. Find the last asset with this exact prefix.
             * We use a specific 'where' to avoid matching similar prefixes 
             * (e.g., 'FAC/MED' shouldn't match 'FAC/MED-LAB')
             */
            $lastAsset = Asset::where('asset_tag', 'LIKE', $prefix . '/%')
                ->whereRaw('LENGTH(asset_tag) = ?', [strlen($prefix) + 1 + 9]) // Optional: Ensures we only match tags with 9-digit suffixes
                ->orderBy('asset_tag', 'desc')
                ->first();

            $nextNumber = 1;

            if ($lastAsset) {
                // 3. Extract the numeric part after the last slash
                $segments = explode('/', $lastAsset->asset_tag);
                $lastNumString = end($segments);
                
                // Cast to int to increment
                if (is_numeric($lastNumString)) {
                    $nextNumber = (int)$lastNumString + 1;
                }
            }

            /**
             * 4. Format the final tag.
             * Use 9 digits as per your requirement: e.g., FAC/SURGERY/000000001
             */
            return $prefix . '/' . str_pad($nextNumber, 9, '0', STR_PAD_LEFT);
        }
}