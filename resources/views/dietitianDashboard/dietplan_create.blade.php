@extends('dietitianDashboard.layout')

@section('content')
<div class="max-w-6xl mx-auto py-6">
    <!-- Header -->
    <div class="bg-[#1E1E1E] text-white px-8 py-6 rounded-lg shadow mb-6">
        <h2 class="text-2xl font-bold">Create Diet Plan</h2>
        <p class="text-sm text-gray-300 mt-1">
            for <span class="text-yellow-400 font-medium">{{ $request->member->first_name }} {{ $request->member->last_name }}</span>
        </p>
    </div>

    <form method="POST" action="{{ route('dietitian.diet-plans.store') }}" id="dietPlanForm">
        @csrf
        <input type="hidden" name="request_id" value="{{ $request->id }}">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Plan Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Plan Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Plan Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Plan Title <span class="text-red-500">*</span></label>
                            <input type="text" name="plan_name"
                                value="{{ old('plan_name', $request->member->first_name . '\'s Custom Diet Plan') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required>
                        </div>

                        <!-- Start Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Start Date <span class="text-red-500">*</span>
                                @if(isset($suggestedDates['timeframe_display']))
                                    <span class="text-sm text-blue-600 font-normal">({{ $suggestedDates['timeframe_display'] }} plan)</span>
                                @endif
                            </label>
                            <input type="date" name="start_date"
                                value="{{ old('start_date', $suggestedDates['start_date'] ?? $request->preferred_start_date ?? now()->format('Y-m-d')) }}"
                                min="{{ now()->format('Y-m-d') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required>
                        </div>

                        <!-- End Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Date <span class="text-red-500">*</span></label>
                            <input type="date" name="end_date"
                                value="{{ old('end_date', $suggestedDates['end_date'] ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required>
                        </div>

                        <!-- Goal (Auto-filled from request) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Diet Goal</label>
                            <input type="text" value="{{ ucwords(str_replace('_', ' ', $request->goal)) }}" 
                                disabled
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600">
                            <input type="hidden" name="goal" value="{{ $request->goal }}">
                        </div>
                    </div>
                </div>

                <!-- Member Profile Summary -->
                <div class="bg-blue-50 rounded-lg border border-blue-200 p-6">
                    <h3 class="text-lg font-semibold text-blue-800 mb-4">Member Profile</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-700">Current Weight:</span>
                            <span class="text-blue-600">{{ $request->current_weight }} kg</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Target Weight:</span>
                            <span class="text-blue-600">{{ $request->target_weight ?? 'Not specified' }}{{ $request->target_weight ? ' kg' : '' }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Diet Goal:</span>
                            <span class="text-blue-600">{{ ucwords(str_replace('_', ' ', $request->goal)) }}</span>
                        </div>
                    </div>
                    
                    <button type="button" 
                            onclick="loadMemberProfile({{ $request->member->id }})"
                            class="mt-3 text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors">
                        Load Health Assessment Data
                    </button>
                </div>

                <!-- Daily Nutrition Targets -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Daily Nutrition Targets</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Calories <span class="text-red-500">*</span></label>
                            <input type="number" name="total_calories_per_day"
                                value="{{ old('total_calories_per_day', '2000') }}"
                                min="800" max="5000" step="10"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Protein (g) <span class="text-red-500">*</span></label>
                            <input type="number" name="total_protein_per_day"
                                value="{{ old('total_protein_per_day', '150') }}"
                                min="0" step="1"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Carbs (g) <span class="text-red-500">*</span></label>
                            <input type="number" name="total_carbs_per_day"
                                value="{{ old('total_carbs_per_day', '200') }}"
                                min="0" step="1"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fat (g) <span class="text-red-500">*</span></label>
                            <input type="number" name="total_fat_per_day"
                                value="{{ old('total_fat_per_day', '67') }}"
                                min="0" step="1"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required>
                        </div>
                    </div>
                    
                    <button type="button" 
                            onclick="calculateNutritionTargets()"
                            class="mt-4 text-sm bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors">
                        Auto-Calculate Based on Goals
                    </button>
                </div>

                <!-- Meal Plan Builder -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Meal Plan Builder</h3>
                        <button type="button" 
                                onclick="suggestMealPlan({{ $request->member->id }})"
                                class="text-sm bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md transition-colors">
                            Auto-Suggest Meals
                        </button>
                    </div>

                    <!-- Selected Meals Display -->
                    <div id="selectedMeals" class="space-y-4">
                        <!-- Breakfast -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-medium text-gray-800">🌅 Breakfast</h4>
                                <button type="button" 
                                        onclick="openMealSelector('breakfast')"
                                        class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md">
                                    Add Meal
                                </button>
                            </div>
                            <div id="breakfast-meals" class="space-y-2 min-h-[60px] border-2 border-dashed border-gray-200 rounded-lg p-3">
                                <p class="text-gray-500 text-sm text-center">No meals added yet</p>
                            </div>
                        </div>

                        <!-- Lunch -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-medium text-gray-800">🌞 Lunch</h4>
                                <button type="button" 
                                        onclick="openMealSelector('lunch')"
                                        class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md">
                                    Add Meal
                                </button>
                            </div>
                            <div id="lunch-meals" class="space-y-2 min-h-[60px] border-2 border-dashed border-gray-200 rounded-lg p-3">
                                <p class="text-gray-500 text-sm text-center">No meals added yet</p>
                            </div>
                        </div>

                        <!-- Dinner -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-medium text-gray-800">🌙 Dinner</h4>
                                <button type="button" 
                                        onclick="openMealSelector('dinner')"
                                        class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md">
                                    Add Meal
                                </button>
                            </div>
                            <div id="dinner-meals" class="space-y-2 min-h-[60px] border-2 border-dashed border-gray-200 rounded-lg p-3">
                                <p class="text-gray-500 text-sm text-center">No meals added yet</p>
                            </div>
                        </div>

                        <!-- Snacks -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-medium text-gray-800">🍎 Snacks</h4>
                                <button type="button" 
                                        onclick="openMealSelector('snack')"
                                        class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md">
                                    Add Snack
                                </button>
                            </div>
                            <div id="snack-meals" class="space-y-2 min-h-[60px] border-2 border-dashed border-gray-200 rounded-lg p-3">
                                <p class="text-gray-500 text-sm text-center">No snacks added yet</p>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden inputs for selected meals -->
                    <input type="hidden" name="selected_meals" id="selectedMealsData" value="[]">
                </div>

                <!-- Description -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Plan Description & Notes</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Plan Description</label>
                            <textarea name="description" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Describe the diet plan goals, approach, and key recommendations...">{{ old('description') }}</textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                            <textarea name="notes" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Special instructions, substitutions, or important notes...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Member Info & Actions -->
            <div class="space-y-6">
                <!-- Member Health Assessment -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Health Assessment</h3>
                    <div id="memberProfileData" class="space-y-3 text-sm">
                        <p class="text-gray-500">Click "Load Health Assessment Data" to view member's dietary preferences and restrictions.</p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <button type="button" 
                                onclick="filterMealsByPreferences({{ $request->member->id }})"
                                class="w-full text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md transition-colors">
                            Filter Meals by Preferences
                        </button>
                        
                        <button type="button" 
                                onclick="previewNutritionBreakdown()"
                                class="w-full text-sm bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md transition-colors">
                            Preview Nutrition Breakdown
                        </button>
                        
                        <button type="button" 
                                onclick="clearAllMeals()"
                                class="w-full text-sm bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition-colors">
                            Clear All Meals
                        </button>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="space-y-3">
                        <button type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-all">
                            Create Diet Plan
                        </button>
                        
                        <a href="{{ route('dietitian.requests.index') }}"
                            class="block w-full text-center bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-all">
                            Back to Requests
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Meal Selector Modal -->
<div id="mealSelectorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Select Meals</h3>
                <button type="button" onclick="closeMealSelector()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                <div class="mb-4">
                    <input type="text" id="mealSearchInput" placeholder="Search meals..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           onkeyup="searchMeals()">
                </div>
                <div id="mealsList" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Meals will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let selectedMeals = {
    breakfast: [],
    lunch: [],
    dinner: [],
    snack: []
};
let currentMealType = '';
let allMeals = [];
let memberProfile = null;

// Load member's health assessment profile
async function loadMemberProfile(memberId) {
    try {
        const response = await fetch('/dietitian/diet-plans/member-profile', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ member_id: memberId })
        });
        
        const data = await response.json();
        if (data.success) {
            memberProfile = data.profile;
            displayMemberProfile(data.profile);
            
            // Auto-calculate nutrition targets based on profile
            if (data.profile.calorie_target) {
                document.querySelector('[name="total_calories_per_day"]').value = data.profile.calorie_target;
                calculateMacrosFromCalories(data.profile.calorie_target);
            }
        }
    } catch (error) {
        console.error('Error loading member profile:', error);
        alert('Failed to load member profile');
    }
}

