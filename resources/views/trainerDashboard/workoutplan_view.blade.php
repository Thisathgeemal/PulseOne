@extends('trainerDashboard.layout')

@section('content')
<div class="p-6 text-gray-800">
    <!-- Header Section -->
    <div class="bg-white text-gray-800 p-6 rounded-xl shadow-md border border-gray-200 mb-6">
        <h2 class="text-3xl font-bold mb-4 text-indigo-700">🏋️‍♂️ Workout Plan: {{ $plan->plan_name }}</h2>
        
        <p class="mb-2"><span class="font-semibold">👤 Member:</span> {{ $plan->member->first_name ?? 'N/A' }}</p>
        <p class="mb-2"><span class="font-semibold">📅 Start Date:</span> {{ $plan->start_date->format('d-m-Y') }}</p>
        <p class="mb-2"><span class="font-semibold">📅 End Date:</span> {{ $plan->end_date->format('d-m-Y') }}</p>
        
        <p class="mt-3">
            <span class="font-semibold">✅ Status:</span>
            <span class="inline-block bg-green-100 text-green-800 text-sm font-semibold px-3 py-1 rounded-full">
                {{ $plan->status }}
            </span>
        </p>
    </div>

    <!-- Exercises Grouped by Day -->
    @foreach($groupedExercises->sortKeys() as $day => $exercises)
        <div class="mb-8">
            <h3 class="text-2xl font-bold text-indigo-700 mb-4 border-b border-indigo-300 pb-2">
                Day {{ $day }}
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($exercises as $exercise)
                    <div class="bg-white rounded-xl border border-gray-200 shadow hover:shadow-lg transition p-4">
                        <h4 class="text-xl font-semibold text-gray-800 mb-2">
                            {{ $exercise->exercise->name }}
                        </h4>
                        <p class="text-gray-600 mb-1"><strong>Description:</strong> {{ $exercise->exercise->description }}</p>
                        <p class="text-gray-600 mb-1"><strong>Sets:</strong> {{ $exercise->sets }}</p>
                        <p class="text-gray-600 mb-1"><strong>Reps:</strong> {{ $exercise->reps }}</p>
                        <p class="text-gray-600"><strong>Notes:</strong> {{ $exercise->notes ?: 'N/A' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
@endsection
