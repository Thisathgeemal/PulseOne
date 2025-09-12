<?php
namespace Database\Factories;

use App\Models\HealthAssessment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class HealthAssessmentFactory extends Factory
{
    protected $model = HealthAssessment::class;

    public function definition(): array
    {
        return [
            'user_id'         => User::factory(),                       // creates a user automatically if not provided
            'weight'          => $this->faker->numberBetween(50, 120),  // kg
            'height'          => $this->faker->numberBetween(150, 200), // cm
            'bmi'             => $this->faker->randomFloat(1, 18, 35),  // BMI between 18 and 35
            'blood_pressure'  => $this->faker->numberBetween(100, 140) . '/' . $this->faker->numberBetween(60, 90),
            'heart_rate'      => $this->faker->numberBetween(60, 100),
            'assessment_date' => $this->faker->date(),
        ];
    }
}
