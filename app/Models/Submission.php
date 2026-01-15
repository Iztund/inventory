<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    use HasFactory;

    protected $primaryKey = 'submission_id';
    
    // Status Constants for easy reference in logic
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'submitted_by_user_id',
        'reviewed_by_user_id',
        'submission_type', 
        'funding_source',
        'notes',
        'summary',
        'status',
        'submitted_at',
        'audited_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'audited_at' => 'datetime',
        
    ];

    // --- RELATIONSHIPS ---

    /**
     * The staff member (from Faculty/Dept/Unit) who made the request.
     */
    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by_user_id');
    }
   

    /**
     * The Admin who approved/rejected the audit.
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    /**
     * The specific assets included in this submission.
     */
    public function items(): HasMany
    {
        return $this->hasMany(SubmissionItem::class, 'submission_id', 'submission_id');
    }

    /**
     * History of changes for this submission.
     */
    public function audits(): HasMany
    {
        return $this->hasMany(Audit::class, 'submission_id', 'submission_id');
    }
}