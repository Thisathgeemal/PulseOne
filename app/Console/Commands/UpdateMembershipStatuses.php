<?php
namespace App\Console\Commands;

use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateMembershipStatuses extends Command
{
    protected $signature   = 'memberships:update-statuses';
    protected $description = 'Update membership statuses based on start and end dates';

    public function handle()
    {
        $today = Carbon::today();

        // Expire memberships
        Membership::where('status', 'Active')
            ->whereDate('end_date', '<', $today)
            ->update(['status' => 'Expired']);

        // Activate pending memberships
        Membership::where('status', 'Pending')
            ->whereDate('start_date', '<=', $today)
            ->update(['status' => 'Active']);

        $this->info('Membership statuses updated successfully.');
    }
}
