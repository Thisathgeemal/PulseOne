<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
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

        return back()->with('success', $message);
    }

}
