<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkImport extends Model
{
    use HasFactory;

    protected $table = 'bulk_imports';
    protected $primaryKey = 'import_id';

    protected $fillable = [
        'imported_by_user_id',
        'import_type',
        'entity_type',
        'entity_id',
        'original_filename',
        'total_rows',
        'successful_imports',
        'failed_imports',
        'error_log',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'error_log' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function importedBy()
    {
        return $this->belongsTo(User::class, 'imported_by_user_id', 'user_id');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class, 'bulk_import_id', 'import_id');
    }

    /**
     * FIX: Removed ->where('entity_type', ...) from all these.
     * The match() logic in the accessor below handles the filtering.
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'entity_id', 'faculty_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'entity_id', 'dept_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'entity_id', 'office_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'entity_id', 'unit_id');
    }

    public function institute()
    {
        return $this->belongsTo(Institute::class, 'entity_id', 'institute_id');
    }

    // Helper method to get entity name
    public function getEntityNameAttribute()
    {
        // Add a safety check for null entity_id
        if (!$this->entity_id) return 'N/A';

        return match($this->entity_type) {
            'faculty'    => $this->faculty?->faculty_name,
            'department' => $this->department?->dept_name,
            'office'     => $this->office?->office_name,
            'unit'       => $this->unit?->unit_name,
            'institute'  => $this->institute?->institute_name,
            default      => 'Unknown',
        } ?? 'Record Not Found';
    }

    public function getSuccessRateAttribute()
    {
        if (!$this->total_rows || $this->total_rows === 0) return 0;
        return round(($this->successful_imports / $this->total_rows) * 100, 2);
    }
}