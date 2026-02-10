<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Institute extends Model
{
    use HasFactory;

    protected $primaryKey = 'institute_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'institute_name',
        'institute_code',
        'institute_director_id', // Links to the User model (Director)
        'faculty_id',            // Optional: for institutes under a Faculty
        // REMOVED 'is_stand_alone' - It's derivable from faculty_id being null
        'is_active',
        'institute_address',
    ];

    /**
     * Define the relationship to the Institute Director (a User).
     */
    public function director(): BelongsTo
    {
        return $this->belongsTo(User::class, 'institute_director_id', 'user_id');
    }

    /**
     * Get the Faculty this Institute reports to (optional).
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'faculty_id', 'faculty_id');
    }

    // app/Models/Institute.php

    public function getUsersCountAttribute(): int
        {
            // 1. Check if 'users_count' was already loaded by the Controller (Eager Loading)
            if (array_key_exists('users_count', $this->attributes)) {
                return (int) $this->attributes['users_count'];
            }

            // 2. Fallback to a manual count if not eager loaded
            return $this->users()->count();
        }

    /*
     * Custom Accessor to determine if the institute is stand-alone.
     * Use $institute->is_stand_alone instead of reading a database column.
     */
    public function getIsStandAloneAttribute(): bool
    {
        return is_null($this->faculty_id);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'institute_id', 'institute_id');
    }
    
    public function userProfiles(): HasMany
    {
        return $this->hasMany(UserProfile::class, 'institute_id', 'institute_id');
    }
}