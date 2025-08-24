<?php
namespace App\Console\Commands;

use App\Models\Membership;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendMembershipExpiryNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'memberships:send-expiry-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications for memberships about to expire';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today            = Carbon::today();
        $daysBeforeExpiry = [5, 1];

        foreach ($daysBeforeExpiry as $days) {
            $targetDate = $today->copy()->addDays($days);

            $memberships = Membership::whereDate('end_date', $targetDate)->get();

            foreach ($memberships as $membership) {
                $exists = Notification::where('user_id', $membership->user_id)
                    ->where('title', 'Membership Expiry Reminder')
                    ->whereDate('created_at', $today)
                    ->exists();

                if (! $exists) {
                    Notification::create([
                        'user_id' => $membership->user_id,
                        'title'   => 'Membership Expiry Reminder',
                        'message' => "Your membership will expire in {$days} day(s). Please renew to continue using our services.",
                        'type'    => 'Membership',
                        'is_read' => false,
                    ]);
                }
            }
        }

        $this->info('Notifications inserted successfully (duplicates avoided).');
    }

}
