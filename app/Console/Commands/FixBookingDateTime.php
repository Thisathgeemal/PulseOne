<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FixBookingDateTime extends Command
{
    protected $signature = 'booking:fix-datetime {id}';
    protected $description = 'Fix a booking start_at datetime';

    public function handle()
    {
        $id = $this->argument('id');
        $booking = Booking::find($id);
        
        if (!$booking) {
            $this->error("Booking {$id} not found");
            return;
        }
        
        // Calculate correct start_at from date + time fields
        $dateStr = $booking->date instanceof \Carbon\Carbon 
            ? $booking->date->format('Y-m-d') 
            : $booking->date;
            
        $localDateTime = Carbon::parse($dateStr . ' ' . $booking->time, config('app.timezone'));
        $utcDateTime = $localDateTime->copy()->utc();
        
        $booking->update([
            'start_at' => $utcDateTime,
            'status' => 'approved'
        ]);
        
        $this->info("Fixed booking {$id}:");
        $this->info("- start_at: {$utcDateTime} (UTC)");
        $this->info("- status: approved");
        $this->info("- Local time: {$localDateTime->format('Y-m-d H:i:s')} Asia/Colombo");
        $this->info("- Config timezone: " . config('app.timezone'));
    }
}
