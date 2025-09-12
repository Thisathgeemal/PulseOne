<?php
namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition()
    {
        return [
            'user_id'        => User::factory(),
            'check_in_time'  => $this->faker->dateTimeBetween('-1 week', 'now'),
            'check_out_time' => $this->faker->optional()->dateTimeBetween('-1 week', 'now'),
            'qr_code'        => $this->faker->optional()->uuid,
            'created_at'     => now(),
            'updated_at'     => now(),
        ];
    }
}
