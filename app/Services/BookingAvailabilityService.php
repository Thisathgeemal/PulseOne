<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\TrainerAvailability;
use App\Models\TrainerTimeOff;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Enhanced booking availability service for real-world scenarios
 * Handles multiple trainers, concurrent bookings, time conflicts, and member preferences
 */
class BookingAvailabilityService
{
    /** Minimum minutes from now a slot must be to be selectable. */
    protected int $minLeadMinutes = 60;

    /** Default session duration in minutes */
    protected int $defaultDurationMinutes = 60;

    /** Default buffer after session in minutes */
    protected int $defaultBufferMinutes = 10;

    /**
     * Get availability summary for all trainers on a specific date
     * Used for member booking interface to show which trainers have availability
     */
    public function getTrainerAvailabilitySummary(Carbon $date): Collection
    {
        $trainers = \App\Models\User::whereHas('roles', fn($q) => $q->where('role_name', 'Trainer'))
            ->with('roles')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        return $trainers->map(function ($trainer) use ($date) {
            $slots = $this->getAvailableSlots($trainer->id, $date);
            return [
                'trainer' => $trainer,
                'available_slots_count' => count($slots),
                'earliest_slot' => $slots ? $slots[0] : null,
                'latest_slot' => $slots ? end($slots) : null,
                'has_availability' => count($slots) > 0,
            ];
        });
    }

    /**
     * Get available time slots for a specific trainer on a date
     * Excludes specific booking IDs (useful for showing member's requested time as available)
     */
    public function getAvailableSlots(int $trainerId, Carbon $date, array $excludeBookingIds = []): array
    {
        // Get trainer's working hours for this weekday
        $weekdayIso = $date->isoWeekday(); // 1..7 (Mon..Sun)
        $weekdayZero = $date->dayOfWeek;   // 0..6 (Sun..Sat)

        $workingBlocks = TrainerAvailability::where('trainer_id', $trainerId)
            ->whereIn('weekday', [$weekdayIso, $weekdayZero])
            ->orderBy('start_time')
            ->get();

        if ($workingBlocks->isEmpty()) {
            return [];
        }

        // Get trainer's time-off for this date
        $timeOffs = TrainerTimeOff::where('trainer_id', $trainerId)
            ->whereDate('date', $date->toDateString())
            ->get();

        // Get existing bookings (approved and pending with time)
        $existingBookings = $this->getExistingBookings($trainerId, $date, $excludeBookingIds);

        $availableSlots = collect();

        foreach ($workingBlocks as $block) {
            $slots = $this->generateSlotsForBlock($block, $date, $timeOffs, $existingBookings);
            $availableSlots = $availableSlots->merge($slots);
        }

        return $availableSlots->unique()->sort()->values()->toArray();
    }

    /**
     * Check if a specific time is available for a trainer
     */
    public function isTimeAvailable(int $trainerId, Carbon $dateTime, int $durationMinutes = null, array $excludeBookingIds = []): bool
    {
        $durationMinutes = $durationMinutes ?: $this->defaultDurationMinutes;
        $date = $dateTime->toDateString();
        $time = $dateTime->format('H:i');

        $availableSlots = $this->getAvailableSlots($trainerId, Carbon::parse($date), $excludeBookingIds);
        
        return in_array($time, $availableSlots);
    }

