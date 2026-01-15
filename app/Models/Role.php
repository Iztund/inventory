<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    // Custom table name since Laravel would default to 'roles' 

    // Custom Primary Key
    protected $primaryKey = 'role_id'; 
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'role_name',
        'role_description',
    ];

    /**
     * Get all users that belong to this role.
     */
    public function users(): HasMany
    {
        // Explicitly defining the foreign key 'role_id'
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }
}