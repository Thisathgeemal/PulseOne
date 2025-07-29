<?php
namespace App\Http\Controllers;

use App\Models\Exercise;
use Illuminate\Http\Request;

class ExerciseController extends Controller
{

    // Show all exercise
    public function index(Request $request)
    {
        $goal = $request->input('goal');

        $query = Exercise::query();

        if ($goal) {
            $query->where('goal_type', $goal);
        }

        $exercises = $query->get();

        $allGoals = Exercise::select('goal_type')->distinct()->pluck('goal_type');

        $goalColors = [
            'Build Muscle'    => '#EF4444',
            'Weight Loss'     => '#3B82F6',
            'Flexibility'     => '#10B981',
            'General Fitness' => '#F59E0B',
            'custom'          => '#8B5CF6',
        ];

        $muscleIcons = [
            'Chest'     => '🏋️‍♂️',
            'Back'      => '🧍‍♂️',
            'Shoulders' => '💪',
            'Legs'      => '🦵',
            'Arms'      => '🫱',
            'Abs'       => '🧘',
            'Full Body' => '🧍',
        ];

        return view('trainerDashboard.exercises', compact('exercises', 'goalColors', 'muscleIcons', 'allGoals'));
    }

    // Store a new exercise
    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'required|string',
            'default_sets' => 'required|integer|min:1',
            'default_reps' => 'required|integer|min:1',
            'goal_type'    => 'nullable|string',
            'muscle_group' => 'nullable|string',
        ]);

        Exercise::create([
            'name'         => $request->name,
            'description'  => $request->description,
            'default_sets' => $request->default_sets,
            'default_reps' => $request->default_reps,
            'goal_type'    => $request->goal_type,
            'muscle_group' => $request->muscle_group,
        ]);

        return redirect()->back()->with('success', 'Exercise added successfully.');
    }

    // Delete an old exercise
    public function destroy($id)
    {
        $exercise = Exercise::findOrFail($id);
        $exercise->delete();

        return redirect()->back()->with('success', 'Exercise deleted successfully.');
    }

}
