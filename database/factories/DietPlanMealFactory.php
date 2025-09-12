<?php
namespace Database\Factories;

use App\Models\DietPlan;
use App\Models\Meal;
use Illuminate\Database\Eloquent\Factories\Factory;

class DietPlanMealFactory extends Factory
{
    protected $model = \App\Models\DietPlanMeal::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return [
            'dietplan_id' => DietPlan::factory(), // creates a new diet plan if none exists
            'meal_id'     => Meal::factory(),     // creates a new meal if none exists
            'day'         => $this->faker->randomElement($days),
            'time'        => $this->faker->time('H:i:s'),
            'quantity'    => $this->faker->randomFloat(2, 50, 500), // 50-500 grams
            'calories'    => $this->faker->numberBetween(100, 800),
            'carbs'       => $this->faker->numberBetween(10, 100),
            'protein'     => $this->faker->numberBetween(10, 100),
            'fat'         => $this->faker->numberBetween(5, 50),
            'notes'       => $this->faker->optional()->sentence(),
        ];
    }
}
