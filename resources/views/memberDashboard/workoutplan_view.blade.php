@extends('memberDashboard.layout')

@section('content')
<div class="p-6 text-gray-800">
    <!-- Header Section -->
    <div class="bg-white text-gray-800 p-6 rounded-xl shadow-md border border-gray-200 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-3xl font-bold text-indigo-700 flex items-center gap-2">
                🏋️‍♂️ Workout Plan: {{ $plan->plan_name }}
            </h2>
            @if(
                $plan->status === 'Active' &&
                \Carbon\Carbon::parse($plan->start_date)->isPast() &&
                \Carbon\Carbon::parse($plan->end_date)->isFuture()
            )
                <a href="{{ route('member.workoutplan.progress', $plan->workoutplan_id) }}"
                class="w-[110px] px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition text-center">
                    Start
                </a>
            @endif
        </div>

        <p class="mb-2"><span class="font-semibold">👤 Trainer:</span> {{ $plan->trainer->first_name ?? 'N/A' }}</p>
        <p class="mb-2"><span class="font-semibold">📅 Start Date:</span> {{ $plan->start_date->format('d-m-Y') }}</p>
        <p class="mb-2"><span class="font-semibold">📅 End Date:</span> {{ $plan->end_date->format('d-m-Y') }}</p>
        
        <p class="mt-3">
            <span class="font-semibold">✅ Status:</span>
            <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full
                {{ $plan->status === 'Active' ? 'bg-green-100 text-green-700' :
                ($plan->status === 'Cancelled' ? 'bg-red-100 text-red-700' :
                'bg-yellow-100 text-yellow-700') }}">
                {{ ucfirst($plan->status) }}
            </span>
        </p>
    </div>


    <!-- Exercises Grouped by Day -->
    @foreach($groupedExercises->sortKeys() as $day => $exercises)
        <div class="mb-8">
            @php
                // Get the muscle_groups from the first exercise of the day (all exercises in a day share the same muscle_groups)
                $muscleGroups = [];
                if ($exercises->first()->muscle_groups) {
                    $muscleGroups = json_decode($exercises->first()->muscle_groups, true);
                }
            @endphp

            <h3 class="text-2xl font-bold text-indigo-700 mb-4 border-b border-indigo-300 pb-2">
                Day {{ $day }}
                @if(!empty($muscleGroups))
                    <span class="text-xl text-gray-600 font-bold">
                        ({{ implode(' / ', $muscleGroups) }})
                    </span>
                @endif
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
                        <p>
                            <span class="font-medium">Video:</span>
                            <a href="{{ $exercise->exercise->video_link }}" target="_blank" class="text-red-600 hover:underline">Watch ▶️ </a>
                        </p>
                        <p class="text-gray-600"><strong>Notes:</strong> {{ $exercise->notes ?: 'N/A' }}</p>                            
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
@endsection
