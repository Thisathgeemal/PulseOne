@extends('dietitianDashboard.layout')

@section('content')

    @if ($errors->any())
        <!-- Meal Image Placeholder -->
        <div class="h-40 bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center relative">
            <span class="text-5xl">🍽️</span>

            {{-- Delete button --}}
            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414
            1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707
            7.293z"
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
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold">Meal Library</h2>
                <p class="text-sm text-gray-300 mt-1">Manage your meal recipes and nutrition database</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-200">Total meals</div>
                <div class="bg-white text-[#1E1E1E] px-3 py-2 rounded-lg font-semibold">
                    {{ isset($meals) ? $meals->total() : 0 }}
                </div>
            </div>

            <a href="{{ route('dietitian.meals.create') }}"
                class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors font-medium">
                Add New Meal
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-800">Filter Meals</h3>
            <div class="text-sm text-gray-500">
                {{ isset($meals) ? $meals->total() : 0 }} meals found
            </div>
        </div>

        @php
            $mealTimeLabels = [
                'breakfast' => 'Morning',
                'lunch' => 'Afternoon',
                'dinner' => 'Night',
                'snack' => 'Evening',
            ];
        @endphp

        <div class="block">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search meals..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        <option value="">All Categories</option>
                        @foreach ($categories ?? ['breakfast', 'lunch', 'dinner', 'snack', 'pre_workout', 'post_workout'] as $category)
                            <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $category)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Meal Time -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meal Time</label>
                    <select name="meal_time"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        <option value="">All Meal Time</option>
                        @foreach ($mealTimes as $time)
                            <option value="{{ $time }}" {{ request('meal_time') === $time ? 'selected' : '' }}>
                                {{ $mealTimeLabels[$time] ?? ucfirst($time) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Visibility -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Visibility</label>
                    <select name="visibility"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        <option value="">All Visibility</option>
                        @foreach ($visibility ?? ['public', 'private'] as $tag)
                            <option value="{{ $tag }}" {{ request('visibility') === $tag ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $tag)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-4 flex justify-end space-x-3">
                    <a href="{{ route('dietitian.meals') }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Clear
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if (isset($meals) && $meals->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            @foreach ($meals as $meal)
                <div
                    class="group bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-xl hover:scale-[1.02] transition-all duration-300 overflow-hidden">
                    <!-- Meal Image Placeholder -->
                    <div
                        class="h-40 bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center relative">
                        <span class="text-5xl">🍽️</span>

                        {{-- Delete button --}}
                        <form action="{{ route('dietitian.meals.destroy', $meal) }}" method="POST"
                            class="delete-form absolute top-3 right-3 z-50">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Delete"
                                class="delete-btn text-white hover:text-black transition-colors text-xl">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>

                        @if (!$meal->is_public)
                            <span
                                class="absolute top-3.5 right-9 px-2 py-1 bg-black bg-opacity-50 text-white text-xs rounded-full">Private</span>
                        @endif
                    </div>

                    <div class="p-4">
                        <div class="mb-3">
                            <h3 class="text-base font-semibold text-gray-800 line-clamp-2 mb-1">{{ $meal->meal_name }}
                            </h3>
                            <span class="font-normal justify-text text-xs text-gray-500">{{ $meal->description }}</span>
                        </div>

                        <!-- Category and Tags -->
                        <div class="flex flex-wrap gap-1 mb-3">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                {{ $meal->category_display ?? ucwords(str_replace('_', ' ', $meal->category)) }}
                            </span>
                        </div>

                        <!-- Nutrition Summary in Grid -->
                        <div class="grid grid-cols-2 gap-2 mb-3">
                            <div class="bg-red-50 rounded-lg p-2 text-center">
                                <p class="text-sm font-bold text-red-600">{{ number_format($meal->calories_per_serving) }}
                                </p>
                                <p class="text-xs text-gray-500">Cal</p>
                            </div>
                            <div class="bg-blue-50 rounded-lg p-2 text-center">
                                <p class="text-sm font-bold text-blue-600">{{ number_format($meal->protein_per_serving) }}g
                                </p>
                                <p class="text-xs text-gray-500">Protein</p>
                            </div>
                        </div>

                        <!-- Time and Serving -->
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                            <span class="flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                {{ $meal->total_time ?? 0 }}min
                            </span>
                            <span class="flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                {{ $meal->serving_size }} servings
                            </span>
                        </div>

                        <!-- Dietary Tags -->
                        @php
                            $dietaryData = $meal->dietary_tags ?: [];
                            $tags = [];
                            if (isset($dietaryData['meal_times']) && is_array($dietaryData['meal_times'])) {
                                $tags = array_merge($tags, $dietaryData['meal_times']);
                            }
                        @endphp
                        @if (is_array($tags) && count($tags) > 0)
                            <div class="flex flex-wrap gap-1 mb-3">
                                @foreach (array_slice($tags, 0, 2) as $tag)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                        {{ ucwords(str_replace('_', ' ', $tag)) }}
                                    </span>
                                @endforeach
                                @if (count($tags) > 2)
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">
                                        +{{ count($tags) - 2 }}
                                    </span>
                                @endif
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="flex space-x-2">
                            <a href="{{ route('dietitian.meals.show', $meal) }}"
                                class="flex-1 px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-center text-sm font-medium">
                                View Details
                            </a>
                            @if ($meal->dietitian_id === auth()->id())
                                <a href="{{ route('dietitian.meals.edit', $meal) }}"
                                    class="px-3 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm">
                                    Edit
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if (isset($meals) && $meals->hasPages())
            <div class="mt-8">
                {{ $meals->withQueryString()->links() }}
            </div>
        @endif
    @else
        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
            <div class="w-20 h-20 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                <span class="text-3xl">🍽️</span>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-4">
                @if (request()->hasAny(['search', 'category', 'meal_time', 'dietary_tag']))
                    No Meals Found
                @else
                    No Meals in Library
                @endif
            </h3>
            <p class="text-gray-600 mb-6">
                @if (request()->hasAny(['search', 'category', 'meal_time', 'dietary_tag']))
                    Try adjusting your filters to find more meals.
                @else
                    Start building your meal library by adding nutritious recipes.
                @endif
            </p>
            <a href="{{ route('dietitian.meals.create') }}"
                class="inline-flex items-center px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors font-medium">
                <span class="mr-2">+</span>
                Add First Meal
            </a>
        </div>
    @endif

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

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
            // Wait for the DOM to load
            document.addEventListener('DOMContentLoaded', function() {
                const deleteForms = document.querySelectorAll('.delete-form');

                deleteForms.forEach(function(form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault(); // Prevent the default form submit

                        Swal.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d32f2f',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Yes'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit(); // Submit the form if confirmed
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
@endsection
