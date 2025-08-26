@extends('dietitianDashboard.layout')

@section('content')

    <!-- Header -->
    <div class="bg-[#1E1E1E] text-white px-8 py-6 rounded-lg shadow mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold">{{ $meal->meal_name }}</h2>
                <p class="text-sm text-gray-300 mt-1">Meal Details & Nutrition Information</p>
            </div>

            <div class="flex space-x-3">
                @if ($meal->dietitian_id === auth()->id())
                    <a href="{{ route('dietitian.meals.edit', $meal) }}"
                        class="px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors font-medium">
                        Edit Meal
                    </a>
                @endif
                <a href="{{ route('dietitian.meals') }}"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Back to Library
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Basic Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Category</label>
                        <span class="px-3 py-2 bg-blue-100 text-blue-800 rounded-lg text-sm font-medium">
                            {{ ucwords(str_replace('_', ' ', $meal->category)) }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Difficulty Level</label>
                        <span class="px-3 py-2 bg-gray-100 text-gray-800 rounded-lg text-sm font-medium">
                            {{ ucfirst($meal->difficulty_level) }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Serving Size</label>
                        <span class="px-3 py-2 bg-green-100 text-green-800 rounded-lg text-sm font-medium">
                            {{ $meal->serving_size }} servings
                        </span>
                    </div>
                </div>

                @if ($meal->description)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Description</label>
                        <span class="px-3 py-2 bg-yellow-100 text-yellow-800 rounded-lg text-sm font-medium">
                            {{ $meal->description }}
                        </span>
                    </div>
                @endif
            </div>

            <!-- Timing Information -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Preparation & Cooking</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ $meal->preparation_time ?? 0 }}</div>
                        <div class="text-sm text-gray-600">Prep Time (min)</div>
                    </div>

                    <div class="text-center p-4 bg-orange-50 rounded-lg">
                        <div class="text-2xl font-bold text-orange-600">{{ $meal->cooking_time ?? 0 }}</div>
                        <div class="text-sm text-gray-600">Cook Time (min)</div>
                    </div>

                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">
                            {{ ($meal->preparation_time ?? 0) + ($meal->cooking_time ?? 0) }}</div>
                        <div class="text-sm text-gray-600">Total Time (min)</div>
                    </div>
                </div>
            </div>

            <!-- Ingredients -->
            @if ($meal->ingredients && is_array($meal->ingredients) && count($meal->ingredients) > 0)
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Ingredients</h3>

                    <div class="space-y-2">
                        @foreach ($meal->ingredients as $ingredient)
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                                <span class="text-gray-700">{{ $ingredient }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Instructions -->
            @if ($meal->instructions && is_array($meal->instructions) && count($meal->instructions) > 0)
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Instructions</h3>

                    <div class="space-y-4">
                        @foreach ($meal->instructions as $index => $instruction)
                            <div class="flex">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-4">
                                    {{ $index + 1 }}
                                </div>
                                <p class="text-gray-700 leading-relaxed pt-1">{{ $instruction }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Nutrition Facts -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Nutrition Facts</h3>
                <p class="text-sm text-gray-600 mb-4">Per serving</p>

                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                        <span class="font-medium text-gray-700">Calories</span>
                        <span class="font-bold text-red-600">{{ number_format($meal->calories_per_serving) }}</span>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                        <span class="font-medium text-gray-700">Protein</span>
                        <span class="font-bold text-blue-600">{{ number_format($meal->protein_per_serving) }}g</span>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                        <span class="font-medium text-gray-700">Carbohydrates</span>
                        <span class="font-bold text-yellow-600">{{ number_format($meal->carbs_per_serving) }}g</span>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                        <span class="font-medium text-gray-700">Fat</span>
                        <span class="font-bold text-purple-600">{{ number_format($meal->fat_per_serving) }}g</span>
                    </div>

                    @if ($meal->fiber_per_serving)
                        <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                            <span class="font-medium text-gray-700">Fiber</span>
                            <span class="font-bold text-green-600">{{ number_format($meal->fiber_per_serving) }}g</span>
                        </div>
                    @endif

                    @if ($meal->sugar_per_serving)
                        <div class="flex justify-between items-center p-3 bg-pink-50 rounded-lg">
                            <span class="font-medium text-gray-700">Sugar</span>
                            <span class="font-bold text-pink-600">{{ number_format($meal->sugar_per_serving) }}g</span>
                        </div>
                    @endif

                    @if ($meal->sodium_per_serving)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="font-medium text-gray-700">Sodium</span>
                            <span class="font-bold text-gray-600">{{ number_format($meal->sodium_per_serving) }}mg</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Dietary Information -->
            @php
                $dietaryData = $meal->dietary_tags ?: [];
                $tags = [];
                if (isset($dietaryData['category'])) {
                    $tags[] = $dietaryData['category'];
                }
                if (isset($dietaryData['meal_times']) && is_array($dietaryData['meal_times'])) {
                    $tags = array_merge($tags, $dietaryData['meal_times']);
                }
            @endphp

            @if (is_array($tags) && count($tags) > 0)
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Dietary Tags</h3>

                    <div class="flex flex-wrap gap-2">
                        @foreach ($tags as $tag)
                            <span class="px-3 py-2 bg-green-100 text-green-800 text-sm rounded-full font-medium">
                                {{ ucwords(str_replace('_', ' ', $tag)) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Allergen Information -->
            @if ($meal->allergen_info && is_array($meal->allergen_info) && count($meal->allergen_info) > 0)
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Allergen Information</h3>

                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
                        <div class="flex items-center mb-2">
                            <span class="text-red-500 mr-2">⚠️</span>
                            <span class="font-medium text-red-800">Contains:</span>
                        </div>
                        <ul class="list-disc list-inside text-red-700 space-y-1">
                            @foreach ($meal->allergen_info as $allergen)
                                <li>{{ ucwords(str_replace('_', ' ', $allergen)) }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Meal Visibility -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Visibility</h3>

                <div class="flex items-center">
                    @if ($meal->is_public)
                        <span class="flex items-center px-3 py-2 bg-green-100 text-green-800 rounded-lg">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                <path fill-rule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Public
                        </span>
                    @else
                        <span class="flex items-center px-3 py-2 bg-gray-100 text-gray-800 rounded-lg">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"
                                    clip-rule="evenodd"></path>
                                <path
                                    d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z">
                                </path>
                            </svg>
                            Private
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-600 mt-2">
                    @if ($meal->is_public)
                        This meal is visible to other dietitians
                    @else
                        This meal is only visible to you
                    @endif
                </p>
            </div>
        </div>
    </div>

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
    @endpush

@endsection
