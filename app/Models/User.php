<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable // Note: This table still uses Laravel's default 'id' as PK for now, or 'user_id' if you updated the migration. I'll assume 'id' for simplicity based on default Laravel, but if you used 'user_id', you should update $primaryKey.
{
    use HasFactory, Notifiable;

    // Assuming your users table uses 'id' by default, or you can uncomment below if you used 'user_id'
    protected $primaryKey = 'user_id'; 
    protected $keyType = 'int'; 

    protected $fillable = [
        'username',
        'email',
        'password',
        'role_id',
        
        // <<< ADDED MISSING ORG IDs HERE >>>
        'faculty_id',   
        'institute_id', 
        'office_id',    
        
        // Secondary Affiliation
        'unit_id',
        'dept_id',
        
        'status',
        'must_change_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'must_change_password' => 'boolean',
        'password' => 'hashed',
    ];
    
    // --- RELATIONSHIPS ---

    /**
     * Get the role associated with the user.
     * Links to user_roles table via role_id.
     */
    public function role(): BelongsTo
    {
        // Adjust the foreign key and owner key if necessary, but standard conventions often work.
        return $this->belongsTo(Role::class, 'role_id', 'role_id'); 
    }
    /**
     * Get the user profile associated with the user.
     * Links to user_profiles table via user_id.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'user_id');
    }
    public function getFullNameAttribute()
        {
            if (!$this->profile) return $this->username; // Fallback

            return trim("{$this->profile->first_name} {$this->profile->middle_name} {$this->profile->last_name}");
        }
    /**
     * Get the department the user belongs to.
     * Links to departments table via dept_id.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'dept_id', 'dept_id');
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'faculty_id', 'faculty_id');
    }

    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class, 'institute_id', 'institute_id');
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'office_id', 'office_id');
    }


    public function unit():BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id','unit_id'); // Adjust foreign key if different
    }
    /**
     * Get the submissions created by the user.
     */
    // app/Models/User.php

    public function headedDepartment()
        {
            return $this->hasOne(Department::class, 'dept_head_id', 'user_id');
        }

    public function headedFaculty()
        {
            return $this->hasOne(Faculty::class, 'faculty_dean_id', 'user_id');
        }

    public function headedOffice()
        {
            return $this->hasOne(Department::class, 'office_head_id', 'user_id');
        }

    public function headedInstitute()
        {
            return $this->hasOne(Institute::class, 'institute_director_id', 'user_id');
        }


    
    public function submittedSubmissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'submitted_by_user_id');
    }

    /**
     * Get the submissions reviewed by the user (Admin/Auditor).
     */
    public function reviewedSubmissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'reviewed_by_user_id');
    }
    
}