<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class Department extends Model
{
    use HasFactory;

    // Custom Primary Key
    protected $primaryKey = 'dept_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'dept_name',
        'dept_code', // Assuming you added this column
        'dept_head_id',
        'faculty_id',
        'is_active',
        'dept_address',
    ];

    /**
     * Define the relationship to the Department Head (a User).
     */
    public function deptHead()
        {
            return $this->belongsTo(User::class, 'dept_head_id', 'user_id')
                        ->where('status', 'active')  // prevents inactive user display
                        ->with('profile');              // ensure full_name loads
        }

    /**
     * Get all organizational units belonging to this department.
     */
    public function faculty(): BelongsTo // <-- NEW RELATIONSHIP
    {
        // Assuming the 'departments' table has a 'faculty_id' foreign key
        return $this->belongsTo(Faculty::class, 'faculty_id', 'faculty_id');
    }

    /**
     * Get all user profiles primarily assigned to this department.
     */
    public function userProfiles(): HasMany
    {
        return $this->hasMany(UserProfile::class, 'dept_id', 'dept_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'dept_id', 'dept_id');
    }
    public function assets(): HasMany
    {
        // Adjust 'dept_id' if your Asset table uses a different foreign key name
        return $this->hasMany(Asset::class, 'current_dept_id', 'dept_id');
    }
}