<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\TrainerAvailability;
use App\Models\TrainerTimeOff;
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
     * Applies working hours, slot length, buffer, existing bookings, and time-off.
     */
    public function getAvailableSlots(int $trainerId, Carbon $date, array $excludeBookingIds = []): array
    {
        // Accept both weekday encodings (defensive): 1..7 (Mon..Sun) and 0..6 (Sun..Sat)
        $weekdayIso  = $date->isoWeekday(); // 1..7
        $weekdayZero = $date->dayOfWeek;    // 0..6

        $blocks = TrainerAvailability::where('trainer_id', $trainerId)
            ->whereIn('weekday', [$weekdayIso, $weekdayZero])
            ->get();

        if ($blocks->isEmpty()) {
            return [];
        }

        // Time-off for the date
        $offs = TrainerTimeOff::where('trainer_id', $trainerId)
            ->whereDate('date', $date->toDateString())
            ->get();

        // Approved bookings for the date
        $approved = Booking::where('trainer_id', $trainerId)
            ->where('status', 'approved')
            ->whereDate('date', $date->toDateString())
            ->when(!empty($excludeBookingIds), function($q) use ($excludeBookingIds) {
                $q->whereNotIn('booking_id', $excludeBookingIds);
            })
            ->get(['date','time','duration_minutes']);

        // Optionally also block pending bookings that already selected a time
        $pendingTimed = collect();
        if ($this->holdPendingWithTime) {
            $pendingTimed = Booking::where('trainer_id', $trainerId)
                ->where('status', 'pending')
                ->whereNotNull('time')
                ->whereDate('date', $date->toDateString())
                ->when(!empty($excludeBookingIds), function($q) use ($excludeBookingIds) {
                    $q->whereNotIn('booking_id', $excludeBookingIds);
                })
                ->get(['date','time','duration_minutes']);
        }

        $now = now();
        $slots = collect();

        foreach ($blocks as $b) {
            $slotMin   = (int)($b->slot_minutes ?? 60);
            $bufferMin = (int)($b->buffer_minutes ?? 10);

            $blockStart = Carbon::parse($date->toDateString().' '.$b->start_time)->seconds(0);
            $blockEnd   = Carbon::parse($date->toDateString().' '.$b->end_time)->seconds(0);

            if ($blockEnd->lte($blockStart)) {
                // Skip bad blocks
                continue;
            }

            // Build excluded windows (time-off + bookings(+buffer))
            $blockedWindows = $this->buildBlockedWindows(
                $offs, $date, $approved, $pendingTimed, $slotMin, $bufferMin
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

                // Reject if intersects any blocked window (existing bookings + buffer, or time-off)
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
     * Build windows to exclude: trainer time-off + existing bookings (+buffer).
     * Also (optionally) blocks pending bookings that already have a chosen time.
     *
     * @param  \Illuminate\Support\Collection  $offs
     * @param  \Illuminate\Support\Collection  $approved
     * @param  \Illuminate\Support\Collection  $pendingTimed
     */
   protected function buildBlockedWindows(
    Collection $offs,
    Carbon $date,
    Collection $approved,
    Collection $pendingTimed,
    int $slotMin,
    int $bufferMin
): Collection {
    $blocked = collect();

    // Time-off (unchanged)
    foreach ($offs as $off) {
        $start = $off->start_time
            ? Carbon::parse($date->toDateString().' '.$off->start_time)->seconds(0)
            : $date->copy()->startOfDay();
        $end = $off->end_time
            ? Carbon::parse($date->toDateString().' '.$off->end_time)->seconds(0)
            : $date->copy()->endOfDay();
        if ($end->gt($start)) {
            $blocked->push(['start' => $start, 'end' => $end]);
        }
    }

    // Helper to push booking windows (approved or pending-with-time)
    $pushBookingWindow = function ($bk) use ($blocked, $slotMin, $bufferMin) {
        if (empty($bk->time)) return;

        $bkDateStr = $bk->date instanceof Carbon
            ? $bk->date->toDateString()
            : Carbon::parse($bk->date)->toDateString();

        $bkTimeStr = strlen($bk->time) === 5 ? $bk->time . ':00' : $bk->time;

        $start = Carbon::parse($bkDateStr.' '.$bkTimeStr)->seconds(0);
        $end   = $start->copy()->addMinutes((int)($bk->duration_minutes ?? $slotMin) + $bufferMin);

        if ($end->gt($start)) {
            $blocked->push(['start' => $start, 'end' => $end]);
        }
    };

    foreach ($approved as $bk)     $pushBookingWindow($bk);
    foreach ($pendingTimed as $bk) $pushBookingWindow($bk);

    return $blocked;
}

    /**
     * Normalize time string to HH:MM format.
     * Accepts various formats: "14:30", "2:30 PM", "14:30:00", etc.
     */
    public function normalizeTime(string $timeStr): ?string
    {
        $timeStr = trim($timeStr);
        
        if (empty($timeStr)) {
            return null;
        }

        // Try to parse the time string
        try {
            $carbon = Carbon::parse($timeStr);
            return $carbon->format('H:i');
        } catch (\Exception $e) {
            // If parsing fails, try to extract HH:MM pattern
            if (preg_match('/(\d{1,2}):(\d{2})/', $timeStr, $matches)) {
                $hour = (int) $matches[1];
                $minute = (int) $matches[2];
                
                if ($hour >= 0 && $hour <= 23 && $minute >= 0 && $minute <= 59) {
                    return sprintf('%02d:%02d', $hour, $minute);
                }
            }
            
            return null;
        }
    }
}
