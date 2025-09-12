@extends('dietitianDashboard.layout')

@section('content')

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
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

    <!-- Header -->
    <div class="bg-[#1E1E1E] text-white px-8 py-6 rounded-lg shadow mb-6 mt-4">
        <h2 class="text-2xl font-bold">Add New Meal</h2>
        <p class="text-sm text-gray-300 mt-1">Create a new recipe for your meal library</p>
    </div>

    <form method="POST" action="{{ route('dietitian.meals.store') }}" class="space-y-6">
        @csrf

        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Basic Information</h3>

            <!-- Name and Category in same line -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="meal_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Meal Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="meal_name" name="meal_name" value="{{ old('meal_name') }}"
                        placeholder="e.g., Grilled Chicken with Quinoa" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                    @error('meal_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select id="category" name="category" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        <option value="">Select category</option>
                        <option value="breakfast" {{ old('category') == 'breakfast' ? 'selected' : '' }}>Breakfast
                        </option>
                        <option value="lunch" {{ old('category') == 'lunch' ? 'selected' : '' }}>Lunch</option>
                        <option value="dinner" {{ old('category') == 'dinner' ? 'selected' : '' }}>Dinner</option>
                        <option value="snack" {{ old('category') == 'snack' ? 'selected' : '' }}>Snack</option>
                        <option value="pre_workout" {{ old('category') == 'pre_workout' ? 'selected' : '' }}>
                            Pre-Workout</option>
                        <option value="post_workout" {{ old('category') == 'post_workout' ? 'selected' : '' }}>
                            Post-Workout</option>
                    </select>
                    @error('category')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Serving Size & Difficulty Level -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="serving_size" class="block text-sm font-medium text-gray-700 mb-2">
                        Serving Size <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="serving_size" name="serving_size"
                        value="{{ old('serving_size', '1 serving') }}" placeholder="e.g., 1 serving, 2 cups" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                    @error('serving_size')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Difficulty Level -->
                <div>
                    <label for="difficulty_level" class="block text-sm font-medium text-gray-700 mb-2">Difficulty
                        Level</label>
                    <select id="difficulty_level" name="difficulty_level"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        <option value="easy">
                            Easy</option>
                        <option value="medium">
                            Medium</option>
                        <option value="hard">
                            Hard</option>
                    </select>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea id="description" name="description" rows="3" placeholder="Brief description of the meal..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Image not supported in this build -->

        <!-- Ingredients -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Ingredients</h3>

            <div id="ingredients-container">
                <div class="ingredient-item flex gap-2 mb-2">
                    <input type="text" name="ingredients[]" placeholder="e.g., 200g chicken breast, diced"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                    <button type="button" onclick="removeIngredient(this)"
                        class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                        Remove
                    </button>
                </div>
            </div>

            <button type="button" onclick="addIngredient()"
                class="mt-2 px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                + Add Ingredient
            </button>
        </div>

        <!-- Preparation Method -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Preparation Method</h3>

            <textarea id="preparation_method" name="preparation_method" rows="6"
                placeholder="Step-by-step cooking instructions..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">{{ old('preparation_method') }}</textarea>
            @error('preparation_method')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Time Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Time Information</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Prep Time -->
                <div>
                    <label for="prep_time_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                        Prep Time (minutes) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="prep_time_minutes" name="prep_time_minutes"
                        value="{{ old('prep_time_minutes') }}" min="0" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"
                        oninput="calculateTotalTime()">
                    @error('prep_time_minutes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cook Time -->
                <div>
                    <label for="cook_time_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                        Cook Time (minutes) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="cook_time_minutes" name="cook_time_minutes"
                        value="{{ old('cook_time_minutes') }}" min="0" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"
                        oninput="calculateTotalTime()">
                    @error('cook_time_minutes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total Time (auto) -->
                <div>
                    <label for="total_time_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                        Total Time (minutes)
                    </label>
                    <input type="number" id="total_time_minutes" name="total_time_minutes"
                        value="{{ old('total_time_minutes') }}" readonly
                        class="w-full px-4 py-2 border border-gray-300 bg-gray-100 rounded-lg focus:outline-none cursor-not-allowed">
                </div>
            </div>
        </div>

        <!-- Nutrition Information (Auto-calculated) -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Nutrition Information (per serving)</h3>
                <button type="button" onclick="calculateNutrition()"
                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                    Auto Calculate
                </button>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label for="calories" class="block text-sm font-medium text-gray-700 mb-2">
                        Calories <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="calories" name="calories" value="{{ old('calories') }}" step="0.1"
                        min="0" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                </div>

                <div>
                    <label for="protein" class="block text-sm font-medium text-gray-700 mb-2">
                        Protein (g) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="protein" name="protein" value="{{ old('protein') }}" step="0.1"
                        min="0" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                </div>

                <div>
                    <label for="carbs" class="block text-sm font-medium text-gray-700 mb-2">
                        Carbs (g) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="carbs" name="carbs" value="{{ old('carbs') }}" step="0.1"
                        min="0" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                </div>

                <div>
                    <label for="fats" class="block text-sm font-medium text-gray-700 mb-2">
                        Fat (g) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="fats" name="fats" value="{{ old('fats') }}" step="0.1"
                        min="0" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                </div>

                <div>
                    <label for="fiber" class="block text-sm font-medium text-gray-700 mb-2">Fiber (g)</label>
                    <input type="number" id="fiber" name="fiber" value="{{ old('fiber') }}" step="0.1"
                        min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                </div>

                <div>
                    <label for="sugar" class="block text-sm font-medium text-gray-700 mb-2">Sugar (g)</label>
                    <input type="number" id="sugar" name="sugar" value="{{ old('sugar') }}" step="0.1"
                        min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                </div>

                <div>
                    <label for="sodium" class="block text-sm font-medium text-gray-700 mb-2">Sodium (mg)</label>
                    <input type="number" id="sodium" name="sodium" value="{{ old('sodium') }}" step="0.1"
                        min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                </div>
            </div>
        </div>

        <!-- Meal Settings -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Meal Settings</h3>

            <!-- Suitable Meal Times -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-800 mb-3">
                    Suitable Meal Times <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @php
                        $mealTimes = [
                            'breakfast' => 'Breakfast',
                            'lunch' => 'Lunch',
                            'dinner' => 'Dinner',
                            'snack' => 'Snack',
                        ];
                        $selectedMealTimes = old('meal_times', []);
                    @endphp
                    @foreach ($mealTimes as $key => $label)
                        <label
                            class="flex items-center bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 shadow-sm hover:border-red-400 hover:bg-red-50 transition-colors cursor-pointer group">
                            <input type="checkbox" name="meal_times[]" value="{{ $key }}"
                                class="rounded border-gray-300 text-red-600 focus:ring-red-500 accent-red-500 transition-all duration-150 group-hover:scale-110">
                            <span
                                class="ml-2 text-sm text-gray-800 font-medium group-hover:text-red-600">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('meal_times')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-2 font-normal">Select the appropriate times of day for this meal.</p>
            </div>

            <!-- Make Public Option -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-800 mb-3">
                    Visibility <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <label
                        class="flex items-center bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 shadow-sm hover:border-red-400 hover:bg-red-50 transition-colors cursor-pointer group">
                        <input type="checkbox" name="is_public" value="1" id="is_public"
                            class="rounded border-gray-300 text-red-600 focus:ring-red-500 accent-red-500 transition-all duration-150 group-hover:scale-110">
                        <span class="ml-2 text-sm text-gray-800 font-medium group-hover:text-red-600">
                            Make this meal public
                        </span>
                    </label>
                </div>
                <p class="text-xs text-gray-500 mt-2 font-normal">
                    Visible to other dietitians for use in their plans.
                </p>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-end space-x-4">
                <a href="{{ route('dietitian.meals') }}"
                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors font-medium">
                    Add Meal to Library
                </button>
            </div>
        </div>
    </form>

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
            function addIngredient() {
                const container = document.getElementById('ingredients-container');
                const div = document.createElement('div');
                div.className = 'ingredient-item flex gap-2 mb-2';
                div.innerHTML = `
        <input type="text" 
               name="ingredients[]" 
               placeholder="e.g., 200g chicken breast, diced"
               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
        <button type="button" 
                onclick="removeIngredient(this)"
                class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
            Remove
        </button> `;
                container.appendChild(div);
            }

            function removeIngredient(button) {
                const container = document.getElementById('ingredients-container');
                if (container.children.length > 1) {
                    button.parentElement.remove();
                }
            }

            function calculateTotalTime() {
                const prep = parseInt(document.getElementById('prep_time_minutes').value) || 0;
                const cook = parseInt(document.getElementById('cook_time_minutes').value) || 0;
                document.getElementById('total_time_minutes').value = prep + cook;
            }

            function calculateNutrition() {
                const ingredients = Array.from(document.querySelectorAll('input[name="ingredients[]"]'))
                    .map(input => input.value.trim())
                    .filter(value => value !== '');

                if (ingredients.length === 0) {
                    alert('Please add some ingredients first.');
                    return;
                }

                // Show loading state
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = 'Calculating...';
                button.disabled = true;

                // Call nutrition API
                fetch('{{ route('meals.calculate-nutrition') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            ingredients
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update nutrition fields with calculated values
                            document.getElementById('calories').value = data.data.calories;
                            document.getElementById('protein').value = data.data.protein;
                            document.getElementById('carbs').value = data.data.carbs;
                            document.getElementById('fats').value = data.data.fats;
                            document.getElementById('fiber').value = data.data.fiber || 0;
                            document.getElementById('sugar').value = data.data.sugar || 0;
                            document.getElementById('sodium').value = data.data.sodium || 0;

                            // Show success message
                            alert('Nutrition values calculated successfully!');
                        } else {
                            alert(data.message || 'Failed to calculate nutrition. Please enter values manually.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to calculate nutrition. Please enter values manually.');
                    })
                    .finally(() => {
                        // Reset button
                        button.textContent = originalText;
                        button.disabled = false;
                    });
            }
        </script>
    @endpush

@endsection