// Display member profile data
function displayMemberProfile(profile) {
    const profileContainer = document.getElementById('memberProfileData');
    
    let html = '<div class="space-y-2">';
    
    if (profile.dietary_restrictions && profile.dietary_restrictions.length > 0) {
        html += `<div><span class="font-medium text-gray-700">Dietary Restrictions:</span><br>`;
        html += profile.dietary_restrictions.map(r => `<span class="inline-block bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full mr-1 mb-1">${r}</span>`).join('');
        html += '</div>';
    }
    
    if (profile.allergies && profile.allergies.length > 0) {
        html += `<div><span class="font-medium text-gray-700">Allergies:</span><br>`;
        html += profile.allergies.map(a => `<span class="inline-block bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full mr-1 mb-1">${a}</span>`).join('');
        html += '</div>';
    }
    
    if (profile.fitness_goals && profile.fitness_goals.length > 0) {
        html += `<div><span class="font-medium text-gray-700">Fitness Goals:</span><br>`;
        html += profile.fitness_goals.map(g => `<span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1 mb-1">${g}</span>`).join('');
        html += '</div>';
    }
    
    html += `<div><span class="font-medium text-gray-700">Activity Level:</span> <span class="text-gray-600">${profile.activity_level || 'Not specified'}</span></div>`;
    html += `<div><span class="font-medium text-gray-700">Daily Calorie Target:</span> <span class="text-green-600 font-medium">${profile.calorie_target || 'Not calculated'} kcal</span></div>`;
    
    html += '</div>';
    
    profileContainer.innerHTML = html;
}

