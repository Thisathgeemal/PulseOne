<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Request as DietPlanRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DietPlanController extends Controller
{
    // Show workout plan page
    public function index()
    {
        $dietitian = User::whereHas('roles', function ($query) {
            $query->where('role_name', 'Dietitian')
                ->where('user_roles.is_active', 1);
        })->get();

        $userId = Auth::id();

        $requests = DietPlanRequest::with('dietitian')
            ->where('type', 'Diet')
            ->where('member_id', $userId)
            ->paginate(5);

        return view('memberDashboard.dietplan', compact('dietitian', 'requests'));
    }

    // Handle diet plan request
    public function requestDietPlan(Request $request)
    {
        $request->validate([
            'dietitian_id' => 'required|exists:users,id',
            'plan_dis'     => 'required|string|max:255',
        ]);

        DietPlanRequest::create([
            'member_id'    => Auth::id(),
            'dietitian_id' => $request->dietitian_id,
            'description'  => $request->plan_dis,
            'type'         => 'Diet',
            'status'       => 'Pending',
        ]);

        return redirect()->route('member.dietplan')->with('success', 'Diet plan request submitted successfully.');
    }
}
