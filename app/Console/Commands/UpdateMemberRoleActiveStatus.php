<?php
namespace App\Console\Commands;

use App\Models\Membership;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateMemberRoleActiveStatus extends Command
{
    protected $signature   = 'members:update-role-active-status';
    protected $description = 'Set user_roles.is_active based on whether a Member role user has an active membership';

    public function handle()
    {
        $today = Carbon::today();

        // Get all users with "Member" role using join
        $users = User::select('users.id')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->join('roles', 'user_roles.role_id', '=', 'roles.role_id')
            ->where('roles.role_name', 'Member')
            ->get();

        foreach ($users as $user) {
            // Check if they have any active membership
            $hasActiveMembership = Membership::where('user_id', $user->id)
                ->where('status', 'Active')
                ->whereDate('end_date', '>=', $today)
                ->exists();

            // Update the pivot table
            DB::table('user_roles')
                ->where('user_id', $user->id)
                ->update(['is_active' => $hasActiveMembership ? 1 : 0]);
        }

        $this->info('Member role active status updated successfully.');
    }
}
