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
                        placeholder="Enter Plan Name"
                        required>
                </div>

                <!-- Start Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date"
                        value="{{ old('start_date', $request->preferred_start_date ?? '') }}"
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

                <!-- Plan Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Plan Type</label>
                    <input type="text" name="plan_type" value="{{ $request->plan_type ?? 'N/A' }}" 
                        disabled
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-red-500" />
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <input type="text" name="description" value="{{ $request->description ?? 'N/A' }}" 
                        disabled
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-red-500" />
                </div>

                <!-- Available Days -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Available Days</label>
                    <input type="text" name="available_days" value="{{ $request->available_days ?? 'N/A' }}" 
                        disabled
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
            <option value="{{ $exercise->exercise_id }}" 
                    data-sets="{{ $exercise->default_sets }}" 
                    data-reps="{{ $exercise->default_reps }}" 
                    data-muscle="{{ $exercise->muscle_group }}">
                {{ $exercise->name }}
            </option>
        @endforeach
    </template>

    <!-- Muscle Groups Options -->
    <template id="muscle-group-options">
        <option value="Chest">Chest</option>
        <option value="Back">Back</option>
        <option value="Shoulders">Shoulders</option>
        <option value="Legs">Legs</option>
        <option value="Biceps">Biceps</option>
        <option value="Triceps">Triceps</option>
        <option value="Abs">Abs</option>
        <option value="Full Body">Full Body</option>
    </template>

    <!-- Container where days are added -->
    <div id="exercise-days"></div>

    <!-- Your custom JS -->
    <script>
        let dayCount = 0;
        let exerciseCounters = {};
        const exerciseOptionsTemplate = document.getElementById('exercise-options').innerHTML;
        const muscleGroupOptionsTemplate = document.getElementById('muscle-group-options').innerHTML;
        const daysContainer = document.getElementById('exercise-days');

        // Store Choices instances to destroy/re-init later if needed
        const muscleGroupChoices = {};

        function initChoicesOnMuscleSelect(dayIndex) {
            const muscleSelect = document.querySelector(`[data-index='${dayIndex}'] .muscle-group-select`);
            if (!muscleSelect) return;

            // Destroy previous instance if exists
            if (muscleGroupChoices[dayIndex]) {
                muscleGroupChoices[dayIndex].destroy();
            }

            muscleGroupChoices[dayIndex] = new Choices(muscleSelect, {
                removeItemButton: true,
                searchEnabled: true,
                placeholderValue: 'Select muscle groups...',
                shouldSort: false,
                itemSelectText: '',
                // styling class names can be customized if needed
            });
        }

        function addDay(dayData = null) {
            const index = dayCount++;
            exerciseCounters[index] = 0;
            const dayNumber = index + 1;

            const dayHTML = document.createElement('div');
            dayHTML.className = 'bg-white border border-gray-200 rounded-xl shadow p-5 mb-6';
            dayHTML.dataset.index = index;
            dayHTML.innerHTML = `
                <h4 class="text-lg font-semibold text-gray-800 mb-2 day-header">
                    Day <span class="day-number">${dayNumber}</span>
                </h4>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Muscle Groups</label>
                    <select name="days[${index}][muscle_groups][]" multiple
                        class="muscle-group-select w-full"
                        style="min-height: 44px;">
                        ${muscleGroupOptionsTemplate}
                    </select>
                </div>
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

            // Init Choices on the new muscle group select
            initChoicesOnMuscleSelect(index);

            if (dayData) {
                // Set muscle groups correctly (wait for Choices to init)
                const muscleSelect = dayHTML.querySelector('.muscle-group-select');
                if (Array.isArray(dayData.muscle_groups)) {
                    // Clear selections
                    Array.from(muscleSelect.options).forEach(opt => opt.selected = false);
                    dayData.muscle_groups.forEach(muscle => {
                        const option = Array.from(muscleSelect.options).find(o => o.value === muscle);
                        if (option) option.selected = true;
                    });
                    // Refresh Choices UI with new selections
                    muscleGroupChoices[index].setValue(dayData.muscle_groups);

                    updateDayHeaderMuscles(dayHTML, dayNumber, dayData.muscle_groups);
                } else {
                    updateDayHeaderMuscles(dayHTML, dayNumber, dayData.muscle_groups ? [dayData.muscle_groups] : []);
                }

                if (Array.isArray(dayData.exercises)) {
                    dayData.exercises.forEach(ex => addExercise(index, ex.exercise_id, ex.sets, ex.reps));
                } else {
                    addExercise(index);
                }
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
                        <!-- Options populated by JS -->
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
            populateExerciseOptions(select, dayIndex);

            if (exerciseId) {
                select.value = exerciseId;
            } else {
                const selected = select.options[select.selectedIndex];
                div.querySelector('input[name$="[sets]"]').value = selected.getAttribute('data-sets') || '';
                div.querySelector('input[name$="[reps]"]').value = selected.getAttribute('data-reps') || '';
            }
        }

        function populateExerciseOptions(selectElement, dayIndex) {
            const muscleSelect = document.querySelector(`[data-index='${dayIndex}'] .muscle-group-select`);
            const selectedMuscles = muscleGroupChoices[dayIndex]
                ? muscleGroupChoices[dayIndex].getValue(true) // get array of selected muscle values
                : (muscleSelect ? Array.from(muscleSelect.selectedOptions).map(o => o.value) : []);

            const allOptions = document.createElement('select');
            allOptions.innerHTML = exerciseOptionsTemplate;

            selectElement.innerHTML = '';

            const filteredOptions = Array.from(allOptions.options).filter(opt => {
                const muscle = opt.getAttribute('data-muscle');
                if (selectedMuscles.length === 0) return true; // show all if none selected
                if (selectedMuscles.includes('Full Body')) return true;
                return selectedMuscles.includes(muscle);
            });

            filteredOptions.forEach(opt => selectElement.appendChild(opt.cloneNode(true)));

            if (selectElement.options.length === 0) {
                const placeholder = document.createElement('option');
                placeholder.text = 'No exercises for selected muscle groups';
                placeholder.disabled = true;
                placeholder.selected = true;
                selectElement.appendChild(placeholder);
            }
        }

        function updateDayHeaderMuscles(dayElement, dayNumber, muscles) {
            const header = dayElement.querySelector('.day-header');
            if (header) {
                if (muscles.length > 0) {
                    header.textContent = `Day ${dayNumber} (${muscles.join(' / ')})`;
                } else {
                    header.textContent = `Day ${dayNumber}`;
                }
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

            // Listen for muscle group changes (Choices fires change event on original select)
            document.body.addEventListener('change', function (e) {
                if (e.target.classList.contains('muscle-group-select')) {
                    const dayElement = e.target.closest('[data-index]');
                    if (!dayElement) return;

                    const dayIndex = dayElement.getAttribute('data-index');
                    // Get muscles from Choices instance if available
                    const muscles = muscleGroupChoices[dayIndex]
                        ? muscleGroupChoices[dayIndex].getValue(true)
                        : Array.from(e.target.selectedOptions).map(opt => opt.value);

                    const dayNumberSpan = dayElement.querySelector('.day-number');
                    const dayNumber = dayNumberSpan ? dayNumberSpan.textContent : (parseInt(dayIndex) + 1);
                    updateDayHeaderMuscles(dayElement, dayNumber, muscles);

                    // Update exercise dropdowns filter
                    const exercises = dayElement.querySelectorAll('.exercise-select');
                    exercises.forEach(select => {
                        const currentValue = select.value;
                        populateExerciseOptions(select, dayIndex);
                        if (Array.from(select.options).some(o => o.value === currentValue)) {
                            select.value = currentValue;
                        }
                    });
                }

                if (e.target.classList.contains('exercise-select')) {
                    const selected = e.target.options[e.target.selectedIndex];
                    const wrapper = e.target.closest('.grid');
                    if (wrapper) {
                        const setsInput = wrapper.querySelector('input[name$="[sets]"]');
                        const repsInput = wrapper.querySelector('input[name$="[reps]"]');
                        if (setsInput) setsInput.value = selected.getAttribute('data-sets') || '';
                        if (repsInput) repsInput.value = selected.getAttribute('data-reps') || '';
                    }
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
                                    ($plan->status === 'Completed' ? 'bg-blue-100 text-blue-700' :
                                    'bg-yellow-100 text-yellow-700')) }}">
                                    {{ ucfirst($plan->status) }}
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-5 flex gap-3 justify-end opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <a href="{{ route('trainer.workoutplan.view', $plan->workoutplan_id) }}"
                            class="w-[110px] px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition text-center">
                                View
                            </a>
                            <a href="{{ route('trainer.workoutplan.progress', $plan->workoutplan_id) }}"
                            class="w-[110px] px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition text-center">
                                Track
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
