<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SubmissionItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'submission_item_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'submission_id',
        'asset_id',      // Null if 'new_purchase', filled if 'transfer/disposal'
        'category_id',    // ADDED: To track the main category
        'subcategory_id', // ADDED: To track the specific subcategory
        'item_name',     
        'quantity',
        'cost',    
        'document_path',
        'serial_number',
        'status',
        'remarks',       // ADDED: To store specific feedback
        'condition',     
        'item_notes',    // Matches the 'item_notes' name in your Blade file
    ];

    protected $casts = [
        'quantity' => 'integer',
        'cost' => 'decimal:2',
        'document_path' => 'array', // Crucial for multiple file uploads
    ];

    /**
     * Relationship: The Parent Submission.
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'submission_id', 'submission_id');
    }

    public function getDocumentsAttribute()
{
    // 1. Get the raw data (Laravel handles casting if defined in $casts)
    $files = $this->document_path;

    // 2. Safety net for string data
    if (is_string($files)) {
        $files = json_decode($files, true);
    }

    // 3. Ensure we have an array to work with
    $files = (array) ($files ?? []);

    return collect($files)->map(function($file) {
        $path = '';
        $name = '';

        // Handle NEW object structure
        if (is_array($file) && isset($file['path'])) {
            $path = $file['path'];
            $name = $file['original_name'] ?? basename($file['path']);
        } 
        // Handle OLD string structure
        elseif (is_string($file)) {
            $path = $file;
            $name = basename($file);
        }

        if (empty($path)) return null;

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return (object) [
            'name'      => $name,
            'path'      => $path,
            'extension' => $extension,
            'is_image'  => in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']),
            'url'       => Storage::url($path)
        ];
    })->filter(); // Remove any null values
}

    /**
     * Relationship: The referenced asset.
     */
    
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id', 'asset_id');
    }

    /**
     * Relationship: The Main Category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    /**
     * Relationship: The Subcategory.
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id', 'subcategory_id');
    }
    // app/Models/SubmissionItem.php
    public function audit(): HasOne
    {
        return $this->hasOne(Audit::class, 'submission_item_id', 'submission_item_id');
    }
    // Inside SubmissionItem.php model

public function getGeneratedTagAttribute()
{
    // 1. If it's an existing asset (Maintenance/Retired), return it immediately.
    if ($this->asset_id && $this->asset) {
        return $this->asset->asset_tag;
    }

    $submission = $this->submission;
    if (!$submission) return 'ASSET_NOT_LINKED';

    // 2. Handle New Purchases
    if ($submission->submission_type === 'new_purchase') {
        
        // FORCE CHECK: If it is NOT explicitly 'approved', it IS pending.
        // We use strtolower to prevent 'Approved' vs 'approved' issues.
        if (strtolower($submission->status) !== 'approved') {
            return 'PENDING_ASSET_TAG';
        }

        // 3. ONLY GENERATE IF APPROVED
        $prefix = Auth::user()?->affiliation?->code ?? 'COM';
        $year = $this->created_at?->format('y') ?? date('y');
        $cat = $this->category?->category_code ?? 'XX';
        $subcat = $this->subcategory?->subcategory_code ?? 'XX';
        
        // Use asset_id if it exists (post-approval), otherwise item ID
        $serialSource = $this->asset_id ?? $this->id;
        $serial = str_pad($serialSource, 6, '0', STR_PAD_LEFT);

        return "COM/{$prefix}/{$cat}/{$subcat}/{$year}/{$serial}";
    }

    // 4. If it's not a new purchase and has no asset_id
    return 'ASSET_NOT_LINKED';
}
// Submission.php

public function getBatchDisplayTagAttribute()
{
    // If it's a new purchase and the BATCH isn't approved, return the pending string
    if ($this->submission_type === 'new_purchase' && $this->status !== 'approved') {
        return 'PENDING_ASSET_TAG';
    }

    // Otherwise, get the tag from the first item to represent the batch
    $firstItem = $this->items->first();
    
    if (!$firstItem) return 'NO_ITEMS';

    // This will correctly return the real tag or ASSET_NOT_LINKED
    return $firstItem->generated_tag;
}
}