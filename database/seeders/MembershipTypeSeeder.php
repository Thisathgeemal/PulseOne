<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MembershipTypeSeeder extends Seeder
{
    public function run(): void
    {
        $membershipTypes = [
            [
                'type_name'  => 'Day',
                'duration'   => 1,
                'amount'     => 500.00,
                'discount'   => 0.00,
                'price'      => 500.00,
                'created_at' => now(),
            ],
            [
                'type_name'  => 'Month',
                'duration'   => 30,
                'amount'     => 3000.00,
                'discount'   => 0.00,
                'price'      => 3000.00,
                'created_at' => now(),
            ],
            [
                'type_name'  => 'Three Months',
                'duration'   => 90,
                'amount'     => 9000.00,
                'discount'   => 8.33,
                'price'      => 8250.00,
                'created_at' => now(),
            ],
            [
                'type_name'  => 'Six Months',
                'duration'   => 180,
                'amount'     => 18000.00,
                'discount'   => 16.67,
                'price'      => 15000.00,
                'created_at' => now(),
            ],
            [
                'type_name'  => 'Annual',
                'duration'   => 365,
                'amount'     => 30000.00,
                'discount'   => 20.00,
                'price'      => 24000.00,
                'created_at' => now(),
            ],
        ];

        DB::table('membership_types')->insertOrIgnore($membershipTypes);
    }
}