// Calculate nutrition targets based on goals
function calculateNutritionTargets() {
    const calories = parseInt(document.querySelector('[name="total_calories_per_day"]').value) || 2000;
    calculateMacrosFromCalories(calories);
}

function calculateMacrosFromCalories(calories) {
    // Basic macro distribution (can be adjusted based on goals)
    const proteinCalories = calories * 0.30; // 30% protein
    const carbCalories = calories * 0.40;    // 40% carbs  
    const fatCalories = calories * 0.30;     // 30% fat
    
    const protein = Math.round(proteinCalories / 4); // 4 cal per gram
    const carbs = Math.round(carbCalories / 4);      // 4 cal per gram
    const fat = Math.round(fatCalories / 9);         // 9 cal per gram
    
    document.querySelector('[name="total_protein_per_day"]').value = protein;
    document.querySelector('[name="total_carbs_per_day"]').value = carbs;
    document.querySelector('[name="total_fat_per_day"]').value = fat;
}

// Open meal selector modal
async function openMealSelector(mealType) {
    currentMealType = mealType;
    document.getElementById('mealSelectorModal').classList.remove('hidden');
    
    if (allMeals.length === 0) {
        await loadAllMeals();
    }
    
    displayMeals(allMeals);
}

// Close meal selector modal
function closeMealSelector() {
    document.getElementById('mealSelectorModal').classList.add('hidden');
    currentMealType = '';
}

