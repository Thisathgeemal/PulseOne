<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Request as WorkoutRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkoutPlanController extends Controller
{
    // Show workout plan page
    public function index()
    {
        $trainers = User::whereHas('roles', function ($query) {
            $query->where('role_name', 'Trainer')
                ->where('user_roles.is_active', 1);
        })->get();

        $userId = Auth::id();

        $requests = WorkoutRequest::with('trainer')
            ->where('type', 'Workout')
            ->where('member_id', $userId)
            ->paginate(5);

        return view('memberDashboard.workoutplan', compact('trainers', 'requests'));
    }

    // Handle workout plan request
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
}
