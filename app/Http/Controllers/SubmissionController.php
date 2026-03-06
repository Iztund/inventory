<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Storage, Log};
use App\Models\{Submission, SubmissionItem, Asset, Category, Subcategory, Audit,
    Faculty, Department, Office, Unit, Institute};

class SubmissionController extends Controller
{
    // ============================================
    // INDEX - Universal (Staff: own, Auditor/Admin: filtered/all)
    // ============================================
    public function index(Request $request)
{
    $user = Auth::user();

    $query = $this->getScopedSubmissionsQuery($user)
        ->with([
            'items.asset',
            'items.category',
            'items.subcategory',
            'submittedBy.profile',
            'submittedBy.faculty',
            'submittedBy.department',
            'submittedBy.office',
            'submittedBy.unit',
            'submittedBy.institute',
            'reviewedBy.profile'
        ]);

    // Admin hitting /submissions/pending OR Auditor default: show pending
    if (in_array((int)$user->role_id, [1, 3])) {
        if (!$request->filled('status')) {
            $query->where('status', 'pending');
        }
    }

    // Status filter (pending/approved/rejected)
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Hierarchy filters on the submittedBy user
    $this->applyHierarchyFilters($query, $request, true);

    // Category/Subcategory filters on items
    if ($request->filled('category_id')) {
        $query->whereHas('items', fn($q) => $q->where('category_id', $request->category_id));
        if ($request->filled('sub_id')) {
            $query->whereHas('items', fn($q) => $q->where('subcategory_id', $request->sub_id));
        }
    }

    // Search
    if ($request->filled('search')) {
        $search = trim($request->search);
        $cleanId = preg_replace('/[^0-9]/', '', $search);

        $query->where(function ($q) use ($search, $cleanId) {
            if ($cleanId) $q->where('submission_id', $cleanId);

            $q->orWhereHas('items', fn($iq) => $iq->where('item_name', 'like', "%$search%")
                ->orWhere('serial_number', 'like', "%$search%"));

            $q->orWhereHas('submittedBy.profile', fn($pq) => $pq->where('first_name', 'like', "%$search%")
                ->orWhere('last_name', 'like', "%$search%"));
        });
    }

    $submissions = $query->latest('submitted_at')->paginate(20)->appends($request->query());

    // Organizational dropdowns for filters
    $dropdownData = $this->getOrganizationalDropdownData();

    $sampleStatus = $submissions->first()?->status ?? $request->get('status', 'pending');

// 2. Set the View based on that status
$view = 'staff.submissions.my_submissions';

if ($user->role_id == 1) {
    $view = 'admin.submissions.index';
} elseif ($user->role_id == 3) {
    // If the submissions being sent to the view are 'approved', use the Registry blade
    $view = ($sampleStatus === 'approved') 
            ? 'auditor.approved_items.index' 
            : 'auditor.submissions.index';
}
    

    return view($view, array_merge(compact('submissions'), $dropdownData));
}