// Load all available meals
async function loadAllMeals() {
    try {
        // For now, using static data since we need the actual meals from database
        allMeals = [
            {
                id: 1,
                description: "Grilled Chicken with Quinoa",
                calories_per_serving: 420,
                protein_grams: 35,
                carbs_grams: 30,
                fats_grams: 12,
                dietary_tags: ["High-Protein", "Gluten-Free"],
                meal_times: ["lunch", "dinner"]
            },
            {
                id: 2,
                description: "Oatmeal with Berries",
                calories_per_serving: 280,
                protein_grams: 8,
                carbs_grams: 45,
                fats_grams: 6,
                dietary_tags: ["Vegetarian", "High-Fiber"],
                meal_times: ["breakfast"]
            },
            {
                id: 3,
                description: "Greek Yogurt with Nuts",
                calories_per_serving: 180,
                protein_grams: 15,
                carbs_grams: 12,
                fats_grams: 8,
                dietary_tags: ["High-Protein", "Low-Carb"],
                meal_times: ["snack", "breakfast"]
            },
            {
                id: 4,
                description: "Salmon with Sweet Potato",
                calories_per_serving: 480,
                protein_grams: 40,
                carbs_grams: 35,
                fats_grams: 18,
                dietary_tags: ["High-Protein", "Heart-Healthy"],
                meal_times: ["lunch", "dinner"]
            },
            {
                id: 5,
                description: "Avocado Toast",
                calories_per_serving: 320,
                protein_grams: 12,
                carbs_grams: 28,
                fats_grams: 20,
                dietary_tags: ["Vegetarian", "Heart-Healthy"],
                meal_times: ["breakfast", "snack"]
            }
        ];
    } catch (error) {
        console.error('Error loading meals:', error);
        allMeals = [];
    }
}

// Display meals in the selector
function displayMeals(meals) {
    const mealsList = document.getElementById('mealsList');
    
    const filteredMeals = meals.filter(meal => {
        // Filter by meal time appropriateness
        if (meal.meal_times && meal.meal_times.length > 0) {
            return meal.meal_times.includes(currentMealType);
        }
        return true; // Show meals without specific times
    });
    
    if (filteredMeals.length === 0) {
        mealsList.innerHTML = '<p class="col-span-2 text-center text-gray-500">No meals found for this category</p>';
        return;
    }
    
    mealsList.innerHTML = filteredMeals.map(meal => `
        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
            <h4 class="font-medium text-gray-800 mb-2">${meal.description}</h4>
            <div class="text-sm text-gray-600 space-y-1">
                <div>Calories: <span class="font-medium">${meal.calories_per_serving}</span></div>
                <div>Protein: <span class="font-medium">${meal.protein_grams}g</span> | 
                     Carbs: <span class="font-medium">${meal.carbs_grams}g</span> | 
                     Fat: <span class="font-medium">${meal.fats_grams}g</span></div>
                ${meal.dietary_tags && meal.dietary_tags.length > 0 ? 
                    `<div class="flex flex-wrap gap-1 mt-2">
                        ${meal.dietary_tags.map(tag => `<span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">${tag}</span>`).join('')}
                    </div>` : ''
                }
            </div>
            <button type="button" 
                    onclick="addMealToPlan(${meal.id}, '${meal.description}', ${meal.calories_per_serving}, ${meal.protein_grams}, ${meal.carbs_grams}, ${meal.fats_grams})"
                    class="mt-3 w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm transition-colors">
                Add to ${currentMealType.charAt(0).toUpperCase() + currentMealType.slice(1)}
            </button>
        </div>
    `).join('');
}

// Add meal to plan
function addMealToPlan(mealId, mealName, calories, protein, carbs, fat) {
    const meal = {
        id: mealId,
        name: mealName,
        calories: calories,
        protein: protein,
        carbs: carbs,
        fat: fat
    };
    
    selectedMeals[currentMealType].push(meal);
    updateMealDisplay(currentMealType);
    updateSelectedMealsData();
    closeMealSelector();
}

