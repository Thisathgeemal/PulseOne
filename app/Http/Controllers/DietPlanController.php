<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DietPlan;
use App\Models\DietPlanMeal;
use App\Models\HealthAssessment;
use App\Models\Meal;
use App\Models\Notification;
use App\Models\Request as DietRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // Update status for plans that should be active today
        DietPlan::where('member_id', $user->id)
            ->whereDate('start_date', now()->toDateString())
            ->where('status', 'Pending')
            ->update(['status' => 'Active']);

        $plans = DietPlan::with(['dietitian'])
            ->where('member_id', $user->id)
            ->orderBy('start_date', 'desc')
            ->get();

        return view('memberDashboard.dietplanMyplan', compact('plans'));
    }

    // Get Track data
    public function progressTracking()
    {
        $userId = auth()->id();

        $dietPlan = DietPlan::where('member_id', $userId)
            ->where('status', 'Active')
            ->first();

        if (! $dietPlan) {
            return redirect()->back()->with('error', 'No active diet plan found. Start a Diet Plan first.');
        }

        // Daily Progress (adherence)
        $totalDays = $dietPlan->start_date && $dietPlan->end_date
        ? \Carbon\Carbon::parse($dietPlan->start_date)->diffInDays(\Carbon\Carbon::parse($dietPlan->end_date)) + 1
        : 0;
        // Adherence logs removed — treat as zero/empty
        $completedDays = 0;
        $dailyProgress = [
            'completed'  => $completedDays,
            'total'      => $totalDays,
            'percentage' => $totalDays ? round(($completedDays / $totalDays) * 100, 1) : 0,
        ];

        // Weekly Progress
        $totalWeeks = $dietPlan->start_date && $dietPlan->end_date
        ? ceil(\Carbon\Carbon::parse($dietPlan->start_date)->diffInWeeks(\Carbon\Carbon::parse($dietPlan->end_date)) + 1)
        : 0;
        // No adherence logs table; assume no completed weeks
        $completedWeeks = 0;
        $weeklyProgress = [
            'completed'  => $completedWeeks,
            'total'      => $totalWeeks,
            'percentage' => $totalWeeks ? round(($completedWeeks / $totalWeeks) * 100, 1) : 0,
        ];

        // Monthly Progress
        $totalMonths = $dietPlan->start_date && $dietPlan->end_date
        ? ceil(\Carbon\Carbon::parse($dietPlan->start_date)->diffInMonths(\Carbon\Carbon::parse($dietPlan->end_date)) + 1)
        : 0;
        // No adherence logs table; assume no completed months
        $completedMonths = 0;
        $monthlyProgress = [
            'completed'  => $completedMonths,
            'total'      => $totalMonths,
            'percentage' => $totalMonths ? round(($completedMonths / $totalMonths) * 100, 1) : 0,
        ];

        // Progress Photos
        $photos        = $dietPlan->progress_photos()->orderBy('photo_date', 'desc')->get();
        $photoProgress = [
            'monthlyCount'     => $dietPlan->progress_photos()->whereRaw('MONTH(photo_date) = MONTH(NOW())')->count(),
            'weeklyPhotoCount' => $dietPlan->progress_photos()->whereRaw('WEEK(photo_date) = WEEK(NOW())')->count(),
        ];

        $isMember = true;
        return view('memberDashboard.dietplanProgress', compact(
            'dietPlan',
            'photos',
            'photoProgress',
            'isMember',
        ));
    }

    // Member View of a Specific Diet Plan
    public function viewMemberPlan(DietPlan $dietPlan)
    {
        // Ensure member can only view their own plans
        if ($dietPlan->member_id !== auth()->id()) {
            abort(403);
        }

        $dietPlan->load([
            'dietitian',
            'dietPlanMeals.meal',
        ]);

        // Pass both variables for template compatibility
        $plan = $dietPlan;
        return view('memberDashboard.dietplan_view', compact('plan', 'dietPlan'));
    }

    // Cancel the diet plan
    public function cancelMemberPlan(DietPlan $dietPlan)
    {
        // Ensure member can only cancel their own plans
        if ($dietPlan->member_id !== auth()->id()) {
            abort(403);
        }

        $dietPlan->status = 'Cancelled';
        $dietPlan->save();

        return redirect()->back()->with('success', 'Your diet plan has been cancelled successfully.');
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

        // Calculate suggested start and end dates based on timeframe
        $suggestedDates = $this->calculateDatesFromTimeframe($request->timeframe);

        // Get available meals for selection
        $meals = Meal::where('is_active', true)
            ->orderBy('calories_per_serving', 'asc')
            ->get();

        return view('dietitianDashboard.dietplanCreate', compact('request', 'meals', 'healthAssessment', 'nutritionTargets', 'suggestedDates'));
    }

    // Store new diet plan
    public function store(Request $request)
    {
        $request->validate([
            'request_id'             => 'required|exists:requests,request_id',
            'plan_name'              => 'required|string|max:255',
            'description'            => 'nullable|string|max:1000',
            'start_date'             => 'required|date|after_or_equal:today',
            'end_date'               => 'required|date|after:start_date',
            'total_calories_per_day' => 'required|numeric|min:800|max:5000',
            'total_protein_per_day'  => 'required|numeric|min:0',
            'total_carbs_per_day'    => 'required|numeric|min:0',
            'total_fat_per_day'      => 'required|numeric|min:0',
            'selected_meals'         => 'nullable|string',
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
            $startDate = Carbon::parse($latestPlan->end_date)->addDay();

            // Duration in days based on original request
            $durationDays = Carbon::parse($request->start_date)
                ->diffInDays(Carbon::parse($request->end_date));

            // Apply that duration to the new start date
            $endDate = $startDate->copy()->addDays($durationDays);
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

            Notification::create([
                'user_id' => $req->member_id,
                'title'   => 'Diet Plan Created',
                'message' => 'You received a new diet plan from ' . Auth::user()->first_name,
                'type'    => 'Diet Plan',
                'is_read' => false,
            ]);

            DB::commit();

            return redirect()->route('dietitian.dietplan')
                ->with('success', 'Diet plan ' . $request->plan_name . ' created successfully for ' . $req->member->first_name . ' ' . $req->member->last_name . '! The plan includes ' . $this->countTotalMeals($selectedMeals) . ' meals and will be visible to both the member and trainer.');

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

    // Calculate nutrition targets based on health assessment and goals
    private function calculateNutritionTargets($request, $healthAssessment)
    {
        $defaults = [
            'calories' => 2000,
            'protein'  => 150,
            'carbs'    => 250,
            'fat'      => 67,
        ];

        if (! $healthAssessment) {
            return $defaults;
        }

        // Calculate BMR using Mifflin-St Jeor Equation
        $age    = $healthAssessment->age ?? 30;
        $weight = $healthAssessment->weight_kg ?? 70;
        $height = $healthAssessment->height_cm ?? 170;
        $gender = $healthAssessment->gender ?? 'male';

        if ($gender === 'male') {
            $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age) + 5;
        } else {
            $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age) - 161;
        }

        // Activity level multiplier
        $activityMultipliers = [
            'sedentary'         => 1.2,
            'lightly_active'    => 1.375,
            'moderately_active' => 1.55,
            'very_active'       => 1.725,
            'extra_active'      => 1.9,
        ];

        $activityLevel = $healthAssessment->activity_level ?? 'moderately_active';
        $tdee          = $bmr * ($activityMultipliers[$activityLevel] ?? 1.55);

        // Adjust based on goals
        $goal = $request->goal ?? 'maintenance';
        switch ($goal) {
            case 'weight_loss':
                $calories     = $tdee - 500; // 500 cal deficit for 1lb/week loss
                $proteinRatio = 0.3;         // Higher protein for weight loss
                $carbRatio    = 0.35;
                $fatRatio     = 0.35;
                break;
            case 'muscle_gain':
                $calories     = $tdee + 300; // 300 cal surplus for muscle gain
                $proteinRatio = 0.25;
                $carbRatio    = 0.45; // Higher carbs for training
                $fatRatio     = 0.3;
                break;
            case 'athletic_performance':
                $calories     = $tdee + 200;
                $proteinRatio = 0.25;
                $carbRatio    = 0.5; // High carbs for performance
                $fatRatio     = 0.25;
                break;
            default: // maintenance
                $calories     = $tdee;
                $proteinRatio = 0.25;
                $carbRatio    = 0.4;
                $fatRatio     = 0.35;
        }

                                                           // Calculate macros
        $protein = round(($calories * $proteinRatio) / 4); // 4 cal per gram
        $carbs   = round(($calories * $carbRatio) / 4);    // 4 cal per gram
        $fat     = round(($calories * $fatRatio) / 9);     // 9 cal per gram

        return [
            'calories'         => round($calories),
            'protein'          => $protein,
            'carbs'            => $carbs,
            'fat'              => $fat,
            'bmr'              => round($bmr),
            'tdee'             => round($tdee),
            'goal_explanation' => $this->getGoalExplanation($goal),
        ];
    }

    // Calculate start and end dates based on the selected timeframe
    private function calculateDatesFromTimeframe($timeframe)
    {
        $startDate = now()->addDays(1);

        $endDate = match ($timeframe) {
            '1 month' => $startDate->copy()->addMonths(1),
            '3 months' => $startDate->copy()->addMonths(3),
            '6 months' => $startDate->copy()->addMonths(6),
            '1 year' => $startDate->copy()->addYear(),
            default => $startDate->copy()->addMonths(3)
        };

        return [
            'start_date'        => $startDate->format('Y-m-d'),
            'end_date'          => $endDate->format('Y-m-d'),
            'timeframe_display' => match ($timeframe) {
                '1 month'           => '1 Month',
                '3 months'          => '3 Months',
                '6 months'          => '6 Months',
                '1 year'            => '1 Year',
                default             => '3 Months'
            },
        ];
    }

    // Get explanation for diet goals
    private function getGoalExplanation($goal)
    {
        return match ($goal) {
            'weight_loss' => 'Caloric deficit with higher protein to preserve muscle mass',
            'muscle_gain' => 'Caloric surplus with balanced macros to support muscle growth',
            'athletic_performance' => 'Higher carbohydrates to fuel training and performance',
            default => 'Balanced macronutrient distribution for maintaining current weight'
        };
    }

    // Convert selected meals to a schedule
    private function convertMealsToSchedule(array $selectedMeals): array
    {
        $schedule = [];

        if (isset($selectedMeals['breakfast']) && count($selectedMeals['breakfast']) > 0) {
            $schedule['breakfast'] = '08:00';
        }
        if (isset($selectedMeals['lunch']) && count($selectedMeals['lunch']) > 0) {
            $schedule['lunch'] = '12:00';
        }
        if (isset($selectedMeals['dinner']) && count($selectedMeals['dinner']) > 0) {
            $schedule['dinner'] = '19:00';
        }
        if (isset($selectedMeals['snack']) && count($selectedMeals['snack']) > 0) {
            $schedule['snack'] = '15:00';
        }

        return $schedule;
    }

    // Create diet plan meals
    private function createDietPlanMeals(int $dietPlanId, array $selectedMeals): void
    {
        $timeSlots = [
            'breakfast' => '08:00:00',
            'lunch'     => '12:00:00',
            'dinner'    => '19:00:00',
            'snack'     => '15:00:00',
        ];

        foreach ($selectedMeals as $mealType => $meals) {
            foreach ($meals as $meal) {
                DietPlanMeal::create([
                    'dietplan_id' => $dietPlanId,
                    'meal_id'     => $meal['id'] ?? null,
                    'day'         => 1,
                    'time'        => $timeSlots[$mealType] ?? '12:00:00',
                    'quantity'    => 1.0,
                    'calories'    => intval($meal['calories'] ?? 0),
                    'protein'     => intval($meal['protein'] ?? 0),
                    'carbs'       => intval($meal['carbs'] ?? 0),
                    'fat'         => intval($meal['fat'] ?? 0),
                    'notes'       => 'Added via meal plan builder - ' . ($meal['name'] ?? 'Custom meal'),
                ]);
            }
        }
    }

    // Count total meals
    private function countTotalMeals(array $selectedMeals): int
    {
        $total = 0;
        foreach ($selectedMeals as $mealType => $meals) {
            $total += count($meals);
        }
        return $total;
    }

    // Show diet plan details
    public function show(DietPlan $dietPlan)
    {
        // Ensure dietitian can only view their own plans
        if ($dietPlan->dietitian_id !== Auth::id()) {
            abort(403);
        }

        $dietPlan->load(['member', 'dietitian', 'dietPlanMeals.meal']);

        return view('dietitianDashboard.dietplan_view', compact('dietPlan'));
    }

    // Track diet plan progress
    public function track(DietPlan $dietPlan)
    {
        $dietPlan->load(['member', 'dietitian']);
        // Adherence model removed; provide empty collection for the view
        $adherenceLogs = collect([]);
        $isMember      = false;

        // Photos
        $photos        = $dietPlan->progress_photos()->orderBy('photo_date', 'desc')->get();
        $photoProgress = [
            'monthlyCount'     => $dietPlan->progress_photos()->whereRaw('MONTH(photo_date) = MONTH(NOW())')->count(),
            'weeklyPhotoCount' => $dietPlan->progress_photos()->whereRaw('WEEK(photo_date) = WEEK(NOW())')->count(),
        ];

        return view('dietitianDashboard.dietplan_progress', compact('dietPlan', 'adherenceLogs', 'isMember', 'photos', 'photoProgress'));
    }
}
