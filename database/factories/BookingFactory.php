<?php
namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition()
    {
        // Create random trainer and member if not provided
        $trainer = User::factory()->create();
        $member  = User::factory()->create();

        $date = Carbon::now()->addDays(rand(1, 7));
        $time = $this->faker->time('H:i:s');

        return [
            'trainer_id'       => $trainer->id,
            'member_id'        => $member->id,
            'date'             => $date->toDateString(),
            'time'             => $time,
            'start_at'         => Carbon::parse($date->toDateString() . ' ' . $time)->utc(),
            'duration_minutes' => 60,
            'buffer_before'    => 0,
            'buffer_after'     => 10,
            'description'      => $this->faker->sentence,
            'status'           => 'pending',
        ];
    }

    // State for approved bookings
    public function approved()
    {
        return $this->state(function () {
            return [
                'status' => 'approved',
            ];
        });
    }

    // State for completed bookings
    public function completed()
    {
        return $this->state(function () {
            return [
                'status' => 'completed',
            ];
        });
    }

    // State for declined bookings
    public function declined()
    {
        return $this->state(function () {
            return [
                'status'         => 'declined',
                'decline_reason' => 'Not available',
            ];
        });
    }

    // State for cancelled bookings
    public function cancelled()
    {
        return $this->state(function () {
            return [
                'status' => 'cancelled',
            ];
        });
    }
}
