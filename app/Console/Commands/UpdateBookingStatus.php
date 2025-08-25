<?php
namespace App\Console\Commands;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateBookingStatus extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'bookings:update-status';

    /**
     * The console command description.
     */
    protected $description = 'Update booking status: pending → expired, approved → completed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now            = Carbon::now('Asia/Colombo'); // adjust your timezone
        $expiredCount   = 0;
        $completedCount = 0;

        /**
         * Handle PENDING → EXPIRED
         */
        $pendingBookings = Booking::where('status', 'pending')->get();

        foreach ($pendingBookings as $booking) {
            // Ensure date is Y-m-d, and time is properly formatted
            $date = $booking->date instanceof Carbon
            ? $booking->date->format('Y-m-d')
            : date('Y-m-d', strtotime($booking->date));

            $time = strlen($booking->time) === 5
            ? $booking->time . ':00' // convert "10:00" → "10:00:00"
            : $booking->time;

            $sessionDateTime = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $date . ' ' . $time,
                'Asia/Colombo'
            );

            if ($sessionDateTime->isPast()) {
                $booking->update(['status' => 'expired']);
                $expiredCount++;
            }
        }

        /**
         * Handle APPROVED → COMPLETED
         */
        $approvedBookings = Booking::where('status', 'approved')->get();

        foreach ($approvedBookings as $booking) {
            $endTime = $booking->start_at->copy()->addMinutes($booking->duration_minutes);

            if ($endTime->isPast()) {
                $booking->update(['status' => 'completed']);
                $completedCount++;
            }
        }

        $this->info("Updated {$expiredCount} expired bookings and {$completedCount} completed bookings.");

        return 0;
    }
}
