<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Request as WorkoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkoutRequestController extends Controller
{
    // Show assigned workout requests
    public function index()
    {
        $trainerId = Auth::id();

        $requests = WorkoutRequest::with('member')
            ->where('trainer_id', $trainerId)
            ->where('type', 'Workout')
            ->whereIn('status', ['Pending', 'Approved'])
            ->whereDoesntHave('workoutPlan')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('trainerDashboard.request', compact('requests'));
    }

    // Approve or Reject
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected',
        ]);

        $req = WorkoutRequest::where('trainer_id', Auth::id())
            ->where('type', 'Workout')
            ->findOrFail($id);

        $req->status = $request->status;
        $req->save();

        $message = $request->status === 'Approved'
        ? 'Request approved successfully.'
        : 'Request rejected successfully.';

        Notification::create([
            'user_id' => $req->member_id,
            'title'   => 'Workout Plan Request ' . $request->status,
            'message' => 'Your workout plan request has been ' . strtolower($request->status) . '.',
            'type'    => 'Request',
            'is_read' => false,
        ]);

        return back()->with('success', $message);
    }

}
