<?php
namespace App\Services;

use App\Models\Booking;
use App\Models\TrainerAvailability;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SlotService
{
    /** Minimum minutes from now a slot must be to be selectable. */
    protected int $minLeadMinutes = 60;

    /** If true, also block pending bookings that already selected a time. */
    protected bool $holdPendingWithTime = true;

    /**
     * Return available start times (HH:MM) for a trainer on a given date.
     * Applies working hours, slot length, buffer, existing bookings.
     */
    public function getAvailableSlots(int $trainerId, Carbon $date, array $excludeBookingIds = []): array
    {
                                            // Accept both weekday encodings (defensive): 1..7 (Mon..Sun) and 0..6 (Sun..Sat)
        $weekdayIso  = $date->isoWeekday(); // 1..7
        $weekdayZero = $date->dayOfWeek;    // 0..6

        $blocks = TrainerAvailability::where('trainer_id', $trainerId)
            ->whereIn('weekday', [$weekdayIso, $weekdayZero])
            ->get();

        // If the trainer has no explicit availability set (common on new/seedless DBs),
        // provide a safe default working block so members can still pick times.
        // This prevents the UI from showing "no times" when the DB simply lacks rows.
        if ($blocks->isEmpty()) {
            // Default: 08:00 - 17:00, 60 minute slots, 10 minute buffer
            $default = (object) [
                'start_time'   => '08:00:00',
                'end_time'     => '17:00:00',
                'slot_minutes' => 60,
                'buffer_minutes' => 10,
            ];

            $blocks = collect([$default]);
        }

        // Approved bookings for the date
        $approved = Booking::where('trainer_id', $trainerId)
            ->where('status', 'approved')
            ->whereDate('date', $date->toDateString())
            ->when(! empty($excludeBookingIds), function ($q) use ($excludeBookingIds) {
                $q->whereNotIn('booking_id', $excludeBookingIds);
            })
            ->get(['date', 'time', 'duration_minutes']);

        // Optionally also block pending bookings that already selected a time
        $pendingTimed = collect();
        if ($this->holdPendingWithTime) {
            $pendingTimed = Booking::where('trainer_id', $trainerId)
                ->where('status', 'pending')
                ->whereNotNull('time')
                ->whereDate('date', $date->toDateString())
                ->when(! empty($excludeBookingIds), function ($q) use ($excludeBookingIds) {
                    $q->whereNotIn('booking_id', $excludeBookingIds);
                })
                ->get(['date', 'time', 'duration_minutes']);
        }

        $now   = now();
        $slots = collect();

        foreach ($blocks as $b) {
            $slotMin   = (int) ($b->slot_minutes ?? 60);
            $bufferMin = (int) ($b->buffer_minutes ?? 10);

            $blockStart = Carbon::parse($date->toDateString() . ' ' . $b->start_time)->seconds(0);
            $blockEnd   = Carbon::parse($date->toDateString() . ' ' . $b->end_time)->seconds(0);

            if ($blockEnd->lte($blockStart)) {
                continue; // Skip bad blocks
            }

            // Build excluded windows (bookings + buffer)
            $blockedWindows = $this->buildBlockedWindows(
                $date, $approved, $pendingTimed, $slotMin, $bufferMin
            );

            // Walk the block in slot-sized steps
            for ($cursor = $blockStart->copy(); $cursor->lt($blockEnd); $cursor->addMinutes($slotMin)) {
                $sessionEnd = $cursor->copy()->addMinutes($slotMin);
                if ($sessionEnd->gt($blockEnd)) {
                    break; // session must fit fully inside block
                }

                // Lead time: avoid too-soon times
                if ($cursor->lte($now->copy()->addMinutes($this->minLeadMinutes))) {
                    continue;
                }

                // Reject if intersects any blocked window
                $overlaps = $blockedWindows->first(function ($win) use ($cursor, $slotMin, $bufferMin) {
                    $start = $cursor->copy();
                    $end   = $cursor->copy()->addMinutes($slotMin + $bufferMin);
                    return $start->lt($win['end']) && $end->gt($win['start']);
                });

                if ($overlaps) {
                    continue;
                }

                $slots->push($cursor->format('H:i'));
            }
        }

        return $slots->unique()->values()->all();
    }

    /**
     * Build windows to exclude: existing bookings (+buffer).
     * Also (optionally) blocks pending bookings that already have a chosen time.
     */
    protected function buildBlockedWindows(
        Carbon $date,
        Collection $approved,
        Collection $pendingTimed,
        int $slotMin,
        int $bufferMin
    ): Collection {
        $blocked = collect();

        // Helper to push booking windows
        $pushBookingWindow = function ($bk) use ($blocked, $slotMin, $bufferMin) {
            if (empty($bk->time)) {
                return;
            }

            $bkDateStr = $bk->date instanceof Carbon
            ? $bk->date->toDateString()
            : Carbon::parse($bk->date)->toDateString();

            $bkTimeStr = strlen($bk->time) === 5 ? $bk->time . ':00' : $bk->time;

            $start = Carbon::parse($bkDateStr . ' ' . $bkTimeStr)->seconds(0);
            $end   = $start->copy()->addMinutes((int) ($bk->duration_minutes ?? $slotMin) + $bufferMin);

            if ($end->gt($start)) {
                $blocked->push(['start' => $start, 'end' => $end]);
            }
        };

        foreach ($approved as $bk) {
            $pushBookingWindow($bk);
        }

        foreach ($pendingTimed as $bk) {
            $pushBookingWindow($bk);
        }

        return $blocked;
    }

    /**
     * Normalize time string to HH:MM format.
     */
    public function normalizeTime(string $timeStr): ?string
    {
        $timeStr = trim($timeStr);

        if (empty($timeStr)) {
            return null;
        }

        try {
            $carbon = Carbon::parse($timeStr);
            return $carbon->format('H:i');
        } catch (\Exception $e) {
            if (preg_match('/(\d{1,2}):(\d{2})/', $timeStr, $matches)) {
                $hour   = (int) $matches[1];
                $minute = (int) $matches[2];

                if ($hour >= 0 && $hour <= 23 && $minute >= 0 && $minute <= 59) {
                    return sprintf('%02d:%02d', $hour, $minute);
                }
            }
            return null;
        }
    }
}
