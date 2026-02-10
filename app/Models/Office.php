<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Office extends Model
{
    use HasFactory;

    protected $primaryKey = 'office_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'office_name',
        'office_code',
        'office_head_id', // Links to the User model (Office Head)
        'is_active',
        'office_address',
    ];

    /**
     * Define the relationship to the Office Head (a User).
     */
    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'office_head_id', 'user_id');
    }

    /**
     * Get all organizational units belonging to this administrative office.
     */
    public function units(): HasMany
    {
        // Assuming the 'units' table has an 'office_id' foreign key
        return $this->hasMany(Unit::class, 'office_id', 'office_id');
    }
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'office_id', 'office_id');
    }

     public function userProfiles(): HasMany
    {
        return $this->hasMany(UserProfile::class, 'office_id', 'office_id');
    }
}