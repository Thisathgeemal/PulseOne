<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Notification;
use App\Models\User;
use App\Services\SlotService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberBookingController extends Controller
{
    // List member bookings and data for the "New Booking" modal.
    public function index(Request $request)
    {
        $user = Auth::user();

        // Fetch active trainers
        $trainers = User::whereHas('roles', function ($query) {
            $query->where('role_name', 'Trainer')
                ->where('user_roles.is_active', 1);
        })->get();

        // Get all bookings relevant to the logged-in user
        $bookings = Booking::with('trainer')
            ->where('member_id', $user->id)
            ->orderByDesc('start_at')
            ->paginate(6);

        return view('memberDashboard.booking', compact('bookings', 'trainers'));
    }

    // Create a pending booking request.
    public function store(Request $request, SlotService $slots)
    {
        $memberId = Auth::id();

        $request->validate([
            'trainer_id'     => 'required|exists:users,id',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'nullable|string',
            'description'    => 'nullable|string|max:255',
        ]);

        $trainerId = (int) $request->trainer_id;
        $dateStr   = $request->preferred_date;
        $timeStr   = $request->preferred_time;

        $trainer = User::whereHas('roles', fn($q) => $q->where('role_name', 'Trainer'))
            ->find($trainerId);
        if (! $trainer) {
            return back()->withErrors(['trainer_id' => 'Invalid trainer.']);
        }

        if ($timeStr) {
            $normalized = $slots->normalizeTime($timeStr);
            if (! $normalized) {
                return back()->withErrors(['preferred_time' => 'Invalid time format.']);
            }

            $dateCarbon = Carbon::parse($dateStr);
            $available  = $slots->getAvailableSlots($trainerId, $dateCarbon);
            if (! in_array($normalized, $available)) {
                return back()->withErrors(['preferred_time' => 'That time slot is not available.']);
            }

            $localDt = Carbon::parse($dateStr . ' ' . $normalized, config('app.timezone'));
            $utcDt   = $localDt->utc();
        } else {
            $localDt = Carbon::parse($dateStr . ' 00:00:00', config('app.timezone'));
            $utcDt   = $localDt->utc();
        }

        // Store booking
        Booking::create([
            'member_id'   => $memberId,
            'trainer_id'  => $trainerId,
            'date'        => $dateStr,
            'time'        => $timeStr ?: '00:00:00',
            'start_at'    => $utcDt,
            'description' => $request->description,
            'status'      => 'pending',
        ]);

        Notification::create([
            'user_id' => $memberId,
            'title'   => 'New Booking Request',
            'message' => 'Your booking request has been submitted successfully.',
            'type'    => 'Booking',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $trainerId,
            'title'   => 'New Booking Request',
            'message' => 'You have a new booking request from ' . Auth::user()->first_name,
            'type'    => 'Booking',
            'is_read' => false,
        ]);

        return redirect()->route('member.bookings.index')
            ->with('success', 'Booking request submitted successfully!');
    }

    // AJAX route to get available time slots.
    public function slots(Request $request, SlotService $slots)
    {
        $trainerId = (int) $request->trainer_id;
        $dateStr   = $request->date;

        if (! $trainerId || ! $dateStr) {
            return response()->json(['slots' => []]);
        }

        try {
            // Convert string date to Carbon object
            $date      = Carbon::parse($dateStr);
            $available = $slots->getAvailableSlots($trainerId, $date);
            return response()->json(['slots' => $available]);
        } catch (\Exception $e) {
            \Log::error('Slots loading error', [
                'trainer_id' => $trainerId,
                'date'       => $dateStr,
                'error'      => $e->getMessage(),
            ]);
            return response()->json(['slots' => []], 500);
        }
    }

    // Cancel a booking (pending or approved).
    public function cancel(Booking $booking)
    {
        if ($booking->member_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Allow cancelling both pending and approved bookings
        if (! in_array($booking->status, ['pending', 'approved'])) {
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
            'user_id' => $booking->trainer_id,
            'title'   => 'Booking Cancelled',
            'message' => 'A booking scheduled on ' . $booking->date->format('F j, Y') . ' ' . $booking->time . ' has been cancelled by the member.',
            'type'    => 'Booking',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $booking->member_id,
            'title'   => 'Booking Cancelled',
            'message' => 'Your booking scheduled on ' . $booking->date->format('F j, Y') . ' ' . $booking->time . ' has been cancelled.',
            'type'    => 'Booking',
            'is_read' => false,
        ]);

        return back()->with('success', 'Session cancelled successfully.');
    }

    // "My Sessions" page: approved bookings split into Upcoming vs Past.
    public function sessions()
    {
        $memberId = Auth::id();
        $now      = now();

        // Upcoming sessions: approved bookings in the future for this member
        $upcoming = Booking::with('trainer')
            ->where('member_id', $memberId)
            ->where('status', 'approved')
            ->where('start_at', '>=', $now) // only future sessions
            ->orderBy('start_at')
            ->get();

        // Past sessions: completed or expired bookings for this member
        $past = Booking::with('trainer')
            ->where('member_id', $memberId)
            ->whereIn('status', ['completed', 'expired'])
            ->orderByDesc('start_at')
            ->get();

        return view('memberDashboard.booking-sessions', compact('upcoming', 'past'));
    }

}
