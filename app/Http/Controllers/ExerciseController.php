<?php
namespace App\Http\Controllers;

use App\Models\Exercise;
use Illuminate\Http\Request;

class ExerciseController extends Controller
{

    // Show all exercise
    public function index(Request $request)
    {
        $muscleGroup = $request->input('muscle_group');

        $query = Exercise::query();

        // Filter by muscle group if selected
        if ($muscleGroup) {
            $query->where('muscle_group', $muscleGroup);
        }

        $exercises       = $query->get();
        $allMuscleGroups = Exercise::select('muscle_group')->distinct()->pluck('muscle_group');

        $muscleColors = [
            'Chest'     => '#EF4444',
            'Legs'      => '#F59E0B',
            'Back'      => '#3B82F6',
            'Shoulders' => '#10B981',
            'Abs'       => '#F472B6',
            'Triceps'   => '#8B5CF6',
            'Biceps'    => '#D946EF',
            'Full Body' => '#6366F1',
        ];

        $muscleIcons = [
            'Chest'     => '🏋️‍♂️',
            'Legs'      => '🦵',
            'Back'      => '🧍‍♂️',
            'Shoulders' => '💪',
            'Abs'       => '🧘',
            'Triceps'   => '🤜',
            'Biceps'    => '💪',
            'Full Body' => '🏃‍♂️',
        ];

        return view('trainerDashboard.exercises', compact('exercises', 'muscleColors', 'muscleIcons', 'allMuscleGroups'));
    }

    // Store a new exercise
    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'required|string',
            'default_sets' => 'required|integer|min:1',
            'default_reps' => 'required|integer|min:1',
            'muscle_group' => 'required|string',
            'video_link'   => 'nullable|url',
        ]);

        Exercise::create([
            'name'         => $request->name,
            'description'  => $request->description,
            'default_sets' => $request->default_sets,
            'default_reps' => $request->default_reps,
            'muscle_group' => $request->muscle_group,
            'video_link'   => $request->video_link,
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
