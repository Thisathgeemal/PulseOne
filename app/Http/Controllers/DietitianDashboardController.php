<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\DietPlan;
use App\Models\Feedback;
use App\Models\HealthAssessment;
use App\Models\Meal;
use App\Models\User;

class DietitianDashboardController extends Controller
{
    public function dashboard()
    {
        $dietitianId = auth()->id();

        // Get stats
        $stats = [
            'active_members'        => User::whereHas('roles', function ($query) {
                $query->where('role_name', 'Member');
            })->where('is_active', true)->count(),
            // Number of meals visible to this dietitian: active meals or meals they created
            'meals'                 => Meal::where(function ($q) use ($dietitianId) {
                $q->where('is_active', true)
                    ->orWhere('created_by_dietitian_id', $dietitianId)
                    ->orWhereNull('created_by_dietitian_id');
            })->count(),
            'diet_plans'            => DietPlan::where('dietitian_id', $dietitianId)->count(),
            // Show TOTAL health assessments in the system (same number for every dietitian as requested)
            'nutrition_assessments' => HealthAssessment::count(),
        ];

        // Get recent health/nutrition assessments
        // Show latest 4 assessments globally (not filtered per dietitian) to keep number consistent
        $recent_assessments = HealthAssessment::with('member')
            ->latest('updated_at')
            ->take(4)
            ->get();

        // Get recent feedback received by this dietitian
        $recent_feedback = Feedback::with('fromUser')
            ->where('to_user_id', $dietitianId)
            ->where('type', 'Dietitian')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Prepare timezone-aware bounds for "today" (convert to UTC for DB comparison)
        $appTz           = config('app.timezone') ?: 'UTC';
        $startOfDayLocal = now()->setTimezone($appTz)->startOfDay();
        $endOfDayLocal   = now()->setTimezone($appTz)->endOfDay();
        $startOfDayUtc   = $startOfDayLocal->copy()->setTimezone('UTC');
        $endOfDayUtc     = $endOfDayLocal->copy()->setTimezone('UTC');

        // Get today's appointments/consultations from bookings that are explicitly assigned to this dietitian
        $todays_appointments = Booking::with('member')
            ->where('trainer_id', $dietitianId)
            ->where(function ($q) use ($startOfDayUtc, $endOfDayUtc) {
                $q->whereDate('date', today())
                    ->orWhereBetween('start_at', [$startOfDayUtc, $endOfDayUtc]);
            })
            ->orderBy('start_at')
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        // Build recent activities from real data: latest feedbacks, diet plans, and completed bookings
        $activities = collect();

        // Latest feedbacks to this dietitian (only Dietitian typed feedback)
        $feedbacks = Feedback::where('to_user_id', $dietitianId)
            ->where('type', 'Dietitian')
            ->where('is_visible', true)
            ->latest('created_at')
            ->take(5)
            ->get();

        foreach ($feedbacks as $f) {
            $activities->push([
                'icon'    => 'fa-star',
                'message' => trim($f->content) ?: 'New feedback received',
                'time'    => optional($f->created_at)->diffForHumans() ?? '',
            ]);
        }

        // Recent diet plans created/updated by this dietitian
        $plans = DietPlan::where('dietitian_id', $dietitianId)
            ->latest('updated_at')
            ->take(5)
            ->get();

        foreach ($plans as $p) {
            $memberName = optional($p->member)->name ?? 'Member';
            $activities->push([
                'icon'    => 'fa-apple-alt',
                'message' => "Diet plan updated for {$memberName}",
                'time' => optional($p->updated_at)->diffForHumans() ?? '',
            ]);
        }

        // Recent completed bookings (consultations)
        // Only include completed bookings explicitly assigned to this dietitian
        $completedBookings = Booking::where('status', 'completed')
            ->where('trainer_id', $dietitianId)
            ->latest('updated_at')
            ->take(5)
            ->get();

        foreach ($completedBookings as $b) {
            $memberName = optional($b->member)->name ?? 'Member';
            $activities->push([
                'icon'    => 'fa-calendar-check',
                'message' => "Consultation completed with {$memberName}",
                'time' => optional($b->updated_at)->diffForHumans() ?? '',
            ]);
        }

        // Sort and limit recent activities
        $recent_activities = $activities->slice(0, 5)->values();

        return view('dietitianDashboard.dashboard', compact(
            'stats',
            'recent_assessments',
            'recent_feedback',
            'todays_appointments',
            'recent_activities'
        ));
    }
}
