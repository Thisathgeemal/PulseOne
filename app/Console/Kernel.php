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
        // Run membership status updates daily at 05:00
        $schedule->command('memberships:update-statuses')->dailyAt('05:00');

        // Run member role is_active updates daily at 05:05
        $schedule->command('members:update-role-active-status')->dailyAt('05:05');

        // Run user is_active status sync daily at 05:10
        $schedule->command('users:sync-is-active')->dailyAt('05:10');
    }
}
