<?php
namespace App\Http\Controllers;

use App\Models\Meal;
use App\Models\Notification;
use App\Services\HealthAssessmentSyncService;
use App\Services\NutritionApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MealController extends Controller
{
    protected $nutritionService;
    protected $healthSyncService;

    public function __construct(NutritionApiService $nutritionService, HealthAssessmentSyncService $healthSyncService)
    {
        $this->nutritionService  = $nutritionService;
        $this->healthSyncService = $healthSyncService;
    }

    // Display meal library for dietitians
    public function index()
    {
        $mealsQuery = Meal::query();

        if ($search = request('search')) {
            $mealsQuery->where('meal_name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        if ($category = request('category')) {
            $mealsQuery->whereJsonContains('dietary_tags->category', $category);
        }

        if ($mealTime = request('meal_time')) {
            $mealsQuery->whereJsonContains('dietary_tags->meal_times', $mealTime);
        }

        if ($visibility = request('visibility')) {
            $mealsQuery->where('dietary_tags->is_public', $visibility === 'public' ? true : false);
        }

        $meals = $mealsQuery->orderBy('meal_name')->paginate(8)->withQueryString();

        $categories = ['breakfast', 'lunch', 'dinner', 'snack', 'pre_workout', 'post_workout'];
        $mealTimes  = ['breakfast', 'lunch', 'dinner', 'snack'];
        $visibility = ['public', 'private'];

        return view('dietitianDashboard.meals', compact('meals', 'categories', 'mealTimes', 'visibility'));
    }

    // Show form for creating new meal
    public function create()
    {
        return view('dietitianDashboard.mealCreate');
    }

    // Store new meal
    public function store(Request $request)
    {
        $request->validate([
            'meal_name'          => 'required|string|max:255',
            'description'        => 'nullable|string|max:1000',
            'category'           => 'nullable|string|max:100',
            'serving_size'       => 'required|string|max:50',
            'difficulty_level'   => 'required|string|in:easy,medium,hard',
            'ingredients'        => 'nullable|array',
            'ingredients.*'      => 'string|max:255',
            'preparation_method' => 'nullable|string|max:5000',
            'prep_time_minutes'  => 'nullable|integer|min:0',
            'cook_time_minutes'  => 'nullable|integer|min:0',
            'total_time_minutes' => 'nullable|integer|min:0',
            'calories'           => 'required|numeric|min:0|max:5000',
            'protein'            => 'required|numeric|min:0|max:1000',
            'carbs'              => 'required|numeric|min:0|max:1000',
            'fats'               => 'required|numeric|min:0|max:1000',
            'fiber'              => 'nullable|numeric|min:0|max:1000',
            'sugar'              => 'nullable|numeric|min:0|max:1000',
            'sodium'             => 'nullable|numeric|min:0|max:10000',
            'meal_times'         => 'nullable|array',
            'is_public'          => 'nullable|boolean',
        ]);

        // Filter out empty ingredients
        $ingredients = array_filter($request->ingredients ?? [], function ($ingredient) {
            return ! empty(trim($ingredient));
        });

        try {
            $meal = Meal::create([
                'created_by_dietitian_id' => Auth::id(),
                'meal_name'               => $request->meal_name,
                'description'             => $request->description,
                'difficulty_level'        => $request->difficulty_level,
                'calories_per_serving'    => $request->calories,
                'protein_grams'           => $request->protein,
                'carbs_grams'             => $request->carbs,
                'fats_grams'              => $request->fats,
                'fiber_grams'             => $request->fiber ?? 0,
                'sugar_grams'             => $request->sugar ?? 0,
                'sodium_mg'               => $request->sodium ?? 0,
                'serving_size'            => 1.0,                    // Default serving size as number
                'serving_unit'            => $request->serving_size, // Store the text in serving_unit
                'ingredients'             => $ingredients,           // Model will automatically encode as JSON
                'preparation_method'      => $request->preparation_method,
                'prep_time_minutes'       => $request->prep_time_minutes ?? 0,
                'cook_time_minutes'       => $request->cook_time_minutes ?? 0,
                'total_time_minutes'      => $request->total_time_minutes ?? 0,
                'dietary_tags'            => [
                    'category'   => $request->category,
                    'meal_times' => $request->meal_times ?? [],
                    'is_public'  => $request->boolean('is_public'),
                ],
                'is_active'               => true,
            ]);

            Notification::create([
                'user_id' => Auth::id(),
                'title'   => 'Meal Added Successfully',
                'message' => 'You have successfully added the meal "' . ($meal->meal_name ?? $request->meal_name) . '".',
                'type'    => 'Diet Plan',
                'is_read' => false,
            ]);

            return redirect()->route('dietitian.meals')
                ->with('success', 'Meal ' . $request->meal_name . ' has been added to your library successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create meal: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // Display specific meal form
    public function show(Meal $meal)
    {
        // Decode JSON fields for easier access in the view
        $meal->ingredients = is_array($meal->ingredients)
        ? $meal->ingredients
        : array_map('trim', explode(',', $meal->ingredients));

        $meal->dietary_tags = is_array($meal->dietary_tags) ? $meal->dietary_tags : json_decode($meal->dietary_tags, true);

        return view('dietitianDashboard.mealShow', compact('meal'));
    }

    // Show meal edit form
    public function edit(Meal $meal)
    {
        if ($meal->created_by_dietitian_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        return view('dietitianDashboard.mealEdit', compact('meal'));
    }

    // Update meal
    public function update(Request $request, Meal $meal)
    {
        if ($meal->created_by_dietitian_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'meal_name'               => 'required|string|max:255',
            'description'             => 'nullable|string|max:1000',
            'category'                => 'required|string|max:100',
            'difficulty_level'        => 'required|string|in:easy,medium,hard',
            'serving_size'            => 'required|numeric|min:1',
            'preparation_time'        => 'nullable|numeric|min:0',
            'cooking_time'            => 'nullable|numeric|min:0',
            'ingredients'             => 'nullable|array',
            'ingredients.*'           => 'string|max:255',
            'instructions'            => 'nullable|array',
            'instructions.*'          => 'string|max:1000',
            'calories_per_serving'    => 'required|numeric|min:0|max:5000',
            'protein_per_serving'     => 'required|numeric|min:0|max:1000',
            'carbs_per_serving'       => 'required|numeric|min:0|max:1000',
            'fat_per_serving'         => 'required|numeric|min:0|max:1000',
            'fiber_per_serving'       => 'nullable|numeric|min:0|max:1000',
            'sugar_per_serving'       => 'nullable|numeric|min:0|max:1000',
            'sodium_per_serving'      => 'nullable|numeric|min:0|max:10000',
            'dietary_tags.meal_times' => 'nullable|array',
            'is_public'               => 'nullable|boolean',
        ]);

        // Filter out empty ingredients and instructions
        $ingredients = array_filter($request->ingredients ?? [], function ($ingredient) {
            return ! empty(trim($ingredient));
        });

        $instructions = array_filter($request->instructions ?? [], function ($instruction) {
            return ! empty(trim($instruction));
        });

        try {
            DB::beginTransaction();

            $meal->update([
                'meal_name'            => $request->meal_name,
                'description'          => $request->description,
                'calories_per_serving' => $request->calories_per_serving,
                'protein_grams'        => $request->protein_per_serving,
                'carbs_grams'          => $request->carbs_per_serving,
                'fats_grams'           => $request->fat_per_serving,
                'fiber_grams'          => $request->fiber_per_serving ?? 0,
                'sugar_grams'          => $request->sugar_per_serving ?? 0,
                'sodium_mg'            => $request->sodium_per_serving ?? 0,
                'serving_size'         => $request->serving_size,
                'serving_unit'         => 'servings',
                'ingredients'          => $ingredients,
                'preparation_method'   => implode("\n", $instructions), // Store as text for now
                'prep_time_minutes'    => $request->preparation_time ?? 0,
                'cook_time_minutes'    => $request->cooking_time ?? 0,
                'total_time_minutes'   => ($request->preparation_time ?? 0) + ($request->cooking_time ?? 0),
                'difficulty_level'     => $request->difficulty_level,
                'dietary_tags'         => [
                    'category'   => $request->category,
                    'meal_times' => $request->input('dietary_tags.meal_times', []),
                    'is_public'  => $request->boolean('is_public'),
                ],
                'is_active'            => true,
            ]);

            Notification::create([
                'user_id' => Auth::id(),
                'title'   => 'Meal Updated Successfully',
                'message' => 'You have successfully updated the meal ' . $meal->meal_name . '.',
                'type'    => 'Diet Plan',
                'is_read' => false,
            ]);

            DB::commit();

            return redirect()->route('dietitian.meals.show', $meal)
                ->with('success', 'Meal ' . $request->meal_name . ' has been updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update meal: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // Delete meal
    public function destroy(Meal $meal)
    {
        if ($meal->created_by_dietitian_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $meal->delete();

        Notification::create([
            'user_id' => Auth::id(),
            'title'   => 'Meal Deleted Successfully',
            'message' => 'You have successfully deleted the meal "' . $meal->meal_name . '".',
            'type'    => 'Diet Plan',
            'is_read' => false,
        ]);

        return redirect()->route('dietitian.meals')->with('success', 'Meal deleted successfully.');
    }

    // Calculate nutrition from ingredients using the Nutrition API
    public function calculateNutrition(Request $request): JsonResponse
    {
        $request->validate([
            'ingredients'   => 'required|array|min:1',
            'ingredients.*' => 'required|string',
        ]);

        $result = $this->nutritionService->calculateNutrition($request->ingredients);

        return response()->json($result);
    }

    // Get meals filtered for a specific member based on their health assessment
    public function getMealsForMember(Request $request): JsonResponse
    {
        $request->validate([
            'member_id' => 'required|integer|exists:users,id',
        ]);

        $meals = $this->healthSyncService->filterMealsForMember($request->member_id);

        return response()->json([
            'success' => true,
            'meals'   => $meals,
            'profile' => $this->healthSyncService->getMemberDietaryProfile($request->member_id),
        ]);
    }

    // Suggest a meal plan for a member based on their health assessment
    public function suggestMealPlan(Request $request): JsonResponse
    {
        $request->validate([
            'member_id' => 'required|integer|exists:users,id',
            'days'      => 'nullable|integer|min:1|max:30',
        ]);

        $days     = $request->days ?? 7;
        $mealPlan = $this->healthSyncService->suggestMealPlan($request->member_id, $days);

        return response()->json([
            'success'        => true,
            'meal_plan'      => $mealPlan,
            'member_profile' => $this->healthSyncService->getMemberDietaryProfile($request->member_id),
        ]);
    }
}
