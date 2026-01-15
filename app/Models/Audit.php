<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Audit extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'submission_item_id',
        'auditor_id',
        'audited_price',
        'comments',
        'decision',
        'audited_at',
    ];

    protected $casts = [
        'audited_price' => 'decimal:2',
        'audited_at' => 'datetime',
    ];

    public function submission(): BelongsTo
    {
        // Links audits.submission_id to submissions.submission_id
        return $this->belongsTo(Submission::class, 'submission_id', 'submission_id');
    }

    public function auditor(): BelongsTo
    {
        // Links audits.auditor_id to users.id
        return $this->belongsTo(User::class, 'auditor_id');
    }
    // app/Models/Audit.php
    public function item(): BelongsTo
    {
        return $this->belongsTo(SubmissionItem::class, 'submission_item_id', 'submission_item_id');
    }
}