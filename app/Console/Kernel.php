<?php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // Run membership status updates daily
        $schedule->command('memberships:update-statuses')->daily();

        // Run member role is_active updates daily
        $schedule->command('members:update-role-active-status')->daily();

        // Run user is_active status sync daily
        $schedule->command('users:sync-is-active')->daily();

        // Delete old read notifications daily
        $schedule->command('notifications:cleanup')->daily();
    }
}