    /**
     * Get booking conflicts for a specific time slot
     * Returns detailed information about why a time slot might not be available
     */
    public function getTimeSlotConflicts(int $trainerId, Carbon $dateTime, int $durationMinutes = null): array
    {
        $durationMinutes = $durationMinutes ?: $this->defaultDurationMinutes;
        $conflicts = [];

        // Check if within working hours
        $weekdayIso = $dateTime->isoWeekday();
        $weekdayZero = $dateTime->dayOfWeek;

        $workingBlock = TrainerAvailability::where('trainer_id', $trainerId)
            ->whereIn('weekday', [$weekdayIso, $weekdayZero])
            ->where('start_time', '<=', $dateTime->format('H:i:s'))
            ->where('end_time', '>', $dateTime->format('H:i:s'))
            ->first();

        if (!$workingBlock) {
            $conflicts[] = [
                'type' => 'outside_working_hours',
                'message' => 'Time is outside trainer\'s working hours'
            ];
        }

        // Check time-off
        $timeOff = TrainerTimeOff::where('trainer_id', $trainerId)
            ->whereDate('date', $dateTime->toDateString())
            ->where(function ($q) use ($dateTime) {
                $q->where(function ($q2) use ($dateTime) {
                    $q2->whereNull('start_time')->whereNull('end_time'); // All day off
                })->orWhere(function ($q2) use ($dateTime) {
                    $q2->where('start_time', '<=', $dateTime->format('H:i:s'))
                       ->where('end_time', '>', $dateTime->format('H:i:s'));
                });
            })
            ->first();

        if ($timeOff) {
            $conflicts[] = [
                'type' => 'time_off',
                'message' => 'Trainer has time-off during this period',
                'details' => $timeOff
            ];
        }

        // Check existing bookings
        $sessionEnd = $dateTime->copy()->addMinutes($durationMinutes);
        $bufferEnd = $sessionEnd->copy()->addMinutes($this->defaultBufferMinutes);

        $conflictingBookings = Booking::where('trainer_id', $trainerId)
            ->where('status', 'approved')
            ->whereDate('date', $dateTime->toDateString())
            ->get()
            ->filter(function ($booking) use ($dateTime, $bufferEnd) {
                $bookingStart = Carbon::parse($booking->date . ' ' . $booking->time);
                $bookingEnd = $bookingStart->copy()->addMinutes($booking->duration_minutes ?: $this->defaultDurationMinutes);
                $bookingBufferEnd = $bookingEnd->copy()->addMinutes($this->defaultBufferMinutes);

                return $dateTime->lt($bookingBufferEnd) && $bufferEnd->gt($bookingStart);
            });

        if ($conflictingBookings->isNotEmpty()) {
            $conflicts[] = [
                'type' => 'booking_conflict',
                'message' => 'Time conflicts with existing booking(s)',
                'details' => $conflictingBookings->map(function ($booking) {
                    return [
                        'booking_id' => $booking->booking_id,
                        'member_name' => $booking->member->first_name . ' ' . $booking->member->last_name,
                        'start_time' => Carbon::parse($booking->date . ' ' . $booking->time)->format('H:i'),
                        'duration' => $booking->duration_minutes ?: $this->defaultDurationMinutes
                    ];
                })
            ];
        }

        return $conflicts;
    }

    /**
     * Generate available slots for a specific working block
     */
    protected function generateSlotsForBlock(TrainerAvailability $block, Carbon $date, Collection $timeOffs, Collection $existingBookings): Collection
    {
        $slots = collect();
        $slotDuration = $block->slot_minutes ?: $this->defaultDurationMinutes;
        $bufferMinutes = $block->buffer_minutes ?: $this->defaultBufferMinutes;

        $blockStart = Carbon::parse($date->toDateString() . ' ' . $block->start_time);
        $blockEnd = Carbon::parse($date->toDateString() . ' ' . $block->end_time);

        // Generate all possible slots in this block
        for ($cursor = $blockStart->copy(); $cursor->lt($blockEnd); $cursor->addMinutes($slotDuration)) {
            $sessionEnd = $cursor->copy()->addMinutes($slotDuration);
            
            // Session must fit completely within the block
            if ($sessionEnd->gt($blockEnd)) {
                break;
            }

            // Must be at least minimum lead time from now
            if ($cursor->lte(now()->addMinutes($this->minLeadMinutes))) {
                continue;
            }

            // Check if this slot conflicts with time-off
            if ($this->conflictsWithTimeOff($cursor, $sessionEnd, $timeOffs)) {
                continue;
            }

            // Check if this slot conflicts with existing bookings (including buffer)
            if ($this->conflictsWithBookings($cursor, $sessionEnd, $bufferMinutes, $existingBookings)) {
                continue;
            }

            $slots->push($cursor->format('H:i'));
        }

        return $slots;
    }

