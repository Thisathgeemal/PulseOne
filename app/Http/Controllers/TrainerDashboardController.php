<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Feedback;
use App\Models\HealthAssessment;
use App\Models\User;
use App\Models\WorkoutPlan;
use Carbon\Carbon;

class TrainerDashboardController extends Controller
{
    public function dashboard()
    {
        $trainerId = auth()->id();

        // Prepare timezone-aware bounds for "today" (convert to UTC for DB comparison)
        $appTz           = config('app.timezone') ?: 'UTC';
        $startOfDayLocal = now()->setTimezone($appTz)->startOfDay();
        $endOfDayLocal   = now()->setTimezone($appTz)->endOfDay();
        $startOfDayUtc   = $startOfDayLocal->copy()->setTimezone('UTC');
        $endOfDayUtc     = $endOfDayLocal->copy()->setTimezone('UTC');

        // Get stats
        $nowLocal = Carbon::now();
        $nowUtc   = Carbon::now('UTC');

        // Upcoming approved bookings (limit 4) for this trainer (legacy date/time first)
        $upcoming_bookings = Booking::with('member')
            ->where('trainer_id', $trainerId)
            ->where('status', 'approved')
            ->where(function ($q) use ($nowLocal, $nowUtc) {
                $today       = $nowLocal->toDateString();
                $currentTime = $nowLocal->format('H:i:s');
                $q->whereDate('date', '>', $today)
                    ->orWhere(function ($sameDay) use ($today, $currentTime) {
                        $sameDay->whereDate('date', $today)->where('time', '>=', $currentTime);
                    })
                    ->orWhere(function ($future) use ($nowUtc) {
                        $future->whereNotNull('start_at')->where('start_at', '>=', $nowUtc);
                    });
            })
            ->orderBy('date')
            ->orderBy('time')
            ->orderBy('start_at')
            ->limit(4)
            ->get();

        $stats = [
            'active_members'     => User::whereHas('roles', function ($query) {$query->where('role_name', 'Member');})
                ->where('is_active', true)->count(),
            'upcoming_sessions'  => $upcoming_bookings->count(),
            'workout_plans'      => WorkoutPlan::where('trainer_id', $trainerId)->count(),
            'health_assessments' => HealthAssessment::count(),
        ];

        // Get recent health assessments for dashboard preview (show latest 4 completed assessments)
        // Show latest 4 assessments (completed or not) so the preview always reflects global activity
        $recent_assessments = HealthAssessment::with('member')
            ->latest('updated_at')
            ->take(4)
            ->get();

        // Get recent feedback received by this trainer
        $recent_feedback = Feedback::with('fromUser')
            ->where('to_user_id', $trainerId)
            ->where('type', 'Trainer')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // (Removed separate today's bookings list; using unified upcoming list)

        // Build recent activities from real data: latest feedbacks, completed bookings, and workoutplan updates
        $activities = collect();

        // Latest feedbacks to this trainer (only Trainer-typed feedback)
        $feedbacks = Feedback::where('to_user_id', $trainerId)
            ->where('type', 'Trainer')
            ->where('is_visible', true)
            ->latest('created_at')
            ->take(5)
            ->get();

        foreach ($feedbacks as $f) {
            $activities->push([
                'icon'    => 'fa-comment-dots',
                'message' => trim($f->content) ?: 'New feedback received',
                'time'    => optional($f->created_at)->diffForHumans() ?? '',
            ]);
        }

        // Recent completed bookings with this trainer
        $completedBookings = Booking::where('trainer_id', $trainerId)
            ->where('status', 'completed')
            ->latest('updated_at')
            ->take(5)
            ->get();

        foreach ($completedBookings as $b) {
            $memberName = $b->member->name ?? 'Member';
            $activities->push([
                'icon'    => 'fa-calendar-check',
                'message' => "Session completed with {$memberName}",
                'time' => optional($b->updated_at)->diffForHumans() ?? '',
            ]);
        }

        // Recent workout plans created/updated by this trainer
        $plans = WorkoutPlan::where('trainer_id', $trainerId)
            ->latest('updated_at')
            ->take(5)
            ->get();

        foreach ($plans as $p) {
            $memberName = $p->member->name ?? 'Member';
            $activities->push([
                'icon'    => 'fa-dumbbell',
                'message' => "Workout plan updated for {$memberName}",
                'time' => optional($p->updated_at)->diffForHumans() ?? '',
            ]);
        }

        // Sort by time roughly by latest (we rely on created_at/updated_at order inserted above) and limit to 5
        $recent_activities = $activities->slice(0, 5)->values();

        return view('trainerDashboard.dashboard', compact(
            'stats',
            'recent_assessments',
            'recent_feedback',
            'upcoming_bookings',
            'recent_activities'
        ));
    }
}
