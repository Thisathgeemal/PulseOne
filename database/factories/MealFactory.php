<?php
namespace Database\Factories;

use App\Models\Meal;
use Illuminate\Database\Eloquent\Factories\Factory;

class MealFactory extends Factory
{
    protected $model = Meal::class;

    public function definition()
    {
        return [
            'meal_name'               => $this->faker->words(3, true),
            'description'             => $this->faker->sentence(),
            'calories_per_serving'    => $this->faker->numberBetween(100, 800),
            'protein_grams'           => $this->faker->numberBetween(5, 50),
            'carbs_grams'             => $this->faker->numberBetween(10, 100),
            'fats_grams'              => $this->faker->numberBetween(5, 50),
            'fiber_grams'             => $this->faker->numberBetween(0, 20),
            'sugar_grams'             => $this->faker->numberBetween(0, 30),
            'sodium_mg'               => $this->faker->numberBetween(0, 2000),
            'serving_size'            => '1',
            'serving_unit'            => 'serving',
            'ingredients'             => ['ingredient1', 'ingredient2'],
            'preparation_method'      => $this->faker->paragraph(),
            'prep_time_minutes'       => $this->faker->numberBetween(5, 60),
            'cook_time_minutes'       => $this->faker->numberBetween(5, 60),
            'total_time_minutes'      => $this->faker->numberBetween(10, 120),
            'difficulty_level'        => 'easy',
            'dietary_tags'            => [
                'category'   => 'lunch',
                'meal_times' => ['lunch'],
                'is_public'  => true,
            ],
            'is_active'               => true,
            'created_by_dietitian_id' => 1, // or link to a User factory
        ];
    }
}
