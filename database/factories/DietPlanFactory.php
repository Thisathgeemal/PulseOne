<?php
namespace Database\Factories;

use App\Models\DietPlan;
use App\Models\Request as DietRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class DietPlanFactory extends Factory
{
    protected $model = DietPlan::class;

    public function definition(): array
    {
        $startDate = Carbon::now()->addDay();
        $endDate   = (clone $startDate)->addMonth();

        return [
            'dietitian_id'           => User::factory(), // assume Dietitian role
            'member_id'              => User::factory(), // assume Member role
            'request_id'             => DietRequest::factory(),
            'plan_name'              => $this->faker->sentence(3),
            'plan_description'       => $this->faker->paragraph(),
            'daily_calories_target'  => $this->faker->numberBetween(1500, 3000),
            'daily_protein_target'   => $this->faker->numberBetween(50, 200),
            'daily_carbs_target'     => $this->faker->numberBetween(100, 400),
            'daily_fats_target'      => $this->faker->numberBetween(20, 100),
            'meals_per_day'          => 3,
            'dietitian_instructions' => $this->faker->sentence(),
            'weekly_schedule'        => json_encode([
                'breakfast' => '08:00',
                'lunch'     => '12:00',
                'dinner'    => '19:00',
            ]),
            'start_date'             => $startDate->toDateString(),
            'end_date'               => $endDate->toDateString(),
            'status'                 => 'Pending',
        ];
    }
}
