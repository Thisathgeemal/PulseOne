<?php
namespace Database\Factories;

use App\Models\TrainerAvailability;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainerAvailabilityFactory extends Factory
{
    protected $model = TrainerAvailability::class;

    public function definition()
    {
        // Random weekday between 1 (Monday) and 7 (Sunday)
        $weekday = $this->faker->numberBetween(1, 7);

        // Random start hour between 6 AM and 4 PM
        $startHour = $this->faker->numberBetween(6, 16);
        $startTime = sprintf('%02d:00:00', $startHour);

        // Slot duration 1 hour by default
        $endTime = sprintf('%02d:00:00', $startHour + 1);

        return [
            'trainer_id'     => User::factory(), // creates a trainer user automatically if needed
            'weekday'        => $weekday,
            'start_time'     => $startTime,
            'end_time'       => $endTime,
            'slot_minutes'   => 60,
            'buffer_minutes' => 10,
        ];
    }
}
