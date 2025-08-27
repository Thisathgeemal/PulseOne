<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DietPlan;
use App\Models\DietPlanMeal;
use App\Models\DietProgressPhoto;
use App\Models\HealthAssessment;
use App\Models\Meal;
use App\Models\MealCompliance;
use App\Models\Notification;
use App\Models\Request as DietRequest;
use App\Models\User;
use App\Models\WeightLog;
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

    // Show diet progress dashboard
    public function progressTracking($dietPlan = null)
    {
        // Get the diet plan
        $dietPlan = $dietPlan
        ? DietPlan::findOrFail($dietPlan)
        : DietPlan::where('member_id', Auth::id())->latest()->first();

        if (! $dietPlan) {
            abort(404, 'No diet plan found.');
        }

        $memberId = $dietPlan->member_id;

        // Daily Meal Compliance
        $dailyData = $this->getDailyMealCompliance($dietPlan->dietplan_id);

        // Weekly Weight Tracking
        $weeklyWeightData = $this->getWeeklyWeight($dietPlan->dietplan_id);

        // Progress Photos
        $photosData = $this->getProgressPhotos($memberId);

        // Add Photo Progress Data (weekly and monthly percentage)
        $photoProgressData = $this->getPhotoProgressData($memberId);

        return view('memberdashboard.dietplanProgress', array_merge(
            compact('dietPlan', 'photoProgressData'),
            $dailyData,
            $weeklyWeightData,
            $photosData,
        ));
    }

    // Get Daily Meal Compliance
    private function getDailyMealCompliance($dietPlanId)
    {
        $userId   = Auth::id();
        $dietPlan = DietPlan::findOrFail($dietPlanId);

        $defaultMeals = ['breakfast', 'lunch', 'dinner', 'snacks']; // Or from diet plan
        $record       = MealCompliance::where('member_id', $userId)
            ->where('dietplan_id', $dietPlanId)
            ->whereDate('log_date', now())
            ->first();

        $dailyMeals = [];
        foreach ($defaultMeals as $meal) {
            // Make sure value is 1 (completed) or 0 (skipped)
            $dailyMeals[$meal] = $record && ! empty($record->meals_completed[$meal]) ? 1 : 0;
        }

        $completedCount = array_sum($dailyMeals); // sum of 1's = completed meals

        // Calculate percentage
        $percentage = count($dailyMeals) > 0
        ? round(($completedCount / count($dailyMeals)) * 100)
        : 0;

        return [
            'dailyMeals'                => $dailyMeals,
            'dailyCompliancePercentage' => $percentage,
        ];
    }

    // Get Weekly Weight
    private function getWeeklyWeight($dietPlanId)
    {
        $memberId  = Auth::id();
        $dietPlan  = DietPlan::findOrFail($dietPlanId);
        $requestId = $dietPlan->request_id;

        // Access the Request table to get target and starting/current weight
        $request        = DietRequest::find($requestId);
        $targetWeight   = $request->target_weight ?? 0;
        $startingWeight = $request->current_weight ?? 0; // starting weight from request

        // Get start and end of the week
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek   = Carbon::now()->endOfWeek();

        // Get all weight logs for this member this week
        $weightLogs = WeightLog::where('member_id', $memberId)
            ->whereBetween('log_date', [$startOfWeek, $endOfWeek])
            ->orderBy('log_date')
            ->get();

        // Current weight is the last logged weight; fallback to starting weight if none
        $currentWeight = $weightLogs->last()->weight ?? $startingWeight;

        // Calculate progress percentage with overshoot handling
        if ($startingWeight > $targetWeight) {
            // Weight loss goal
            if ($currentWeight >= $targetWeight) {
                // Normal progress toward target
                $progressPercentage = ($startingWeight - $currentWeight) / ($startingWeight - $targetWeight) * 100;
            } else {
                // Overshoot: progress decreases after target
                $progressPercentage = 100 - (($targetWeight - $currentWeight) / ($startingWeight - $targetWeight) * 100);
            }
        } else {
            // Weight gain goal
            if ($currentWeight <= $targetWeight) {
                // Normal progress toward target
                $progressPercentage = ($currentWeight - $startingWeight) / ($targetWeight - $startingWeight) * 100;
            } else {
                // Overshoot: progress decreases after target
                $progressPercentage = 100 - (($currentWeight - $targetWeight) / ($targetWeight - $startingWeight) * 100);
            }
        }

        // Prevent invalid values
        if (! is_finite($progressPercentage)) {
            $progressPercentage = 0;
        }

        // Progress bar capped at 0% - 100%
        $progressBarPercentage = min(max(round($progressPercentage), 0), 100);

        return compact(
            'weightLogs',
            'currentWeight',
            'startingWeight',
            'targetWeight',
            'progressPercentage',    // Actual number (can exceed 100 or go below 0)
            'progressBarPercentage', // Capped for visual bar
            'startOfWeek',
            'endOfWeek'
        );
    }

    // Get Progress Photos
    private function getProgressPhotos($memberId)
    {
        $photos = DietProgressPhoto::where('user_id', $memberId)
            ->whereMonth('photo_date', Carbon::now()->month)
            ->orderBy('photo_date', 'desc')
            ->get();

        return compact('photos');
    }

    // Get Photo Progress Data
    protected function getPhotoProgressData($userId)
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek   = Carbon::now()->endOfWeek();

        // Weekly
        $weeklyPhotoCount = DietProgressPhoto::where('user_id', $userId)
            ->whereBetween('photo_date', [$startOfWeek, $endOfWeek])
            ->count();

        $weeklyTarget   = 2;
        $weeklyProgress = $weeklyTarget > 0
        ? min(round(($weeklyPhotoCount / $weeklyTarget) * 100), 100) // cap at 100%
        : 0;

        // Monthly
        $photoCountThisMonth = DietProgressPhoto::where('user_id', $userId)
            ->whereMonth('photo_date', now()->month)
            ->whereYear('photo_date', now()->year)
            ->count();

        $monthlyTarget   = 8;
        $monthlyProgress = $monthlyTarget > 0
        ? min(round(($photoCountThisMonth / $monthlyTarget) * 100), 100) // cap at 100%
        : 0;

        return [
            'weeklyPhotoCount'       => $weeklyPhotoCount,
            'weeklyTarget'           => $weeklyTarget,
            'weeklyPhotoPercentage'  => $weeklyProgress, // pass to frontend
            'monthlyPhotoCount'      => $photoCountThisMonth,
            'monthlyTarget'          => $monthlyTarget,
            'monthlyPhotoPercentage' => $monthlyProgress, // pass to frontend
        ];
    }

    // Get Weight Chart Data
    public function getWeightChartData($dietPlanId)
    {
        $dietPlan = DietPlan::findOrFail($dietPlanId);
        $memberId = $dietPlan->member_id;
        $request  = DietRequest::find($dietPlan->request_id);

        $startingWeight = $request->current_weight ?? 0;
        $targetWeight   = $request->target_weight ?? 0;

        $startOfWeek = now()->startOfWeek(); // Monday
        $endOfWeek   = now()->endOfWeek();   // Sunday

        // Filter weight logs for current week
        $weightLogs = WeightLog::where('member_id', $memberId)
            ->where('dietplan_id', $dietPlanId)
            ->whereBetween('log_date', [$startOfWeek, $endOfWeek])
            ->orderBy('log_date')
            ->get();

        $labels = []; // dates
        $data   = []; // weight values

        foreach ($weightLogs as $log) {
            $labels[] = \Carbon\Carbon::parse($log->log_date)->format('d M'); // e.g., 27 Aug
            $data[]   = $log->weight;
        }

        return response()->json([
            'labels'         => $labels,
            'data'           => $data,
            'startingWeight' => $startingWeight,
            'targetWeight'   => $targetWeight,
        ]);
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
        $memberId = $dietPlan->member_id;

        // --- Daily Meal Compliance ---
        $defaultMeals = ['breakfast', 'lunch', 'dinner', 'snacks'];
        $record       = MealCompliance::where('member_id', $memberId)
            ->where('dietplan_id', $dietPlan->dietplan_id)
            ->whereDate('log_date', now())
            ->first();

        $dailyMeals = [];
        foreach ($defaultMeals as $meal) {
            $dailyMeals[$meal] = $record && ! empty($record->meals_completed[$meal]) ? 1 : 0;
        }
        $dailyCompliancePercentage = count($dailyMeals) > 0
        ? round(array_sum($dailyMeals) / count($dailyMeals) * 100)
        : 0;

        // --- Weekly Weight Tracking ---
        $request        = DietRequest::find($dietPlan->request_id);
        $startingWeight = $request->current_weight ?? 0;
        $targetWeight   = $request->target_weight ?? 0;

        $startOfWeek = now()->startOfWeek();
        $endOfWeek   = now()->endOfWeek();

        $weightLogs = WeightLog::where('member_id', $memberId)
            ->where('dietplan_id', $dietPlan->dietplan_id)
            ->whereBetween('log_date', [$startOfWeek, $endOfWeek])
            ->orderBy('log_date')
            ->get();

        $currentWeight = $weightLogs->last()->weight ?? $startingWeight;

        // Progress bar calculation
        if ($startingWeight > $targetWeight) {
            $progressPercentage = $currentWeight >= $targetWeight
            ? ($startingWeight - $currentWeight) / ($startingWeight - $targetWeight) * 100
            : 100 - (($targetWeight - $currentWeight) / ($startingWeight - $targetWeight) * 100);
        } else {
            $progressPercentage = $currentWeight <= $targetWeight
            ? ($currentWeight - $startingWeight) / ($targetWeight - $startingWeight) * 100
            : 100 - (($currentWeight - $targetWeight) / ($targetWeight - $startingWeight) * 100);
        }
        $progressBarPercentage = min(max(round($progressPercentage), 0), 100);

        // --- Progress Photos ---
        $photos = DietProgressPhoto::where('user_id', $memberId)
            ->whereMonth('photo_date', now()->month)
            ->orderBy('photo_date', 'desc')
            ->get();

        // Weekly and monthly photo progress
        $weeklyPhotoCount = DietProgressPhoto::where('user_id', $memberId)
            ->whereBetween('photo_date', [$startOfWeek, $endOfWeek])
            ->count();
        $weeklyTarget          = 2;
        $weeklyPhotoPercentage = $weeklyTarget > 0
        ? min(round(($weeklyPhotoCount / $weeklyTarget) * 100), 100)
        : 0;

        $monthlyPhotoCount = DietProgressPhoto::where('user_id', $memberId)
            ->whereMonth('photo_date', now()->month)
            ->whereYear('photo_date', now()->year)
            ->count();
        $monthlyTarget          = 8;
        $monthlyPhotoPercentage = $monthlyTarget > 0
        ? min(round(($monthlyPhotoCount / $monthlyTarget) * 100), 100)
        : 0;

        $photoProgressData = [
            'weeklyPhotoCount'       => $weeklyPhotoCount,
            'weeklyTarget'           => $weeklyTarget,
            'weeklyPhotoPercentage'  => $weeklyPhotoPercentage,
            'monthlyPhotoCount'      => $monthlyPhotoCount,
            'monthlyTarget'          => $monthlyTarget,
            'monthlyPhotoPercentage' => $monthlyPhotoPercentage,
        ];

        return view('dietitianDashboard.dietplan_progress', compact(
            'dietPlan',
            'dailyMeals',
            'dailyCompliancePercentage',
            'weightLogs',
            'currentWeight',
            'startingWeight',
            'targetWeight',
            'progressPercentage',
            'progressBarPercentage',
            'startOfWeek',
            'endOfWeek',
            'photos',
            'photoProgressData'
        ));
    }

}
