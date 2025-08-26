@extends('memberDashboard.layout')

@section('content')
    <div class="p-6 text-gray-800">

        <!-- Header -->
        <div class="bg-white text-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-indigo-700 flex items-center gap-2">
                    🥗 Diet Plan: {{ $plan->plan_name }}
                </h2>
                @if (
                    $plan->status === 'Active' &&
                        \Carbon\Carbon::parse($plan->start_date)->isPast() &&
                        \Carbon\Carbon::parse($plan->end_date)->isFuture())
                    <a href="{{ route('member.dietplan.progress') }}"
                        class="w-[110px] px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition text-center">
                        Start
                    </a>
                @endif
            </div>

            <p class="mb-2"><span class="font-semibold">👤 Dietitian:</span> {{ $plan->dietitian->first_name }}
                {{ $plan->dietitian->last_name }}</p>
            <p class="mb-2"><span class="font-semibold">📅 Start Date:</span> {{ $plan->start_date->format('d-m-Y') }}</p>
            <p class="mb-2"><span class="font-semibold">📅 End Date:</span> {{ $plan->end_date->format('d-m-Y') }}</p>

            <p class="mt-3">
                <span class="font-semibold">✅ Status:</span>
                <span
                    class="inline-block text-xs font-semibold px-3 py-1 rounded-full
                    {{ $plan->status === 'Active'
                        ? 'bg-green-100 text-green-700'
                        : ($plan->status === 'Cancelled'
                            ? 'bg-red-100 text-red-700'
                            : ($plan->status === 'Completed'
                                ? 'bg-blue-100 text-blue-700'
                                : 'bg-yellow-100 text-yellow-700')) }}">
                    {{ ucfirst($plan->status) }}
                </span>
            </p>
        </div>

        <!-- Nutrition Overview -->
        <div class="bg-gradient-to-r from-blue-50 shadow-sm to-purple-50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Daily Nutrition Targets</h3>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-white rounded-lg shadow-sm">
                    <div class="text-2xl font-bold text-blue-600">{{ number_format($plan->daily_calories_target) }}</div>
                    <div class="text-sm text-blue-800 font-medium">Calories</div>
                </div>
                <div class="text-center p-4 bg-white rounded-lg shadow-sm">
                    <div class="text-2xl font-bold text-green-600">{{ number_format($plan->daily_protein_target) }}g</div>
                    <div class="text-sm text-green-800 font-medium">Protein</div>
                </div>
                <div class="text-center p-4 bg-white rounded-lg shadow-sm">
                    <div class="text-2xl font-bold text-orange-600">{{ number_format($plan->daily_carbs_target) }}g</div>
                    <div class="text-sm text-orange-800 font-medium">Carbs</div>
                </div>
                <div class="text-center p-4 bg-white rounded-lg shadow-sm">
                    <div class="text-2xl font-bold text-purple-600">{{ number_format($plan->daily_fats_target) }}g</div>
                    <div class="text-sm text-purple-800 font-medium">Fat</div>
                </div>
            </div>
        </div>

        <!-- Plan Description -->
        @if ($plan->plan_description)
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">About Your Diet Plan</h3>
                <p class="text-gray-700 leading-relaxed">{{ $plan->plan_description }}</p>
            </div>
        @endif

        <!-- Meal Schedule -->
        @if ($plan->weekly_schedule && !empty($plan->weekly_schedule))
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recommended Meal Times</h3>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach ($plan->weekly_schedule as $meal => $time)
                        <div class="flex flex-col items-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-lg mb-2">
                                @switch($meal)
                                    @case('breakfast')
                                        🍳
                                    @break

                                    @case('lunch')
                                        🍽️
                                    @break

                                    @case('dinner')
                                        🍛
                                    @break

                                    @case('snack')
                                        🥨
                                    @break

                                    @default
                                        🍴
                                @endswitch
                            </div>
                            <div class="font-medium text-gray-900 capitalize">{{ $meal }}</div>
                            <div class="text-sm text-gray-600 mt-1">{{ \Carbon\Carbon::parse($time)->format('h:i A') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Meal Plans -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Meal Plan</h3>

            @if ($plan->dietPlanMeals && $plan->dietPlanMeals->count() > 0)
                <div class="space-y-6">
                    @php
                        $groupedMeals = $plan->dietPlanMeals->groupBy(function ($meal) {
                            $hour = $meal->time->format('H');
                            if ($hour >= 6 && $hour < 11) {
                                return 'breakfast';
                            } elseif ($hour >= 11 && $hour < 16) {
                                return 'lunch';
                            } elseif ($hour >= 16 && $hour < 22) {
                                return 'dinner';
                            } else {
                                return 'snack';
                            }
                        });
                    @endphp

                    @foreach (['breakfast', 'lunch', 'dinner', 'snack'] as $mealType)
                        @if ($groupedMeals->has($mealType))
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-4 flex items-center capitalize">
                                    <span class="text-xl mr-2">
                                        @switch($mealType)
                                            @case('breakfast')
                                                🍳
                                            @break

                                            @case('lunch')
                                                🍽️
                                            @break

                                            @case('dinner')
                                                🍛
                                            @break

                                            @case('snack')
                                                🥨
                                            @break

                                            @default
                                                🍴
                                        @endswitch
                                    </span>
                                    {{ ucfirst($mealType) }}
                                </h4>
                                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach ($groupedMeals[$mealType] as $dietPlanMeal)
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <h5 class="font-medium text-gray-900 mb-2">
                                                {{ $dietPlanMeal->meal->description ?? 'Custom Meal' }}
                                            </h5>
                                            <div class="text-sm text-gray-600 space-y-1">
                                                <div><strong>Time:</strong> {{ $dietPlanMeal->time->format('g:i A') }}
                                                </div>
                                                <div><strong>Serving:</strong> {{ $dietPlanMeal->quantity }}x</div>
                                                <div><strong>Calories:</strong>
                                                    {{ number_format($dietPlanMeal->calories) }} cal</div>
                                                <div><strong>Protein:</strong> {{ number_format($dietPlanMeal->protein) }}g
                                                </div>
                                                <div><strong>Carbs:</strong> {{ number_format($dietPlanMeal->carbs) }}g
                                                </div>
                                                <div><strong>Fat:</strong> {{ number_format($dietPlanMeal->fat) }}g</div>
                                                @if ($dietPlanMeal->notes)
                                                    <div class="mt-2 text-xs text-gray-500">{{ $dietPlanMeal->notes }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-6xl mb-4">🍽️</div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">No Meals Added Yet</h4>
                    <p class="text-gray-600">Your dietitian is still working on your meal plan. Check back soon!</p>
                </div>
            @endif
        </div>

        <!-- Instructions & Notes -->
        @if ($plan->dietitian_instructions)
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Important Instructions</h3>
                <div class="prose max-w-none">
                    <div class="text-gray-700 whitespace-pre-line">{{ $plan->dietitian_instructions }}</div>
                </div>
            </div>
        @endif

        <!-- Dietitian Contact -->
        <div class="bg-gray-50 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Your Dietitian</h3>
            <div class="flex items-center space-x-4">
                <div
                    class="w-12 h-12 rounded-full bg-gradient-to-r from-green-400 to-blue-500 flex items-center justify-center text-white font-bold text-lg">
                    {{ strtoupper(substr($plan->dietitian->first_name ?? 'D', 0, 1)) }}{{ strtoupper(substr($plan->dietitian->last_name ?? '', 0, 1)) }}
                </div>
                <div>
                    <div class="font-medium text-gray-900">{{ $plan->dietitian->first_name }}
                        {{ $plan->dietitian->last_name }}</div>
                    <div class="text-sm text-gray-600">{{ $plan->dietitian->email }}</div>
                    <div class="text-xs text-gray-500 mt-1">Contact your dietitian for any questions about this plan</div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="mt-8 flex justify-between">
            <a href="{{ route('member.dietplan.myplan') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                ← Back to My Plans
            </a>
        </div>
    </div>
    </div>
@endsection
