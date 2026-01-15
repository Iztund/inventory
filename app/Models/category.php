<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $primaryKey = 'category_id';
    protected $keyType = 'int';

    protected $fillable = [
        'category_name',
        'description',
        'is_consumable',
        'is_active',
    ];

    /**
     * Helper to get names easily in the Blade template
     * This allows $category->name to work even if the column is category_name
     */
    public function getNameAttribute()
    {
        return $this->category_name;
    }
    public function scopeActive($query)
        {
            return $query->where('is_active', 'active');
        }

    /**
     * Scope to get only Main Categories (where parent_id is null)
     */
    

    /**
     * Relationship: Get the Parent Category
     */
   

    /**
     * Relationship: Get the Sub-categories
     */
    
    public function subcategories(): HasMany
{
    return $this->hasMany(Subcategory::class, 'category_id', 'category_id');
}
    /**
     * Relationship: Get all assets under this specific category
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'category_id', 'category_id');
    }

    /**
     * Relationship: Link to Submission Items
     */
    public function submissionItems(): HasMany
    {
        return $this->hasMany(SubmissionItem::class, 'category_id', 'category_id');
    }
}