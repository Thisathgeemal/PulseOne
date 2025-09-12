<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Notification;
use App\Models\TrainerAvailability;
use App\Services\SlotService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrainerBookingController extends Controller
{
    // Pending requests list for a trainer.
    public function index()
    {
        $trainerId = Auth::id();

        $requests = Booking::with('member')
            ->where('trainer_id', $trainerId)
            ->where('status', 'pending')
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        // Build per-request meta: available slots + member preferred time (if any)
        $slots   = app(SlotService::class);
        $reqMeta = [];
        foreach ($requests as $r) {
            $dateObj                 = $r->date instanceof Carbon ? $r->date->copy() : Carbon::parse($r->date);
            $reqMeta[$r->booking_id] = [
                'slots'     => $slots->getAvailableSlots($trainerId, $dateObj, [$r->booking_id]), // Exclude this booking from availability check
                'preferred' => $r->time ? Carbon::parse($r->time)->format('H:i') : null,          // '17:00' or null
            ];
        }

        return view('trainerDashboard.booking', [
            'requests' => $requests,
            'reqMeta'  => $reqMeta,
        ]);
    }

    // Approve a pending request at a chosen time.
    public function approve(Request $request, $bookingId)
    {
        $trainerId = Auth::id();

        $data = $request->validate([
            'time' => ['required', 'string'],
        ]);

        DB::transaction(function () use ($trainerId, $bookingId, $data) {
            $booking = Booking::where('booking_id', $bookingId)
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $booking->trainer_id !== (int) $trainerId || $booking->status !== 'pending') {
                abort(403, 'Not allowed or already processed.');
            }

            // Resolve the request date (local app TZ)
            $appTz    = config('app.timezone');
            $dateOnly = $booking->date
                ? Carbon::parse($booking->date, $appTz)->toDateString()
                : ($booking->start_at
                    ? $booking->start_at->clone()->timezone($appTz)->toDateString()
                    : now($appTz)->toDateString());

            // Normalize chosen time to "H:i:s" in local TZ
            $chosenTimeLocal = Carbon::parse($data['time'], $appTz)->format('H:i:s');

            // Local datetime for all checks
            $chosenLocal = Carbon::parse($dateOnly, $appTz)
                ->setTimeFromTimeString($chosenTimeLocal)
                ->seconds(0);

                                                                           // Working-hour blocks for that weekday (accept 1..7 and 0..6 encodings)
            $weekdayIso  = Carbon::parse($dateOnly, $appTz)->isoWeekday(); // 1..7
            $weekdayZero = Carbon::parse($dateOnly, $appTz)->dayOfWeek;    // 0..6

            $blocks = TrainerAvailability::where('trainer_id', $trainerId)
                ->whereIn('weekday', [$weekdayIso, $weekdayZero])
                ->get();

            if ($blocks->isEmpty()) {
                abort(422, 'You have no working hours on that day.');
            }

            // Find the block containing chosen time
            $block = $blocks->first(function ($b) use ($chosenLocal) {
                $bStart = Carbon::parse($chosenLocal->toDateString() . ' ' . $b->start_time, $chosenLocal->timezone);
                $bEnd   = Carbon::parse($chosenLocal->toDateString() . ' ' . $b->end_time, $chosenLocal->timezone);
                return $chosenLocal->gte($bStart) && $chosenLocal->lt($bEnd);
            });

            if (! $block) {
                abort(422, 'Selected time is outside your working hours.');
            }

            $buffer = (int) ($block->buffer_minutes ?? 10);
            $dur    = (int) ($booking->duration_minutes ?: ($block->slot_minutes ?? 60));

            $sessionEndLocal    = $chosenLocal->copy()->addMinutes($dur);
            $endWithBufferLocal = $chosenLocal->copy()->addMinutes($dur + $buffer);

            // Must finish within working hours
            $blockEndLocal = Carbon::parse($chosenLocal->toDateString() . ' ' . $block->end_time, $chosenLocal->timezone);
            if ($sessionEndLocal->gt($blockEndLocal)) {
                abort(422, 'Session overruns your working hours.');
            }

            // Overlap vs APPROVED sessions that same day (match legacy date OR start_at date)
            $approved = Booking::where('trainer_id', $trainerId)
                ->where('status', 'approved')
                ->where(function ($q) use ($dateOnly) {
                    $q->whereDate('date', $dateOnly)
                        ->orWhereDate('start_at', $dateOnly);
                })
                ->lockForUpdate()
                ->get();

            foreach ($approved as $ex) {
                // Convert existing to local for fair comparison
                $exStartLocal = $ex->start_at
                    ? $ex->start_at->clone()->timezone($appTz)->seconds(0)
                    : Carbon::parse(
                    (Carbon::parse($ex->date, $appTz)->toDateString() . ' ' .
                        (strlen($ex->time ?? '') === 5 ? $ex->time . ':00' : ($ex->time ?? '00:00:00'))
                    ),
                    $appTz
                )->seconds(0);

                $exDur       = (int) ($ex->duration_minutes ?: ($block->slot_minutes ?? 60));
                $exEndBufLoc = $exStartLocal->copy()->addMinutes($exDur + $buffer);

                if ($chosenLocal->lt($exEndBufLoc) && $endWithBufferLocal->gt($exStartLocal)) {
                    abort(409, 'Time already taken. Choose another time.');
                }
            }

            // Success: write start_at (UTC) + legacy time
            $chosenUtc = $chosenLocal->clone()->utc();

            $booking->update([
                'start_at'        => $chosenUtc,                    // NEW primary
                'time'            => $chosenLocal->format('H:i:s'), // legacy
                'status'          => 'approved',
                'hold_expires_at' => null,
            ]);

            Notification::create([
                'user_id' => $booking->member_id,
                'title'   => 'Booking Request Approved',
                'message' => 'Your booking scheduled on ' . $booking->date->format('F j, Y') . ' ' . $booking->time . ' has been approved.',
                'type'    => 'Booking',
                'is_read' => false,
            ]);
        });

        return back()->with('success', 'Booking approved.');
    }

    // Decline a pending request with a reason.
    public function decline(Request $request, $bookingId)
    {
        $trainerId = Auth::id();

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $booking = Booking::where('booking_id', $bookingId)->firstOrFail();

        if ((int) $booking->trainer_id !== (int) $trainerId || $booking->status !== 'pending') {
            abort(403, 'Not allowed or already processed.');
        }

        $booking->update([
            'status'         => 'declined',
            'decline_reason' => $data['reason'],
        ]);

        Notification::create([
            'user_id' => $booking->member_id,
            'title'   => 'Booking Request Declined',
            'message' => 'Your booking scheduled on ' . $booking->date->format('F j, Y') . ' ' . $booking->time . ' has been declined by the trainer.',
            'type'    => 'Booking',
            'is_read' => false,
        ]);

        return back()->with('success', 'Booking declined.');
    }

    // Cancel a booking (pending or approved).
    public function cancel(Booking $booking)
    {
        if ($booking->trainer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Allow cancelling approved bookings
        if (! in_array($booking->status, ['approved'])) {
            return back()->withErrors(['error' => 'This booking cannot be cancelled.']);
        }

        // For approved bookings, check if it's still in the future
        if ($booking->status === 'approved') {
            if ($booking->start_at) {
                $utc       = Carbon::createFromFormat('Y-m-d H:i:s', $booking->getRawOriginal('start_at'), 'UTC');
                $localTime = $utc->setTimezone(config('app.timezone'));
                if ($localTime->isPast()) {
                    return back()->withErrors(['error' => 'Cannot cancel a session that has already started.']);
                }
            }
        }

        $booking->update(['status' => 'cancelled']);

        Notification::create([
            'user_id' => $booking->member_id,
            'title'   => 'Booking Cancelled',
            'message' => 'Your booking scheduled on ' . $booking->date->format('F j, Y') . ' ' . $booking->time . ' has been cancelled by the member.',
            'type'    => 'Booking',
            'is_read' => false,
        ]);

        return back()->with('success', 'Session cancelled successfully.');
    }

    // Trainer "My Sessions" page (approved only).
    public function sessions()
    {
        $trainerId = Auth::id();
        $now       = now()->utc();

        // Upcoming sessions: approved bookings in the future for this trainer
        $upcoming = Booking::with('member')
            ->where('trainer_id', $trainerId)
            ->where('status', 'approved')
            ->where('start_at', '>=', $now) // only future sessions
            ->orderBy('start_at')
            ->get();

        // Past sessions: completed bookings for this trainer
        $past = Booking::with('member')
            ->where('trainer_id', $trainerId)
            ->where('status', 'completed')
            ->orderByDesc('start_at')
            ->get();

        return view('trainerDashboard.sessions', compact('upcoming', 'past'));
    }
}
