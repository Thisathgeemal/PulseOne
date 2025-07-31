<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    public function run(): void
    {
        if (User::count() === 0) {
            User::create([
                'first_name'    => 'PulseOne',
                'last_name'     => 'Admin',
                'email'         => 'pulseone.app@gmail.com',
                'password'      => Hash::make('PulseOne'),
                'mobile_number' => '0812345678',
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            User::create([
                'first_name'    => 'Thisath',
                'last_name'     => 'Geemal',
                'email'         => 'thisathgeemal38@gmail.com',
                'password'      => Hash::make('123456789'),
                'mobile_number' => '0701733646',
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
}
