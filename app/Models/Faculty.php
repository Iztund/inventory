<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faculty extends Model
{
    use HasFactory;

    protected $primaryKey = 'faculty_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'faculty_name',
        'faculty_code',
        'faculty_dean_id', // Links to the User model (Dean)
        'faculty_address',
        'is_active',
    ];

    /**
     * Define the relationship to the Faculty Dean (a User).
     */
    public function dean(): BelongsTo
    {
        // Assuming 'faculty_dean_id' links to 'user_id' in the User model
        return $this->belongsTo(User::class, 'faculty_dean_id', 'user_id')
        ->where('status', 'active')  // prevents inactive user display
        ->with('profile');
    }

    /**
     * Get all Departments belonging to this Faculty.
     */
    public function departments(): HasMany
    {
        // Assuming the 'departments' table has a 'faculty_id' foreign key
        return $this->hasMany(Department::class, 'faculty_id', 'faculty_id');
    }

    /**
     * Get all Institutes reporting directly to this Faculty.
     */
    public function institutes(): HasMany
    {
        // Assuming the 'institutes' table has a 'faculty_id' foreign key
        return $this->hasMany(Institute::class, 'faculty_id', 'faculty_id');
    }
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'dept_id', 'dept_id');
    }
     /**
     * Get all user profiles primarily assigned to this department.
     */
    public function userProfiles(): HasMany
    {
        return $this->hasMany(UserProfile::class, 'dept_id', 'dept_id');
    }

}