@extends('dietitianDashboard.layout')

@section('content')

    <!-- Diet Progress Tracker -->
    <div id="dietProgressSections" class="mt-4">

        <!-- Header -->
        <div class="bg-[#1E1E1E] text-white px-8 py-6 rounded-lg shadow mb-6 mt-4">
            <h2 class="text-2xl font-bold">Diet Plan Progress Tracking</h2>
            <p class="text-sm text-gray-300 mt-1">
                for <span class="text-yellow-400 font-medium">{{ $dietPlan->member->first_name }}
                    {{ $dietPlan->member->last_name }}</span>
            </p>
        </div>

        <!-- Daily Compliance Section -->
        <div id="dailyComplianceSectionTop"
            class="bg-white p-8 rounded-lg w-full max-w-xs md:max-w-7xl mb-6 text-center shadow-md mx-auto">
            <h2 class="text-xl text-left mb-6 text-gray-800 sm:text-2xl font-bold">Daily Meal Completion
                ({{ \Carbon\Carbon::now()->format('d M Y') }})</h2>
            <div class="col-span-1 md:col-span-4 bg-gray-100 p-4 rounded-lg shadow mb-6">
                <div class="w-full bg-gray-300 rounded-full h-4 mt-2">
                    <div class="bg-blue-500 h-4 rounded-full transition-all duration-300 ease-in-out"
                        style="width: {{ $dailyCompliancePercentage }}%;"></div>
                </div>
                <p class="text-sm text-center font-semibold mt-4 text-gray-700">
                    {{ $dailyCompliancePercentage }}% meals completed
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-left">
                @foreach ($dailyMeals as $meal => $completed)
                    <div class="col-span-1 bg-gray-100 p-4 rounded-lg shadow">
                        <h3 class="text-lg text-center font-semibold mb-2 text-blue-800">{{ ucfirst($meal) }}</h3>
                        <p class="text-sm text-center font-semibold {{ $completed ? 'text-green-700' : 'text-red-700' }}">
                            {{ $completed ? 'Completed ✅' : 'Skipped ❌' }}
                        </p>
                    </div>
                @endforeach
            </div>

            <div class="bg-white p-6 rounded-lg w-full max-w-md mx-auto mt-6">
                <canvas id="mealComplianceChart" width="400" height="400"></canvas>
            </div>
        </div>

        <!-- Weekly Weight Section -->
        <div id="weeklyWeightSectionBottom"
            class="bg-white p-8 rounded-lg w-full max-w-xs md:max-w-7xl mb-6 text-center shadow-md mx-auto">

            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <h2 class="text-xl text-gray-800 sm:text-2xl mb-6 font-bold"> Weekly Weight Tracking
                    ({{ \Carbon\Carbon::now()->startOfWeek()->format('d M Y') }} -
                    {{ \Carbon\Carbon::now()->endOfWeek()->format('d M Y') }})</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-center mb-6">
                <!-- Left Box (1/4) -->
                <div class="col-span-1 bg-gray-100 p-4 rounded-lg shadow flex flex-col items-center justify-center">
                    <h3 class="text-lg font-semibold mb-2 text-green-800">Current Weight</h3>
                    <p class="text-md font-semibold text-green-700">{{ $currentWeight }} kg</p>
                </div>

                <!-- Middle Box (2/4 = Half Width) -->
                <div class="col-span-2 bg-gray-100 p-4 rounded-lg shadow flex flex-col items-center justify-center">
                    <h3 class="text-lg font-semibold mb-2 text-green-800">Progress</h3>
                    <div class="w-full bg-gray-300 rounded-full h-4 mt-2">
                        <div class="bg-green-500 h-4 rounded-full transition-all duration-300 ease-in-out"
                            style="width: {{ $progressBarPercentage }}%;"></div>
                    </div>
                    <p class="text-sm font-semibold mt-4 text-gray-700">{{ $progressPercentage }}% toward goal</p>
                </div>

                <!-- Right Box (1/4) -->
                <div class="col-span-1 bg-gray-100 p-4 rounded-lg shadow flex flex-col items-center justify-center">
                    <h3 class="text-lg font-semibold mb-2 text-green-800">Target Weight</h3>
                    <p class="text-md font-semibold text-green-700">{{ $targetWeight }} kg</p>
                </div>
            </div>

            <div id="weeklyWeightChartContainer" class="mt-8">
                <canvas id="weightChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Progress Photos Section -->
        <div id="dietProgressPhotosSectionTop"
            class="bg-white p-8 rounded-lg w-full max-w-xs md:max-w-7xl mb-4 text-center shadow-md mx-auto">
            <h2 class="text-xl text-gray-800 text-left mb-6 sm:text-2xl font-bold">Progress Photos
                ({{ \Carbon\Carbon::now()->format('F Y') }}) </h2>

            <!-- Monthly Progress -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-left">
                <div class="col-span-1 bg-gray-100 p-4 rounded-lg shadow">
                    <h3 class="text-lg text-center font-semibold mb-2 text-orange-800">Monthly Photos</h3>
                    <p class="font-semibold text-sm text-center text-orange-700">
                        {{ $photoProgressData['monthlyPhotoCount'] }} / 8 photos
                    </p>
                </div>

                @php
                    $monthlyProgress = $photoProgressData['monthlyPhotoPercentage'] ?? 0;
                    $monthlyWidth = min($monthlyProgress, 100);
                    $monthlyExtra = $monthlyProgress > 100 ? $monthlyProgress - 100 : 0;
                @endphp

                <div class="col-span-1 md:col-span-3 bg-gray-100 p-4 rounded-lg shadow">
                    <div class="w-full bg-gray-300 rounded-full h-4 mt-2">
                        <div class="bg-orange-500 h-4 rounded-full transition-all duration-300 ease-in-out"
                            style="width: {{ $monthlyWidth }}%;"></div>
                    </div>
                    <p class="text-sm text-center font-semibold mt-4 text-gray-700">
                        {{ min($monthlyProgress, 100) }}% completed
                        @if ($monthlyExtra > 0)
                            <span class="text-orange-600">+ {{ $monthlyExtra }}% Keep it up! 📸</span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Weekly Progress -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6 text-left">
                <div class="col-span-1 bg-gray-100 p-4 rounded-lg shadow">
                    <h3 class="text-lg text-center font-semibold mb-2 text-yellow-800">Weekly Photos</h3>
                    <p class="font-semibold text-sm text-center text-yellow-700">
                        {{ $photoProgressData['weeklyPhotoCount'] }} / 2 photos
                    </p>
                </div>

                @php
                    $weeklyPhotoProgress = $photoProgressData['weeklyPhotoPercentage'] ?? 0;
                    $weeklyPhotoWidth = min($weeklyPhotoProgress, 100);
                    $weeklyPhotoExtra = $weeklyPhotoProgress > 100 ? $weeklyPhotoProgress - 100 : 0;
                @endphp

                <div class="col-span-1 md:col-span-3 bg-gray-100 p-4 rounded-lg shadow">
                    <div class="w-full bg-gray-300 rounded-full h-4 mt-2">
                        <div class="bg-yellow-500 h-4 rounded-full transition-all duration-300 ease-in-out"
                            style="width: {{ $weeklyPhotoWidth }}%;"></div>
                    </div>
                    <p class="text-sm text-center font-semibold mt-4 text-gray-700">
                        {{ min($weeklyPhotoProgress, 100) }}% completed
                        @if ($weeklyPhotoExtra > 0)
                            <span class="text-yellow-600">+ {{ $weeklyPhotoExtra }}% Keep going! 🔥</span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Photo Gallery -->
            <div id="photoGallery" class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-6">
                @foreach ($photos as $photo)
                    <div class="border rounded overflow-hidden shadow-md">
                        <img src="{{ asset('storage/' . $photo->photo_path) }}"
                            alt="Progress photo on {{ $photo->photo_date }}" class="w-full h-48 object-cover" />
                        <div class="p-2 text-left">
                            <p class="text-sm font-semibold">Date:
                                {{ \Carbon\Carbon::parse($photo->photo_date)->format('Y-m-d') }}</p>
                            @if ($photo->note)
                                <p class="text-xs mt-1 text-gray-600">Note: {{ $photo->note }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
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

        <script>
            fetch('/api/weight-chart/{{ $dietPlan->dietplan_id }}')
                .then(res => res.json())
                .then(data => {
                    const ctx = document.getElementById('weightChart').getContext('2d');

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                    label: 'Weight (kg)',
                                    data: data.data,
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    fill: true,
                                    tension: 0.3,
                                    segment: {
                                        borderColor: ctx => ctx.p1.parsed.y > data.targetWeight ? 'red' :
                                            'green'
                                    }
                                },
                                {
                                    label: 'Target Weight',
                                    data: Array(data.labels.length).fill(data.targetWeight),
                                    borderColor: 'rgba(255, 99, 132, 1)',
                                    borderDash: [5, 5],
                                    pointRadius: 0,
                                    fill: false,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const value = context.parsed.y;
                                            const overshoot = value > data.targetWeight ? ' (overshoot)' : '';
                                            return `Weight: ${value} kg${overshoot}`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: false
                                }
                            }
                        }
                    });
                });

            document.addEventListener("DOMContentLoaded", function() {
                // Pass daily meals from backend to JS
                const dailyMeals = @json($dailyMeals);
                const labels = Object.keys(dailyMeals).map(meal => meal.charAt(0).toUpperCase() + meal.slice(1));
                const data = Object.values(dailyMeals).map(val => 1); // Each meal is 1 slice
                const colors = Object.values(dailyMeals).map(val => val === 1 ? 'green' : 'red');

                const ctx = document.getElementById('mealComplianceChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: colors,
                            borderColor: '#fff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const meal = context.label;
                                        const status = dailyMeals[meal.toLowerCase()] ? 'Completed ✅' :
                                            'Skipped ❌';
                                        return meal + ': ' + status;
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush

@endsection
