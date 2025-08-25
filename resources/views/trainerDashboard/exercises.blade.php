@extends('trainerDashboard.layout')

@section('content')

    <!-- Header -->
    <div class="w-full px-8 py-6 bg-[#1E1E1E] rounded-lg mb-4 text-left mx-auto shadow-md mt-4">
        <h2 class="text-2xl text-white font-bold">Add New Exercise</h2>
        <p class="text-sm text-gray-300 mt-1">The only bad workout is the one that didn’t happen.</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-md p-6 mt-6">
        <form action="{{ route('trainer.exercises.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Row 1: Exercise Name, Default Sets, Default Reps -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Exercise Name</label>
                    <input type="text" name="name" placeholder="e.g. Push Ups"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"
                        required />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Default Sets</label>
                    <input type="number" name="default_sets" placeholder="e.g. 3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"
                        required />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Default Reps</label>
                    <input type="number" name="default_reps" placeholder="e.g. 10"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"
                        required />
                </div>
            </div>

            <!-- Row 2: Description, Video link, Muscle Group -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Muscle Group</label>
                    <select name="muscle_group"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"
                        required>
                        <option value="">--Select Muscle Group--</option>
                        <option value="Chest">Chest</option>
                        <option value="Back">Back</option>
                        <option value="Shoulders">Shoulders</option>
                        <option value="Legs">Legs</option>
                        <option value="Biceps">Biceps</option>
                        <option value="Triceps">Triceps</option>
                        <option value="Abs">Abs</option>
                        <option value="Full Body">Full Body</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Video Link (YouTube)</label>
                    <input type="url" name="video_link" placeholder="https://www.youtube.com/..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"
                        required />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <input type="text" name="description" placeholder="Describe the exercise"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 resize-none h-10"></input>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md shadow font-medium transition-all">
                    Add Exercise
                </button>
            </div>

        </form>
    </div>

    <!-- Filter by Muscle Group -->
    <div class="flex justify-end items-center mt-6">
        <form method="GET" action="{{ route('trainer.exercises') }}"
            class="flex items-center space-x-2 bg-white p-4 rounded-md">
            <label for="muscle_filter" class="text-sm font-medium text-gray-700">Filter by Muscle Group</label>
            <select name="muscle_group" id="muscle_filter" onchange="this.form.submit()"
                class="w-60 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 text-sm">
                <option value="">Full Body</option>
                @foreach ($allMuscleGroups as $muscle)
                    <option value="{{ $muscle }}" {{ request('muscle_group') == $muscle ? 'selected' : '' }}>
                        {{ $muscle }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Exercise Grid -->
    @if ($exercises->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mt-8">
            @foreach ($exercises as $exercise)
                <div class="relative group transition-transform transform hover:scale-[1.02] duration-300
                    border-l-4 p-5 rounded-lg shadow-sm bg-white border-gray-200 hover:shadow-md"
                    style="border-left-color: {{ $muscleColors[$exercise->muscle_group] ?? '#CBD5E1' }}"
                    data-muscle="{{ $exercise->muscle_group }}">
                    {{-- Delete button --}}
                    @if (isset($exercise->exercise_id))
                        <form action="{{ route('trainer.exercises.destroy', ['id' => $exercise->exercise_id]) }}"
                            method="POST" class="delete-form absolute top-3 right-3 z-50">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Delete"
                                class="delete-btn text-gray-400 hover:text-red-600 transition-colors text-xl">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    @endif

                    {{-- Exercise Content --}}
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $exercise->name }}</h3>
                    <p class="text-gray-700 text-sm mb-3">{{ $exercise->description }}</p>

                    <div class="text-sm space-y-1 text-gray-800">
                        <p>
                            <span class="font-medium">💪 Muscle:</span>
                            {{ $muscleIcons[$exercise->muscle_group] ?? '' }} {{ $exercise->muscle_group }}
                        </p>
                        <p>
                            <span class="font-medium">🔁 Sets:</span> {{ $exercise->default_sets }}
                            <span class="ml-4 font-medium">Reps:</span> {{ $exercise->default_reps }}
                        </p>
                        @if ($exercise->video_link)
                            <p>
                                <span class="font-medium">▶️ Video:</span>
                                <a href="{{ $exercise->video_link }}" target="_blank"
                                    class="text-red-600 hover:underline">Watch</a>
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 shadow-md p-6 mt-8 text-center">
            <p class="text-gray-500">No exercises added yet.</p>
        </div>
    @endif

    <script>
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonColor: '#d32f2f',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>

@endsection
