<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncUserIsActiveStatus extends Command
{
    protected $signature   = 'users:sync-is-active';
    protected $description = 'Sync users is_active based on their user_roles is_active values';

    public function handle()
    {
        // Get all distinct user_ids from user_roles
        $userIds = DB::table('user_roles')->distinct()->pluck('user_id');

        foreach ($userIds as $userId) {
            // Check if user has any active role
            $hasActiveRole = DB::table('user_roles')
                ->where('user_id', $userId)
                ->where('is_active', 1)
                ->exists();

            // Update user table is_active accordingly
            DB::table('users')
                ->where('id', $userId)
                ->update(['is_active' => $hasActiveRole ? 1 : 0]);
        }

        $this->info('User is_active status synced successfully.');
    }
}
