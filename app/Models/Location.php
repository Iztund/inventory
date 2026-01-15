<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    // Primary Key is default 'id'
    // Foreign Key to Unit is 'unit_id'
    protected $primaryKey = 'location_id';
    protected $keyType = 'int';

    protected $fillable = [
        'unit_id',
        'address',
    ];

    /**
     * Get the parent unit this location belongs to.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Get all assets currently located here.
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'location_id');
    }
}