<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Feedback;
use App\Models\Booking;
use App\Models\HealthAssessment;
use App\Models\DietPlan;
use App\Models\WorkoutPlan;
use App\Models\ExerciseLog;
use App\Models\MealLog;
use App\Models\Attendance;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

class MemberDashboardController extends Controller
{
    public function dashboard()
    {
        $member = Auth::user();
        $memberId = $member->id;
        
        // Member-specific statistics
        $stats = [
            'total_workouts' => ExerciseLog::where('member_id', $memberId)->count(),
            'this_month_attendance' => Attendance::where('user_id', $memberId)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
            'active_diet_plans' => DietPlan::where('member_id', $memberId)
                ->where('status', 'active')
                ->count(),
            'total_feedback_given' => Feedback::where('from_user_id', $memberId)->count(),
        ];
        
        // Upcoming bookings (next 4) - align logic with My Sessions page using UTC for start_at
        $nowLocal = Carbon::now();              // App timezone (Asia/Colombo)
        $nowUtc   = Carbon::now('UTC');         // Explicit UTC for start_at comparison

        // Custom upcoming logic: prefer legacy date/time; include even if start_at (creation) is in past
        $upcoming_bookings = Booking::with('trainer')
            ->where('member_id', $memberId)
            ->where('status', 'approved')
            ->where(function ($q) use ($nowLocal, $nowUtc) {
                $today = $nowLocal->toDateString();
                $currentTime = $nowLocal->format('H:i:s');

                // 1. Future dates
                $q->whereDate('date', '>', $today)
                  // 2. Same day but later time
                  ->orWhere(function ($sameDay) use ($today, $currentTime) {
                      $sameDay->whereDate('date', $today)
                              ->where('time', '>=', $currentTime);
                  })
                  // 3. Fallback: start_at in future (only if truly future)
                  ->orWhere(function ($hasStart) use ($nowUtc) {
                      $hasStart->whereNotNull('start_at')
                               ->where('start_at', '>=', $nowUtc);
                  });
            })
            ->orderBy('date')
            ->orderBy('time')
            ->orderBy('start_at')
            ->limit(2)
            ->get();

        // Debug: detailed logging to diagnose missing sessions
        \Log::info('Dashboard upcoming bookings (diagnostic v2)', [
            'member_id'        => $memberId,
            'now_local'        => $nowLocal->toDateTimeString(),
            'now_utc'          => $nowUtc->toDateTimeString(),
            'app_timezone'     => config('app.timezone'),
            'bookings_count'   => $upcoming_bookings->count(),
            'raw_sample'       => $upcoming_bookings->map(function ($b) {
                return [
                    'id'       => $b->booking_id,
                    'start_at' => $b->start_at ? $b->start_at->copy()->setTimezone('UTC')->format('Y-m-d H:i:s').'Z' : null,
                    'start_at_local' => $b->start_at ? $b->start_at->format('Y-m-d H:i:s') : null,
                    'date'     => $b->date,
                    'time'     => $b->time,
                    'status'   => $b->status,
                    'trainer'  => $b->trainer?->first_name,
                ];
            })->toArray(),
            'logic_note' => 'Using legacy date/time precedence; includes booking if legacy future even if start_at past.'
        ]);
        
        // Recent workout logs (last 4)
        $recent_workouts = ExerciseLog::where('member_id', $memberId)
            ->with(['exercise'])
            ->orderByDesc('log_date')
            ->limit(4)
            ->get();
        
        // Active diet plans
        $active_diet_plans = DietPlan::where('member_id', $memberId)
            ->where('status', 'active')
            ->with(['dietitian'])
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();
        
        // Health assessment status
        $health_assessment = HealthAssessment::where('member_id', $memberId)
            ->orderByDesc('completed_at')
            ->first();
        
        // Recent feedback given by member
        $recent_feedback = Feedback::where('from_user_id', $memberId)
            ->with(['toUser'])
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();
        
        // Recent activity (combined timeline)
        $recent_activities = collect();
        
        // Add recent workouts to activity
        $recent_workouts->each(function($workout) use ($recent_activities) {
            $exerciseName = $workout->exercise ? $workout->exercise->name : 'workout';
            $recent_activities->push([
                'icon' => 'fa-dumbbell',
                'message' => "Completed {$exerciseName} - {$workout->sets_completed} sets × {$workout->reps_completed} reps",
                'time' => Carbon::parse($workout->log_date)->diffForHumans(),
                'created_at' => Carbon::parse($workout->log_date)
            ]);
        });
        
        // Add bookings to activity
        $upcoming_bookings->take(2)->each(function($booking) use ($recent_activities) {
            $trainerName = $booking->trainer ? $booking->trainer->first_name . ' ' . $booking->trainer->last_name : 'Trainer';
            // Create datetime from separate date and time fields
            try {
                $sessionDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $booking->date . ' ' . $booking->time);
            } catch (Exception $e) {
                // Fallback to just the date if time parsing fails
                $sessionDateTime = Carbon::parse($booking->date);
            }
            $recent_activities->push([
                'icon' => 'fa-calendar-check',
                'message' => "Upcoming session with {$trainerName}",
                'time' => $sessionDateTime->diffForHumans(),
                'created_at' => $sessionDateTime
            ]);
        });
        
        // Add diet plan activities
        $active_diet_plans->each(function($plan) use ($recent_activities) {
            $dietitianName = $plan->dietitian ? $plan->dietitian->first_name . ' ' . $plan->dietitian->last_name : 'Dietitian';
            $recent_activities->push([
                'icon' => 'fa-apple-alt',
                'message' => "Active diet plan from {$dietitianName}",
                'time' => $plan->created_at->diffForHumans(),
                'created_at' => $plan->created_at
            ]);
        });
        
        // Sort activities by time and limit
        $recent_activities = $recent_activities->sortByDesc('created_at')->take(6)->values();
        
        return view('memberDashboard.dashboard', compact(
            'stats',
            'upcoming_bookings',
            'recent_workouts',
            'active_diet_plans',
            'health_assessment',
            'recent_feedback',
            'recent_activities'
        ));
    }
}
