<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{

    public function run(): void
    {
        $role = DB::table('roles')->where('role_name', 'Admin')->first();
        if (! $role) {
            $this->command->error('Admin role not found. Run RoleSeeder first!');
            return;
        }

        $user = User::where('email', 'pulseone.app@gmail.com')->first();
        if (! $user) {
            $this->command->error('Admin user not found. Run UserSeeder first!');
            return;
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
        }
    }
}
