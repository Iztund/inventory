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
    $this->applyHierarchyFilters($query, $request, 'submittedBy');

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

    // Route name determines view:
    // admin.submissions.pending -> admin.submissions.pending blade
    // auditor.submissions.index -> auditor.submissions.index blade
    $view = match(true) {
        $user->role_id == 1 && request()->routeIs('admin.submissions.index') => 'admin.submissions.index',
        $user->role_id == 1 => 'admin.submissions.index',
        $user->role_id == 3 => 'auditor.submissions.index',
        default => 'staff.submissions.index',
    };

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

        $myAssets = $this->getScopedAssetsForUser($user);
        $categories = Category::whereIn('is_active', ['active', 1])->orderBy('category_name')->get();
        $subcategoryMap = Subcategory::whereIn('is_active', ['active', 1])
            ->get()
            ->groupBy('category_id')
            ->map(fn($items) => $items->values())
            ->toArray();

        return view('staff.submissions.new_submission', compact('myAssets', 'categories', 'subcategoryMap'));
    }

    // ============================================
    // STORE - Staff Only
    // ============================================
    public function store(Request $request)
    {
        $this->authorizeRole([2]);
        $userId = Auth::id();

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.submission_type' => 'required|string',
            'items.*.category_id'     => 'required|exists:categories,category_id',
            'items.*.subcategory_id'  => 'required|exists:subcategories,subcategory_id',
            'items.*.item_name'       => 'required|string',
            'items.*.quantity'        => 'required|integer|min:1',
            'items.*.cost'            => 'nullable|numeric',
            'items.*.funding_source_per_item' => 'nullable|string',
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
                    $itemFilePaths = [];

                    if ($request->hasFile("items.$index.documents")) {
                        foreach ($request->file("items.$index.documents") as $file) {
                            $path = $file->store('audit_docs/' . $submission->submission_id, 'public');
                            $itemFilePaths[] = $path;
                        }
                    }

                    if ($itemData['submission_type'] === 'new_purchase') {
                        $serial = $itemData['serial_number'] ?? 'TEMP-' . strtoupper(uniqid());
                        $existingAsset = Asset::where('serial_number', $serial)
                            ->where('status', '!=', 'rejected')
                            ->first();

                        if ($existingAsset) {
                            $assetId = $existingAsset->asset_id;
                        } else {
                            $asset = Asset::create([
                                'serial_number'        => $serial,
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
                            ]);
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
                        'document_path'           => json_encode($itemFilePaths),
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
    // ============================================
    public function update(Request $request, $submission_id)
    {
        $this->authorizeRole([2]);
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
                    $item = isset($itemData['submission_item_id'])
                        ? SubmissionItem::find($itemData['submission_item_id'])
                        : null;

                    $filesToKeep = $itemData['existing_files'] ?? [];
                    $newFiles = [];

                    if ($request->hasFile("items.$index.new_evidence")) {
                        foreach ($request->file("items.$index.new_evidence") as $file) {
                            $path = $file->store('audit_docs/' . $submission->submission_id, 'public');
                            $newFiles[] = $path;
                        }
                    }

                    $finalFiles = array_merge($filesToKeep, $newFiles);

                    if ($item) {
                        $oldFiles = is_string($item->document_path) ? json_decode($item->document_path, true) : ($item->document_path ?? []);
                        $deletedFiles = array_diff($oldFiles, $filesToKeep);
                        foreach ($deletedFiles as $fileToDelete) {
                            if ($fileToDelete) Storage::disk('public')->delete($fileToDelete);
                        }
                    }

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
                            'item_notes'              => $itemData['item_notes'] ?? null,
                            'document_path'           => $finalFiles,
                        ]
                    );

                    $updatedItemIds[] = $newItem->submission_item_id;
                }

                $itemsToDelete = $submission->items()->whereNotIn('submission_item_id', $updatedItemIds)->get();
                foreach ($itemsToDelete as $oldItem) {
                    $paths = is_string($oldItem->document_path) ? json_decode($oldItem->document_path, true) : ($oldItem->document_path ?? []);
                    foreach ($paths as $p) {
                        Storage::disk('public')->delete($p);
                    }
                    $oldItem->delete();
                }

                $submission->update([
                    'funding_source' => $request->funding_source,
                    'notes'          => $request->notes,
                    'summary'        => $request->summary,
                ]);

                return redirect()->route('staff.submissions.index')
                    ->with('success', 'Submission #' . $submission->submission_id . ' updated successfully.');
            });
        } catch (\Exception $e) {
            Log::error("Update failed for ID " . $submission->submission_id . ": " . $e->getMessage());
            return back()->withInput()->with('error', 'Update failed: ' . $e->getMessage());
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
        $query = Asset::query()
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
                    $q->whereRaw('1 = 0');
                }
            })
            ->orderBy('item_name')
            ->get();

        return $query;
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

    private function applyHierarchyFilters($query, Request $request, string $relation = null): void
    {
        $prefix = $relation ? "$relation." : '';

        if ($request->filled('faculty_id')) $query->where("{$prefix}faculty_id", $request->faculty_id);
        if ($request->filled('dept_id'))     $query->where("{$prefix}dept_id", $request->dept_id);
        if ($request->filled('office_id'))   $query->where("{$prefix}office_id", $request->office_id);
        if ($request->filled('unit_id'))     $query->where("{$prefix}unit_id", $request->unit_id);
        if ($request->filled('institute_id')) $query->where("{$prefix}institute_id", $request->institute_id);
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