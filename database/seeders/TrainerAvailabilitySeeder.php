<?php

namespace Database\Seeders;

use App\Models\TrainerAvailability;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrainerAvailabilitySeeder extends Seeder
{
    public function run(): void
    {
        // Trainers = users with role_id = 2 in user_roles
        $trainerIds = DB::table('user_roles')
            ->where('role_id', 2)
            ->pluck('user_id')
            ->toArray();

        if (empty($trainerIds)) return;

        // Real-world hours (1=Mon ... 7=Sun)
        $hours = [
            1 => ['06:00:00','21:00:00'], // Mon
            2 => ['06:00:00','21:00:00'], // Tue
            3 => ['06:00:00','21:00:00'], // Wed
            4 => ['06:00:00','21:00:00'], // Thu
            5 => ['06:00:00','21:00:00'], // Fri
            6 => ['08:00:00','18:00:00'], // Sat
            7 => ['08:00:00','16:00:00'], // Sun
        ];

        foreach ($trainerIds as $id) {
            foreach ($hours as $weekday => [$start, $end]) {
                TrainerAvailability::updateOrCreate(
                    [
                        'trainer_id' => $id,
                        'weekday'    => $weekday,
                        'start_time' => $start,
                        'end_time'   => $end,
                    ],
                    [
                        'slot_minutes'   => 60, // change to 30 if you want half-hour sessions
                        'buffer_minutes' => 10,
                    ]
                );
            }
        }
    }
}
