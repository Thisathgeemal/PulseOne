<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateBookingStatuses extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'bookings:update-statuses';

    /**
     * The description of the console command.
     */
    protected $description = 'Update booking statuses automatically (completed → auto-remove after 1 day)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $yesterday = $now->copy()->subDay();
        
        // 1. Mark approved sessions as completed when they're in the past
        $toComplete = Booking::where('status', 'approved')
            ->where(function ($query) use ($now) {
                $query->where('start_at', '<', $now)
                      ->orWhere(function ($q) use ($now) {
                          // Fallback for legacy bookings without start_at
                          // We need to check both date AND time
                          $q->whereNull('start_at')
                            ->where(function ($x) use ($now) {
                                $x->whereDate('date', '<', $now->toDateString())
                                  ->orWhere(function ($y) use ($now) {
                                      // Same date but time has passed
                                      $y->whereDate('date', '=', $now->toDateString())
                                        ->whereRaw('TIME(time) < ?', [$now->format('H:i:s')]);
                                  });
                            });
                      });
            })
            ->get();

        foreach ($toComplete as $booking) {
            $booking->update(['status' => 'completed']);
            $this->info("Booking {$booking->booking_id} marked as completed");
        }

        // 2. Auto-remove completed sessions older than 1 day
        $toRemove = Booking::where('status', 'completed')
            ->where(function ($query) use ($yesterday) {
                $query->where('start_at', '<', $yesterday)
                      ->orWhere(function ($q) use ($yesterday) {
                          // Fallback for legacy bookings
                          $q->whereNull('start_at')
                            ->where(function ($x) use ($yesterday) {
                                $x->whereDate('date', '<', $yesterday->toDateString())
                                  ->orWhere(function ($y) use ($yesterday) {
                                      // Same date but time has passed by more than 1 day
                                      $y->whereDate('date', '=', $yesterday->toDateString())
                                        ->whereRaw('TIME(time) < ?', [$yesterday->format('H:i:s')]);
                                  });
                            });
                      });
            })
            ->get();

        foreach ($toRemove as $booking) {
            $this->info("Auto-removing completed booking {$booking->booking_id} (older than 1 day)");
            $booking->delete();
        }

        // 3. Expire pending requests older than 7 days
        $weekAgo = $now->copy()->subWeek();
        $toExpire = Booking::where('status', 'pending')
            ->where('created_at', '<', $weekAgo)
            ->get();

        foreach ($toExpire as $booking) {
            $booking->update(['status' => 'expired']);
            $this->info("Booking {$booking->booking_id} marked as expired (pending > 7 days)");
        }

        $this->info("Booking status update completed: {$toComplete->count()} completed, {$toRemove->count()} removed, {$toExpire->count()} expired");
        
        Log::info('Automatic booking status update completed', [
            'completed_count' => $toComplete->count(),
            'removed_count' => $toRemove->count(),
            'expired_count' => $toExpire->count(),
        ]);
    }
}
