<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BackfillBookingStartAt extends Command
{
    protected $signature   = 'bookings:backfill-start-at {--dry-run} {--chunk=500}';
    protected $description = 'Backfill bookings.start_at from legacy date/time (stores UTC)';

    public function handle(): int
    {
        $dry   = (bool) $this->option('dry-run');
        $chunk = (int) $this->option('chunk');

        $total = 0;

        Booking::whereNull('start_at')
            ->whereNotNull('date')
            ->orderBy('booking_id')
            ->chunkById($chunk, function ($rows) use (&$total, $dry) {
                foreach ($rows as $b) {
                    // DATE (required for backfill)
                    $dateStr = $b->date ? Carbon::parse($b->date)->toDateString() : null;
                    if (!$dateStr) continue;

                    // TIME may be NULL or HH:MM or HH:MM:SS -> normalize
                    $timeStr = $b->time ? (strlen($b->time) === 5 ? $b->time.':00' : $b->time) : '00:00:00';

                    // Parse in app TZ, then store as UTC
                    $local   = Carbon::parse("$dateStr $timeStr", config('app.timezone'));
                    $startAt = $local->clone()->utc();

                    if (!$dry) {
                        $b->start_at = $startAt;
                        $b->save();
                    }

                    $this->line(($dry ? '[DRY] ' : '')."booking #{$b->booking_id} => {$startAt->toDateTimeString()} UTC");
                    $total++;
                }
            });

        $this->info(($dry ? '[DRY RUN] ' : '')."Done. Processed {$total} bookings.");
        return self::SUCCESS;
    }
}
