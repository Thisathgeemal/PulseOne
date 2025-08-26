<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DailyWorkoutLog;
use App\Models\Exercise;
use App\Models\ExerciseLog;
use App\Models\HealthAssessment;
use App\Models\Notification;
use App\Models\ProgressPhoto;
use App\Models\Request as WorkoutRequest;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkoutPlanController extends Controller
{

    // -----------------------------Member-------------------------------

    // Get request data
    public function request()
    {
        $user = Auth::user();

        // Check if user has completed health assessment
        $healthAssessment = HealthAssessment::where('member_id', $user->id)
            ->where('is_complete', true)
            ->first();

        if (! $healthAssessment || $healthAssessment->needs_update) {
            return redirect()
                ->route('member.health-assessment')
                ->with('error', 'You must complete your health assessment before requesting a workout plan.');
        }

        // Fetch active trainers
        $trainers = User::whereHas('roles', function ($query) {
            $query->where('role_name', 'Trainer')
                ->where('user_roles.is_active', 1);
        })->get();

        // Fetch workout requests by the member
        $requests = WorkoutRequest::with('trainer')
            ->where('type', 'Workout')
            ->where('member_id', $user->id)
            ->latest()
            ->paginate(7);

        return view('memberDashboard.workoutplanRequest', compact('trainers', 'requests'));
    }

    // Handle workout plan request
    public function requestWorkout(Request $request)
    {
        $request->validate([
            'trainer_id'           => 'required|exists:users,id',
            'plan_type'            => 'required|in:Basic,Intermediate,Advanced',
            'plan_dis'             => 'required|string|max:255',
            'available_days'       => 'nullable|string|max:255',
            'preferred_start_date' => 'nullable|date|after_or_equal:today',
        ]);

        WorkoutRequest::create([
            'member_id'            => Auth::id(),
            'trainer_id'           => $request->trainer_id,
            'plan_type'            => $request->plan_type,
            'description'          => $request->plan_dis,
            'available_days'       => $request->available_days,
            'preferred_start_date' => $request->preferred_start_date,
            'type'                 => 'Workout',
            'status'               => 'Pending',
        ]);

        Notification::create([
            'user_id' => $request->trainer_id,
            'title'   => 'Workout Plan Request',
            'message' => 'You received a new workout plan request from ' . Auth::user()->first_name,
            'type'    => 'Workout Plan',
            'is_read' => false,
        ]);

        return redirect()
            ->route('member.workoutplan.request')
            ->with('success', 'Workout plan request submitted successfully.');
    }

    // Get plan data
    public function myPlan()
    {
        $user = Auth::user();

        WorkoutPlan::where('member_id', $user->id)
            ->whereDate('start_date', now()->toDateString())
            ->where('status', 'Pending') // <-- only Pending
            ->update(['status' => 'Active']);

        $plans = WorkoutPlan::with(['trainer', 'workoutPlanExercises'])
            ->where('member_id', $user->id)
            ->orderBy('start_date', 'desc')
            ->get();

        return view('memberDashboard.workoutplanMyplan', compact('plans'));
    }

    // Get Track data
    public function progressTracking()
    {
        $userId = auth()->id();
        $today  = Carbon::today();

        $workoutPlan = WorkoutPlan::where('member_id', $userId)
            ->where('status', 'Active')
            ->first();

        if (! $workoutPlan) {
            return redirect()->back()->with('error', 'No active workout plan found. Start a Workout Plan first.');
        }

        // Daily Log Data
        $dailyData = $this->getDailyLogData($workoutPlan, $userId, $today);

        // Weekly Log Data
        $weeklyLogs = $this->getWeeklyLogData($userId);

        // Monthly Log Data
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        $monthlyLogs = DailyWorkoutLog::where('member_id', $userId)
            ->whereBetween('log_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orderBy('log_date', 'asc')
            ->paginate(10);

        $monthlyProgress = $this->getMonthlyProgress($userId);

        // Photos & Photo Progress
        $photos = ProgressPhoto::where('user_id', $userId)
            ->orderBy('photo_date', 'desc')
            ->paginate(8);

        $photoProgress = $this->getPhotoProgressData($userId);

        return view('memberDashboard.workoutplanProgress', [
            'workoutPlan'         => $workoutPlan,
            'exercises'           => $dailyData['exercises'],
            'todayLogs'           => $dailyData['todayLogs'],
            'dailyProgress'       => $dailyData['dailyProgress'],

            'weeklyLogs'          => $weeklyLogs['weeklyLogs'],
            'startOfWeek'         => $weeklyLogs['startOfWeek'],
            'endOfWeek'           => $weeklyLogs['endOfWeek'],
            'weeklyProgress'      => $weeklyLogs['weeklyProgress'],

            'monthlyLogs'         => $monthlyLogs,
            'startOfMonth'        => $startOfMonth,
            'endOfMonth'          => $endOfMonth,
            'monthlyProgressData' => $monthlyProgress,

            'photos'              => $photos,
            'photoProgress'       => $photoProgress,
        ]);

    }

    // Get daily log data
    protected function getDailyLogData($workoutPlan, $userId, $today)
    {
        // Get today's log (if any)
        $todayLog = DailyWorkoutLog::where('member_id', $userId)
            ->where('workoutplan_id', $workoutPlan->workoutplan_id)
            ->whereDate('log_date', $today)
            ->first();

        // Get total unique days in workout schedule
        $totalDays = WorkoutPlanExercise::where('workoutplan_id', $workoutPlan->workoutplan_id)
            ->distinct('day_number')
            ->count('day_number');

        if ($todayLog) {
            // If there's already a log for today, use the same day_number
            $dayNumber = $todayLog->day_number;
        } else {
            // Count total workout days (distinct log dates)
            $completedWorkoutDays = DailyWorkoutLog::where('member_id', $userId)
                ->where('workoutplan_id', $workoutPlan->workoutplan_id)
                ->count();

            // Move to next day in rotation
            $dayNumber = ($completedWorkoutDays % $totalDays) + 1;
        }

        // Get exercises for the day
        $exercises = WorkoutPlanExercise::with('exercise')
            ->where('workoutplan_id', $workoutPlan->workoutplan_id)
            ->where('day_number', $dayNumber)
            ->get();

        // Get today's logged exercises
        $todayLogs = ExerciseLog::with('exercise')
            ->where('member_id', $userId)
            ->where('workoutplan_id', $workoutPlan->workoutplan_id)
            ->whereDate('log_date', $today)
            ->get();

        $dailyProgress = $this->getDailyProgress($userId, $workoutPlan->workoutplan_id, $today);

        return [
            'dayNumber'     => $dayNumber,
            'exercises'     => $exercises,
            'todayLogs'     => $todayLogs,
            'dailyProgress' => $dailyProgress,
        ];
    }

    // Get weekly log data
    protected function getWeeklyLogData($userId)
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek   = Carbon::now()->endOfWeek();

        $weeklyLogs = DailyWorkoutLog::where('member_id', $userId)
            ->whereBetween('log_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->orderBy('log_date', 'asc')
            ->get();

        $weeklyProgress = $this->getWeeklyProgress($userId);

        return [
            'weeklyLogs'     => $weeklyLogs,
            'startOfWeek'    => $startOfWeek,
            'endOfWeek'      => $endOfWeek,
            'weeklyProgress' => $weeklyProgress,
        ];
    }

    // Calculate the daily progress
    public function getDailyProgress($userId, $workoutPlanId, $date)
    {
        $totalDays = WorkoutPlanExercise::where('workoutplan_id', $workoutPlanId)
            ->distinct('day_number')
            ->count('day_number');

        // Count how many days the user has completed so far
        $completedWorkoutDays = DailyWorkoutLog::where('member_id', $userId)
            ->where('workoutplan_id', $workoutPlanId)
            ->count();

        $dayNumber = ($completedWorkoutDays % $totalDays) + 1;

        // Count how many exercises are assigned for today (for that day number)
        $totalAssigned = WorkoutPlanExercise::where('workoutplan_id', $workoutPlanId)
            ->where('day_number', $dayNumber)
            ->count();

        // Count how many of those exercises are completed today
        $completedToday = ExerciseLog::where('workoutplan_id', $workoutPlanId)
            ->where('member_id', $userId)
            ->whereDate('log_date', $date)
            ->distinct('exercise_id')
            ->count('exercise_id');

        $percentage = ($totalAssigned > 0) ? ($completedToday / $totalAssigned) * 100 : 0;

        return [
            'percentage' => round($percentage),
            'completed'  => $completedToday,
        ];
    }

    // Calculate the weekly progress
    public function getWeeklyProgress($userId)
    {
        $availableDaysStr = DB::table('requests')
            ->where('member_id', $userId)
            ->value('available_days');

        if (! $availableDaysStr) {
            return [
                'completed'  => 0,
                'percentage' => 0,
            ];
        }

        // Count how many available days
        $availableDaysArray = array_map('trim', explode(',', $availableDaysStr));
        $targetDays         = count($availableDaysArray);

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek   = Carbon::now()->endOfWeek();

        // Count how many unique days user worked out this week (any day)
        $actualWorkoutDays = DailyWorkoutLog::where('member_id', $userId)
            ->whereBetween('log_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->distinct('log_date')
            ->count('log_date');

        // Calculate progress % (even if it exceeds 100%)
        $percentage = $targetDays > 0
        ? ($actualWorkoutDays / $targetDays) * 100
        : 0;

        return [
            'completed'  => $actualWorkoutDays,
            'percentage' => round($percentage, 0),
        ];
    }

    public function getMonthlyProgress($userId)
    {
        $availableDaysStr = DB::table('requests')
            ->where('member_id', $userId)
            ->value('available_days');

        if (! $availableDaysStr) {
            return [
                'completed'  => 0,
                'percentage' => 0,
            ];
        }

        // Convert CSV string to array of 3-letter day abbreviations, trimming spaces
        $preferredWeekdays = array_map('trim', explode(',', $availableDaysStr));

        if (empty($preferredWeekdays)) {
            return [
                'completed'  => 0,
                'percentage' => 0,
            ];
        }

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);

        $availableWorkoutDays = 0;

        foreach ($period as $date) {
            // Use 3-letter abbreviation here for matching
            if (in_array($date->format('D'), $preferredWeekdays)) {
                $availableWorkoutDays++;
            }
        }

        if ($availableWorkoutDays === 0) {
            return [
                'completed'  => 0,
                'percentage' => 0,
            ];
        }

        $completedWorkoutDays = DB::table('daily_workout_logs')
            ->where('member_id', $userId)
            ->whereBetween('log_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->distinct()
            ->count('log_date');

        $percentage = ($completedWorkoutDays / $availableWorkoutDays) * 100;

        return [
            'completed'  => $completedWorkoutDays,
            'percentage' => round($percentage, 0),
        ];
    }

    // Get Photo Progress Data
    protected function getPhotoProgressData($userId)
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek   = Carbon::now()->endOfWeek();

        // Weekly
        $weeklyPhotoCount = ProgressPhoto::where('user_id', $userId)
            ->whereBetween('photo_date', [$startOfWeek, $endOfWeek])
            ->count();

        $weeklyTarget   = 2;
        $weeklyProgress = $weeklyTarget > 0
        ? round(($weeklyPhotoCount / $weeklyTarget) * 100)
        : 0;

        // Monthly
        $photoCountThisMonth = ProgressPhoto::where('user_id', $userId)
            ->whereMonth('photo_date', now()->month)
            ->whereYear('photo_date', now()->year)
            ->count();

        $monthlyTarget   = 8;
        $monthlyProgress = $monthlyTarget > 0
        ? round(($photoCountThisMonth / $monthlyTarget) * 100)
        : 0;

        return [
            'weeklyPhotoCount' => $weeklyPhotoCount,
            'weeklyTarget'     => $weeklyTarget,
            'weeklyProgress'   => $weeklyProgress,
            'monthlyCount'     => $photoCountThisMonth,
            'monthlyTarget'    => $monthlyTarget,
            'monthlyProgress'  => $monthlyProgress,
        ];
    }

    // Member View of a Specific Workout Plan
    public function viewMemberPlan($id)
    {
        $plan = WorkoutPlan::with([
            'trainer',
            'workoutPlanExercises.exercise',
        ])
            ->where('member_id', auth()->id())
            ->findOrFail($id);

        $groupedExercises = $plan->workoutPlanExercises
            ->sortBy('day_number')
            ->groupBy(function ($item) {
                return (int) $item->day_number;
            });

        return view('memberDashboard.workoutplan_view', compact('plan', 'groupedExercises'));
    }

    // Member cancel the workout plan
    public function cancelMemberPlan($id)
    {
        $plan = WorkoutPlan::where('member_id', auth()->id())
            ->where('workoutplan_id', $id)
            ->firstOrFail();

        $plan->status = 'Cancelled';
        $plan->save();

        Notification::create([
            'user_id' => $plan->member_id,
            'title'   => 'Workout Plan Cancelled',
            'message' => 'Your workout plan has been cancelled.',
            'type'    => 'Workout Plan',
            'is_read' => false,
        ]);

        return redirect()->back()->with('success', 'Your workout plan has been cancelled successfully.');
    }

    // Check if member has workout plan (AJAX)
    public function checkWorkoutPlan()
    {
        $user = Auth::user();

        $hasWorkoutPlan = WorkoutPlan::where('member_id', $user->id)
            ->whereIn('status', ['Active', 'Pending'])
            ->exists();

        return response()->json(['hasWorkoutPlan' => $hasWorkoutPlan]);
    }

    // -----------------------------Trainer------------------------------

    // Get Workout Plan (Trainer)
    public function index(Request $request)
    {
        $user = Auth::user();

        WorkoutPlan::where('trainer_id', $user->id)
            ->whereDate('start_date', now()->toDateString())
            ->whereNotIn('status', ['Active', 'Completed', 'Cancelled'])
            ->update(['status' => 'Active']);

        // Trainer workout plans
        WorkoutPlan::where('trainer_id', $user->id)
            ->where('end_date', '<', now())
            ->whereNotIn('status', ['Completed'])
            ->update(['status' => 'Completed']);

        $plans = WorkoutPlan::with('member')
            ->where('trainer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('trainerDashboard.workoutplan', compact('plans'));
    }

    // Create Plan Form (Trainer)
    public function create($request_id)
    {
        $request = WorkoutRequest::with('member')
            ->where('request_id', $request_id)
            ->where('type', 'Workout')
            ->where('status', 'Approved')
            ->firstOrFail();

        $exercises = Exercise::all();

        return view('trainerDashboard.workoutplan', compact('request', 'exercises'));
    }

    // Store Workout Plan + Assigned Exercises
    public function store(Request $request)
    {
        $request->validate([
            'request_id'                     => 'required|exists:requests,request_id',
            'plan_name'                      => 'required|string|max:255',
            'start_date'                     => 'required|date',
            'end_date'                       => 'required|date|after_or_equal:start_date',
            'days'                           => 'required|array|min:1',
            'days.*.day_number'              => 'required|integer|min:1',
            'days.*.muscle_groups'           => 'required|array|min:1',
            'days.*.muscle_groups.*'         => 'string|max:50',
            'days.*.exercises'               => 'required|array|min:1',
            'days.*.exercises.*.exercise_id' => 'required|exists:exercises,exercise_id',
            'days.*.exercises.*.sets'        => 'required|integer|min:1',
            'days.*.exercises.*.reps'        => 'required|integer|min:1',
            'days.*.notes'                   => 'nullable|string|max:255',
        ]);

        $req = WorkoutRequest::findOrFail($request->request_id);

        $latestPlan = WorkoutPlan::where('member_id', $req->member_id)
            ->whereIn('status', ['Pending', 'Active'])
            ->orderBy('end_date', 'desc')
            ->first();

        if ($latestPlan && Carbon::parse($request->start_date)->lte(Carbon::parse($latestPlan->end_date))) {
            return redirect()->back()->withErrors(['start_date' => 'Start date must be after the end date of the existing workout plan (' . $latestPlan->end_date . ').'])->withInput();
        }

        $plan = WorkoutPlan::create([
            'trainer_id' => Auth::id(),
            'member_id'  => $req->member_id,
            'request_id' => $req->request_id,
            'plan_name'  => $request->plan_name,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'status'     => 'Pending',
        ]);

        foreach ($request->days as $day) {
            foreach ($day['exercises'] as $exercise) {
                WorkoutPlanExercise::create([
                    'workoutplan_id' => $plan->workoutplan_id,
                    'exercise_id'    => $exercise['exercise_id'],
                    'sets'           => $exercise['sets'],
                    'reps'           => $exercise['reps'],
                    'day_number'     => $day['day_number'],
                    'muscle_groups'  => json_encode($day['muscle_groups']),
                    'notes'          => $day['notes'] ?? null,
                ]);
            }
        }

        $req->update(['status' => 'Completed']);

        Notification::create([
            'user_id' => $plan->member_id,
            'title'   => 'New Workout Plan Created',
            'message' => 'A new workout plan has been created for you.',
            'type'    => 'Workout Plan',
            'is_read' => false,
        ]);

        return redirect()->route('trainer.workoutplan')->with('success', 'Workout plan created successfully!');
    }

    // View Specific Workout Plan
    public function viewPlan($id)
    {
        $plan = WorkoutPlan::with([
            'member',
            'workoutPlanExercises.exercise',
        ])->findOrFail($id);

        $groupedExercises = $plan->workoutPlanExercises
            ->sortBy('day_number')
            ->groupBy(function ($item) {
                return (int) $item->day_number;
            });

        return view('trainerDashboard.workoutplan_view', compact('plan', 'groupedExercises'));
    }

    // View Specific Workout progress
    public function viewProgress($id)
    {
        $workoutPlan = WorkoutPlan::where('workoutplan_id', $id)
            ->where('status', 'Active')
            ->first();

        if (! $workoutPlan) {
            return redirect()->back()->with('error', 'Workout plan not found or inactive.');
        }

        $memberId = $workoutPlan->member_id;
        $today    = Carbon::today();

        $dailyProgress = $this->getDailyProgress($memberId, $id, $today);
        $weeklyLogData = $this->getWeeklyLogData($memberId);
        $photoProgress = $this->getPhotoProgressData($memberId);

        // Monthly Log Data
        $startOfMonth    = Carbon::now()->startOfMonth();
        $endOfMonth      = Carbon::now()->endOfMonth();
        $monthlyProgress = $this->getMonthlyProgress($memberId);

        $photos = ProgressPhoto::where('user_id', $memberId)->orderBy('photo_date', 'desc')->get();
        $member = User::find($memberId);

        return view('trainerDashboard.workoutplan_progress', [
            'workoutPlan'         => $workoutPlan,
            'member'              => $member,
            'dailyProgress'       => $dailyProgress,

            'weeklyProgress'      => $weeklyLogData['weeklyProgress'],
            'weeklyLogs'          => $weeklyLogData['weeklyLogs'],
            'startOfWeek'         => $weeklyLogData['startOfWeek'],
            'endOfWeek'           => $weeklyLogData['endOfWeek'],

            'startOfMonth'        => $startOfMonth,
            'endOfMonth'          => $endOfMonth,
            'monthlyProgressData' => $monthlyProgress,

            'photos'              => $photos,
            'photoProgress'       => $photoProgress,
        ]);
    }

}
