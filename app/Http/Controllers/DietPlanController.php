<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DietPlan;
use App\Models\HealthAssessment;
use App\Models\Request as DietRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DietPlanController extends Controller
{
    // -----------------------------Member-------------------------------

    // Get request data
    public function request()
    {
        $user = Auth::user();

        // Check if user has completed health assessment
        $healthAssessment = HealthAssessment::where('member_id', $user->id)
            ->where('is_complete', true)
            ->first();

        if (! $healthAssessment || $healthAssessment->needs_update) {
            return redirect()
                ->route('member.health-assessment')
                ->with('error', 'You must complete your health assessment before requesting a workout plan.');
        }

        // Extract fitness goals and weight from health assessment
        $healthGoals        = null;
        $healthGoalsDisplay = null;
        $currentWeight      = null;

        if ($healthAssessment && $healthAssessment->fitness_goals) {
            // Extract current weight from weight_kg field
            $currentWeight = $healthAssessment->weight_kg;

            // Convert health assessment goals to diet plan goals
            $fitnessGoals = $healthAssessment->fitness_goals;

            // Collect all matching diet plan goals
            $matchedGoals = [];
            $goalPriority = [];

            foreach ($fitnessGoals as $goal) {
                $goalLower = strtolower($goal);

                if (in_array($goalLower, ['weight_loss', 'fat_loss']) || strpos($goalLower, 'weight loss') !== false) {
                    $matchedGoals[] = 'Weight Loss';
                    $goalPriority[] = 1; // Highest priority
                } elseif (in_array($goalLower, ['muscle_gain', 'muscle gain', 'strength_training', 'strength training']) || strpos($goalLower, 'muscle') !== false) {
                    $matchedGoals[] = 'Muscle Gain';
                    $goalPriority[] = 2;
                } elseif (in_array($goalLower, ['endurance', 'cardio', 'athletic_performance']) || strpos($goalLower, 'endurance') !== false || strpos($goalLower, 'cardio') !== false) {
                    $matchedGoals[] = 'Endurance/Cardio';
                    $goalPriority[] = 3;
                } elseif (in_array($goalLower, ['general_fitness', 'general fitness', 'maintenance']) || strpos($goalLower, 'general') !== false) {
                    $matchedGoals[] = 'General Fitness';
                    $goalPriority[] = 4;
                }
            }

            // Create combined goal text and determine primary goal value for database
            if (! empty($matchedGoals)) {
                // Remove duplicates while preserving order
                $uniqueGoals = array_unique($matchedGoals);

                // Create display text
                $healthGoalsDisplay = implode(' & ', $uniqueGoals);

                // Determine primary goal value for database (highest priority)
                $primaryGoalIndex = array_search(min($goalPriority), $goalPriority);
                $primaryGoal      = $matchedGoals[$primaryGoalIndex];

                $healthGoals = match ($primaryGoal) {
                    'Weight Loss' => 'weight_loss',
                    'Muscle Gain' => 'muscle_gain',
                    'Endurance/Cardio' => 'athletic_performance',
                    'General Fitness' => 'maintenance',
                    default => 'maintenance'
                };
            } else {
                // If no specific goals matched, use maintenance
                $healthGoals        = 'maintenance';
                $healthGoalsDisplay = 'General Fitness';
            }
        }

        // Fetch diet requests by the member from the diet_requests table
        $requests = DietRequest::with('dietitian')
            ->where('type', 'Diet')
            ->where('member_id', $user->id)
            ->latest()
            ->paginate(7);

        // Fetch available dietitians for the dropdown
        $dietitians = User::whereHas('roles', function ($query) {
            $query->where('role_name', 'Dietitian');
        })->where('is_active', true)->get();

        return view('memberDashboard.dietplanRequest', compact('requests', 'healthGoals', 'healthGoalsDisplay', 'currentWeight', 'dietitians', 'healthAssessment'));
    }

    // Handle diet plan request
    public function requestDietPlan(Request $request)
    {
        $request->validate([
            'dietitian_id'         => 'required|exists:users,id',
            'goal'                 => 'required|string|max:255',
            'timeframe'            => 'required|in:1_month,3_months,6_months,1_year',
            'current_weight'       => 'required|numeric|min:1|max:500',
            'target_weight'        => 'nullable|numeric|min:1|max:500',
            'special_requirements' => 'nullable|string|max:1000',
        ]);

        DietRequest::create([
            'member_id'      => Auth::id(),
            'dietitian_id'   => $request->dietitian_id,
            'goal'           => $request->goal,
            'timeframe'      => $request->timeframe,
            'current_weight' => $request->current_weight,
            'target_weight'  => $request->target_weight,
            'meals_per_day'  => 3,
            'description'    => $request->special_requirements,
            'type'           => 'Diet',
            'status'         => 'Pending',
        ]);

        Notification::create([
            'user_id' => $request->dietitian_id,
            'title'   => 'Diet Plan Request',
            'message' => 'You received a new diet plan request from ' . Auth::user()->first_name,
            'type'    => 'Diet Plan',
            'is_read' => false,
        ]);

        return redirect()
            ->route('member.dietplan.request')
            ->with('success', 'Diet plan request submitted successfully.');
    }

    // Get plan data
    public function myPlan()
    {
        $user = Auth::user();

        return view('memberDashboard.dietplanMyplan');
    }

    // Get Track data
    public function progressTracking()
    {
        $userId = auth()->id();
        $today  = Carbon::today();

        $dietPlan = DietPlan::where('member_id', $userId)
            ->where('status', 'Active')
            ->first();

        if (! $dietPlan) {
            return redirect()->back()->with('error', 'No active diet plan found. Start a Diet Plan first.');
        }

        return view('memberDashboard.dietplanProgress');

    }

    // -----------------------------Dietitian------------------------------
}
