<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory;

    protected $primaryKey = 'unit_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'unit_name',
        'unit_code',
        'unit_head_id', // FK → users.user_id
        'office_id',
        'is_active',
    ];

    /**
     * Unit Supervisor (User)
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'unit_head_id', 'user_id');
    }

     public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'office_id', 'office_id');
    }
    /**
     * Users assigned to this Unit
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'unit_id', 'unit_id');
    }

    /**
     * User profiles belonging to this Unit
     */
    public function userProfiles(): HasMany
    {
        return $this->hasMany(UserProfile::class, 'unit_id', 'unit_id');
    }
}
