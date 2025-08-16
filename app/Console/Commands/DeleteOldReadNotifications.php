<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use Carbon\Carbon;

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
        $cutoffDate = Carbon::now()->subDays(7);

        $deleted = Notification::where('is_read', true)   
            ->where('created_at', '<', $cutoffDate)
            ->delete();

        $this->info("Deleted {$deleted} old read notifications.");
    }
}
