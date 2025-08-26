<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $primaryKey = 'meal_id';

    protected $fillable = [
        'created_by_dietitian_id',
        'meal_name',
        'description',
        'calories_per_serving',
        'difficulty_level',
        'protein_grams',
        'carbs_grams',
        'fats_grams',
        'fiber_grams',
        'sugar_grams',
        'sodium_mg',
        'serving_size',
        'serving_unit',
        'dietary_tags',
        'ingredients',
        'preparation_method',
        'prep_time_minutes',
        'cook_time_minutes',
        'total_time_minutes',
        'difficulty_level',
        'is_active',
    ];

    protected $casts = [
        'is_active'          => 'boolean',
        'dietary_tags'       => 'array',
        'ingredients'        => 'array',
        'prep_time_minutes'  => 'integer',
        'cook_time_minutes'  => 'integer',
        'total_time_minutes' => 'integer',
    ];

    public function dietitian()
    {
        return $this->belongsTo(User::class, 'created_by_dietitian_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDietaryTag($query, $tag)
    {
        return $query->whereJsonContains('dietary_tags', $tag);
    }

    // Accessors for backward compatibility and display
    public function getNameAttribute()
    {
        return $this->description; // Using description as the meal name
    }

    public function getDietitianIdAttribute()
    {
        return $this->created_by_dietitian_id;
    }

    public function getIsPublicAttribute()
    {
        $dietaryTags = $this->dietary_tags ?: [];
        return $dietaryTags['is_public'] ?? true;
    }

    public function getCategoryAttribute()
    {
        $dietaryTags = $this->dietary_tags ?: [];
        return $dietaryTags['category'] ?? 'general';
    }

    public function getCategoryDisplayAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->category));
    }

    public function getCuisineTypeAttribute()
    {
        $dietaryTags = $this->dietary_tags ?: [];
        return $dietaryTags['cuisine_type'] ?? null;
    }

    public function getDifficultyLevelAttribute()
    {
        return $this->attributes['difficulty_level'] ?? 'easy';
    }

    public function getDifficultyDisplayAttribute()
    {
        return ucfirst($this->difficulty_level);
    }

    public function getPreparationTimeAttribute()
    {
        return $this->prep_time_minutes;
    }

    public function getCookingTimeAttribute()
    {
        return $this->cook_time_minutes;
    }

    public function getInstructionsAttribute()
    {
        // Split preparation_method by lines or keep as is if it's already an array
        if (is_string($this->preparation_method)) {
            return array_filter(explode("\n", $this->preparation_method));
        }
        return $this->preparation_method ?: [];
    }

    public function getAllergenInfoAttribute()
    {
        return $this->allergens ?: [];
    }

    public function getServingsAttribute()
    {
        return (int) $this->serving_size;
    }

    public function getProteinPerServingAttribute()
    {
        return $this->protein_grams;
    }

    public function getCarbsPerServingAttribute()
    {
        return $this->carbs_grams;
    }

    public function getFatPerServingAttribute()
    {
        return $this->fats_grams;
    }

    public function getFiberPerServingAttribute()
    {
        return $this->fiber_grams;
    }

    public function getSugarPerServingAttribute()
    {
        return $this->sugar_grams;
    }

    public function getSodiumPerServingAttribute()
    {
        return $this->sodium_mg;
    }

    public function getTotalTimeAttribute()
    {
        return ($this->prep_time_minutes ?? 0) + ($this->cook_time_minutes ?? 0);
    }
}
