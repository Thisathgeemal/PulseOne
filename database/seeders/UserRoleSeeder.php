<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{

    public function run(): void
    {
        // Define roles
        $rolesToAssign = [
            'pulseone.app@gmail.com'    => ['Admin'],
            'thisathgeemal38@gmail.com' => ['Admin', 'Trainer', 'Dietitian'],
        ];

        foreach ($rolesToAssign as $email => $roles) {
            $user = User::where('email', $email)->first();

            if (! $user) {
                $this->command->warn("User with email {$email} not found. Run UserSeeder first!");
                continue;
            }

            foreach ($roles as $roleName) {
                $role = DB::table('roles')->where('role_name', $roleName)->first();

                if (! $role) {
                    $this->command->warn("Role '{$roleName}' not found. Run RoleSeeder first!");
                    continue;
                }

                $exists = DB::table('user_roles')
                    ->where('user_id', $user->id)
                    ->where('role_id', $role->role_id)
                    ->exists();

                if (! $exists) {
                    DB::table('user_roles')->insert([
                        'user_id'    => $user->id,
                        'role_id'    => $role->role_id,
                        'is_active'  => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $this->command->info("Assigned '{$roleName}' role to {$email}");
                } else {
                    $this->command->warn("{$email} already has '{$roleName}' role");
                }
            }
        }
    }

}
