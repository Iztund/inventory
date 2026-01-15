<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'funding_source_per_item',
        'cost',    
        'document_path',
        'serial_number',
        'status',
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
}