<?php
namespace Database\Factories;

use App\Models\Request;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkoutPlanFactory extends Factory
{
    protected $model = WorkoutPlan::class;

    public function definition(): array
    {
        return [
            'trainer_id' => User::factory(), // creates a trainer
            'member_id'  => User::factory(), // creates a member
            'request_id' => Request::factory(),
            'plan_name'  => $this->faker->sentence(3),
            'start_date' => $this->faker->date(),
            'end_date'   => $this->faker->date(),
            'status'     => 'Pending',
        ];
    }
}
