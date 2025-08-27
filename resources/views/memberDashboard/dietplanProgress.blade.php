@extends('memberDashboard.layout')

@section('content')

    <!-- Diet Progress Tracker -->
    <div id="dietProgressSections" class="mt-4">

        <!-- Section Header -->
        <div class="bg-[#1E1E1E] text-white px-8 py-6 rounded-lg shadow mb-6 mt-4">
            <h2 class="text-2xl font-bold">Track Your Diet Progress</h2>
            <p class="text-sm text-gray-300 mt-1">Monitor your meal compliance, weekly weight, and progress photos.</p>
        </div>

        <!-- Daily Compliance Section -->
        <div id="dailyComplianceSectionTop"
            class="hidden bg-white p-8 rounded-lg w-full max-w-xs md:max-w-7xl my-4 text-center shadow-md mx-auto">
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
        </div>

        <!-- Weekly Weight Section -->
        <div id="weeklyWeightSectionTop"
            class="hidden bg-white p-8 rounded-lg w-full max-w-xs md:max-w-7xl my-4 text-center shadow-md mx-auto">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-center">
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
        </div>

        <!-- Progress Photos Section -->
        <div id="dietProgressPhotosSectionTop"
            class="hidden bg-white p-8 rounded-lg w-full max-w-xs md:max-w-7xl my-4 text-center shadow-md mx-auto">
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
        </div>

        <!-- Buttons -->
        <div class="w-full max-w-xs md:max-w-7xl p-8 bg-white rounded-lg my-4 text-center shadow-md mx-auto">
            <div class="flex flex-col sm:flex-row sm:flex-wrap gap-4 justify-start text-left">
                <button id="dailyComplianceBtn"
                    class="bg-blue-100 text-blue-800 font-semibold px-4 py-2 rounded-lg w-48 hover:bg-blue-200 hover:scale-105 transition duration-200 shadow">
                    Daily Compliance
                </button>
                <button id="weeklyWeightBtn"
                    class="bg-green-100 text-green-800 font-semibold px-4 py-2 rounded-lg w-48 hover:bg-green-200 hover:scale-105 transition duration-200 shadow">
                    Weekly Weight
                </button>
                <button id="progressPhotosBtn"
                    class="bg-yellow-100 text-yellow-800 font-semibold px-4 py-2 rounded-lg w-48 hover:bg-yellow-200 hover:scale-105 transition duration-200 shadow">
                    Progress Photos
                </button>
            </div>
        </div>

        <!-- Daily Compliance Section -->
        <div id="dailyComplianceSectionBottom"
            class="hidden bg-white p-8 rounded-lg w-full max-w-xs md:max-w-7xl my-4 text-center shadow-md mx-auto">

            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <h2 class="text-xl text-gray-800 sm:text-2xl font-bold">Daily Meal Completion
                    ({{ \Carbon\Carbon::now()->format('d M Y') }})</h2>
                <button onclick="openMealCompletionModal()"
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                    + Add Meal Status
                </button>
            </div>

            <div class="bg-white p-6 rounded-lg w-full max-w-md mx-auto mt-6">
                <canvas id="mealComplianceChart" width="400" height="400"></canvas>
            </div>

            <div id="MealCompletionModal" role="dialog" aria-modal="true"
                class="fixed inset-0 flex backdrop-blur-sm bg-white/20 hidden items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-[0_0_15px_4px_rgba(241,30,19,0.5)]">
                    <h2 class="text-md md:text-3xl font-bold text-center mb-6 mt-2">Meal Completion Details</h2>

                    <!-- Form -->
                    <form action="{{ route('member.dietplan.mealCompletion.store') }}" method="POST"
                        enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                            @foreach ($dailyMeals as $meal => $completed)
                                <div
                                    class="bg-white rounded-xl border border-gray-300 shadow-lg p-5 flex flex-col items-center text-center transform transition hover:scale-105 hover:shadow-2xl">
                                    <h3 class="text-lg font-bold mb-4 text-blue-700">{{ ucfirst($meal) }}</h3>

                                    <div class="flex flex-col gap-3 w-full">
                                        <label
                                            class="inline-flex items-center justify-center gap-2 p-2 rounded-lg cursor-pointer transition hover:bg-green-100">
                                            <input type="radio" name="meals[{{ $meal }}]" value="1"
                                                class="form-radio text-green-600" {{ $completed ? 'checked' : '' }}>
                                            <span class="font-medium text-green-700">Completed ✅</span>
                                        </label>

                                        <label
                                            class="inline-flex items-center justify-center gap-2 p-2 rounded-lg cursor-pointer transition hover:bg-red-100">
                                            <input type="radio" name="meals[{{ $meal }}]" value="0"
                                                class="form-radio text-red-600" {{ !$completed ? 'checked' : '' }}>
                                            <span class="font-medium text-red-700">Skipped ❌</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <input type="hidden" name="dietplan_id" value="{{ $dietPlan->dietplan_id }}">

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-2 mt-6">
                            <button type="button" onclick="closeMealCompletionModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded">
                                Cancel
                            </button>
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                                Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Weekly Weight Section -->
        <div id="weeklyWeightSectionBottom"
            class="hidden bg-white p-8 rounded-lg w-full max-w-xs md:max-w-7xl my-4 text-center shadow-md mx-auto">

            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <h2 class="text-xl text-gray-800 sm:text-2xl font-bold"> Weekly Weight Tracking
                    ({{ \Carbon\Carbon::now()->startOfWeek()->format('d M Y') }} -
                    {{ \Carbon\Carbon::now()->endOfWeek()->format('d M Y') }})</h2>
                <button onclick="openWeightTrackingModal()"
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                    + Add Weight
                </button>
            </div>

            <div id="weeklyWeightChartContainer" class="mt-8">
                <canvas id="weightChart" width="400" height="200"></canvas>
            </div>

            <div id="WeightTrackingModal" role="dialog" aria-modal="true"
                class="fixed inset-0 flex backdrop-blur-sm bg-white/20 hidden items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-[0_0_15px_4px_rgba(241,30,19,0.5)]">
                    <h2 class="text-md md:text-3xl font-bold text-center mb-6 mt-2">Weight Tracking Details</h2>

                    <!-- Form -->
                    <form action="{{ route('member.dietplan.weight.store') }}" method="POST"
                        enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div class="flex flex-col">
                            <label for="weight" class="font-medium text-left text-gray-700">Current Weight (kg)</label>
                            <input type="number" name="weight" id="weight" step="0.1"
                                class="mt-1 p-2 border border-gray-300 rounded focus:outline-none focus:ring-red-500 focus:border-red-500"
                                required>
                        </div>

                        <div class="flex flex-col">
                            <label for="weightNote" class="font-medium text-left text-gray-700">Note (optional)</label>
                            <textarea name="weightNote" id="weightNote" rows="2"
                                class="mt-1 p-2 border border-gray-300 rounded focus:outline-none focus:ring-red-500 focus:border-red-500"></textarea>
                        </div>

                        <input type="hidden" name="dietplan_id" value="{{ $dietPlan->dietplan_id }}">

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-2 mt-6">
                            <button type="button" onclick="closeWeightTrackingModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded">
                                Cancel
                            </button>
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                                Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Progress Photos Section -->
        <div id="dietProgressPhotosSectionBottom"
            class="hidden bg-white p-8 rounded-lg w-full max-w-xs md:max-w-7xl my-4 text-center shadow-md mx-auto">

            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <h2 class="text-xl text-gray-800 sm:text-2xl font-bold">Progress Photos
                    ({{ \Carbon\Carbon::now()->format('F Y') }}) </h2>
                <button onclick="openProgressPhotosModal()"
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                    + Add Photo
                </button>
            </div>

            <div id="progressPhotosModal" role="dialog" aria-modal="true"
                class="fixed inset-0 flex backdrop-blur-sm bg-white/20 hidden items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-[0_0_15px_4px_rgba(241,30,19,0.5)]">
                    <h2 class="text-md md:text-3xl font-bold text-center mb-5">Add Progress Photo</h2>

                    <!-- Form -->
                    <form action="{{ route('member.dietplan.photo') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf

                        <input type="hidden" name="dietplan_id" value="{{ $dietPlan->dietplan_id }}">

                        <!-- Date of Photo -->
                        <div>
                            <label for="photoDate" class="block mb-2 font-semibold text-left">Date of Photo:</label>
                            <input type="date" id="photoDate" name="photoDate" required
                                class="border border-gray-300 rounded p-2 mb-4 w-full focus:outline-none focus:ring-red-500 focus:border-red-500" />
                        </div>

                        <!-- Photo Upload Input -->
                        <div>
                            <label for="photoFile" class="block mb-2 font-semibold text-left">Upload Photo:</label>
                            <input type="file" id="photoFile" name="photoFile" accept="image/*" required
                                class="mb-4 w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-red-500 focus:border-red-500" />
                        </div>

                        <!-- Optional Notes Textarea -->
                        <div>
                            <label for="photoNote" class="block mb-2 font-semibold text-left">Notes (optional):</label>
                            <textarea id="photoNote" name="photoNote" rows="2"
                                class="border border-gray-300 rounded p-2 w-full focus:outline-none focus:ring-red-500 focus:border-red-500"></textarea>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-2 mt-6">
                            <button type="button" onclick="closeProgressPhotosModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded">
                                Cancel
                            </button>
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                                Upload
                            </button>
                        </div>
                    </form>
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
            document.addEventListener("DOMContentLoaded", function() {
                const dailyBtn = document.getElementById("dailyComplianceBtn");
                const weeklyBtn = document.getElementById("weeklyWeightBtn");
                const photosBtn = document.getElementById("progressPhotosBtn");

                // Sections (top + bottom)
                const dailySections = [document.getElementById("dailyComplianceSectionTop"), document.getElementById(
                    "dailyComplianceSectionBottom")];
                const weeklySections = [document.getElementById("weeklyWeightSectionTop"), document.getElementById(
                    "weeklyWeightSectionBottom")];
                const photoSections = [document.getElementById("dietProgressPhotosSectionTop"), document.getElementById(
                    "dietProgressPhotosSectionBottom")];

                function hideAll() {
                    [...dailySections, ...weeklySections, ...photoSections].forEach(sec => sec.classList.add("hidden"));
                }

                // Show daily by default
                hideAll();
                dailySections.forEach(sec => sec.classList.remove("hidden"));

                dailyBtn.addEventListener("click", function() {
                    hideAll();
                    dailySections.forEach(sec => sec.classList.remove("hidden"));
                });

                weeklyBtn.addEventListener("click", function() {
                    hideAll();
                    weeklySections.forEach(sec => sec.classList.remove("hidden"));
                });

                photosBtn.addEventListener("click", function() {
                    hideAll();
                    photoSections.forEach(sec => sec.classList.remove("hidden"));
                });
            });

            function openProgressPhotosModal() {
                document.getElementById('progressPhotosModal').classList.remove('hidden');
            }

            function closeProgressPhotosModal() {
                document.getElementById('progressPhotosModal').classList.add('hidden');
            }

            function openMealCompletionModal() {
                document.getElementById('MealCompletionModal').classList.remove('hidden');
            }

            function closeMealCompletionModal() {
                document.getElementById('MealCompletionModal').classList.add('hidden');
            }

            function openWeightTrackingModal() {
                document.getElementById('WeightTrackingModal').classList.remove('hidden');
            }

            function closeWeightTrackingModal() {
                document.getElementById('WeightTrackingModal').classList.add('hidden');
            }

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
