<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{

    public function run(): void
    {
        $roles = ['Admin', 'Trainer', 'Dietitian', 'Member'];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['role_name' => $role],
                ['created_at' => now()]
            );
        }
    }

}
