<?php
namespace Database\Factories;

use App\Models\Request as WorkoutRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestFactory extends Factory
{
    protected $model = WorkoutRequest::class;

    public function definition(): array
    {
        $types     = ['Workout', 'Diet'];
        $statuses  = ['Pending', 'Completed', 'Approved', 'Rejected'];
        $planTypes = ['Basic', 'Intermediate', 'Advanced'];
        $days      = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

        return [
            'member_id'            => User::factory(),
            'trainer_id'           => User::factory(),
            'dietitian_id'         => null,
            'plan_type'            => $this->faker->randomElement($planTypes),
            'preferred_start_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'available_days'       => implode(',', $this->faker->randomElements($days, 3)),
            'goal'                 => $this->faker->sentence(),
            'current_weight'       => $this->faker->randomFloat(2, 50, 100),
            'target_weight'        => $this->faker->randomFloat(2, 50, 100),
            'timeframe'            => $this->faker->numberBetween(4, 12) . ' weeks',
            'meals_per_day'        => $this->faker->numberBetween(3, 6),
            'description'          => $this->faker->sentence(),
            'type'                 => $this->faker->randomElement($types),
            'status'               => $this->faker->randomElement($statuses),
        ];
    }
}
