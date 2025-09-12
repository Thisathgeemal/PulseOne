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

    <!-- Header -->
    <div class="bg-[#1E1E1E] text-white px-8 py-6 rounded-lg shadow mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold">Edit Meal</h2>
                <p class="text-sm text-gray-300 mt-1">Update your meal recipe and nutrition information</p>
            </div>

            <div class="flex space-x-3">
                <a href="{{ route('dietitian.meals.show', $meal) }}"
                    class="px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors font-medium">
                    View Meal
                </a>
                <a href="{{ route('dietitian.meals') }}"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Back to Library
                </a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('dietitian.meals.update', $meal) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">Basic Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Meal Name -->
                        <div>
                            <label for="meal_name" class="block text-sm font-medium text-gray-700 mb-2">Meal Name *</label>
                            <input type="text" id="meal_name" name="meal_name"
                                value="{{ old('meal_name', $meal->meal_name) }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                            <select id="category" name="category" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                                <option value="">Select Category</option>
                                <option value="breakfast"
                                    {{ old('category', $meal->category) === 'breakfast' ? 'selected' : '' }}>Breakfast
                                </option>
                                <option value="lunch" {{ old('category', $meal->category) === 'lunch' ? 'selected' : '' }}>
                                    Lunch</option>
                                <option value="dinner"
                                    {{ old('category', $meal->category) === 'dinner' ? 'selected' : '' }}>Dinner</option>
                                <option value="snack" {{ old('category', $meal->category) === 'snack' ? 'selected' : '' }}>
                                    Snack</option>
                                <option value="dessert"
                                    {{ old('category', $meal->category) === 'dessert' ? 'selected' : '' }}>Dessert</option>
                            </select>
                        </div>

                        <!-- Difficulty Level -->
                        <div>
                            <label for="difficulty_level" class="block text-sm font-medium text-gray-700 mb-2">Difficulty
                                Level</label>
                            <select id="difficulty_level" name="difficulty_level"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                                <option value="easy"
                                    {{ old('difficulty_level', $meal->difficulty_level) === 'easy' ? 'selected' : '' }}>
                                    Easy</option>
                                <option value="medium"
                                    {{ old('difficulty_level', $meal->difficulty_level) === 'medium' ? 'selected' : '' }}>
                                    Medium</option>
                                <option value="hard"
                                    {{ old('difficulty_level', $meal->difficulty_level) === 'hard' ? 'selected' : '' }}>
                                    Hard</option>
                            </select>
                        </div>

                        <!-- Serving Size -->
                        <div>
                            <label for="serving_size" class="block text-sm font-medium text-gray-700 mb-2">Serving Size
                                *</label>
                            <input type="number" id="serving_size" name="serving_size"
                                value="{{ old('serving_size', $meal->serving_size) }}" min="1" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mt-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="description" name="description" rows="3" placeholder="Brief description of the meal..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">{{ old('description', $meal->description) }}</textarea>
                    </div>
                </div>

                <!-- Timing -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">Preparation & Cooking Time</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="preparation_time" class="block text-sm font-medium text-gray-700 mb-2">Preparation
                                Time (minutes)</label>
                            <input type="number" id="preparation_time" name="preparation_time"
                                value="{{ old('preparation_time', $meal->preparation_time) }}" min="0"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        </div>

                        <div>
                            <label for="cooking_time" class="block text-sm font-medium text-gray-700 mb-2">Cooking Time
                                (minutes)</label>
                            <input type="number" id="cooking_time" name="cooking_time"
                                value="{{ old('cooking_time', $meal->cooking_time) }}" min="0"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        </div>
                    </div>
                </div>

                <!-- Ingredients -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">Ingredients</h3>

                    <div id="ingredients-container" class="space-y-3">
                        @if (old('ingredients') || ($meal->ingredients && is_array($meal->ingredients) && count($meal->ingredients) > 0))
                            @foreach (old('ingredients', $meal->ingredients ?? []) as $index => $ingredient)
                                <div class="flex items-center space-x-3">
                                    <input type="text" name="ingredients[]" value="{{ $ingredient }}"
                                        placeholder="e.g., 200g chicken breast"
                                        class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                                    <button type="button" onclick="removeIngredient(this)"
                                        class="px-4 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                        Remove
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="flex items-center space-x-3">
                                <input type="text" name="ingredients[]" placeholder="e.g., 200g chicken breast"
                                    class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                                <button type="button" onclick="removeIngredient(this)"
                                    class="px-4 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                    Remove
                                </button>
                            </div>
                        @endif
                    </div>

                    <button type="button" onclick="addIngredient()"
                        class="mt-2 px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                        + Add Ingredient
                    </button>
                </div>

                <!-- Instructions -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">Instructions</h3>

                    <div id="instructions-container" class="space-y-3">
                        @if (old('instructions') || ($meal->instructions && is_array($meal->instructions) && count($meal->instructions) > 0))
                            @foreach (old('instructions', $meal->instructions ?? []) as $index => $instruction)
                                <div class="flex items-start space-x-3">
                                    <span
                                        class="mt-3 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                        {{ $index + 1 }}
                                    </span>
                                    <textarea name="instructions[]" rows="2" placeholder="Describe this step..."
                                        class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">{{ $instruction }}</textarea>
                                    <button type="button" onclick="removeInstruction(this)"
                                        class="mt-3 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                        Remove
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="flex items-start space-x-3">
                                <span
                                    class="mt-3 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">1</span>
                                <textarea name="instructions[]" rows="2" placeholder="Describe this step..."
                                    class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"></textarea>
                                <button type="button" onclick="removeInstruction(this)"
                                    class="mt-3 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                    Remove
                                </button>
                            </div>
                        @endif
                    </div>

                    <button type="button" onclick="addInstruction()"
                        class="mt-4 px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                        Add Step
                    </button>
                </div>
            </div>

            <!-- Image not supported in this build -->

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Nutrition Information -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-gray-800">Nutrition Information</h3>
                        <button type="button" onclick="calculateNutrition()"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm">
                            Auto Calculate
                        </button>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Per serving</p>

                    <div class="space-y-4">
                        <div>
                            <label for="calories" class="block text-sm font-medium text-gray-700 mb-1">Calories *</label>
                            <input type="number" id="calories" name="calories_per_serving"
                                value="{{ old('calories_per_serving', $meal->calories_per_serving) }}" step="0.1"
                                min="0" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        </div>

                        <div>
                            <label for="protein" class="block text-sm font-medium text-gray-700 mb-1">Protein (g)
                                *</label>
                            <input type="number" id="protein" name="protein_per_serving"
                                value="{{ old('protein_per_serving', $meal->protein_per_serving) }}" step="0.1"
                                min="0" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        </div>

                        <div>
                            <label for="carbs" class="block text-sm font-medium text-gray-700 mb-1">Carbohydrates (g)
                                *</label>
                            <input type="number" id="carbs" name="carbs_per_serving"
                                value="{{ old('carbs_per_serving', $meal->carbs_per_serving) }}" step="0.1"
                                min="0" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        </div>

                        <div>
                            <label for="fats" class="block text-sm font-medium text-gray-700 mb-1">Fat (g) *</label>
                            <input type="number" id="fats" name="fat_per_serving"
                                value="{{ old('fat_per_serving', $meal->fat_per_serving) }}" step="0.1"
                                min="0" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        </div>

                        <div>
                            <label for="fiber" class="block text-sm font-medium text-gray-700 mb-1">Fiber (g)</label>
                            <input type="number" id="fiber" name="fiber_per_serving"
                                value="{{ old('fiber_per_serving', $meal->fiber_per_serving) }}" step="0.1"
                                min="0"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        </div>

                        <div>
                            <label for="sugar" class="block text-sm font-medium text-gray-700 mb-1">Sugar (g)</label>
                            <input type="number" id="sugar" name="sugar_per_serving"
                                value="{{ old('sugar_per_serving', $meal->sugar_per_serving) }}" step="0.1"
                                min="0"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        </div>

                        <div>
                            <label for="sodium" class="block text-sm font-medium text-gray-700 mb-1">Sodium (mg)</label>
                            <input type="number" id="sodium" name="sodium_per_serving"
                                value="{{ old('sodium_per_serving', $meal->sodium_per_serving) }}" step="0.1"
                                min="0"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        </div>
                    </div>
                </div>

                <!-- Dietary Tags -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Dietary Tags</h3>

                    @php
                        $dietaryData = old('dietary_tags', $meal->dietary_tags ?: []);
                        $selectedMealTimes = $dietaryData['meal_times'] ?? [];
                    @endphp

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meal Times</label>
                            <div class="space-y-2">
                                @foreach (['breakfast', 'lunch', 'dinner', 'snack'] as $time)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="dietary_tags[meal_times][]"
                                            value="{{ $time }}"
                                            {{ in_array($time, $selectedMealTimes) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">{{ ucfirst($time) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visibility -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">Visibility</h3>

                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="radio" name="is_public" value="1"
                                {{ old('is_public', $meal->is_public) == '1' ? 'checked' : '' }}
                                class="text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-700">Public</div>
                                <div class="text-sm text-gray-500">Other dietitians can view this meal</div>
                            </div>
                        </label>

                        <label class="flex items-center">
                            <input type="radio" name="is_public" value="0"
                                {{ old('is_public', $meal->is_public) == '0' ? 'checked' : '' }}
                                class="text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-700">Private</div>
                                <div class="text-sm text-gray-500">Only you can view this meal</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="space-y-3">
                        <button type="submit"
                            class="w-full px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors font-medium">
                            Update Meal
                        </button>

                        <a href="{{ route('dietitian.meals.show', $meal) }}"
                            class="w-full block px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-center font-medium">
                            Cancel
                        </a>
                    </div>
                </div>
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
                div.className = 'flex items-center space-x-3';
                div.innerHTML = `
                    <input type="text" name="ingredients[]" placeholder="e.g., 200g chicken breast"
                        class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                    <button type="button" onclick="removeIngredient(this)" 
                            class="px-4 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                        Remove
                    </button>
                `;
                container.appendChild(div);
            }

            function removeIngredient(button) {
                const container = document.getElementById('ingredients-container');
                if (container.children.length > 1) {
                    button.parentElement.remove();
                }
            }

            function addInstruction() {
                const container = document.getElementById('instructions-container');
                const stepNumber = container.children.length + 1;
                const div = document.createElement('div');
                div.className = 'flex items-start space-x-3';
                div.innerHTML = `
                    <span class="mt-3 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                        ${stepNumber}
                    </span>
                    <textarea name="instructions[]" rows="2" placeholder="Describe this step..."
                            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"></textarea>
                    <button type="button" onclick="removeInstruction(this)" 
                            class="mt-3 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                        Remove
                    </button>
                `;
                container.appendChild(div);
            }

            function removeInstruction(button) {
                const container = document.getElementById('instructions-container');
                if (container.children.length > 1) {
                    button.parentElement.remove();
                    // Update step numbers
                    Array.from(container.children).forEach((child, index) => {
                        const stepNumber = child.querySelector('span');
                        if (stepNumber) {
                            stepNumber.textContent = index + 1;
                        }
                    });
                }
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
