<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatusHistory extends Model
{
    use HasFactory;

    protected $primaryKey = 'history_id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    // Disable Laravel's default 'updated_at' since this table tracks immutable history
    public $updated_at = false;
    // Overriding 'created_at' since the column is named 'changed_at' and uses useCurrent()
    public const CREATED_AT = 'changed_at';

    protected $fillable = [
        'submission_id',
        'user_id',
        'old_status',
        'new_status',
        'comment',
    ];
    
    /**
     * Get the submission this history entry belongs to.
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'submission_id', 'submission_id');
    }

    /**
     * Get the user who made the status change.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