    // ============================================
    // SHOW - Universal (pending = audit form, others = read-only)
    // ============================================
 public function show($id) 
{
    $user = Auth::user();

    // 1. Fetch the Submission with deep eager loading
    $submission = Submission::with([
        'submittedBy.profile',
        'reviewedBy.profile',
        'items.category',
        'items.subcategory',
        'items.asset.faculty',
        'items.asset.department',
        'items.asset.office',
        'items.asset.unit',
        'items.asset.institute',
        'items.asset.category',
        'items.asset.subcategory',
        'items.audit',            // Load audit record for each item
        'audits.auditor.profile'
    ])->findOrFail($id);
     Log::info("Loading submission: {$submission->submission_id}, Items: " . $submission->items->pluck('item_name')->implode(', '));
    // 2. Security Check
    $this->authorizeSubmissionAccess($submission);

    // 3. Fetch Audit History
    $history = $submission->audits()
        ->with('auditor.profile')
        ->latest()
        ->get();

    // 4. Determine View Path based on Role
    $roleFolder = match ((int)$user->role_id) {
        1 => 'admin',
        3 => 'auditor',
        default => 'staff',
    };

    $viewPath = "$roleFolder.submissions.show";

    return view($viewPath, compact('submission', 'history'));
}
    // ============================================
    // CREATE - Staff Only
    // ============================================
    public function create()
{
    $this->authorizeRole([2]);
    $user = Auth::user();

    // Use ONLY the scoped helper - no need for the manual $assets query
    $myAssets = $this->getScopedAssetsForUser($user);

    $categories = Category::whereIn('is_active', ['active', 1])
        ->orderBy('category_name')
        ->get();

    $subcategoryMap = Subcategory::whereIn('is_active', ['active', 1])
        ->get()
        ->groupBy('category_id')
        ->map(fn($items) => $items->values())
        ->toArray();

    // Pass 'myAssets' as 'assets' to the view so your existing @foreach works
    return view('staff.submissions.new_submission', [
        'assets' => $myAssets, 
        'categories' => $categories, 
        'subcategoryMap' => $subcategoryMap
    ]);
}

    // ============================================
    // STORE - Staff Only
    // ============================================
    public function store(Request $request)
{
    $this->authorizeRole([2]);
    $userId = Auth::id();
    $user = Auth::user();
    
    // 1. Validate
    $request->validate([
        'items' => 'required|array|min:1',
        'items.*.submission_type' => 'required|string',
        'items.*.category_id'     => 'required|exists:categories,category_id',
        'items.*.subcategory_id'  => 'required|exists:subcategories,subcategory_id',
        'items.*.item_name'       => 'required|string',
        'items.*.quantity'        => 'required|integer|min:1',
        'items.*.cost'            => 'nullable|numeric|min:0',
        'items.*.documents'       => 'nullable|array',
        'items.*.item_notes'      => 'nullable|string',
        'items.*.serial_number'   => 'nullable|string',
        'items.*.condition'       => 'nullable|string',
        'items.*.documents.*'     => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
        'notes'                   => 'nullable|string',
        'summary'                 => 'nullable|string',

    ]);

    try {
        DB::transaction(function () use ($request, $userId, $user) {
            // Create the main submission record
            $submission = Submission::create([
                'submitted_by_user_id' => $userId,
                'submission_type'      => $request->items[0]['submission_type'] ?? 'new_purchase',
                'status'               => 'pending',
                'notes'                => $request->notes,
                'summary'              => $request->summary,
                'submitted_at'         => now(),
            ]);

            // Loop through items
            foreach ($request->items as $index => $itemData) {
                
                // --- FILE UPLOAD LOGIC (FIXED) ---
                // --- FILE UPLOAD LOGIC ---
                $itemFileDetails = [];

                // Use dot notation to target the specific row's documents
                if ($request->hasFile("items.$index.documents")) {
                    $files = $request->file("items.$index.documents");
                    
                    foreach ($files as $file) {
                        // Store the file and get the path
                        $path = $file->store('audit_docs/' . $submission->submission_id, 'public');
                        
                        $itemFileDetails[] = [
                            'original_name' => $file->getClientOriginalName(),
                            'path'          => $path
                        ];
                    }
                }
                // If $itemFileDetails is still empty here, the files aren't reaching the server at all.

                // --- ASSET CREATION ---
                $assetId = $itemData['asset_id'] ?? null;
                $asset = null;

                if ($itemData['submission_type'] === 'new_purchase') {
                    $serial = !empty($itemData['serial_number']) 
                        ? $itemData['serial_number'] 
                        : 'TEMP-' . strtoupper(uniqid()) . '-' . $index;
                }

                // --- CREATE SUBMISSION ITEM ---
                $submissionItem = $submission->items()->create([
                    'asset_id'       => $assetId,
                    'category_id'    => $itemData['category_id'],
                    'subcategory_id' => $itemData['subcategory_id'],
                    'condition'      => $itemData['condition'] ?? null,
                    'item_notes'     => $itemData['item_notes'] ?? null,
                    'item_name'      => $itemData['item_name'],
                    'cost'           => $itemData['cost'] ?? 0,
                    'serial_number'  => $itemData['serial_number'] ?? null,
                    'quantity'       => $itemData['quantity'],
                    'document_path'  => $itemFileDetails,
                    'status'         =>'pending',
                ]);

                if ($asset) {
                    $asset->update(['submission_item_id' => $submissionItem->submission_item_id]);
                }
            }
        });

        return redirect()->route('staff.submissions.index')
            ->with('success', 'Inventory submission created successfully.');

    } catch (\Exception $e) {
        Log::error("Store failed: " . $e->getMessage());
        return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
    }
}
    // ============================================
    // EDIT - Staff Only (Own Pending Submissions)
    // ============================================
    public function edit($submission_id)
    {
        $this->authorizeRole([2]);
        $user = Auth::user();

        $submission = Submission::where('submission_id', $submission_id)
            ->where('submitted_by_user_id', $user->user_id)
            ->where('status', 'pending')
            ->with(['items.category', 'items.subcategory'])
            ->firstOrFail();

        $categories = Category::whereIn('is_active', ['active', 1])->orderBy('category_name')->get();
        $subcategoryMap = Subcategory::whereIn('is_active', ['active', 1])
            ->get()
            ->groupBy('category_id')
            ->map(fn($items) => $items->values())
            ->toArray();

        $myAssets = $this->getScopedAssetsForUser($user);

        return view('staff.submissions.edit_submission', compact('submission', 'categories', 'subcategoryMap', 'myAssets'));
    }