    /**
     * Get existing bookings for a trainer on a specific date
     */
    protected function getExistingBookings(int $trainerId, Carbon $date, array $excludeBookingIds = []): Collection
    {
        return Booking::where('trainer_id', $trainerId)
            ->whereIn('status', ['approved', 'pending'])
            ->whereDate('date', $date->toDateString())
            ->when(!empty($excludeBookingIds), function ($q) use ($excludeBookingIds) {
                $q->whereNotIn('booking_id', $excludeBookingIds);
            })
            ->where(function ($q) {
                // Only include pending bookings that have a specific time chosen
                $q->where('status', 'approved')
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'pending')->whereNotNull('time');
                  });
            })
            ->get();
    }

    /**
     * Check if a time slot conflicts with trainer time-off
     */
    protected function conflictsWithTimeOff(Carbon $slotStart, Carbon $slotEnd, Collection $timeOffs): bool
    {
        return $timeOffs->contains(function ($timeOff) use ($slotStart, $slotEnd) {
            if (!$timeOff->start_time && !$timeOff->end_time) {
                // All day time-off
                return true;
            }

            $offStart = $timeOff->start_time 
                ? Carbon::parse($slotStart->toDateString() . ' ' . $timeOff->start_time)
                : $slotStart->copy()->startOfDay();
            
            $offEnd = $timeOff->end_time
                ? Carbon::parse($slotStart->toDateString() . ' ' . $timeOff->end_time)
                : $slotStart->copy()->endOfDay();

            return $slotStart->lt($offEnd) && $slotEnd->gt($offStart);
        });
    }

    /**
     * Check if a time slot conflicts with existing bookings
     */
    protected function conflictsWithBookings(Carbon $slotStart, Carbon $slotEnd, int $bufferMinutes, Collection $existingBookings): bool
    {
        $slotEndWithBuffer = $slotEnd->copy()->addMinutes($bufferMinutes);

        return $existingBookings->contains(function ($booking) use ($slotStart, $slotEndWithBuffer) {
            $bookingStart = Carbon::parse($booking->date . ' ' . $booking->time);
            $bookingDuration = $booking->duration_minutes ?: $this->defaultDurationMinutes;
            $bookingEnd = $bookingStart->copy()->addMinutes($bookingDuration + $this->defaultBufferMinutes);

            return $slotStart->lt($bookingEnd) && $slotEndWithBuffer->gt($bookingStart);
        });
    }

    /**
     * Get suggested alternative times when member's preferred time is not available
     */
    public function getSuggestedAlternatives(int $trainerId, Carbon $preferredDateTime, int $maxSuggestions = 5): array
    {
        $date = $preferredDateTime->copy()->startOfDay();
        $preferredTime = $preferredDateTime->format('H:i');
        
        $allSlots = $this->getAvailableSlots($trainerId, $date);
        
        if (empty($allSlots)) {
            return [];
        }

        // Sort by proximity to preferred time
        $suggestions = collect($allSlots)->map(function ($slot) use ($preferredTime) {
            $slotTime = Carbon::createFromFormat('H:i', $slot);
            $preferredTimeCarbon = Carbon::createFromFormat('H:i', $preferredTime);
            $diff = abs($slotTime->diffInMinutes($preferredTimeCarbon));
            
            return [
                'time' => $slot,
                'formatted_time' => $slotTime->format('h:i A'),
                'minutes_difference' => $diff
            ];
        })->sortBy('minutes_difference')->take($maxSuggestions);

        return $suggestions->values()->toArray();
    }
}
