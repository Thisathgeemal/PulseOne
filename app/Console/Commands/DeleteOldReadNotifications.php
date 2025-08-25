<?php
namespace App\Console\Commands;

use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteOldReadNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Example usage:
     * php artisan notifications:cleanup
     */
    protected $signature = 'notifications:cleanup';

    /**
     * The console command description.
     */
    protected $description = 'Delete read notifications older than 7 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cutoffDate = Carbon::now()->subDays(3);

        $deleted = Notification::where('is_read', true)
            ->where('created_at', '<', $cutoffDate)
            ->delete();

        $this->info("Deleted {$deleted} old read notifications.");
    }
}
