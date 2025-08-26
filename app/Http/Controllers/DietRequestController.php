<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Request as DietRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DietRequestController extends Controller
{
    // Show assigned diet requests
    public function index()
    {
        $dietitianId = Auth::id();

        $requests = DietRequest::with('member')
            ->where('dietitian_id', $dietitianId)
            ->where('type', 'Diet')
            ->whereIn('status', ['Pending', 'Approved'])
            ->whereDoesntHave('dietPlan')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dietitianDashboard.request', compact('requests'));
    }

    // Show Diet Request
    public function show(DietRequest $dietRequest)
    {
        if (! Auth::user()->hasRole('Dietitian')) {
            abort(403);
        }

        return view('dietitianDashboard.dietRequest_view', compact('dietRequest'));
    }

    // Assign Diet Request
    public function assign(DietRequest $dietRequest)
    {
        if (! Auth::user()->hasRole('Dietitian')) {
            abort(403);
        }

        $dietRequest->assignToDietitian(Auth::id());

        return redirect()->back()->with('success', 'Diet request assigned successfully.');
    }

    // Approve or Reject
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected',
        ]);

        $req = DietRequest::where('dietitian_id', Auth::id())
            ->where('type', 'Diet')
            ->findOrFail($id);

        $req->status = $request->status;
        $req->save();

        $message = $request->status === 'Approved'
        ? 'Request approved successfully.'
        : 'Request rejected successfully.';

        Notification::create([
            'user_id' => $req->member_id,
            'title'   => 'Diet Request ' . $request->status,
            'message' => 'Your diet plan request has been ' . strtolower($request->status) . '.',
            'type'    => 'Request',
            'is_read' => false,
        ]);

        return back()->with('success', $message);
    }

}