    // ============================================
    // UPDATE - Staff Only

public function update(Request $request, $submission_id)
{
    $this->authorizeRole([2]);
    $user = Auth::user();

    $submission = Submission::where('submission_id', $submission_id)
        ->where('submitted_by_user_id', $user->user_id)
        ->where('status', 'pending')
        ->firstOrFail();

    $request->validate([
        'notes'                  => 'nullable|string',
        'summary'                => 'nullable|string',
        'items'                  => 'required|array|min:1',
        'items.*.item_name'      => 'required|string',
        'items.*.new_evidence.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
    ]);

    try {
        return DB::transaction(function () use ($request, $submission) {
            // Track which items were updated (to delete removed ones later)
            $processedItemIds = [];

            foreach ($request->items as $index => $itemData) {
                
                // ============================================
                // DETERMINE IF UPDATE OR CREATE
                // ============================================
                
                $submissionItemId = $itemData['submission_item_id'] ?? null;
                
                // If has submission_item_id, it's an EXISTING item (UPDATE)
                // If no submission_item_id, it's a NEW item (CREATE)
                
                if ($submissionItemId) {
                    // UPDATE existing item
                    $item = SubmissionItem::where('submission_item_id', $submissionItemId)
                        ->where('submission_id', $submission->submission_id)
                        ->firstOrFail();
                } else {
                    // CREATE new item
                    $item = new SubmissionItem();
                    $item->submission_id = $submission->submission_id;
                }

                // ============================================
                // HANDLE FILES
                // ============================================
                
                // Start with existing files that user wants to keep
                $filesToKeep = [];
                if (isset($itemData['existing_files']) && is_array($itemData['existing_files'])) {
                    foreach ($itemData['existing_files'] as $existingPath) {
                        // Handle both string paths and array objects
                        if (is_string($existingPath)) {
                            $filesToKeep[] = ['path' => $existingPath];
                        } elseif (is_array($existingPath) && isset($existingPath['path'])) {
                            $filesToKeep[] = $existingPath;
                        }
                    }
                }

                // Add newly uploaded files
                $newFiles = [];
                if ($request->hasFile('items')) {
                    $allItems = $request->file('items');
                    
                    if (isset($allItems[$index]['new_evidence'])) {
                        $files = $allItems[$index]['new_evidence'];
                        
                        foreach ($files as $file) {
                            $path = $file->store('audit_docs/' . date('Y') . '/' . $submission->submission_id, 'public');
                            $newFiles[] = [
                                'original_name' => $file->getClientOriginalName(),
                                'path'          => $path,
                                'size'          => $file->getSize(),
                                'uploaded_at'   => now()->toDateTimeString(),
                            ];
                        }
                    }
                }

                // Merge files
                $finalFiles = array_merge($filesToKeep, $newFiles);

                // Delete removed files from storage
                if ($item->exists) {
                    $oldFiles = (array) ($item->document_path ?? []);
                    $finalPaths = array_column($finalFiles, 'path');
                    
                    foreach ($oldFiles as $oldFile) {
                        $oldPath = is_array($oldFile) ? ($oldFile['path'] ?? null) : $oldFile;
                        
                        if ($oldPath && !in_array($oldPath, $finalPaths)) {
                            Storage::disk('public')->delete($oldPath);
                            Log::info("Deleted removed file: $oldPath");
                        }
                    }
                }

                // ============================================
                // SAVE ITEM
                // ============================================
                
                $item->fill([
                    'category_id'    => $itemData['category_id'],
                    'subcategory_id' => $itemData['subcategory_id'],
                    'item_name'      => $itemData['item_name'],
                    'quantity'       => $itemData['quantity'],
                    'cost'           => $itemData['cost'] ?? 0,
                    'serial_number'  => $itemData['serial_number'] ?? null,
                    'item_notes'     => $itemData['item_notes'] ?? null,
                    'document_path'  => $finalFiles,
                ]);
                
                $item->save();
                
                $processedItemIds[] = $item->submission_item_id;
                
                Log::info("Processed item", [
                    'submission_id' => $submission->submission_id,
                    'item_id' => $item->submission_item_id,
                    'action' => $submissionItemId ? 'updated' : 'created',
                ]);
            }

            // ============================================
            // DELETE REMOVED ITEMS
            // ============================================
            
            $removedItems = $submission->items()
                ->whereNotIn('submission_item_id', $processedItemIds)
                ->get();
            
            foreach ($removedItems as $removedItem) {
                // Delete associated files
                if ($removedItem->document_path) {
                    foreach ((array) $removedItem->document_path as $file) {
                        $path = is_array($file) ? ($file['path'] ?? null) : $file;
                        if ($path) {
                            Storage::disk('public')->delete($path);
                        }
                    }
                }
                
                $removedItem->delete();
                Log::info("Deleted removed item: " . $removedItem->submission_item_id);
            }

            // ============================================
            // UPDATE SUBMISSION META
            // ============================================
            
            $submission->update([
                'notes'   => $request->notes,
                'summary' => $request->summary,
            ]);

            return redirect()->route('staff.submissions.index', $submission->submission_id)
                ->with('success', 'Submission updated successfully.');
        });
        
    } catch (\Exception $e) {
        Log::error("Update failed for submission $submission_id", [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        
        return back()
            ->withInput()
            ->with('error', 'Update failed: ' . $e->getMessage());
    }
}

    // ============================================
    // DESTROY - Staff Only
    // ============================================
    public function destroy($id)
    {
        $this->authorizeRole([2]);
        $submission = Submission::findOrFail($id);

        if ($submission->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending submissions can be deleted.');
        }

        $submission->items()->delete();
        $submission->delete();

        return redirect()->route('staff.submissions.index')->with('success', 'Submission deleted successfully.');
    }

    // ============================================
    // HELPERS
    // ============================================

    private function getScopedSubmissionsQuery($user)
    {
        $query = Submission::query();

        if ((int)$user->role_id === 2) {
            $query->where('submitted_by_user_id', $user->user_id);
        }

        return $query;
    }

    private function getScopedAssetsForUser($user)
{
    // If the user has none of these assigned, don't even hit the DB
    if (!$user->faculty_id && !$user->department_id && !$user->office_id && !$user->unit_id && !$user->institute_id) {
        return collect(); 
    }

    return Asset::query()
        ->where('status', '!=', 'retired')
        ->where(function($q) use ($user) {
            if ($user->institute_id)   return $q->where('current_institute_id', $user->institute_id);
            if ($user->unit_id)        return $q->where('current_unit_id', $user->unit_id);
            if ($user->department_id)  return $q->where('current_dept_id', $user->department_id);
            if ($user->office_id)      return $q->where('current_office_id', $user->office_id);
            if ($user->faculty_id)     return $q->where('current_faculty_id', $user->faculty_id);
        })
        ->orderBy('item_name')
        ->get();
}

    private function getOrganizationalDropdownData(): array
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

    private function applyHierarchyFilters($query, Request $request, bool $isSubmission = false): void
{
    // Define the branch pairings for standalone/exclusive logic
    $hierarchy = [
        'faculty_id' => 'dept_id',
        'office_id'  => 'unit_id',
    ];
    $standalone = ['dept_id', 'unit_id', 'institute_id'];

    // 1. Determine the prefix for direct table filtering
    // Assets use 'current_', Submissions (if filtered directly) usually don't.
    $modelTable = $query->getModel()->getTable();
    $p = ($modelTable === 'assets') ? 'current_' : '';

    if ($isSubmission) {
        // --- SUBMISSION LOGIC (Filter via User Profile) ---
        foreach (array_merge(array_keys($hierarchy), $standalone) as $field) {
            if ($request->filled($field)) {
                $query->whereHas('submittedBy', function ($q) use ($field, $request, $hierarchy) {
                    $q->where($field, $request->get($field));

                    // Exclusive Logic: If parent is picked but child isn't, child must be NULL
                    if (isset($hierarchy[$field]) && !$request->filled($hierarchy[$field])) {
                        $q->whereNull($hierarchy[$field]);
                    }
                });
            }
        }
    } else {
        // --- ASSET/DIRECT LOGIC (Filter table columns directly) ---
        foreach ($hierarchy as $parent => $child) {
            if ($request->filled($parent)) {
                $query->where($p . $parent, $request->get($parent));
                if (!$request->filled($child)) {
                    $query->whereNull($p . $child);
                }
            }
        }
        foreach ($standalone as $field) {
            if ($request->filled($field)) {
                $query->where($p . $field, $request->get($field));
            }
        }
    }
}

    private function enrichItems($submission)
    {
        return $submission->items->map(function ($item) use ($submission) {
            $item->resolved_location = $this->resolveLocation($item->asset);
            $item->calculated_unit_cost = $item->unit_cost > 0
                ? $item->unit_cost
                : ($item->cost / max($item->quantity, 1));
            $item->auditor_name = $submission->reviewedBy->profile->full_name
                ?? $submission->reviewedBy->username
                ?? 'System Auditor';
            return $item;
        });
    }

    private function resolveLocation($asset): string
    {
        if (!$asset) return 'College of Medicine';
        return $asset->unit?->unit_name
            ?? $asset->office?->office_name
            ?? $asset->department?->dept_name
            ?? $asset->faculty?->faculty_name
            ?? $asset->institute?->institute_name
            ?? 'College of Medicine';
    }

    private function authorizeSubmissionAccess(Submission $submission): void
    {
        $user = Auth::user();
        $isAdminOrAuditor = in_array((int)$user->role_id, [1, 3]);
        $isOwner = (int)$submission->submitted_by_user_id === (int)$user->user_id;

        if (!$isAdminOrAuditor && !$isOwner) {
            abort(403, 'Unauthorized Access');
        }
    }

    private function authorizeRole(array $allowedRoles)
    {
        if (!in_array((int)Auth::user()->role_id, $allowedRoles)) {
            abort(403, 'Unauthorized action.');
        }
    }

    private function getViewPath($path)
    {
        $folder = match ((int)Auth::user()->role_id) {
            1 => 'admin',
            3 => 'auditor',
            default => 'staff',
        };

        return "$folder.$path";
    }
}