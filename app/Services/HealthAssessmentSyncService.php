<?php

namespace App\Services;

use App\Models\HealthAssessment;
use App\Models\Meal;
use App\Models\User;
use Illuminate\Support\Collection;

class HealthAssessmentSyncService
{
    /**
     * Get member's dietary preferences from their health assessment
     */
    public function getMemberDietaryProfile(int $memberId): array
    {
        $healthAssessment = HealthAssessment::where('user_id', $memberId)->latest()->first();
        
        if (!$healthAssessment) {
            return $this->getDefaultDietaryProfile();
        }

        return [
            'dietary_restrictions' => $healthAssessment->dietary_restrictions ?? [],
            'fitness_goals' => $healthAssessment->fitness_goals ?? [],
            'allergies' => $healthAssessment->allergies ?? [],
            'current_weight' => $healthAssessment->weight_kg,
            'target_weight' => $healthAssessment->target_weight_kg,
            'activity_level' => $healthAssessment->activity_level,
            'medical_conditions' => $healthAssessment->medical_conditions ?? [],
            'preferred_meal_times' => $this->inferMealTimes($healthAssessment),
            'calorie_target' => $this->calculateDailyCalorieTarget($healthAssessment)
        ];
    }

    /**
     * Filter meals based on member's dietary restrictions and preferences
     */
    public function filterMealsForMember(int $memberId): Collection
    {
        $profile = $this->getMemberDietaryProfile($memberId);
        
        $query = Meal::query();

        // Filter by dietary restrictions
        if (!empty($profile['dietary_restrictions'])) {
            foreach ($profile['dietary_restrictions'] as $restriction) {
                $query->whereJsonContains('dietary_tags', $restriction);
            }
        }

        // Exclude meals with allergens
        if (!empty($profile['allergies'])) {
            foreach ($profile['allergies'] as $allergen) {
                $query->whereJsonDoesntContain('allergens', $allergen);
            }
        }

        // Filter by fitness goals (calories, protein content)
        if (in_array('Muscle Gain', $profile['fitness_goals'])) {
            $query->where('protein', '>=', 20); // High protein meals
        }

        if (in_array('Weight Loss', $profile['fitness_goals'])) {
            $query->where('calories', '<=', 400); // Lower calorie meals
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get available dietary tags that match health assessment options
     */
    public function getAlignedDietaryTags(): array
    {
        return [
            'Vegetarian',
            'Vegan',
            'Gluten-Free',
            'Dairy-Free',
            'Keto',
            'Low-Carb',
            'High-Protein',
            'Low-Fat',
            'Heart-Healthy',
            'Diabetic-Friendly',
            'Low-Sodium',
            'Anti-Inflammatory',
            'Mediterranean',
            'Paleo',
            'Whole30',
            'Organic',
            'Raw Food'
        ];
    }

    /**
     * Get common allergens that align with health assessments
     */
    public function getCommonAllergens(): array
    {
        return [
            'Nuts',
            'Peanuts',
            'Dairy',
            'Eggs',
            'Fish',
            'Shellfish',
            'Soy',
            'Wheat',
            'Gluten',
            'Sesame'
        ];
    }

    /**
     * Suggest meal plan based on member's health assessment
     */
    public function suggestMealPlan(int $memberId, int $days = 7): array
    {
        $profile = $this->getMemberDietaryProfile($memberId);
        $availableMeals = $this->filterMealsForMember($memberId);
        
        $mealPlan = [];
        $dailyCalorieTarget = $profile['calorie_target'];
        
        for ($day = 1; $day <= $days; $day++) {
            $dayPlan = [
                'day' => $day,
                'meals' => [],
                'total_calories' => 0,
                'total_protein' => 0,
                'total_carbs' => 0,
                'total_fats' => 0
            ];

            // Distribute calories across meals
            $breakfastCalories = $dailyCalorieTarget * 0.25; // 25%
            $lunchCalories = $dailyCalorieTarget * 0.35; // 35%
            $dinnerCalories = $dailyCalorieTarget * 0.30; // 30%
            $snackCalories = $dailyCalorieTarget * 0.10; // 10%

            $mealTargets = [
                'breakfast' => $breakfastCalories,
                'lunch' => $lunchCalories,
                'dinner' => $dinnerCalories,
                'snack' => $snackCalories
            ];

            foreach ($mealTargets as $mealType => $targetCalories) {
                $suitableMeal = $this->findSuitableMeal($availableMeals, $mealType, $targetCalories);
                
                if ($suitableMeal) {
                    $dayPlan['meals'][] = [
                        'type' => $mealType,
                        'meal' => $suitableMeal,
                        'serving_size' => $suitableMeal->serving_size
                    ];
                    
                    $dayPlan['total_calories'] += $suitableMeal->calories;
                    $dayPlan['total_protein'] += $suitableMeal->protein;
                    $dayPlan['total_carbs'] += $suitableMeal->carbs;
                    $dayPlan['total_fats'] += $suitableMeal->fats;
                }
            }

            $mealPlan[] = $dayPlan;
        }

        return $mealPlan;
    }

    /**
     * Calculate daily calorie target based on health assessment
     */
    private function calculateDailyCalorieTarget(HealthAssessment $assessment): int
    {
        // Basic BMR calculation (Mifflin-St Jeor Equation)
        $bmr = 0;
        
        if ($assessment->gender === 'male') {
            $bmr = (10 * $assessment->weight_kg) + (6.25 * $assessment->height_cm) - (5 * $assessment->age) + 5;
        } else {
            $bmr = (10 * $assessment->weight_kg) + (6.25 * $assessment->height_cm) - (5 * $assessment->age) - 161;
        }

        // Activity level multiplier
        $activityMultipliers = [
            'sedentary' => 1.2,
            'lightly_active' => 1.375,
            'moderately_active' => 1.55,
            'very_active' => 1.725,
            'extremely_active' => 1.9
        ];

        $activityLevel = $assessment->activity_level ?? 'moderately_active';
        $tdee = $bmr * ($activityMultipliers[$activityLevel] ?? 1.55);

        // Adjust based on fitness goals
        if (in_array('Weight Loss', $assessment->fitness_goals ?? [])) {
            $tdee *= 0.85; // 15% deficit
        } elseif (in_array('Muscle Gain', $assessment->fitness_goals ?? [])) {
            $tdee *= 1.1; // 10% surplus
        }

        return round($tdee);
    }

    /**
     * Infer preferred meal times from health assessment
     */
    private function inferMealTimes(HealthAssessment $assessment): array
    {
        // Default meal times, could be customized based on activity level or goals
        $defaultTimes = ['breakfast', 'lunch', 'dinner'];
        
        if (in_array('Muscle Gain', $assessment->fitness_goals ?? [])) {
            $defaultTimes[] = 'pre_workout';
            $defaultTimes[] = 'post_workout';
        }
        
        if ($assessment->activity_level === 'very_active' || $assessment->activity_level === 'extremely_active') {
            $defaultTimes[] = 'snack';
        }

        return $defaultTimes;
    }

    /**
     * Find suitable meal for specific meal type and calorie target
     */
    private function findSuitableMeal(Collection $meals, string $mealType, float $targetCalories): ?Meal
    {
        // Filter meals that are suitable for this meal type and calorie range
        $suitableMeals = $meals->filter(function ($meal) use ($mealType, $targetCalories) {
            $mealTimes = $meal->meal_times ?? [];
            
            // Check if meal is appropriate for this time
            $isTimeAppropriate = empty($mealTimes) || in_array($mealType, $mealTimes);
            
            // Check if calories are within 20% of target
            $calorieRange = $targetCalories * 0.2;
            $isCalorieAppropriate = abs($meal->calories - $targetCalories) <= $calorieRange;
            
            return $isTimeAppropriate && $isCalorieAppropriate;
        });

        // Return the closest match by calories
        return $suitableMeals->sortBy(function ($meal) use ($targetCalories) {
            return abs($meal->calories - $targetCalories);
        })->first();
    }

    /**
     * Get default dietary profile when no health assessment exists
     */
    private function getDefaultDietaryProfile(): array
    {
        return [
            'dietary_restrictions' => [],
            'fitness_goals' => ['General Fitness'],
            'allergies' => [],
            'current_weight' => 70,
            'target_weight' => 70,
            'activity_level' => 'moderately_active',
            'medical_conditions' => [],
            'preferred_meal_times' => ['breakfast', 'lunch', 'dinner'],
            'calorie_target' => 2000
        ];
    }

    /**
     * Update meal dietary tags to ensure consistency with health assessments
     */
    public function syncMealTagsWithHealthAssessments(): int
    {
        $alignedTags = $this->getAlignedDietaryTags();
        $updated = 0;

        Meal::chunk(100, function ($meals) use ($alignedTags, &$updated) {
            foreach ($meals as $meal) {
                $currentTags = $meal->dietary_tags ?? [];
                $needsUpdate = false;
                
                // Check if any tags need to be standardized
                foreach ($currentTags as $index => $tag) {
                    $standardizedTag = $this->standardizeDietaryTag($tag, $alignedTags);
                    if ($standardizedTag !== $tag) {
                        $currentTags[$index] = $standardizedTag;
                        $needsUpdate = true;
                    }
                }
                
                if ($needsUpdate) {
                    $meal->update(['dietary_tags' => array_values(array_unique($currentTags))]);
                    $updated++;
                }
            }
        });

        return $updated;
    }

    /**
     * Standardize dietary tag to match health assessment options
     */
    private function standardizeDietaryTag(string $tag, array $alignedTags): string
    {
        $lowercaseTag = strtolower($tag);
        
        foreach ($alignedTags as $alignedTag) {
            if (strtolower($alignedTag) === $lowercaseTag || 
                strpos(strtolower($alignedTag), $lowercaseTag) !== false ||
                strpos($lowercaseTag, strtolower($alignedTag)) !== false) {
                return $alignedTag;
            }
        }
        
        return $tag; // Return original if no match found
    }
}
