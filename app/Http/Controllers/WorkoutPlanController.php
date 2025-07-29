<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\Request as WorkoutRequest;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkoutPlanController extends Controller
{

    // Controller Method for Workout Plan (Trainer and Member Views)
    public function index(Request $request)
    {
        $user = Auth::user();

        // Trainer View
        if ($request->routeIs('trainer.workoutplan')) {
            $plans = WorkoutPlan::with('member')
                ->where('trainer_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return view('trainerDashboard.workoutplan', compact('plans'));
        }

        // Member View
        if ($request->routeIs('member.workoutplan')) {
            $trainers = User::whereHas('roles', function ($query) {
                $query->where('role_name', 'Trainer')
                    ->where('user_roles.is_active', 1);
            })->get();

            $requests = WorkoutRequest::with('trainer')
                ->where('type', 'Workout')
                ->where('member_id', $user->id)
                ->latest()
                ->paginate(5);

            $plans = WorkoutPlan::with('trainer')
                ->where('member_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return view('memberDashboard.workoutplan', compact('trainers', 'requests', 'plans'));
        }

        abort(403, 'Unauthorized access to workout plan page.');
    }

    // Handle workout plan request (Member)
    public function requestWorkout(Request $request)
    {
        $request->validate([
            'trainer_id' => 'required|exists:users,id',
            'plan_dis'   => 'required|string|max:255',
        ]);

        WorkoutRequest::create([
            'member_id'   => Auth::id(),
            'trainer_id'  => $request->trainer_id,
            'description' => $request->plan_dis,
            'type'        => 'Workout',
            'status'      => 'Pending',
        ]);

        return redirect()->route('member.workoutplan')->with('success', 'Workout plan request submitted successfully.');
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
            'days.*.exercises'               => 'required|array|min:1',
            'days.*.exercises.*.exercise_id' => 'required|exists:exercises,exercise_id',
            'days.*.exercises.*.sets'        => 'required|integer|min:1',
            'days.*.exercises.*.reps'        => 'required|integer|min:1',
            'days.*.notes'                   => 'nullable|string|max:255',
        ]);

        $req = WorkoutRequest::findOrFail($request->request_id);

        $plan = WorkoutPlan::create([
            'trainer_id' => Auth::id(),
            'member_id'  => $req->member_id,
            'request_id' => $req->request_id,
            'plan_name'  => $request->plan_name,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'status'     => 'Active',
        ]);

        foreach ($request->days as $day) {
            foreach ($day['exercises'] as $exercise) {
                WorkoutPlanExercise::create([
                    'workoutplan_id' => $plan->workoutplan_id,
                    'exercise_id'    => $exercise['exercise_id'],
                    'sets'           => $exercise['sets'],
                    'reps'           => $exercise['reps'],
                    'day_number'     => $day['day_number'],
                    'notes'          => $day['notes'] ?? null,
                ]);
            }
        }

        return redirect()->route('trainer.workoutplan')->with('success', 'Workout plan created successfully!');
    }

    // Trainer View of a Specific Workout Plan
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

}
