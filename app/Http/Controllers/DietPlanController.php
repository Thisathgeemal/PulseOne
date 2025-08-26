<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DietPlan;
use App\Models\HealthAssessment;
use App\Models\Notification;
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
            'timeframe'            => 'required|in:1 month,3 months,6 months,1 year',
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

    // Show assigned diet plans
    public function index()
    {
        $user = Auth::user();

        DietPlan::where('dietitian_id', $user->id)
            ->whereDate('start_date', now()->toDateString())
            ->whereNotIn('status', ['Active', 'Completed', 'Cancelled'])
            ->update(['status' => 'Active']);

        // Update expired plans to completed
        DietPlan::where('dietitian_id', $user->id)
            ->where('end_date', '<', now())
            ->whereNotIn('status', ['Completed'])
            ->update(['status' => 'Completed']);

        $plans = DietPlan::with('member')
            ->where('dietitian_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dietitianDashboard.dietPlan', compact('plans'));
    }

    // Show diet plan creation form
    public function create($request_id)
    {
        $request = DietRequest::with('member')
            ->where('request_id', $request_id)
            ->where('type', 'Diet')
            ->where('status', 'Approved')
            ->firstOrFail();

        // Get member's health assessment for pre-filling nutrition targets
        $healthAssessment = HealthAssessment::where('member_id', $request->member_id)
            ->where('is_complete', true)
            ->first();

        // Calculate recommended daily nutrition based on health assessment
        $nutritionTargets = $this->calculateNutritionTargets($request, $healthAssessment);

        // Get available meals for selection
        $meals = Meal::where('is_active', true)
            ->orderBy('description')
            ->get();

        return view('dietitianDashboard.dietplan_create_working', compact('request', 'meals', 'healthAssessment', 'nutritionTargets'));
    }

    // Store new diet plan
    public function store(Request $request)
    {
        $request->validate([
            'request_id'             => 'required|exists:diet_requests,id',
            'plan_name'              => 'required|string|max:255',
            'description'            => 'nullable|string|max:1000',
            'start_date'             => 'required|date|after_or_equal:today',
            'end_date'               => 'required|date|after:start_date',
            'total_calories_per_day' => 'required|numeric|min:800|max:5000',
            'total_protein_per_day'  => 'required|numeric|min:0',
            'total_carbs_per_day'    => 'required|numeric|min:0',
            'total_fat_per_day'      => 'required|numeric|min:0',
            'selected_meals'         => 'nullable|string', // JSON string of selected meals
            'notes'                  => 'nullable|string|max:1000',
        ]);

        $req = DietRequest::findOrFail($request->request_id);

        // Get the latest Active or Pending diet plan for the member
        $latestPlan = DietPlan::where('member_id', $req->member_id)
            ->where('status', 'Active')
            ->orderBy('end_date', 'desc')
            ->first();

        // Determine the status and start date for the new diet plan
        if ($latestPlan) {
            // There is an active plan, so this plan should be pending
            $status    = 'Pending';
            $startDate = Carbon::parse($latestPlan->end_date)->addDay()->format('Y-m-d'); // Start after the existing plan
            $endDate   = Carbon::parse($startDate)->addDays(Carbon::parse($request->end_date)->diffInDays(Carbon::parse($request->start_date)))->format('Y-m-d');
        } else {
            // No active plan, plan can be active immediately
            $status    = 'Active';
            $startDate = $request->start_date;
            $endDate   = $request->end_date;
        }

        try {
            DB::beginTransaction();

            // Parse selected meals
            $selectedMeals = [];
            if ($request->selected_meals) {
                $selectedMeals = json_decode($request->selected_meals, true) ?? [];
            }

            // Create diet plan
            $dietPlan = DietPlan::create([
                'dietitian_id'           => Auth::id(),
                'member_id'              => $req->member_id,
                'request_id'             => $req->request_id,
                'plan_name'              => $request->plan_name,
                'plan_description'       => $request->description,
                'start_date'             => $startDate,
                'end_date'               => $endDate,
                'daily_calories_target'  => $request->total_calories_per_day,
                'daily_protein_target'   => $request->total_protein_per_day,
                'daily_carbs_target'     => $request->total_carbs_per_day,
                'daily_fats_target'      => $request->total_fat_per_day,
                'meals_per_day'          => 3, // Default value
                'dietitian_instructions' => $request->notes,
                'weekly_schedule'        => $this->convertMealsToSchedule($selectedMeals),
                'status'                 => $status,
            ]);

            // Create DietPlanMeal entries for each selected meal
            $this->createDietPlanMeals($dietPlan->dietplan_id, $selectedMeals);

            // Update the diet request status to completed
            $req->update(['status' => 'Completed']);

            DB::commit();

            return redirect()->route('dietitian.dietplan')
                ->with('success', 'Diet plan "' . $request->plan_name . '" created successfully for ' . $req->member->first_name . ' ' . $req->member->last_name . '! The plan includes ' . $this->countTotalMeals($selectedMeals) . ' meals and will be visible to both the member and trainer.');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Diet Plan Creation Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'user_id'      => Auth::id(),
                'stack_trace'  => $e->getTraceAsString(),
            ]);
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create diet plan: ' . $e->getMessage()])
                ->withInput();
        }
    }

}
