<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DietPlan;
use App\Models\Request as DietRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DietPlanController extends Controller
{
    // Controller Method for Diet Plan (Dietitian and Member Views)
    public function index(Request $request)
    {
        $user = Auth::user();

        // Dietitian View
        if ($request->routeIs('dietitian.dietplan')) {
            DietPlan::where('dietitian_id', $user->id)
                ->where('end_date', '<', now())
                ->whereNotIn('status', ['Completed'])
                ->update(['status' => 'Completed']);

            // Get plans with member details
            $plans = DietPlan::with('member')
                ->where('dietitian_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return view('dietitianDashboard.dietplan', compact('plans'));
        }

        // Member View
        if ($request->routeIs('member.dietplan')) {
            DietPlan::where('member_id', $user->id)
                ->where('end_date', '<', now())
                ->whereNotIn('status', ['Completed'])
                ->update(['status' => 'Completed']);

            // Get active dietitians
            $dietitians = User::whereHas('roles', function ($query) {
                $query->where('role_name', 'Dietitian')
                    ->where('user_roles.is_active', 1);
            })->get();

            // Get diet requests made by member
            $requests = DietRequest::with('dietitian')
                ->where('type', 'Diet')
                ->where('member_id', $user->id)
                ->latest()
                ->paginate(5);

            // Get all diet plans of this member
            $plans = DietPlan::with('dietitian')
                ->where('member_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return view('memberDashboard.dietplan', compact('dietitians', 'requests', 'plans'));
        }

        abort(403, 'Unauthorized access to diet plan page.');
    }

    // Handle diet plan request
    public function requestDietPlan(Request $request)
    {
        $request->validate([
            'dietitian_id'         => 'required|exists:users,id',
            'plan_dis'             => 'required|string|max:255',
            'height'               => 'nullable|numeric|min:0',
            'weight'               => 'nullable|numeric|min:0',
            'target_weight'        => 'nullable|numeric|min:0',
            'preferred_start_date' => 'nullable|date|after_or_equal:today',
        ]);

        DietRequest::create([
            'member_id'            => Auth::id(),
            'dietitian_id'         => $request->dietitian_id,
            'description'          => $request->plan_dis,
            'height'               => $request->height,
            'weight'               => $request->weight,
            'target_weight'        => $request->target_weight,
            'preferred_start_date' => $request->preferred_start_date,
            'type'                 => 'Diet',
            'status'               => 'Pending',
        ]);

        return redirect()->route('member.dietplan')->with('success', 'Diet plan request submitted successfully.');
    }
}