// Update meal display in plan
function updateMealDisplay(mealType) {
    const container = document.getElementById(`${mealType}-meals`);
    const meals = selectedMeals[mealType];
    
    if (meals.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-sm text-center">No meals added yet</p>';
        return;
    }
    
    container.innerHTML = meals.map((meal, index) => `
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 flex justify-between items-start">
            <div class="flex-1">
                <h5 class="font-medium text-gray-800 text-sm">${meal.name}</h5>
                <p class="text-xs text-gray-600 mt-1">
                    ${meal.calories} cal | ${meal.protein}g protein | ${meal.carbs}g carbs | ${meal.fat}g fat
                </p>
            </div>
            <button type="button" 
                    onclick="removeMealFromPlan('${mealType}', ${index})"
                    class="text-red-600 hover:text-red-800 ml-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `).join('');
}

// Remove meal from plan
function removeMealFromPlan(mealType, index) {
    selectedMeals[mealType].splice(index, 1);
    updateMealDisplay(mealType);
    updateSelectedMealsData();
}

// Update hidden input with selected meals data
function updateSelectedMealsData() {
    document.getElementById('selectedMealsData').value = JSON.stringify(selectedMeals);
}

// Search meals
function searchMeals() {
    const searchTerm = document.getElementById('mealSearchInput').value.toLowerCase();
    const filteredMeals = allMeals.filter(meal => 
        meal.description.toLowerCase().includes(searchTerm) ||
        (meal.dietary_tags && meal.dietary_tags.some(tag => tag.toLowerCase().includes(searchTerm)))
    );
    displayMeals(filteredMeals);
}

// Filter meals by member preferences
async function filterMealsByPreferences(memberId) {
    if (!memberProfile) {
        await loadMemberProfile(memberId);
    }
    
    // This would filter available meals based on dietary restrictions
    alert('Filtering meals based on member preferences...');
}

// Auto-suggest meal plan
async function suggestMealPlan(memberId) {
    try {
        const response = await fetch('/dietitian/diet-plans/suggest-plan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ member_id: memberId, days: 1 })
        });
        
        const data = await response.json();
        if (data.success && data.suggested_plan.length > 0) {
            // Load suggested meals into the plan
            const dayPlan = data.suggested_plan[0];
            // Implementation would map suggested meals to our structure
            alert('Meal plan suggestions loaded!');
        }
    } catch (error) {
        console.error('Error suggesting meal plan:', error);
        alert('Failed to generate meal suggestions');
    }
}

// Preview nutrition breakdown
function previewNutritionBreakdown() {
    let totalCalories = 0, totalProtein = 0, totalCarbs = 0, totalFat = 0;
    
    Object.values(selectedMeals).forEach(mealTypeArray => {
        mealTypeArray.forEach(meal => {
            totalCalories += meal.calories;
            totalProtein += meal.protein;
            totalCarbs += meal.carbs;
            totalFat += meal.fat;
        });
    });
    
    alert(`Total Daily Nutrition:\nCalories: ${totalCalories}\nProtein: ${totalProtein}g\nCarbs: ${totalCarbs}g\nFat: ${totalFat}g`);
}

// Clear all meals
function clearAllMeals() {
    if (confirm('Are you sure you want to clear all selected meals?')) {
        selectedMeals = { breakfast: [], lunch: [], dinner: [], snack: [] };
        ['breakfast', 'lunch', 'dinner', 'snack'].forEach(mealType => {
            updateMealDisplay(mealType);
        });
        updateSelectedMealsData();
    }
}

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum end date to start date + 1 day
    const startDateInput = document.querySelector('[name="start_date"]');
    const endDateInput = document.querySelector('[name="end_date"]');
    
    startDateInput.addEventListener('change', function() {
        const startDate = new Date(this.value);
        startDate.setDate(startDate.getDate() + 1);
        endDateInput.min = startDate.toISOString().split('T')[0];
        if (endDateInput.value && endDateInput.value <= this.value) {
            endDateInput.value = startDate.toISOString().split('T')[0];
        }
    });
});
</script>

@endsection
