@extends('dietitianDashboard.layout')

@section('content')

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 mt-4">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">Please fix the following errors:</span>
            </div>
            <ul class="list-disc list-inside ml-7">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (isset($request))

        <!-- Header -->
        <div class="bg-[#1E1E1E] text-white px-8 py-6 rounded-lg shadow mb-6 mt-4">
            <h2 class="text-2xl font-bold">Create Diet Plan</h2>
            <p class="text-sm text-gray-300 mt-1">
                for <span class="text-yellow-400 font-medium">{{ $request->member->first_name }}
                    {{ $request->member->last_name }}</span>
            </p>
        </div>

        @if (isset($healthAssessment))
            <!-- Member Health Summary -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-2">❤️ Member Health Summary</h3>
                <p class="text-sm text-blue-700 mb-5">
                    Overview of the member’s health profile and key metrics from their latest assessment.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white rounded-lg p-3">
                        <p class="text-xs text-gray-500 uppercase">Current Weight</p>
                        <p class="text-lg font-bold text-gray-900">{{ $healthAssessment->weight_kg ?? 'N/A' }} kg</p>
                    </div>

                    <div class="bg-white rounded-lg p-3">
                        <p class="text-xs text-gray-500 uppercase">Height</p>
                        <p class="text-lg font-bold text-gray-900">{{ $healthAssessment->height_cm ?? 'N/A' }} cm</p>
                    </div>

                    <div class="bg-white rounded-lg p-3">
                        <p class="text-xs text-gray-500 uppercase">BMI</p>
                        <p class="text-lg font-bold text-gray-900">{{ $healthAssessment->bmi ?? 'N/A' }}</p>
                        @if ($healthAssessment && $healthAssessment->bmi)
                            <p class="text-xs text-gray-600">{{ $healthAssessment->bmi_category }}</p>
                        @endif
                    </div>

                    <div class="bg-white rounded-lg p-3">
                        <p class="text-xs text-gray-500 uppercase">Activity Level</p>
                        <p class="text-lg font-bold text-gray-900">
                            {{ ucwords(str_replace('_', ' ', $healthAssessment->activity_level ?? 'N/A')) }}</p>
                    </div>
                </div>
            </div>

            @if (isset($nutritionTargets))
                <!-- Recommended Nutrition Targets -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-green-900 mb-2">🎯 Recommended Nutrition Targets</h3>
                    <p class="text-sm text-green-700 mb-5">
                        {{ $nutritionTargets['goal_explanation'] ?? 'Based on member health assessment and goals' }}
                    </p>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-white rounded-lg p-3 text-center">
                            <p class="text-2xl font-bold text-blue-600">{{ $nutritionTargets['calories'] ?? 2000 }}</p>
                            <p class="text-xs text-gray-500">Calories/day</p>
                        </div>

                        <div class="bg-white rounded-lg p-3 text-center">
                            <p class="text-2xl font-bold text-green-600">{{ $nutritionTargets['protein'] ?? 150 }}g</p>
                            <p class="text-xs text-gray-500">Protein</p>
                        </div>

                        <div class="bg-white rounded-lg p-3 text-center">
                            <p class="text-2xl font-bold text-orange-600">{{ $nutritionTargets['carbs'] ?? 250 }}g</p>
                            <p class="text-xs text-gray-500">Carbs</p>
                        </div>

                        <div class="bg-white rounded-lg p-3 text-center">
                            <p class="text-2xl font-bold text-purple-600">{{ $nutritionTargets['fat'] ?? 67 }}g</p>
                            <p class="text-xs text-gray-500">Fat</p>
                        </div>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button type="button" onclick="useRecommendedTargets()"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                            Use These Targets
                        </button>
                    </div>
                </div>
            @endif
        @endif

        <!-- Form Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-md p-6">
            <form method="POST" action="{{ route('dietitian.dietplan.store') }}" id="dietPlanForm">
                @csrf
                <input type="hidden" name="request_id" value="{{ $request->request_id }}">

                <!-- Plan Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Plan Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plan Title</label>
                        <input type="text" name="plan_name"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                            placeholder="Enter Diet Plan Name" required>
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Start Date
                            @if (isset($suggestedDates['timeframe_display']))
                                <span class="text-sm text-blue-600 font-normal">({{ $suggestedDates['timeframe_display'] }}
                                    plan)</span>
                            @endif
                        </label>
                        <input type="date" name="start_date"
                            value="{{ old('start_date', $suggestedDates['start_date'] ?? now()->format('Y-m-d')) }}"
                            min="{{ now()->format('Y-m-d') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                            required>
                    </div>

                    <!-- End Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" name="end_date"
                            value="{{ old('end_date', $suggestedDates['end_date'] ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                            required>
                    </div>

                    <!-- Daily Calories -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Daily Calories Target</label>
                        <input type="number" name="total_calories_per_day" min="800" max="5000"
                            value="{{ $nutritionTargets['calories'] ?? 2000 }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                            required>
                    </div>
                </div>

                <!-- Nutrition Targets -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Protein (g)</label>
                        <input type="number" name="total_protein_per_day" min="0"
                            value="{{ $nutritionTargets['protein'] ?? 150 }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Carbs (g)</label>
                        <input type="number" name="total_carbs_per_day" min="0"
                            value="{{ $nutritionTargets['carbs'] ?? 250 }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fat (g)</label>
                        <input type="number" name="total_fat_per_day" min="0"
                            value="{{ $nutritionTargets['fat'] ?? 67 }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                            required>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                        placeholder="Brief description of the diet plan"></textarea>
                </div>

                <!-- Meal Selection Section -->
                <div class="mb-6">
                    @if (isset($healthAssessment))
                        <!-- Dietary Filtering Based on Health Assessment -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                            <h4 class="text-lg font-semibold text-yellow-800 mb-2">🔍 Smart Meal Filtering</h4>
                            <p class="text-sm text-yellow-700 mb-5">Meals are automatically filtered based on member's
                                health assessment:</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if ($healthAssessment->allergies && count($healthAssessment->allergies) > 0)
                                    <div>
                                        <p class="text-sm font-medium text-red-700 mb-3">⚠️ Allergies:</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($healthAssessment->allergies as $allergy)
                                                <span
                                                    class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">{{ $allergy }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if ($healthAssessment->dietary_restrictions && count($healthAssessment->dietary_restrictions) > 0)
                                    <div>
                                        <p class="text-sm font-medium text-green-700 mb-3">🥗 Dietary Restrictions:</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($healthAssessment->dietary_restrictions as $restriction)
                                                <span
                                                    class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">{{ $restriction }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Meal Type Tabs -->
                    <div class="flex border-b border-gray-200 mb-4">
                        <button type="button"
                            class="meal-tab-btn active px-4 py-2 text-sm font-medium border-b-2 border-red-500 text-red-600"
                            data-meal-type="breakfast">
                            🍳 Breakfast
                        </button>
                        <button type="button"
                            class="meal-tab-btn px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700"
                            data-meal-type="lunch">
                            🍽️ Lunch
                        </button>
                        <button type="button"
                            class="meal-tab-btn px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700"
                            data-meal-type="dinner">
                            🍛 Dinner
                        </button>
                        <button type="button"
                            class="meal-tab-btn px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700"
                            data-meal-type="snack">
                            🥨 Snack
                        </button>
                    </div>

                    <!-- Available Meals -->
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-700 mb-2">Available Meals</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-80 overflow-y-auto border rounded-lg p-4"
                            id="availableMeals">
                            @foreach ($meals as $meal)
                                <div class="meal-item border rounded-lg p-3 cursor-pointer hover:bg-gray-50 hover:border-red-300"
                                    data-meal-id="{{ $meal->meal_id }}" data-meal-name="{{ $meal->description }}"
                                    data-calories="{{ $meal->calories_per_serving }}"
                                    data-protein="{{ $meal->protein_grams }}" data-carbs="{{ $meal->carbs_grams }}"
                                    data-fat="{{ $meal->fats_grams }}">
                                    <h5 class="font-medium text-gray-900">{{ $meal->description }}</h5>
                                    <p class="text-sm text-gray-600">{{ $meal->calories_per_serving }} cal</p>
                                    <p class="text-xs text-gray-500">{{ $meal->protein_grams }}g protein •
                                        {{ $meal->carbs_grams }}g carbs • {{ $meal->fats_grams }}g fat</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Selected Meals for Current Type -->
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-700 mb-2">Selected Meals for <span
                                id="currentMealType">Breakfast</span></h4>
                        <div class="min-h-20 border-2 border-dashed border-gray-300 rounded-lg p-4"
                            id="selectedMealsContainer">
                            <p class="text-gray-500 text-sm" id="emptyMessage">Click on meals above to add them here</p>
                        </div>
                    </div>

                    <!-- Nutrition Summary with Target Comparison -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-700 mb-2">Selected Meals Nutrition Summary</h4>
                        <div class="grid grid-cols-4 gap-4 text-center mb-3">
                            <div>
                                <span class="block text-lg font-bold text-blue-600" id="totalCalories">0</span>
                                <span class="text-sm text-gray-600">Calories</span>
                                <div class="progress-bar mt-1">
                                    <div class="bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full transition-all" id="caloriesProgress"
                                            style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <span class="block text-lg font-bold text-green-600" id="totalProtein">0</span>
                                <span class="text-sm text-gray-600">Protein (g)</span>
                                <div class="progress-bar mt-1">
                                    <div class="bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full transition-all" id="proteinProgress"
                                            style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <span class="block text-lg font-bold text-orange-600" id="totalCarbs">0</span>
                                <span class="text-sm text-gray-600">Carbs (g)</span>
                                <div class="progress-bar mt-1">
                                    <div class="bg-gray-200 rounded-full h-2">
                                        <div class="bg-orange-600 h-2 rounded-full transition-all" id="carbsProgress"
                                            style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <span class="block text-lg font-bold text-purple-600" id="totalFat">0</span>
                                <span class="text-sm text-gray-600">Fat (g)</span>
                                <div class="progress-bar mt-1">
                                    <div class="bg-gray-200 rounded-full h-2">
                                        <div class="bg-purple-600 h-2 rounded-full transition-all" id="fatProgress"
                                            style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Target Comparison Alert -->
                        <div id="targetAlert" class="hidden p-3 rounded-lg text-sm"></div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Special Instructions</label>
                    <textarea name="notes" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                        placeholder="Any special instructions or notes for the member"></textarea>
                </div>

                <!-- Hidden input for selected meals -->
                <input type="hidden" name="selected_meals" id="selectedMealsInput" value="{}">

                <!-- Action Buttons -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('dietitian.dietplan') }}"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Create Diet Plan
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="container mx-auto">
            @include('dietitianDashboard.dietplan')
        </div>
    @endif

    @push('scripts')
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d32f2f'
                });
            </script>
        @endif

        @if (session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "{{ session('error') }}",
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d32f2f'
                });
            </script>
        @endif
        <script>
            // Diet Plan Creation JavaScript
            let selectedMeals = {
                breakfast: [],
                lunch: [],
                dinner: [],
                snack: []
            };

            let currentMealType = 'breakfast';

            document.addEventListener('DOMContentLoaded', function() {
                // Meal type tab switching
                document.querySelectorAll('.meal-tab-btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const mealType = this.dataset.mealType;
                        switchMealType(mealType);
                    });
                });

                // Meal item clicking
                document.querySelectorAll('.meal-item').forEach(item => {
                    item.addEventListener('click', function() {
                        addMealToSelection(this);
                    });
                });

                // Update nutrition summary initially
                updateNutritionSummary();
            });

            function switchMealType(mealType) {
                currentMealType = mealType;

                // Update tab styling
                document.querySelectorAll('.meal-tab-btn').forEach(btn => {
                    btn.classList.remove('active', 'border-red-500', 'text-red-600');
                    btn.classList.add('border-transparent', 'text-gray-500');
                });

                document.querySelector(`[data-meal-type="${mealType}"]`).classList.add('active', 'border-red-500',
                    'text-red-600');
                document.querySelector(`[data-meal-type="${mealType}"]`).classList.remove('border-transparent',
                    'text-gray-500');

                // Update current meal type display
                document.getElementById('currentMealType').textContent = mealType.charAt(0).toUpperCase() + mealType.slice(1);

                // Show selected meals for this type
                displaySelectedMeals();
            }

            function addMealToSelection(mealElement) {
                const mealData = {
                    id: mealElement.dataset.mealId,
                    name: mealElement.dataset.mealName,
                    calories: parseFloat(mealElement.dataset.calories),
                    protein: parseFloat(mealElement.dataset.protein),
                    carbs: parseFloat(mealElement.dataset.carbs),
                    fat: parseFloat(mealElement.dataset.fat)
                };

                // Check if meal already selected for this type
                const alreadySelected = selectedMeals[currentMealType].find(meal => meal.id === mealData.id);
                if (alreadySelected) {
                    alert('This meal is already selected for ' + currentMealType);
                    return;
                }

                // Add to selected meals
                selectedMeals[currentMealType].push(mealData);

                // Update display
                displaySelectedMeals();
                updateNutritionSummary();
                updateHiddenInput();
            }

            function removeMealFromSelection(mealId) {
                selectedMeals[currentMealType] = selectedMeals[currentMealType].filter(meal => meal.id !== mealId);
                displaySelectedMeals();
                updateNutritionSummary();
                updateHiddenInput();
            }

            function displaySelectedMeals() {
                const container = document.getElementById('selectedMealsContainer');
                const meals = selectedMeals[currentMealType];

                if (meals.length === 0) {
                    container.innerHTML =
                        '<p class="text-gray-500 text-sm" id="emptyMessage">Click on meals above to add them here</p>';
                    return;
                }

                container.innerHTML = meals.map(meal => `
        <div class="flex justify-between items-center bg-white rounded-lg p-3 border mb-2">
            <div>
                <h5 class="font-medium text-gray-900">${meal.name}</h5>
                <p class="text-sm text-gray-600">${meal.calories} cal • ${meal.protein}g protein • ${meal.carbs}g carbs • ${meal.fat}g fat</p>
            </div>
            <button type="button" onclick="removeMealFromSelection('${meal.id}')" 
                    class="text-red-600 hover:text-red-800 text-sm">
                Remove
            </button>
        </div>
        `).join('');
            }

            function updateNutritionSummary() {
                let totalCalories = 0,
                    totalProtein = 0,
                    totalCarbs = 0,
                    totalFat = 0;

                Object.values(selectedMeals).forEach(mealType => {
                    mealType.forEach(meal => {
                        totalCalories += meal.calories;
                        totalProtein += meal.protein;
                        totalCarbs += meal.carbs;
                        totalFat += meal.fat;
                    });
                });

                // Update display
                document.getElementById('totalCalories').textContent = Math.round(totalCalories);
                document.getElementById('totalProtein').textContent = Math.round(totalProtein);
                document.getElementById('totalCarbs').textContent = Math.round(totalCarbs);
                document.getElementById('totalFat').textContent = Math.round(totalFat);

                // Update progress bars and target comparison
                updateTargetComparison(totalCalories, totalProtein, totalCarbs, totalFat);
            }

            function updateTargetComparison(calories, protein, carbs, fat) {
                // Get target values from form inputs
                const targetCalories = parseFloat(document.querySelector('input[name="total_calories_per_day"]').value) || 2000;
                const targetProtein = parseFloat(document.querySelector('input[name="total_protein_per_day"]').value) || 150;
                const targetCarbs = parseFloat(document.querySelector('input[name="total_carbs_per_day"]').value) || 250;
                const targetFat = parseFloat(document.querySelector('input[name="total_fat_per_day"]').value) || 67;

                // Calculate percentages
                const calPercentage = Math.min((calories / targetCalories) * 100, 100);
                const proteinPercentage = Math.min((protein / targetProtein) * 100, 100);
                const carbsPercentage = Math.min((carbs / targetCarbs) * 100, 100);
                const fatPercentage = Math.min((fat / targetFat) * 100, 100);

                // Update progress bars
                document.getElementById('caloriesProgress').style.width = calPercentage + '%';
                document.getElementById('proteinProgress').style.width = proteinPercentage + '%';
                document.getElementById('carbsProgress').style.width = carbsPercentage + '%';
                document.getElementById('fatProgress').style.width = fatPercentage + '%';

                // Show target comparison alert
                const alertDiv = document.getElementById('targetAlert');
                const caloriesMatch = Math.abs(calories - targetCalories) <= (targetCalories * 0.1); // Within 10%

                if (calories === 0) {
                    alertDiv.className = 'hidden p-3 rounded-lg text-sm';
                } else if (caloriesMatch) {
                    alertDiv.className = 'block p-3 rounded-lg text-sm bg-green-100 text-green-800';
                    alertDiv.innerHTML = '✅ Great! Your meal selection closely matches the recommended targets.';
                } else if (calories < targetCalories * 0.8) {
                    alertDiv.className = 'block p-3 rounded-lg text-sm bg-yellow-100 text-yellow-800';
                    alertDiv.innerHTML = '⚠️ Current selection is below target calories. Consider adding more meals.';
                } else if (calories > targetCalories * 1.2) {
                    alertDiv.className = 'block p-3 rounded-lg text-sm bg-red-100 text-red-800';
                    alertDiv.innerHTML = '⚠️ Current selection exceeds target calories. Consider lighter options.';
                } else {
                    alertDiv.className = 'block p-3 rounded-lg text-sm bg-blue-100 text-blue-800';
                    alertDiv.innerHTML = '📊 You\'re on track! Fine-tune by adding or removing meals as needed.';
                }
            }

            // Function to use recommended targets
            function useRecommendedTargets() {
                @if (isset($nutritionTargets))
                    document.querySelector('input[name="total_calories_per_day"]').value =
                        {{ $nutritionTargets['calories'] ?? 2000 }};
                    document.querySelector('input[name="total_protein_per_day"]').value =
                        {{ $nutritionTargets['protein'] ?? 150 }};
                    document.querySelector('input[name="total_carbs_per_day"]').value =
                        {{ $nutritionTargets['carbs'] ?? 250 }};
                    document.querySelector('input[name="total_fat_per_day"]').value = {{ $nutritionTargets['fat'] ?? 67 }};

                    // Update nutrition summary with new targets
                    updateNutritionSummary();

                    // Show success message
                    const button = event.target;
                    const originalText = button.textContent;
                    button.textContent = '✅ Targets Applied!';
                    button.className = 'mt-4 px-4 py-2 bg-green-700 text-white rounded-lg text-sm';

                    setTimeout(() => {
                        button.textContent = originalText;
                        button.className =
                            'mt-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm';
                    }, 2000);
                @endif
            }

            function updateHiddenInput() {
                document.getElementById('selectedMealsInput').value = JSON.stringify(selectedMeals);
            }

            // Function to use recommended nutrition targets
            function useRecommendedTargets() {
                @if (isset($nutritionTargets))
                    document.querySelector('input[name="total_calories_per_day"]').value =
                        {{ $nutritionTargets['calories'] ?? 2000 }};
                    document.querySelector('input[name="total_protein_per_day"]').value =
                        {{ $nutritionTargets['protein'] ?? 150 }};
                    document.querySelector('input[name="total_carbs_per_day"]').value =
                        {{ $nutritionTargets['carbs'] ?? 250 }};
                    document.querySelector('input[name="total_fat_per_day"]').value = {{ $nutritionTargets['fat'] ?? 67 }};

                    // Show success message
                    const button = event.target;
                    const originalText = button.textContent;
                    button.textContent = '✓ Applied!';
                    button.className = 'mt-4 px-4 py-2 bg-green-700 text-white rounded-lg text-sm';

                    setTimeout(() => {
                        button.textContent = originalText;
                        button.className =
                            'mt-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm';
                    }, 2000);
                @endif
            }
        </script>
    @endpush

@endsection
