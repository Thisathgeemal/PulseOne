<?php
namespace App\Http\Controllers;

use App\Models\DailyWorkoutLog;
use App\Models\ExerciseLog;
use App\Models\ProgressPhoto;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExerciseLogController extends Controller
{
    // Store daily workout log
    public function store(Request $request)
    {
        $request->validate([
            'workoutplan_id' => 'required|exists:workout_plans,workoutplan_id',
            'exercise_id'    => 'required|exists:exercises,exercise_id',
            'sets_completed' => 'nullable|integer|min:0',
            'reps_completed' => 'nullable|integer|min:0',
            'weight'         => 'nullable|numeric|min:0',
        ]);

        $userId = auth()->id();
        $today  = Carbon::today();

        $existingLog = ExerciseLog::where([
            'member_id'      => $userId,
            'workoutplan_id' => $request->workoutplan_id,
            'exercise_id'    => $request->exercise_id,
            'log_date'       => $today,
        ])->first();

        if ($existingLog) {
            return back()->with('error', 'You have already logged this exercise today.');
        }

        ExerciseLog::create([
            'member_id'      => $userId,
            'workoutplan_id' => $request->workoutplan_id,
            'exercise_id'    => $request->exercise_id,
            'log_date'       => $today,
            'sets_completed' => $request->sets_completed,
            'reps_completed' => $request->reps_completed,
            'weight'         => $request->weight,
        ]);

        $dayNumber = $this->getDayNumber($request->workoutplan_id);

        $totalForDay = WorkoutPlanExercise::where('workoutplan_id', $request->workoutplan_id)
            ->where('day_number', $dayNumber)->count();

        $completedToday = ExerciseLog::where('member_id', $userId)
            ->where('workoutplan_id', $request->workoutplan_id)
            ->whereDate('log_date', $today)->count();

        $completion = $totalForDay > 0 ? round(($completedToday / $totalForDay) * 100) : 0;

        $firstLogTime = ExerciseLog::where('member_id', $userId)
            ->where('workoutplan_id', $request->workoutplan_id)
            ->whereDate('log_date', $today)
            ->orderBy('created_at', 'asc')
            ->value('created_at');

        $lastLogTime = ExerciseLog::where('member_id', $userId)
            ->where('workoutplan_id', $request->workoutplan_id)
            ->whereDate('log_date', $today)
            ->orderBy('created_at', 'desc')
            ->value('created_at');

        $workoutDuration = 0;
        if ($firstLogTime && $lastLogTime) {
            $workoutDuration = Carbon::parse($firstLogTime)->diffInMinutes(Carbon::parse($lastLogTime));
        }

        DailyWorkoutLog::updateOrCreate(
            [
                'member_id'      => $userId,
                'workoutplan_id' => $request->workoutplan_id,
                'log_date'       => $today,
            ],
            [
                'completed_exercises'   => $completedToday,
                'total_exercises'       => $totalForDay,
                'completion_percentage' => $completion,
                'workout_duration'      => $workoutDuration,
            ]
        );

        return back()->with('success', 'Exercise log saved successfully.');
    }

    private function getDayNumber($workoutplan_id)
    {
        $plan  = WorkoutPlan::findOrFail($workoutplan_id);
        $today = Carbon::today();
        $start = Carbon::parse($plan->start_date);
        return $start->diffInDays($today) + 1;
    }

    // Store image
    public function storeImage(Request $request)
    {
        $request->validate([
            'workoutplan_id' => 'required|exists:workout_plans,workoutplan_id',
            'photoDate'      => 'required|date',
            'photoFile'      => 'required|image|max:5120',
            'photoNote'      => 'nullable|string|max:500',
        ]);

        try {
            $path = $request->file('photoFile')->store('progress_photos', 'public');

            $photo                 = new ProgressPhoto();
            $photo->workoutplan_id = $request->workoutplan_id;
            $photo->user_id        = Auth::id();
            $photo->photo_date     = $request->photoDate;
            $photo->photo_path     = $path;
            $photo->note           = $request->photoNote;
            $photo->save();

            return redirect()->back()->with('success', 'Progress photo uploaded successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to upload photo. Please try again.');
        }
    }

}
