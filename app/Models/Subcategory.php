<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subcategory extends Model
{
    use HasFactory;

    protected $primaryKey = 'subcategory_id';
    
    protected $fillable = [
        'category_id',
        'subcategory_name',
        'description',
        'is_active'
    ];

    /**
     * Helper to allow $subcategory->name to work in Blade
     */
    public function getNameAttribute()
    {
        return $this->subcategory_name;
    }
    /**
 * Scope to only include active subcategories
 */
    public function scopeActive($query)
        {
            return $query->where('is_active', 'active');
        }
    /**
     * Relationship back to the Parent Category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    /**
     * Relationship to Assets
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'subcategory_id', 'subcategory_id');
    }
}