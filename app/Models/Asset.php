<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory;

    protected $primaryKey = 'asset_id';
    protected $keyType = 'int';

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'current_faculty_id',
        'current_dept_id',
        'current_office_id',
        'current_unit_id',
        'current_institute_id',
        'location_id',
        'serial_number',
        'item_name',
        
        
        'description',
        'purchase_date',
        'purchase_price',
        'quantity',
        'status',
        'asset_tag',
        'funding_source',
        'funding_source_per_item', // ADDED: To persist funding info from submission to inventory
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'purchase_date' => 'date',
    ];

    /**
     * RELATIONSHIPS: Organizational Hierarchy
     */

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'current_faculty_id', 'faculty_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'current_dept_id', 'dept_id'); 
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'current_office_id', 'office_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'current_unit_id', 'unit_id');
    }

    public function institute(): BelongsTo
    {
        // Fixed: Use current_institute_id to match your fillable and logic
        return $this->belongsTo(Institute::class, 'current_institute_id', 'institute_id');
    }

    /**
     * RELATIONSHIPS: Categorization & Location
     */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * RELATIONSHIPS: Audit & History
     */
    public function submissionItem()
        {
            return $this->belongsTo(SubmissionItem::class, 'submission_item_id', 'submission_item_id');
        }
    public function submissionItems(): HasMany
    {
        return $this->hasMany(SubmissionItem::class, 'asset_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(SubmissionItem::class, 'asset_id')->with('submission');
    }
   // app/Models/Asset.php

public function scopeApplyScopeForUser($query, $user)
{
    // Admin & Auditor: see all
    if (in_array((int)$user->role_id, [1, 3])) {
        return $query;
    }

    // Staff: scoped by hierarchy priority
    return $query->where(function($q) use ($user) {
        if ($user->unit_id) {
            $q->where('current_unit_id', $user->unit_id);
        } elseif ($user->department_id) {
            $q->where('current_dept_id', $user->department_id);
        } elseif ($user->institute_id) {
            $q->where('current_institute_id', $user->institute_id);
        } elseif ($user->office_id) {
            $q->where('current_office_id', $user->office_id);
        } elseif ($user->faculty_id) {
            $q->where('current_faculty_id', $user->faculty_id);
        } else {
            $q->whereRaw('1 = 0'); // No assignment, no access
        }
    });
}
}