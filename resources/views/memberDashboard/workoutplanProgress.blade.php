@extends('memberDashboard.layout')

@section('content')
    <!-- Progress Track View -->
    <div id="sections" class="mt-4">

        <div class="bg-[#1E1E1E] text-white px-8 py-6 rounded-lg shadow mb-6 mt-4">
            <h2 class="text-2xl font-bold">Tracker Your Progress</h2>
            <p class="text-sm text-gray-300 mt-1">Monitor your workout performance and improvements.</p>
        </div>

        <div id="dailyProgressSection" class="hidden bg-white p-8 rounded-lg w-full max-w-xs md:max-w-7xl my-4 text-center shadow-md mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-left">
                <div class="col-span-1 bg-gray-100 p-4 rounded-lg shadow">
                    <h3 class="text-lg text-center font-semibold mb-2 text-blue-800">Completed Exercise</h3>
                    <p class="completed-exercise-count font-semibold text-sm text-center text-blue-700">
                        {{ $dailyProgress['completed'] == 0 ? '0' : str_pad($dailyProgress['completed'], 2, '0', STR_PAD_LEFT) }}
                    </p>
                </div>
                <div class="col-span-1 md:col-span-3 bg-gray-100 p-4 rounded-lg shadow">
                    <div class="w-full bg-gray-300 rounded-full h-4 mt-2">
                        <div id="progress-bar" class="bg-blue-500 h-4 rounded-full transition-all duration-300 ease-in-out"
                            style="width: {{ $dailyProgress['percentage'] }}%;">
                        </div>
                    </div>
                    <p id="progress-text" class="text-sm text-center font-semibold mt-4 text-gray-700">
                        {{ $dailyProgress['percentage'] }}% completed
                    </p>
                </div>
            </div>
        </div>

        <div id="weeklyProgressSection" class="hidden bg-white p-8 rounded-lg w-full max-w-xs md:max-w-7xl my-4 text-center shadow-md mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-left">
                <div class="col-span-1 bg-gray-100 p-4 rounded-lg shadow">
                    <h3 class="text-lg text-center font-semibold mb-2 text-green-800">Number of Days</h3>
                    <p class="completed-exercise-count font-semibold text-sm text-center text-green-700">
                        {{ $weeklyProgress['completed'] == 0 ? '0' : str_pad($weeklyProgress['completed'], 2, '0', STR_PAD_LEFT) }}
                    </p>
                </div>

                @php
                    $progressPercentage = $weeklyProgress['percentage'];
                    $displayedWidth = min($progressPercentage, 100);
                    $extra = $progressPercentage > 100 ? $progressPercentage - 100 : 0;
                @endphp

                <div class="col-span-1 md:col-span-3 bg-gray-100 p-4 rounded-lg shadow">
                    <div class="w-full bg-gray-300 rounded-full h-4 mt-2">
                        <div id="progress-bar" class="bg-green-500 h-4 rounded-full transition-all duration-300 ease-in-out"
                            style="width: {{ $displayedWidth }}%;">
                        </div>
                    </div>
                    <p id="progress-text" class="text-sm text-center font-semibold mt-4 text-gray-700">
                        {{ min($progressPercentage, 100) }}% completed
                        @if ($extra > 0)
                            <span class="text-green-600">+ {{ $extra }}%  Going above and beyond! Keep crushing it 🔥</span>
                        @endif
                    </p>
                </div>

            </div>
        </div>

        <div id="PhotosTrackSection" class="hidden bg-white p-8 rounded-lg w-full max-w-xs md:max-w-7xl my-4 text-center shadow-md mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-left">
                <!-- Monthly Photos Count -->
                <div class="col-span-1 bg-gray-100 p-4 rounded-lg shadow">
                    <h3 class="text-lg text-center font-semibold mb-2 text-orange-800">Monthly Photos</h3>
                    <p class="font-semibold text-sm text-center text-orange-700">
                        {{ $photoProgress['monthlyCount'] }} / 8 photos
                    </p>
                </div>

                @php
                    $monthlyProgress = $photoProgress['monthlyProgress'] ?? 0;
                    $monthlyWidth = min($monthlyProgress, 100);
                    $monthlyExtra = $monthlyProgress > 100 ? $monthlyProgress - 100 : 0;
                @endphp

                <div class="col-span-1 md:col-span-3 bg-gray-100 p-4 rounded-lg shadow">
                    <div class="w-full bg-gray-300 rounded-full h-4 mt-2">
                        <div class="bg-orange-500 h-4 rounded-full transition-all duration-300 ease-in-out"
                            style="width: {{ $monthlyWidth }}%;">
                        </div>
                    </div>
                    <p class="text-sm text-center font-semibold mt-4 text-gray-700">
                        {{ min($monthlyProgress, 100) }}% completed
                        @if ($monthlyExtra > 0)
                            <span class="text-orange-600">+ {{ $monthlyExtra }}% Keep it up! 📸</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6 text-left">
                <!-- Weekly Photos Count -->
                <div class="col-span-1 bg-gray-100 p-4 rounded-lg shadow">
                    <h3 class="text-lg text-center font-semibold mb-2 text-yellow-800">Weekly Photos</h3>
                    <p class="font-semibold text-sm text-center text-yellow-700">
                        {{ $photoProgress['weeklyPhotoCount'] }} / 2 photos
                    </p>
                </div>

                @php
                    $weeklyPhotoProgress = $photoProgress['weeklyProgress'];
                    $weeklyPhotoWidth = min($weeklyPhotoProgress, 100);
                    $weeklyPhotoExtra = $weeklyPhotoProgress > 100 ? $weeklyPhotoProgress - 100 : 0;
                @endphp

                <div class="col-span-1 md:col-span-3 bg-gray-100 p-4 rounded-lg shadow">
                    <div class="w-full bg-gray-300 rounded-full h-4 mt-2">
                        <div class="bg-yellow-500 h-4 rounded-full transition-all duration-300 ease-in-out"
                            style="width: {{ $weeklyPhotoWidth }}%;">
                        </div>
                    </div>
                    <p class="text-sm text-center font-semibold mt-4 text-gray-700">
                        {{ min($weeklyPhotoProgress, 100) }}% completed
                        @if ($weeklyPhotoExtra > 0)
                            <span class="text-yellow-600">+ {{ $weeklyPhotoExtra }}% Keep going! 🔥</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div id="monthlyProgressSection" class="hidden bg-white p-8 rounded-lg w-full max-w-xs md:max-w-7xl my-4 text-center shadow-md mx-auto">

            @php
                // Extract values from backend variable
                $monthlyCompletedDays = $monthlyProgressData['completed'] ?? 0;
                $monthlyProgressPercentage = $monthlyProgressData['percentage'] ?? 0;

                // Limit bar to 100% width
                $monthlyDisplayedWidth = min($monthlyProgressPercentage, 100);

                // Bonus message if exceeded
                $monthlyExtra = $monthlyProgressPercentage > 100 ? $monthlyProgressPercentage - 100 : 0;
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-left">
                <!-- Days Completed Card -->
                <div class="col-span-1 bg-gray-100 p-4 rounded-lg shadow">
                    <h3 class="text-lg text-center font-semibold mb-2 text-purple-800">Number of Days</h3>
                    <p class="completed-exercise-count font-semibold text-sm text-center text-purple-700">
                        {{ $monthlyCompletedDays }}
                    </p>
                </div>

                <!-- Progress Bar -->
                <div class="col-span-1 md:col-span-3 bg-gray-100 p-4 rounded-lg shadow">
                    <div class="w-full bg-gray-300 rounded-full h-4 mt-2">
                        <div id="progress-bar" 
                            class="bg-purple-500 h-4 rounded-full transition-all duration-300 ease-in-out" 
                            style="width: {{ $monthlyDisplayedWidth }}%;">
                        </div>
                    </div>
                    <p id="progress-text" class="text-sm text-center font-semibold mt-4 text-gray-700">
                        {{ $monthlyDisplayedWidth }}% completed
                        @if ($monthlyExtra > 0)
                            <span class="text-purple-600">+ {{ $monthlyExtra }}% Exceptional consistency! Keep dominating 🔥</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

    </div>

    <!-- Buttons -->
    <div class="w-full max-w-xs md:max-w-7xl p-8 bg-white rounded-lg my-4 text-center shadow-md mx-auto">
        <div class="flex flex-col sm:flex-row sm:flex-wrap gap-4 justify-start text-left">
            <button id="dailyLogBtn" class="bg-blue-100 text-blue-800 font-semibold px-4 py-2 rounded-lg w-48 hover:bg-blue-200 hover:scale-105 transition duration-200 shadow">
                Daily Exercise Log
            </button>
            <button id="weeklyLogBtn" class="bg-green-100 text-green-800 font-semibold px-4 py-2 rounded-lg w-48 hover:bg-green-200 hover:scale-105 transition duration-200 shadow">
                Weekly Workout Log
            </button>
            <button id="monthlyLogBtn" class="bg-purple-100 text-purple-800 font-semibold px-4 py-2 rounded-lg w-48 hover:bg-purple-300 hover:scale-105 transition duration-200 shadow">
                Monthly workout Log
            </button>
            <button id="progressPhotosBtn" class="bg-yellow-100 text-yellow-800 font-semibold px-4 py-2 rounded-lg w-48 hover:bg-yellow-200 hover:scale-105 transition duration-200 shadow">
                Progress Photos
            </button>
        </div>
    </div>

    <!-- Sections -->
    <div id="sections" class="mt-4">
        <div id="dailyLogSection" class="hidden bg-white p-8 rounded-lg w-full max-w-xs md:max-w-7xl my-4 text-center shadow-md mx-auto">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <h2 class="text-xl text-gray-800 sm:text-2xl font-bold">Daily Exercise Log ({{ \Carbon\Carbon::today()->format('F d, Y') }})</h2>
                <button onclick="openDailyLogModal()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                    + Log Exercise 
                </button>
            </div>

            <!-- Data Table -->
            <div class="overflow-x-auto mt-6">
                <table class="min-w-full bg-white shadow-md rounded-lg border text-base border-gray-200">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-3 px-4 text-left border-b border-gray-300">No</th>
                            <th class="py-3 px-4 text-left border-b border-gray-300">Exercise Name</th>
                            <th class="py-3 px-4 text-left border-b border-gray-300">Sets Completed</th>
                            <th class="py-3 px-4 text-left border-b border-gray-300">Reps Completed</th>
                            <th class="py-3 px-4 text-left border-b border-gray-300">Weight </th>
                            <th class="py-3 px-4 text-left border-b border-gray-300">Logged At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($todayLogs as $index => $log)
                            <tr class="hover:bg-gray-100 transition duration-150">
                                <td class="py-3 px-4 text-left border-b border-gray-200">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 text-left border-b border-gray-200">{{ $log->exercise->name ?? '-' }}</td>
                                <td class="py-3 px-4 text-left border-b border-gray-200">{{ $log->sets_completed }}</td>
                                <td class="py-3 px-4 text-left border-b border-gray-200">{{ $log->reps_completed }}</td>
                                <td class="py-3 px-4 text-left border-b border-gray-200">{{ $log->weight }}</td>
                                <td class="py-3 px-4 text-left border-b border-gray-200">{{ $log->created_at->format('h:i A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Request Modal -->
            <div id="dailyLogModal" role="dialog" aria-modal="true" class="fixed inset-0 flex backdrop-blur-sm bg-white/20 hidden items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-[0_0_15px_4px_rgba(241,30,19,0.5)]">
                    <h2 class="text-md md:text-3xl font-bold text-center mb-5">Add Daily Exercise Log</h2>
                    
                    <!-- Form -->
                    <form action="{{ route('member.workoutplan.exercise.log') }}" method="POST" class="space-y-4">
                        @csrf

                        <input type="hidden" name="workoutplan_id" value="{{ $workoutPlan->workoutplan_id }}">
                        
                        <!-- Exercise -->
                        <div>
                            <label class="block mb-1 text-left text-sm font-medium text-gray-700">Exercise</label>
                            <select id="exerciseSelect" name="exercise_id" required class="border px-4 py-2 rounded w-full focus:outline-none focus:ring-red-500 focus:border-red-500">
                                <option value="">Select Exercise</option>
                                @foreach ($exercises as $ex)
                                    @if (!in_array($ex->exercise->exercise_id, $todayLogs->pluck('exercise_id')->toArray()))
                                        <option value="{{ $ex->exercise->exercise_id }}">
                                            {{ $ex->exercise->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>                    

                        <!-- Sets Completed -->
                        <div>
                            <label for="sets_completed" class="block text-left text-sm font-medium text-gray-700">Sets Completed</label>
                            <input type="number" name="sets_completed" min="0" placeholder="Enter Completed Sets"
                                class="w-full mt-1 px-3 py-2 border rounded-md border-gray-300 text-sm focus:outline-none focus:ring-red-500 focus:border-red-500" />
                        </div>

                        <!-- Reps Completed -->
                        <div>
                            <label for="reps_completed" class="block text-left text-sm font-medium text-gray-700">Reps Completed</label>
                            <input type="number" name="reps_completed" min="0" placeholder="Enter Completed Reps"
                                class="w-full mt-1 px-3 py-2 border rounded-md border-gray-300 text-sm focus:outline-none focus:ring-red-500 focus:border-red-500" />
                        </div>

                        <!-- Weight  -->
                        <div>
                            <label for="weight" class="block text-left text-sm font-medium text-gray-700">Weight (kg)</label>
                            <input type="number" name="weight" step="0.1" min="0" placeholder="Enter Weight"
                                class="w-full mt-1 px-3 py-2 border rounded-md border-gray-300 text-sm focus:outline-none focus:ring-red-500 focus:border-red-500" />
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-2 mt-6">
                            <button type="button" onclick="closeDailyLogModal()" class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded">
                                Cancel
                            </button>
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                                Save Log
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="weeklyLogSection" class="hidden bg-white p-8 rounded-lg w-full max-w-xs md:max-w-7xl my-4 text-center shadow-md mx-auto">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <h2 class="text-xl text-gray-800 sm:text-2xl font-bold my-1">
                    Weekly Workout Log ({{ $startOfWeek->format('M d, Y') }} - {{ $endOfWeek->format('M d, Y') }})
                </h2>
            </div>

            <!-- Data Table -->
            <div class="overflow-x-auto mt-6">
                <table class="min-w-full bg-white shadow-md rounded-lg border text-base border-gray-200">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-3 px-4 text-left border-b border-gray-300">No</th>
                            <th class="py-3 px-4 text-left border-b border-gray-300">Date</th>
                            <th class="py-3 px-4 text-left border-b border-gray-300">Total Exercises</th>
                            <th class="py-3 px-4 text-left border-b border-gray-300">Completed Exercises</th>
                            <th class="py-3 px-4 text-left border-b border-gray-300">Completion Percentage</th>
                            <th class="py-3 px-4 text-left border-b border-gray-300">Workout Duration (minutes)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($weeklyLogs as $index => $log)
                            <tr class="hover:bg-gray-100 transition duration-150">
                                <td class="py-3 px-4 text-left border-b border-gray-200">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 text-left border-b border-gray-200">{{ \Carbon\Carbon::parse($log->log_date)->format('M d, Y') }}</td>
                                <td class="py-3 px-4 text-left border-b border-gray-200">{{ $log->total_exercises }}</td>
                                <td class="py-3 px-4 text-left border-b border-gray-200">{{ $log->completed_exercises }}</td>
                                <td class="py-3 px-4 text-left border-b border-gray-200">{{ $log->completion_percentage }}%</td>
                                <td class="py-3 px-4 text-left border-b border-gray-200">{{ $log->workout_duration }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div id="progressPhotosSection" class="hidden bg-white p-8 rounded-lg w-full max-w-xs md:max-w-7xl my-4 text-center shadow-md mx-auto">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <h2 class="text-xl text-gray-800 sm:text-2xl font-bold">Progress Photos ({{ \Carbon\Carbon::now()->format('F Y') }}) </h2>
                <button onclick="openProgressPhotosModal()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                    + Add Photo 
                </button>
            </div>

            <!-- Request Modal -->
            <div id="progressPhotosModal" role="dialog" aria-modal="true" class="fixed inset-0 flex backdrop-blur-sm bg-white/20 hidden items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-[0_0_15px_4px_rgba(241,30,19,0.5)]">
                    <h2 class="text-md md:text-3xl font-bold text-center mb-5">Add Progress Photo</h2>
                    
                    <!-- Form -->
                    <form action="{{ route('member.workoutplan.photo') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <input type="hidden" name="workoutplan_id" value="{{ $workoutPlan->workoutplan_id }}">
                        
                        <!-- Date of Photo -->
                        <div>
                            <label for="photoDate" class="block mb-2 font-semibold text-left">Date of Photo:</label>
                            <input 
                                type="date" 
                                id="photoDate" 
                                name="photoDate" 
                                required 
                                class="border rounded p-2 mb-4 w-full focus:outline-none focus:ring-red-500 focus:border-red-500" 
                            />
                        </div>                    

                        <!-- Photo Upload Input -->
                        <div>
                            <label for="photoFile" class="block mb-2 font-semibold text-left">Upload Photo:</label>
                            <input 
                                type="file" 
                                id="photoFile" 
                                name="photoFile" 
                                accept="image/*" 
                                required 
                                class="mb-4 w-full border rounded p-2 focus:outline-none focus:ring-red-500 focus:border-red-500" 
                            />
                        </div>

                        <!-- Optional Notes Textarea -->
                        <div>
                            <label for="photoNote" class="block mb-2 font-semibold text-left">Notes (optional):</label>
                            <textarea 
                                id="photoNote" 
                                name="photoNote" 
                                rows="2" 
                                class="border rounded p-2 w-full focus:outline-none focus:ring-red-500 focus:border-red-500"
                            ></textarea>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-2 mt-6">
                            <button type="button" onclick="closeProgressPhotosModal()" class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded">
                                Cancel
                            </button>
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                                Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Gallery -->
            <div id="photoGallery" class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-6">
                @foreach ($photos as $photo)
                    <div class="border rounded overflow-hidden shadow-md">
                        <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Progress photo on {{ $photo->photo_date }}" class="w-full h-48 object-cover" />
                        <div class="p-2 text-left">
                            <p class="text-sm font-semibold">Date: {{ \Carbon\Carbon::parse($photo->photo_date)->format('Y-m-d') }}</p>
                            @if ($photo->note)
                            <p class="text-xs mt-1 text-gray-600">Note: {{ $photo->note }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div id="monthlyLogSection" class="hidden bg-white p-8 rounded-lg w-full max-w-xs md:max-w-7xl my-4 text-center shadow-md mx-auto">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <h2 class="text-xl text-gray-800 sm:text-2xl font-bold my-1">
                    Monthly Workout Log ({{ $startOfMonth->format('M d, Y') }} - {{ $endOfMonth->format('M d, Y') }})
                </h2>
            </div>

            <!-- Data Table -->
            <div class="overflow-x-auto mt-6">
                <table class="min-w-full bg-white shadow-md rounded-lg border text-base border-gray-200">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-3 px-4 text-left border-b border-gray-300">No</th>
                            <th class="py-3 px-4 text-left border-b border-gray-300">Date</th>
                            <th class="py-3 px-4 text-left border-b border-gray-300">Total Exercises</th>
                            <th class="py-3 px-4 text-left border-b border-gray-300">Completed Exercises</th>
                            <th class="py-3 px-4 text-left border-b border-gray-300">Completion Percentage</th>
                            <th class="py-3 px-4 text-left border-b border-gray-300">Workout Duration (minutes)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyLogs as $index => $log)
                            <tr class="hover:bg-gray-100 transition duration-150">
                                <td class="py-3 px-4 text-left border-b border-gray-200">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 text-left border-b border-gray-200">{{ \Carbon\Carbon::parse($log->log_date)->format('M d, Y') }}</td>
                                <td class="py-3 px-4 text-left border-b border-gray-200">{{ $log->total_exercises }}</td>
                                <td class="py-3 px-4 text-left border-b border-gray-200">{{ $log->completed_exercises }}</td>
                                <td class="py-3 px-4 text-left border-b border-gray-200">{{ $log->completion_percentage }}%</td>
                                <td class="py-3 px-4 text-left border-b border-gray-200">{{ $log->workout_duration }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $monthlyLogs->links() }}
            </div>

        </div>
    </div>

    @push('scripts')
        @if(session('success'))
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

        @if(session('error'))
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
            document.addEventListener('DOMContentLoaded', function () {
                // Button keys mapped to section identifiers
                const buttons = {
                    dailyLogBtn: 'daily',
                    weeklyLogBtn: 'weekly',
                    monthlyLogBtn: 'monthly', 
                    progressPhotosBtn: 'progressPhotos',
                };

                const progressSections = {
                    daily: document.getElementById('dailyProgressSection'),
                    weekly: document.getElementById('weeklyProgressSection'),
                    monthly: document.getElementById('monthlyProgressSection'), 
                    progressPhotos: document.getElementById('PhotosTrackSection'),
                };

                const contentSections = {
                    daily: document.getElementById('dailyLogSection'),
                    weekly: document.getElementById('weeklyLogSection'),
                    monthly: document.getElementById('monthlyLogSection'), 
                    progressPhotos: document.getElementById('progressPhotosSection'),
                };

                // Show 'daily' section by default
                progressSections.daily?.classList.remove('hidden');
                contentSections.daily?.classList.remove('hidden');

                // Add click listeners to buttons to toggle sections
                Object.entries(buttons).forEach(([btnId, key]) => {
                    document.getElementById(btnId)?.addEventListener('click', function () {
                        // Hide all sections
                        Object.values(progressSections).forEach(sec => sec?.classList.add('hidden'));
                        Object.values(contentSections).forEach(sec => sec?.classList.add('hidden'));

                        // Show selected section
                        progressSections[key]?.classList.remove('hidden');
                        contentSections[key]?.classList.remove('hidden');
                    });
                });
            });


            function openDailyLogModal() {
                document.getElementById('dailyLogModal').classList.remove('hidden');
            }

            function closeDailyLogModal() {
                document.getElementById('dailyLogModal').classList.add('hidden');
            }

            function openProgressPhotosModal() {
                document.getElementById('progressPhotosModal').classList.remove('hidden');
            }

            function closeProgressPhotosModal() {
                document.getElementById('progressPhotosModal').classList.add('hidden');
            }

        </script>

    @endpush
@endsection
