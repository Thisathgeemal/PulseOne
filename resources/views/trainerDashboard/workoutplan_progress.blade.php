@extends('trainerDashboard.layout')

@section('content')

    <!-- Header Section -->
    <div class="bg-[#1E1E1E] text-white px-8 py-6 rounded-lg shadow mb-6 mt-4">
        <h2 class="text-2xl font-bold">Fitness Progress Summary</h2>
        <p class="text-sm text-gray-300 mt-1">
            for <span class="text-yellow-400 font-medium">{{ $member->first_name }} {{ $member->last_name }}</span>
        </p>
    </div>

    <div id="dailyProgressSection" class="bg-white p-8 rounded-lg w-full max-w-xs md:max-w-7xl my-4 text-left shadow-md mx-auto">
        <h2 class="text-xl text-gray-800 sm:text-2xl font-bold mb-5">Daily Exercise Log ({{ \Carbon\Carbon::today()->format('F d, Y') }})</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-left">
            <div class="col-span-1 bg-gray-100 p-4 rounded-lg shadow">
                <h3 class="text-lg text-center font-semibold mb-3 text-blue-800">Completed Exercise</h3>
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

    <div id="weeklyProgressSection" class="bg-white p-8 rounded-lg w-full max-w-xs md:max-w-7xl my-4 text-left shadow-md mx-auto">
        <h2 class="text-xl text-gray-800 sm:text-2xl font-bold mb-5">
            Weekly Workout Log ({{ $startOfWeek->format('M d, Y') }} - {{ $endOfWeek->format('M d, Y') }})
        </h2>
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

    <div id="monthlyProgressSection" class="bg-white p-8 rounded-lg w-full max-w-xs md:max-w-7xl my-4 text-left shadow-md mx-auto">
        <h2 class="text-xl text-gray-800 sm:text-2xl font-bold mb-5">
            Monthly Workout Log ({{ $startOfMonth->format('M d, Y') }} - {{ $endOfMonth->format('M d, Y') }})
        </h2>
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

    <div id="PhotosTrackSection" class="bg-white p-8 rounded-lg w-full max-w-xs md:max-w-7xl my-4 text-left shadow-md mx-auto">
        <h2 class="text-xl text-gray-800 sm:text-2xl font-bold mb-5">Progress Photos ({{ \Carbon\Carbon::now()->format('F Y') }}) </h2>
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
                <p class="font-semibold text-sm text-center text-yellow-600">
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

@endsection
