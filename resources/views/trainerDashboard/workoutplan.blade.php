@extends('trainerDashboard.layout')

@section('content')

    @if(isset($request))
    
    <!-- Header -->
    <div class="bg-[#1E1E1E] text-white px-8 py-6 rounded-lg shadow mb-6 mt-4">
        <h2 class="text-2xl font-bold">Create Workout Plan</h2>
        <p class="text-sm text-gray-300 mt-1">
            for <span class="text-yellow-400 font-medium">{{ $request->member->first_name }} {{ $request->member->last_name }}</span>
        </p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-md p-6">
        <form method="POST" action="{{ route('trainer.workoutplan.store') }}">
            @csrf
            <input type="hidden" name="request_id" value="{{ $request->request_id }}">

            <!-- Plan Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Plan Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Plan Title</label>
                    <input type="text" name="plan_name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                        required>
                </div>

                <!-- Start Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                        required>
                </div>

                <!-- End Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                        required>
                </div>

                <!-- Goal (Disabled) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Goal</label>
                    <input type="text" name="goal_type" value="Weight Loss" disabled
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-red-500" />
                </div>

                <!-- Height & Weight (Disabled) -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Height -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Height</label>
                        <input type="text" name="height" value="175 cm" disabled
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-red-500" />
                    </div>

                    <!-- Weight -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Weight</label>
                        <input type="text" name="weight" value="75 kg" disabled
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-red-500" />
                    </div>
                </div>

                <!-- Available Days -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Available Days</label>
                    <input type="text" name="available_days" value="Mon, Wed, Fri" disabled
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-red-500" />
                </div>
            </div>

            <!-- Exercise Section -->
            <h3 class="text-xl font-bold text-gray-800 mb-4">Assign Exercises (Day-wise)</h3>
            <div id="exercise-days" class="space-y-6"></div>

            <!-- Buttons -->
            <div class="flex flex-col md:flex-row justify-between items-center mt-6 gap-4">
                <button type="button" onclick="addDay()"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2 rounded-lg shadow font-medium transition-all">
                    + Add Another Day
                </button>
                <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg shadow font-medium transition-all">
                    Assign Plan
                </button>
            </div>
        </form>
    </div>

    <!-- Template for Exercise Options -->
    <template id="exercise-options">
        @foreach($exercises as $exercise)
            <option value="{{ $exercise->exercise_id }}" data-sets="{{ $exercise->default_sets }}" data-reps="{{ $exercise->default_reps }}">
                {{ $exercise->name }}
            </option>
        @endforeach
    </template>

    <!-- JavaScript Section -->
    <script>
        let dayCount = 0;
        let exerciseCounters = {};
        const exerciseOptions = document.getElementById('exercise-options').innerHTML;
        const daysContainer = document.getElementById('exercise-days');

        function addDay(dayData = null) {
            const index = dayCount++;
            exerciseCounters[index] = 0;
            const dayNumber = index + 1;

            const dayHTML = document.createElement('div');
            dayHTML.className = 'bg-white border border-gray-200 rounded-xl shadow p-5';
            dayHTML.dataset.index = index;
            dayHTML.innerHTML = `
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Day <span class="day-number">${dayNumber}</span></h4>
                <input type="hidden" name="days[${index}][day_number]" value="${dayNumber}">
                <div class="exercise-group space-y-4"></div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes for this day (optional)</label>
                    <textarea name="days[${index}][notes]"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                        rows="2"></textarea>
                </div>
                <button type="button"
                    class="add-exercise bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 mt-4 rounded-lg text-sm shadow font-medium transition-all">
                    + Add Another Exercise
                </button>
            `;
            daysContainer.appendChild(dayHTML);

            if (dayData) {
                dayData.forEach((ex, i) => addExercise(index, ex.exercise_id, ex.sets, ex.reps));
            } else {
                addExercise(index);
            }
        }

        function addExercise(dayIndex, exerciseId = '', sets = '', reps = '') {
            const group = document.querySelector(`[data-index='${dayIndex}'] .exercise-group`);
            const exerciseIndex = exerciseCounters[dayIndex]++;
            const div = document.createElement('div');
            div.className = 'grid grid-cols-1 md:grid-cols-3 gap-4 mb-2';
            div.innerHTML = `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Exercise</label>
                    <select name="days[${dayIndex}][exercises][${exerciseIndex}][exercise_id]"
                        class="exercise-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        ${exerciseOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sets</label>
                    <input type="number" name="days[${dayIndex}][exercises][${exerciseIndex}][sets]"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                        value="${sets}" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reps</label>
                    <input type="number" name="days[${dayIndex}][exercises][${exerciseIndex}][reps]"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                        value="${reps}" required>
                </div>
            `;
            group.appendChild(div);

            const select = div.querySelector('select');
            if (exerciseId) {
                select.value = exerciseId;
            } else {
                const selected = select.options[select.selectedIndex];
                div.querySelector('input[name$="[sets]"]').value = selected.getAttribute('data-sets');
                div.querySelector('input[name$="[reps]"]').value = selected.getAttribute('data-reps');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            addDay();

            document.body.addEventListener('click', function (e) {
                if (e.target.classList.contains('add-exercise')) {
                    const dayIndex = e.target.closest('[data-index]').getAttribute('data-index');
                    addExercise(dayIndex);
                }
            });

            document.body.addEventListener('change', function (e) {
                if (e.target.classList.contains('exercise-select')) {
                    const selected = e.target.options[e.target.selectedIndex];
                    const wrapper = e.target.closest('.grid');
                    wrapper.querySelector('input[name$="[sets]"]').value = selected.getAttribute('data-sets') || '';
                    wrapper.querySelector('input[name$="[reps]"]').value = selected.getAttribute('data-reps') || '';
                }
            });
        });
    </script>

    @else
        <!-- Trainer's Existing Workout Plans -->
        <div class="bg-[#1E1E1E] text-white px-8 py-6 rounded-lg shadow mb-6 mt-4">
            <h2 class="text-2xl font-bold">Your Workout Plans</h2>
            <p class="text-sm text-gray-300 mt-1">Below are the workout plans you've created for members.</p>
        </div>

        @if($plans->count())
            <div class="grid gap-6 sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-2">
                @foreach($plans as $plan)
                    <div class="group bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-lg hover:scale-[1.02] transition-all duration-300 p-6 overflow-hidden">
                        <div class="flex justify-between items-start">
                            <div class="flex items-start gap-4">
                                <!-- Avatar with Gradient -->
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 text-white rounded-full flex items-center justify-center font-bold text-lg shadow">
                                    {{ strtoupper(substr($plan->member->first_name, 0, 1)) }}
                                </div>

                                <!-- Info -->
                                <div class="text-left">
                                    <h3 class="text-lg font-bold text-gray-800 mb-1">
                                        {{ $plan->plan_name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 leading-snug">
                                        <span class="font-semibold text-gray-700">Member:</span> {{ $plan->member->first_name }} {{ $plan->member->last_name }}<br>
                                        <span class="font-semibold text-gray-700">Duration:</span>
                                        {{ \Carbon\Carbon::parse($plan->start_date)->format('Y-m-d') }} to
                                        {{ \Carbon\Carbon::parse($plan->end_date)->format('Y-m-d') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div class="mt-1">
                                <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full
                                    {{ $plan->status === 'Active' ? 'bg-green-100 text-green-700' :
                                    ($plan->status === 'Cancelled' ? 'bg-red-100 text-red-700' :
                                    'bg-yellow-100 text-yellow-700') }}">
                                    {{ ucfirst($plan->status) }}
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-3 flex gap-3 justify-end opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <a href="{{ route('trainer.workoutplan.view', $plan->workoutplan_id) }}"
                            class="w-[110px] px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition text-center">
                                View
                            </a>
                            <a href="{{ route('workout.report', $plan->workoutplan_id) }}"
                            class="w-[110px] px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition text-center">
                                Download 
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-200 shadow-md p-6 mt-8 text-center">
                <h2 class="text-xl font-semibold text-gray-600">No workout plans have been created yet.</h2>
                <p class="text-gray-500">Once you approve a workout request and create a plan, it will appear here.</p>
                <a href="{{ route('trainer.request') }}" class="mt-4 inline-block bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Go to Requests</a>
            </div>
        @endif

    @endif

@endsection
