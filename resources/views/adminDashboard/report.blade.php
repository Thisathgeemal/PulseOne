@extends('adminDashboard.layout')

@section('content')
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Analytics & Reports</h1>
        <p class="text-gray-600 mt-1">Overview of application data, revenue, members, and performance.</p>
    </div>

    <!-- Quick Stats Navigation -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div data-tab="revenue"
            class="analytics-tab bg-red-500 text-white rounded-2xl shadow-md p-4 flex flex-col items-center justify-center cursor-pointer hover:shadow-lg transition duration-200">
            <i class="fas fa-dollar-sign text-3xl mb-2"></i>
            <p class="font-medium text-center">Revenue Analysis</p>
            <span class="w-10 h-1 bg-white rounded-full mt-1 block"></span>
        </div>

        <div data-tab="users"
            class="analytics-tab bg-white text-gray-700 rounded-2xl shadow-md p-5 flex flex-col items-center justify-center cursor-pointer hover:shadow-lg transition duration-200">
            <i class="fas fa-users text-blue-500 text-3xl mb-2"></i>
            <p class="font-medium text-center">User Analysis</p>
            <span class="w-10 h-1 bg-white rounded-full mt-1 block"></span>
        </div>

        <div data-tab="sessions"
            class="analytics-tab bg-white text-gray-700 rounded-2xl shadow-md p-5 flex flex-col items-center justify-center cursor-pointer hover:shadow-lg transition duration-200">
            <i class="fas fa-dumbbell text-teal-500 text-3xl mb-2"></i>
            <p class="font-medium text-center">Sessions Analysis</p>
            <span class="w-10 h-1 bg-white rounded-full mt-1 block"></span>
        </div>

        <div data-tab="feedback"
            class="analytics-tab bg-white text-gray-700 rounded-2xl shadow-md p-5 flex flex-col items-center justify-center cursor-pointer hover:shadow-lg transition duration-200">
            <i class="fas fa-comments text-yellow-500 text-3xl mb-2"></i>
            <p class="font-medium text-center">Feedback Analysis</p>
            <span class="w-10 h-1 bg-white rounded-full mt-1 block"></span>
        </div>
    </div>

    <!-- Revenue Analysis -->
    <div id="revenue" class="analytics-content">
        <!-- Month Navigation & Export Header -->
        <div
            class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4 bg-white rounded-2xl shadow-md p-5 border border-gray-100 hover:shadow-lg transition duration-300">

            <!-- Month Navigation Component -->
            <div class="flex items-center gap-4">
                <button id="prevMonth"
                    class="flex items-center justify-center w-12 h-12 bg-red-50 text-red-500 hover:bg-red-100 rounded-full shadow-md transition duration-200">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <span id="currentMonth"
                    class="px-6 py-3 !bg-red-50 rounded-full shadow-sm font-semibold text-gray-800 text-lg min-w-[140px] text-center"></span>

                <button id="nextMonth"
                    class="flex items-center justify-center w-12 h-12 bg-red-50 text-red-500 hover:bg-red-100 rounded-full shadow-md transition duration-200">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <!-- Export Button Component -->
            <button id="exportReportBtn"
                class="flex items-center gap-3 px-5 py-2 bg-gradient-to-r bg-red-500 hover:bg-red-600 text-white rounded-2xl shadow-md font-semibold transition duration-200 transform hover:-translate-y-1 hover:scale-105">
                <i class="fas fa-file-download"></i> Export Report
            </button>
        </div>

        <!-- Revenue Summary Cards -->
        <div class="grid md:grid-cols-3 gap-6 mb-6">
            <!-- Total Income -->
            <div
                class="bg-white p-5 rounded-2xl shadow-md flex items-center gap-4 border border-gray-100 hover:shadow-lg transition duration-300">
                <span class="bg-red-100 text-red-600 p-4 rounded-full text-xl"><i class="fas fa-coins"></i></span>
                <div>
                    <p class="text-gray-500 text-sm">Total Income</p>
                    <p id="totalIncome" class="text-gray-800 font-bold text-lg">Rs. 0</p>
                </div>
            </div>

            <!-- Daily Avg -->
            <div
                class="bg-white p-5 rounded-2xl shadow-md flex items-center gap-4 border border-gray-100 hover:shadow-lg transition duration-300">
                <span class="bg-blue-100 text-blue-600 p-4 rounded-full text-xl"><i class="fas fa-calendar-day"></i></span>
                <div>
                    <p class="text-gray-500 text-sm">Daily Avg Income</p>
                    <p id="dailyAvg" class="text-gray-800 font-bold text-lg">Rs. 0</p>
                </div>
            </div>

            <!-- Best Day -->
            <div
                class="bg-white p-5 rounded-2xl shadow-md flex items-center gap-4 border border-gray-100 hover:shadow-lg transition duration-300">
                <span class="bg-yellow-100 text-yellow-600 p-4 rounded-full text-xl"><i class="fas fa-fire"></i></span>
                <div>
                    <p class="text-gray-500 text-sm">Best Performing Day</p>
                    <p id="bestDay" class="text-gray-800 font-bold text-lg">-</p>
                </div>
            </div>

            <!-- Top Membership -->
            <div
                class="bg-white p-5 rounded-2xl shadow-md flex items-center gap-4 border border-gray-100 hover:shadow-lg transition duration-300">
                <span class="bg-green-100 text-green-600 p-4 rounded-full text-xl"><i class="fas fa-trophy"></i></span>
                <div>
                    <p class="text-gray-500 text-sm">Top Membership</p>
                    <p id="topMembership" class="text-gray-800 font-bold text-lg">-</p>
                </div>
            </div>

            <!-- Preferred Payment -->
            <div
                class="bg-white p-5 rounded-2xl shadow-md flex items-center gap-4 border border-gray-100 hover:shadow-lg transition duration-300">
                <span class="bg-purple-100 text-purple-600 p-4 rounded-full text-xl"><i
                        class="fas fa-credit-card"></i></span>
                <div>
                    <p class="text-gray-500 text-sm">Preferred Payment</p>
                    <p id="paymentMethod" class="text-gray-800 font-bold text-lg">-</p>
                </div>
            </div>
        </div>

        <!-- Full-width Chart -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8 border border-gray-100 hover:shadow-2xl transition duration-300">
            <h3 class="text-2xl font-bold mb-4 text-red-600 flex items-center justify-center gap-3">
                <i class="fas fa-chart-line"></i> Monthly Revenue
            </h3>
            <canvas id="monthlyPaymentsChart" style="max-height:400px; padding: 25px;"></canvas>
        </div>
    </div>

    <!-- User Analysis -->
    <div id="users" class="analytics-content hidden">
        <!-- Month Navigation & Export Header -->
        <div
            class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4 bg-white rounded-2xl shadow-md p-5 border border-gray-100 hover:shadow-lg transition duration-300">
            <!-- Month Navigation Component -->
            <div class="flex items-center gap-4">
                <button id="prevMonthUsers"
                    class="flex items-center justify-center w-12 h-12 bg-red-50 text-red-500 hover:bg-red-100 rounded-full shadow-md transition duration-200">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <span id="currentMonthUsers"
                    class="px-6 py-3 bg-red-50 rounded-full shadow-sm font-semibold text-gray-800 text-lg min-w-[140px] text-center"></span>

                <button id="nextMonthUsers"
                    class="flex items-center justify-center w-12 h-12 bg-red-50 text-red-500 hover:bg-red-100 rounded-full shadow-md transition duration-200">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <!-- Export Button Component -->
            <button id="exportUserReportBtn"
                class="flex items-center gap-3 px-5 py-2 bg-gradient-to-r bg-red-500 hover:bg-red-600 text-white rounded-2xl shadow-md font-semibold transition duration-200 transform hover:-translate-y-1 hover:scale-105">
                <i class="fas fa-file-download"></i> Export Report
            </button>
        </div>

        <!-- User Summary Cards -->
        <div class="grid md:grid-cols-3 gap-6 mb-6">
            <!-- Total Users -->
            <div
                class="bg-white p-5 rounded-2xl shadow-md flex items-center gap-4 border border-gray-100 hover:shadow-lg transition duration-300">
                <span class="bg-red-100 text-red-600 p-4 rounded-full text-xl"><i class="fas fa-users"></i></span>
                <div>
                    <p class="text-gray-500 text-sm">Total Users</p>
                    <p id="totalUsers" class="text-gray-800 font-bold text-lg">0</p>
                </div>
            </div>

            <!-- Active Users -->
            <div
                class="bg-white p-5 rounded-2xl shadow-md flex items-center gap-4 border border-gray-100 hover:shadow-lg transition duration-300">
                <span class="bg-green-100 text-green-600 p-4 rounded-full text-xl"><i class="fas fa-user-check"></i></span>
                <div>
                    <p class="text-gray-500 text-sm">Active Users</p>
                    <p id="activeUsers" class="text-gray-800 font-bold text-lg">0</p>
                </div>
            </div>

            <!-- Inactive Users -->
            <div
                class="bg-white p-5 rounded-2xl shadow-md flex items-center gap-4 border border-gray-100 hover:shadow-lg transition duration-300">
                <span class="bg-yellow-100 text-yellow-600 p-4 rounded-full text-xl"><i
                        class="fas fa-user-times"></i></span>
                <div>
                    <p class="text-gray-500 text-sm">Inactive Users</p>
                    <p id="inactiveUsers" class="text-gray-800 font-bold text-lg">0</p>
                </div>
            </div>

            <!-- New Users This Month -->
            <div
                class="bg-white p-5 rounded-2xl shadow-md flex items-center gap-4 border border-gray-100 hover:shadow-lg transition duration-300">
                <span class="bg-blue-100 text-blue-600 p-4 rounded-full text-xl"><i class="fas fa-user-plus"></i></span>
                <div>
                    <p class="text-gray-500 text-sm">New Users This Month</p>
                    <p id="newUsers" class="text-gray-800 font-bold text-lg">0</p>
                </div>
            </div>

            <!-- Most Users Registered Date -->
            <div
                class="bg-white p-5 rounded-2xl shadow-md flex items-center gap-4 border border-gray-100 hover:shadow-lg transition duration-300">
                <span class="bg-purple-100 text-purple-600 p-4 rounded-full text-xl"><i
                        class="fas fa-calendar-day"></i></span>
                <div>
                    <p class="text-gray-500 text-sm">Most Users Registered Date</p>
                    <p id="mostRegisteredDate" class="text-gray-800 font-bold text-lg">-</p>
                </div>
            </div>
        </div>

        <!-- Grid with Two Charts -->
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <!-- User Roles Chart -->
            <div class="bg-white rounded-lg shadow-lg p-8 border border-gray-100 hover:shadow-2xl transition duration-300">
                <h3 class="text-2xl font-bold mb-4 text-red-600 flex items-center justify-center gap-3">
                    <i class="fas fa-chart-bar"></i> Users By Role
                </h3>
                <canvas id="userRolesChart" style="max-height:400px; padding: 15px;"></canvas>
            </div>

            <!-- User Growth Over Time -->
            <div class="bg-white rounded-lg shadow-lg p-8 border border-gray-100 hover:shadow-2xl transition duration-300">
                <h3 class="text-2xl font-bold mb-4 text-red-600 flex items-center justify-center gap-3">
                    <i class="fas fa-chart-line"></i> User Growth Over Time
                </h3>
                <canvas id="userGrowthChart" style="max-height:400px; padding: 15px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Sessions Analysis -->
    <div id="sessions" class="analytics-content hidden">
        <!-- Month Navigation & Export Header -->
        <div
            class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4 bg-white rounded-2xl shadow-md p-5 border border-gray-100 hover:shadow-lg transition duration-300">
            <!-- Month Navigation Component -->
            <div class="flex items-center gap-4">
                <button id="prevMonthSessions"
                    class="flex items-center justify-center w-12 h-12 bg-red-50 text-red-500 hover:bg-red-100 rounded-full shadow-md transition duration-200">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <span id="currentMonthSessions"
                    class="px-6 py-3 bg-red-50 rounded-full shadow-sm font-semibold text-gray-800 text-lg min-w-[140px] text-center"></span>

                <button id="nextMonthSessions"
                    class="flex items-center justify-center w-12 h-12 bg-red-50 text-red-500 hover:bg-red-100 rounded-full shadow-md transition duration-200">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <!-- Export Button -->
            <button id="exportSessionReportBtn"
                class="flex items-center gap-3 px-5 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-2xl shadow-md font-semibold transition duration-200 transform hover:-translate-y-1 hover:scale-105">
                <i class="fas fa-file-download"></i> Export Report
            </button>
        </div>

        <!-- Session Summary Cards -->
        <div class="grid md:grid-cols-3 gap-6 mb-6">
            <!-- Assign Diet Plan -->
            <div
                class="bg-white p-5 rounded-2xl shadow-md flex items-center gap-4 border border-gray-100 hover:shadow-lg transition duration-300">
                <span class="bg-red-100 text-red-600 p-4 rounded-full text-xl"><i class="fas fa-apple-alt"></i></span>
                <div>
                    <p class="text-gray-500 text-sm">Assign Diet Plan</p>
                    <p id="assignDietPlan" class="text-gray-800 font-bold text-lg">0</p>
                </div>
            </div>

            <!-- Assign Workout Plan -->
            <div
                class="bg-white p-5 rounded-2xl shadow-md flex items-center gap-4 border border-gray-100 hover:shadow-lg transition duration-300">
                <span class="bg-blue-100 text-blue-600 p-4 rounded-full text-xl"><i class="fas fa-dumbbell"></i></span>
                <div>
                    <p class="text-gray-500 text-sm">Assign Workout Plan</p>
                    <p id="assignWorkoutPlan" class="text-gray-800 font-bold text-lg">0</p>
                </div>
            </div>

            <!-- Completed Sessions -->
            <div
                class="bg-white p-5 rounded-2xl shadow-md flex items-center gap-4 border border-gray-100 hover:shadow-lg transition duration-300">
                <span class="bg-green-100 text-green-600 p-4 rounded-full text-xl"><i
                        class="fas fa-check-circle"></i></span>
                <div>
                    <p class="text-gray-500 text-sm">Completed Sessions</p>
                    <p id="completedSessions" class="text-gray-800 font-bold text-lg">0</p>
                </div>
            </div>

            <!-- Pending Requests -->
            <div
                class="bg-white p-5 rounded-2xl shadow-md flex items-center gap-4 border border-gray-100 hover:shadow-lg transition duration-300">
                <span class="bg-yellow-100 text-yellow-600 p-4 rounded-full text-xl"><i
                        class="fas fa-hourglass-half"></i></span>
                <div>
                    <p class="text-gray-500 text-sm">Pending Requests</p>
                    <p id="pendingRequests" class="text-gray-800 font-bold text-lg">0</p>
                </div>
            </div>

            <!-- Upcoming Sessions -->
            <div
                class="bg-white p-5 rounded-2xl shadow-md flex items-center gap-4 border border-gray-100 hover:shadow-lg transition duration-300">
                <span class="bg-purple-100 text-purple-600 p-4 rounded-full text-xl"><i
                        class="fas fa-calendar-alt"></i></span>
                <div>
                    <p class="text-gray-500 text-sm">Upcoming Sessions</p>
                    <p id="upcomingSessions" class="text-gray-800 font-bold text-lg">0</p>
                </div>
            </div>
        </div>

        <!-- Session Charts -->
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <!-- Monthly Sessions Trend -->
            <div class="bg-white rounded-lg shadow-lg p-8 border border-gray-100 hover:shadow-2xl transition duration-300">
                <h3 class="text-2xl font-bold mb-4 text-red-600 flex items-center justify-center gap-3">
                    <i class="fas fa-chart-line"></i> Monthly Sessions Trend
                </h3>
                <canvas id="sessionsTrendChart" style="max-height:350px; padding: 5px;"></canvas>
            </div>

            <!-- Session Type Distribution -->
            <div class="bg-white rounded-lg shadow-lg p-8 border border-gray-100 hover:shadow-2xl transition duration-300">
                <h3 class="text-2xl font-bold mb-4 text-red-600 flex items-center justify-center gap-3">
                    <i class="fas fa-chart-pie"></i> Session Type Distribution
                </h3>
                <canvas id="sessionsTypeChart" style="max-height:300px; padding: 5px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Feedback Analysis -->
    <div id="feedback" class="analytics-content hidden">
        <!-- Month Navigation & Export Header -->
        <div
            class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4 bg-white rounded-2xl shadow-md p-5 border border-gray-100 hover:shadow-lg transition duration-300">
            <!-- Month Navigation Component -->
            <div class="flex items-center gap-4">
                <button id="prevMonthFeedback"
                    class="flex items-center justify-center w-12 h-12 bg-red-50 text-red-500 hover:bg-red-100 rounded-full shadow-md transition duration-200">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <span id="currentMonthFeedback"
                    class="px-6 py-3 bg-red-50 rounded-full shadow-sm font-semibold text-gray-800 text-lg min-w-[140px] text-center"></span>

                <button id="nextMonthFeedback"
                    class="flex items-center justify-center w-12 h-12 bg-red-50 text-red-500 hover:bg-red-100 rounded-full shadow-md transition duration-200">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <!-- Export Button -->
            <button id="exportFeedbackReportBtn"
                class="flex items-center gap-3 px-5 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-2xl shadow-md font-semibold transition duration-200 transform hover:-translate-y-1 hover:scale-105">
                <i class="fas fa-file-download"></i> Export Report
            </button>
        </div>

        <!-- User Summary Cards -->
        <div class="grid md:grid-cols-3 gap-6 mb-6">
            <!-- Average Rating -->
            <div
                class="bg-white p-5 rounded-2xl shadow-md flex items-center gap-4 border border-gray-100 hover:shadow-lg transition duration-300">
                <span class="bg-red-100 text-red-600 p-4 rounded-full text-xl"><i class="fas fa-star"></i></span>
                <div>
                    <p class="text-gray-500 text-sm">Average Rating</p>
                    <p id="avgRating" class="text-gray-800 font-bold text-lg">0 / 5</p>
                </div>
            </div>

            <!-- Total Feedbacks -->
            <div
                class="bg-white p-5 rounded-2xl shadow-md flex items-center gap-4 border border-gray-100 hover:shadow-lg transition duration-300">
                <span class="bg-blue-100 text-blue-600 p-4 rounded-full text-xl"><i class="fas fa-comments"></i></span>
                <div>
                    <p class="text-gray-500 text-sm">Total Feedbacks</p>
                    <p id="totalFeedbacks" class="text-gray-800 font-bold text-lg">0</p>
                </div>
            </div>

            <!-- Positive Feedbacks -->
            <div
                class="bg-white p-5 rounded-2xl shadow-md flex items-center gap-4 border border-gray-100 hover:shadow-lg transition duration-300">
                <span class="bg-green-100 text-green-600 p-4 rounded-full text-xl"><i class="fas fa-thumbs-up"></i></span>
                <div>
                    <p class="text-gray-500 text-sm">Positive Feedback</p>
                    <p id="positiveFeedbacks" class="text-gray-800 font-bold text-lg">0</p>
                </div>
            </div>

            <!-- Negative Feedbacks -->
            <div
                class="bg-white p-5 rounded-2xl shadow-md flex items-center gap-4 border border-gray-100 hover:shadow-lg transition duration-300">
                <span class="bg-yellow-100 text-yellow-600 p-4 rounded-full text-xl"><i
                        class="fas fa-thumbs-down"></i></span>
                <div>
                    <p class="text-gray-500 text-sm">Negative Feedback</p>
                    <p id="negativeFeedbacks" class="text-gray-800 font-bold text-lg">0</p>
                </div>
            </div>

            <!-- Most Mentioned Type -->
            <div
                class="bg-white p-5 rounded-2xl shadow-md flex items-center gap-4 border border-gray-100 hover:shadow-lg transition duration-300">
                <span class="bg-purple-100 text-purple-600 p-4 rounded-full text-xl"><i
                        class="fas fa-lightbulb"></i></span>
                <div>
                    <p class="text-gray-500 text-sm">Most Mentioned Type</p>
                    <p id="mostMentionedType" class="text-gray-800 font-bold text-lg">-</p>
                </div>
            </div>
        </div>

        <!-- Feedback Charts -->
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <!-- Monthly Rating Trend -->
            <div class="bg-white rounded-lg shadow-lg p-8 border border-gray-100 hover:shadow-2xl transition duration-300">
                <h3 class="text-2xl font-bold mb-4 text-red-600 flex items-center justify-center gap-3">
                    <i class="fas fa-chart-line"></i> Monthly Rating Trend
                </h3>
                <canvas id="feedbackTrendChart" style="max-height:350px; padding: 5px;"></canvas>
            </div>

            <!-- Feedback Sentiment Distribution -->
            <div class="bg-white rounded-lg shadow-lg p-8 border border-gray-100 hover:shadow-2xl transition duration-300">
                <h3 class="text-2xl font-bold mb-4 text-red-600 flex items-center justify-center gap-3">
                    <i class="fas fa-chart-pie"></i> Feedback Sentiment
                </h3>
                <canvas id="feedbackSentimentChart" style="max-height:300px; padding: 5px;"></canvas>
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
            // Get the Monthly Revenue
            document.addEventListener('DOMContentLoaded', () => {
                let currentDate = new Date();
                let chart = null;

                function fetchRevenue(year, month) {
                    fetch(`{{ route('admin.report.monthlyRevenue') }}?year=${year}&month=${month}`)
                        .then(res => res.json())
                        .then(data => {
                            // Update Month Label
                            document.getElementById('currentMonth').textContent =
                                new Intl.DateTimeFormat('en-US', {
                                    month: 'long',
                                    year: 'numeric'
                                })
                                .format(new Date(year, month - 1));

                            // Update Stats
                            document.getElementById('totalIncome').textContent =
                                `Rs. ${data.totalIncome}`;
                            document.getElementById('dailyAvg').textContent = `Rs. ${data.dailyAvg}`;
                            document.getElementById('bestDay').textContent = `${data.bestDay}`;
                            document.getElementById('topMembership').textContent =
                                `${data.topMembership}`;
                            document.getElementById('paymentMethod').textContent =
                                `${data.paymentMethod}`;

                            // Chart Data
                            const labels = Object.keys(data.dailyRevenue);
                            const amounts = Object.values(data.dailyRevenue);

                            if (chart) {
                                chart.data.labels = labels;
                                chart.data.datasets[0].data = amounts;
                                chart.update();
                            } else {
                                const ctx = document.getElementById('monthlyPaymentsChart').getContext('2d');
                                chart = new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: labels,
                                        datasets: [{
                                            label: 'Daily Revenue (Rs.)',
                                            data: amounts,
                                            borderColor: 'rgb(239, 68, 68)',
                                            backgroundColor: 'rgba(239, 68, 68, 0.2)',
                                            tension: 0.3,
                                            fill: true,
                                            pointRadius: 5
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        plugins: {
                                            legend: {
                                                display: false
                                            },
                                            tooltip: {
                                                mode: 'index',
                                                intersect: false
                                            }
                                        },
                                        scales: {
                                            x: {
                                                title: {
                                                    display: true,
                                                    text: 'Date'
                                                }
                                            },
                                            y: {
                                                title: {
                                                    display: true,
                                                    text: 'Revenue (Rs.)'
                                                },
                                                beginAtZero: true
                                            }
                                        }
                                    }
                                });
                            }
                        });
                }

                // Initial Fetch
                fetchRevenue(currentDate.getFullYear(), currentDate.getMonth() + 1);

                // Month Navigation
                document.getElementById('prevMonth').addEventListener('click', () => {
                    currentDate.setMonth(currentDate.getMonth() - 1);
                    fetchRevenue(currentDate.getFullYear(), currentDate.getMonth() + 1);
                });

                document.getElementById('nextMonth').addEventListener('click', () => {
                    currentDate.setMonth(currentDate.getMonth() + 1);
                    fetchRevenue(currentDate.getFullYear(), currentDate.getMonth() + 1);
                });
            });

            // Monthly Revenue PDF
            document.addEventListener("DOMContentLoaded", () => {
                const {
                    jsPDF
                } = window.jspdf;

                document.getElementById("exportReportBtn").addEventListener("click", async () => {
                    const pdf = new jsPDF("p", "mm", "a4");

                    // ===== Page Border =====
                    pdf.setDrawColor(0, 0, 0);
                    pdf.setLineWidth(0.8);
                    pdf.rect(10, 10, 190, 277);

                    // ===== Header Background =====
                    pdf.setFillColor(161, 0, 0);
                    pdf.rect(10, 10, 190, 15, "F");

                    // ===== Header Text =====
                    pdf.setFont("helvetica", "bold");
                    pdf.setFontSize(18);
                    pdf.setTextColor(255, 255, 255);
                    pdf.text("PULSEONE", 105, 20, {
                        align: "center"
                    });

                    // ===== Report Title =====
                    pdf.setFont("helvetica", "bold");
                    pdf.setFontSize(16);
                    pdf.setTextColor(0, 0, 0);
                    pdf.text("Revenue Report", 105, 40, {
                        align: "center"
                    });

                    // ===== Generated Date =====
                    pdf.setFontSize(11);
                    pdf.setFont("helvetica", "normal");
                    pdf.text("Generated on: " + new Date().toLocaleDateString(), 14, 50);

                    // ===== Revenue Summary Cards =====
                    pdf.setFont("helvetica", "bold");
                    pdf.setFontSize(14);
                    pdf.text("Revenue Summary", 14, 65);

                    const startY = 75;
                    const cardWidth = 50; // 3 cards per row
                    const cardHeight = 20;
                    const gapX = 8;
                    const gapY = 10;
                    const startX = 20;

                    function drawCard(x, y, bgColor, title, value) {
                        // Card background
                        pdf.setFillColor(...bgColor);
                        pdf.roundedRect(x, y, cardWidth, cardHeight, 3, 3, "F");

                        // Card border
                        pdf.setDrawColor(200, 200, 200);
                        pdf.roundedRect(x, y, cardWidth, cardHeight, 3, 3);

                        // Title
                        pdf.setFont("helvetica", "bold");
                        pdf.setFontSize(10);
                        pdf.setTextColor(255, 255, 255);
                        pdf.text(title, x + 3, y + 8);

                        // Value
                        pdf.setFont("helvetica", "normal");
                        pdf.setFontSize(11);
                        pdf.text(value, x + 3, y + 16);
                    }

                    const cards = [{
                            title: "Total Income",
                            value: document.getElementById("totalIncome").innerText,
                            color: [220, 53, 69]
                        },
                        {
                            title: "Daily Avg",
                            value: document.getElementById("dailyAvg").innerText,
                            color: [0, 123, 255]
                        },
                        {
                            title: "Best Day",
                            value: document.getElementById("bestDay").innerText,
                            color: [255, 193, 7]
                        },
                        {
                            title: "Top Membership",
                            value: document.getElementById("topMembership").innerText,
                            color: [40, 167, 69]
                        },
                        {
                            title: "Payment Method",
                            value: document.getElementById("paymentMethod").innerText,
                            color: [111, 66, 193]
                        }
                    ];

                    // Draw cards in 3 per row
                    cards.forEach((card, index) => {
                        const col = index % 3;
                        const row = Math.floor(index / 3);
                        const x = startX + col * (cardWidth + gapX);
                        const y = startY + row * (cardHeight + gapY);
                        drawCard(x, y, card.color, card.title, card.value);
                    });

                    // ===== Add Chart =====
                    const chartCanvas = document.getElementById("monthlyPaymentsChart");
                    const chartImage = chartCanvas.toDataURL("image/png", 1.0);

                    const chartY = startY + Math.ceil(cards.length / 3) * (cardHeight + gapY) + 15;
                    const chartWidth = 180;
                    const chartHeight = 90;

                    pdf.setFontSize(14);
                    pdf.setFont("helvetica", "bold");
                    pdf.setTextColor(0, 0, 0);
                    pdf.text("Monthly Revenue Chart", 14, chartY);

                    pdf.addImage(chartImage, "PNG", 14, chartY + 10, chartWidth, chartHeight);

                    // ===== Save PDF =====
                    pdf.save("Revenue_Report.pdf");
                });
            });

            // Get the Monthly Users
            document.addEventListener('DOMContentLoaded', () => {
                const ctxRoles = document.getElementById('userRolesChart').getContext('2d');
                const ctxGrowth = document.getElementById('userGrowthChart').getContext('2d');

                let userRolesChart = null;
                let userGrowthChart = null;
                let currentDate = new Date();

                async function fetchUserAnalytics(year, month) {
                    try {
                        // Update month label
                        document.getElementById('currentMonthUsers').textContent =
                            new Intl.DateTimeFormat('en-US', {
                                month: 'long',
                                year: 'numeric'
                            }).format(new Date(year, month - 1));

                        const response = await fetch(
                            `{{ route('admin.report.monthlyUsers') }}?year=${year}&month=${month}`
                        );
                        if (!response.ok) throw new Error('Network response was not ok');
                        const data = await response.json();

                        // Update summary cards
                        document.getElementById('totalUsers').textContent = data.totalUsers;
                        document.getElementById('activeUsers').textContent = data.activeUsers;
                        document.getElementById('inactiveUsers').textContent = data.inactiveUsers;
                        document.getElementById('newUsers').textContent = data.newUsers;
                        document.getElementById('mostRegisteredDate').textContent = data.mostRegisteredDate;

                        // ===== Users by Role (bar chart) =====
                        const roleData = {
                            labels: ['Admins', 'Members', 'Trainers', 'Dietitians'],
                            datasets: [{
                                label: 'Registered Users',
                                data: [
                                    data.rolesCount.admins,
                                    data.rolesCount.members,
                                    data.rolesCount.trainers,
                                    data.rolesCount.dietitians
                                ],
                                backgroundColor: [
                                    'rgba(239, 68, 68, 0.7)',
                                    'rgba(34, 197, 94, 0.7)',
                                    'rgba(59, 130, 246, 0.7)',
                                    'rgba(139, 92, 246, 0.7)'
                                ],
                                borderColor: [
                                    'rgba(239, 68, 68, 1)',
                                    'rgba(34, 197, 94, 1)',
                                    'rgba(59, 130, 246, 1)',
                                    'rgba(139, 92, 246, 1)'
                                ],
                                borderWidth: 1
                            }]
                        };

                        if (userRolesChart) {
                            userRolesChart.data = roleData;
                            userRolesChart.update();
                        } else {
                            userRolesChart = new Chart(ctxRoles, {
                                type: 'bar',
                                data: roleData,
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            display: false
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        }

                        // ===== User Growth Over Time (line chart) =====
                        const growthLabels = data.monthlyGrowth.map(m =>
                            new Intl.DateTimeFormat('en-US', {
                                month: 'short'
                            }).format(new Date(year, m.month - 1))
                        );
                        const growthTotals = data.monthlyGrowth.map(m => m.total);

                        const growthData = {
                            labels: growthLabels,
                            datasets: [{
                                label: 'Total Users',
                                data: growthTotals,
                                borderColor: '#3b82f6',
                                backgroundColor: '#3b82f6',
                                tension: 0.3,
                                pointRadius: 5,
                                pointHoverRadius: 7,
                                fill: false
                            }]
                        };

                        if (userGrowthChart) {
                            userGrowthChart.data = growthData;
                            userGrowthChart.update();
                        } else {
                            userGrowthChart = new Chart(ctxGrowth, {
                                type: 'line',
                                data: growthData,
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            position: 'bottom'
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        }

                    } catch (error) {
                        console.error('Error fetching user analytics:', error);
                    }
                }

                // Month navigation
                document.getElementById('prevMonthUsers').addEventListener('click', () => {
                    currentDate.setMonth(currentDate.getMonth() - 1);
                    fetchUserAnalytics(currentDate.getFullYear(), currentDate.getMonth() + 1);
                });

                document.getElementById('nextMonthUsers').addEventListener('click', () => {
                    currentDate.setMonth(currentDate.getMonth() + 1);
                    fetchUserAnalytics(currentDate.getFullYear(), currentDate.getMonth() + 1);
                });

                // Initial fetch
                fetchUserAnalytics(currentDate.getFullYear(), currentDate.getMonth() + 1);
            });

            // Monthly Users PDF
            document.addEventListener("DOMContentLoaded", () => {
                const {
                    jsPDF
                } = window.jspdf;

                document.getElementById("exportUserReportBtn").addEventListener("click", async () => {
                    const pdf = new jsPDF("p", "mm", "a4");

                    // ===== Page Border =====
                    pdf.setDrawColor(0, 0, 0);
                    pdf.setLineWidth(0.8);
                    pdf.rect(10, 10, 190, 277);

                    // ===== Header Background =====
                    pdf.setFillColor(161, 0, 0);
                    pdf.rect(10, 10, 190, 15, "F");

                    // ===== Header Text =====
                    pdf.setFont("helvetica", "bold");
                    pdf.setFontSize(18);
                    pdf.setTextColor(255, 255, 255);
                    pdf.text("PULSEONE", 105, 20, {
                        align: "center"
                    });

                    // ===== Report Title =====
                    pdf.setFont("helvetica", "bold");
                    pdf.setFontSize(16);
                    pdf.setTextColor(0, 0, 0);
                    pdf.text("User Analysis Report", 105, 40, {
                        align: "center"
                    });

                    // ===== Generated Date =====
                    pdf.setFontSize(11);
                    pdf.setFont("helvetica", "normal");
                    pdf.text("Generated on: " + new Date().toLocaleDateString(), 14, 50);

                    // ===== User Summary Cards =====
                    pdf.setFont("helvetica", "bold");
                    pdf.setFontSize(14);
                    pdf.text("User Summary", 14, 65);

                    const startY = 75;
                    const cardWidth = 50; // same as Session cards
                    const cardHeight = 20;
                    const gapX = 8;
                    const gapY = 10;
                    const startX = 20;

                    function drawCard(x, y, bgColor, title, value) {
                        pdf.setFillColor(...bgColor);
                        pdf.roundedRect(x, y, cardWidth, cardHeight, 3, 3, "F");

                        pdf.setDrawColor(200, 200, 200);
                        pdf.roundedRect(x, y, cardWidth, cardHeight, 3, 3);

                        pdf.setFont("helvetica", "bold");
                        pdf.setFontSize(10);
                        pdf.setTextColor(255, 255, 255);
                        pdf.text(title, x + 3, y + 8);

                        pdf.setFont("helvetica", "normal");
                        pdf.setFontSize(11);
                        pdf.text(value, x + 3, y + 16);
                    }

                    const cards = [{
                            title: "Total Users",
                            value: document.getElementById("totalUsers").innerText,
                            color: [220, 53, 69]
                        },
                        {
                            title: "Active Users",
                            value: document.getElementById("activeUsers").innerText,
                            color: [0, 123, 255]
                        },
                        {
                            title: "Inactive Users",
                            value: document.getElementById("inactiveUsers").innerText,
                            color: [40, 167, 69]
                        },
                        {
                            title: "New Users (This Month)",
                            value: document.getElementById("newUsers").innerText,
                            color: [255, 193, 7]
                        },
                        {
                            title: "Most Registered Date",
                            value: document.getElementById("mostRegisteredDate").innerText,
                            color: [111, 66, 193]
                        }
                    ];

                    // Draw cards in 3 per row
                    cards.forEach((card, index) => {
                        const col = index % 3;
                        const row = Math.floor(index / 3);
                        const x = startX + col * (cardWidth + gapX);
                        const y = startY + row * (cardHeight + gapY);
                        drawCard(x, y, card.color, card.title, card.value);
                    });

                    // ===== Add Charts =====
                    const userRolesCanvas = document.getElementById("userRolesChart");
                    const userGrowthCanvas = document.getElementById("userGrowthChart");

                    const userRolesImage = userRolesCanvas.toDataURL("image/png");
                    const userGrowthImage = userGrowthCanvas.toDataURL("image/png");

                    const chartY = startY + Math.ceil(cards.length / 3) * (cardHeight + gapY) + 15;
                    const chartWidth = 85;
                    const chartHeight = 70;

                    pdf.setFont("helvetica", "bold");
                    pdf.setFontSize(14);
                    pdf.setTextColor(0, 0, 0);

                    pdf.text("Users by Role", startX, chartY);
                    pdf.addImage(userRolesImage, "PNG", startX, chartY + 5, chartWidth, chartHeight);

                    pdf.text("User Growth Over Time", startX + chartWidth + gapX, chartY);
                    pdf.addImage(userGrowthImage, "PNG", startX + chartWidth + gapX, chartY + 5, chartWidth,
                        chartHeight);

                    // ===== Save PDF =====
                    pdf.save("User_Analysis_Report.pdf");
                });
            });

            // Get the Monthly Sessions
            document.addEventListener('DOMContentLoaded', () => {
                const ctxTrend = document.getElementById('sessionsTrendChart').getContext('2d');
                const ctxType = document.getElementById('sessionsTypeChart').getContext('2d');

                let sessionsTrendChart = null;
                let sessionsTypeChart = null;

                let currentDate = new Date();

                async function fetchMonthlySessions(year, month) {
                    try {
                        // Update the month navigation label
                        document.getElementById('currentMonthSessions').textContent =
                            new Intl.DateTimeFormat('en-US', {
                                month: 'long',
                                year: 'numeric'
                            })
                            .format(new Date(year, month - 1));

                        const response = await fetch(
                            `{{ route('admin.report.monthlySessions') }}?year=${year}&month=${month}`
                        );
                        const data = await response.json();

                        // Update Cards
                        document.getElementById('assignDietPlan').textContent = data.assignDietPlanCount;
                        document.getElementById('assignWorkoutPlan').textContent = data.assignWorkoutPlanCount;
                        document.getElementById('completedSessions').textContent = data.completedSessions;
                        document.getElementById('pendingRequests').textContent = data.pendingRequests;
                        document.getElementById('upcomingSessions').textContent = data.upcomingSessions;

                        // --- Trend Chart ---
                        const trendData = {
                            labels: data.dates,
                            datasets: [{
                                    label: 'Diet Plans',
                                    data: data.dietCounts,
                                    borderColor: '#f87171',
                                    backgroundColor: 'rgba(248,113,113,0.2)',
                                    fill: true,
                                    tension: 0.3
                                },
                                {
                                    label: 'Workout Plans',
                                    data: data.workoutCounts,
                                    borderColor: '#60a5fa',
                                    backgroundColor: 'rgba(96,165,250,0.2)',
                                    fill: true,
                                    tension: 0.3
                                },
                                {
                                    label: 'Completed Sessions',
                                    data: data.bookingCounts,
                                    borderColor: '#34d399',
                                    backgroundColor: 'rgba(52,211,153,0.2)',
                                    fill: true,
                                    tension: 0.3
                                }
                            ]
                        };

                        if (sessionsTrendChart) {
                            sessionsTrendChart.data = trendData;
                            sessionsTrendChart.update();
                        } else {
                            sessionsTrendChart = new Chart(ctxTrend, {
                                type: 'line',
                                data: trendData,
                                options: {
                                    responsive: true,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            precision: 0
                                        }
                                    }
                                }
                            });
                        }

                        // --- Type Distribution Chart ---
                        const typeData = {
                            labels: ['Diet Plans', 'Workout Plans', 'Completed Sessions'],
                            datasets: [{
                                data: [
                                    data.sessionTypeData.dietPlans,
                                    data.sessionTypeData.workoutPlans,
                                    data.sessionTypeData.completedSessions
                                ],
                                backgroundColor: ['#f87171', '#60a5fa', '#34d399']
                            }]
                        };

                        if (sessionsTypeChart) {
                            sessionsTypeChart.data = typeData;
                            sessionsTypeChart.update();
                        } else {
                            sessionsTypeChart = new Chart(ctxType, {
                                type: 'pie',
                                data: typeData,
                                options: {
                                    responsive: true
                                }
                            });
                        }

                    } catch (error) {
                        console.error('Error fetching monthly sessions:', error);
                    }
                }

                // Month navigation
                document.getElementById('prevMonthSessions').addEventListener('click', () => {
                    currentDate.setMonth(currentDate.getMonth() - 1);
                    fetchMonthlySessions(currentDate.getFullYear(), currentDate.getMonth() + 1);
                });
                document.getElementById('nextMonthSessions').addEventListener('click', () => {
                    currentDate.setMonth(currentDate.getMonth() + 1);
                    fetchMonthlySessions(currentDate.getFullYear(), currentDate.getMonth() + 1);
                });

                // Initial fetch
                fetchMonthlySessions(currentDate.getFullYear(), currentDate.getMonth() + 1);
            });

            // Monthly Sessions PDF
            document.addEventListener("DOMContentLoaded", () => {
                const {
                    jsPDF
                } = window.jspdf;

                document.getElementById("exportSessionReportBtn").addEventListener("click", async () => {
                    const pdf = new jsPDF("p", "mm", "a4");

                    // ===== Page Border =====
                    pdf.setDrawColor(0, 0, 0);
                    pdf.setLineWidth(0.8);
                    pdf.rect(10, 10, 190, 277);

                    // ===== Header Background =====
                    pdf.setFillColor(161, 0, 0);
                    pdf.rect(10, 10, 190, 15, "F");

                    // ===== Header Text =====
                    pdf.setFont("helvetica", "bold");
                    pdf.setFontSize(18);
                    pdf.setTextColor(255, 255, 255);
                    pdf.text("PULSEONE", 105, 20, {
                        align: "center"
                    });

                    // ===== Report Title =====
                    pdf.setFont("helvetica", "bold");
                    pdf.setFontSize(16);
                    pdf.setTextColor(0, 0, 0);
                    pdf.text("Session Analysis Report", 105, 40, {
                        align: "center"
                    });

                    // ===== Generated Date =====
                    pdf.setFontSize(11);
                    pdf.setFont("helvetica", "normal");
                    pdf.text("Generated on: " + new Date().toLocaleDateString(), 14, 50);

                    // ===== Session Summary Cards =====
                    pdf.setFont("helvetica", "bold");
                    pdf.setFontSize(14);
                    pdf.text("Session Summary", 14, 65);

                    const startY = 75;
                    const cardWidth = 50; // 3 cards per row
                    const cardHeight = 20;
                    const gapX = 8;
                    const gapY = 10;
                    const startX = 20;

                    function drawCard(x, y, bgColor, title, value) {
                        pdf.setFillColor(...bgColor);
                        pdf.roundedRect(x, y, cardWidth, cardHeight, 3, 3, "F");

                        pdf.setDrawColor(200, 200, 200);
                        pdf.roundedRect(x, y, cardWidth, cardHeight, 3, 3);

                        pdf.setFont("helvetica", "bold");
                        pdf.setFontSize(10);
                        pdf.setTextColor(255, 255, 255);
                        pdf.text(title, x + 3, y + 8);

                        pdf.setFont("helvetica", "normal");
                        pdf.setFontSize(11);
                        pdf.text(value, x + 3, y + 16);
                    }

                    const cards = [{
                            title: "Assign Diet Plan",
                            value: document.getElementById("assignDietPlan").innerText,
                            color: [220, 53, 69]
                        },
                        {
                            title: "Assign Workout Plan",
                            value: document.getElementById("assignWorkoutPlan").innerText,
                            color: [0, 123, 255]
                        },
                        {
                            title: "Completed Sessions",
                            value: document.getElementById("completedSessions").innerText,
                            color: [40, 167, 69]
                        },
                        {
                            title: "Pending Requests",
                            value: document.getElementById("pendingRequests").innerText,
                            color: [255, 193, 7]
                        },
                        {
                            title: "Upcoming Sessions",
                            value: document.getElementById("upcomingSessions").innerText,
                            color: [111, 66, 193]
                        }
                    ];

                    for (let i = 0; i < cards.length; i++) {
                        const col = i % 3;
                        const row = Math.floor(i / 3);
                        const x = startX + col * (cardWidth + gapX);
                        const y = startY + row * (cardHeight + gapY);
                        drawCard(x, y, cards[i].color, cards[i].title, cards[i].value);
                    }

                    // ===== Add First Chart (Line Chart) =====
                    const trendCanvas = document.getElementById("sessionsTrendChart");
                    const trendImage = trendCanvas.toDataURL("image/png");

                    const chartWidth = 160;
                    const chartHeight = 70;
                    const chartX = 20; // left-aligned
                    const chartY = startY + Math.ceil(cards.length / 3) * (cardHeight + gapY) + 15;

                    pdf.setFont("helvetica", "bold");
                    pdf.setFontSize(14);
                    pdf.setTextColor(0, 0, 0);
                    pdf.text("Monthly Sessions Trend", chartX, chartY - 8); // left-aligned title
                    pdf.addImage(trendImage, "PNG", chartX, chartY, chartWidth, chartHeight);

                    // ===== Save PDF =====
                    pdf.save("Session_Analysis_Report.pdf");

                });
            });

            // Get the Monthly Feedback
            document.addEventListener('DOMContentLoaded', () => {
                const ctxTrend = document.getElementById('feedbackTrendChart').getContext('2d');
                const ctxSentiment = document.getElementById('feedbackSentimentChart').getContext('2d');

                let feedbackTrendChart = null;
                let feedbackSentimentChart = null;
                let currentDate = new Date();

                async function fetchMonthlyFeedback(year, month) {
                    try {
                        document.getElementById('currentMonthFeedback').textContent =
                            new Intl.DateTimeFormat('en-US', {
                                month: 'long',
                                year: 'numeric'
                            })
                            .format(new Date(year, month - 1));

                        const response = await fetch(
                            `{{ route('admin.report.monthlyFeedback') }}?year=${year}&month=${month}`
                        );
                        const data = await response.json();

                        // Update KPIs
                        document.getElementById('avgRating').textContent = `${data.kpis.avgRating} / 5`;
                        document.getElementById('totalFeedbacks').textContent = data.kpis.totalFeedbacks;
                        document.getElementById('positiveFeedbacks').textContent = data.kpis.positiveFeedbacks;
                        document.getElementById('negativeFeedbacks').textContent = data.kpis.negativeFeedbacks;
                        document.getElementById('mostMentionedType').textContent = data.kpis.mostMentionedType;

                        // Monthly Trend Chart
                        const trendData = {
                            labels: data.charts.dailyLabels,
                            datasets: [{
                                label: 'Average Rating',
                                data: data.charts.dailyRatings,
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239,68,68,0.2)',
                                tension: 0.3,
                                fill: true,
                                pointRadius: 5
                            }]
                        };

                        if (feedbackTrendChart) {
                            feedbackTrendChart.data = trendData;
                            feedbackTrendChart.update();
                        } else {
                            feedbackTrendChart = new Chart(ctxTrend, {
                                type: 'line',
                                data: trendData,
                                options: {
                                    responsive: true,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            max: 5
                                        }
                                    }
                                }
                            });
                        }

                        // Sentiment Chart
                        const sentimentData = {
                            labels: ['Positive', 'Negative'],
                            datasets: [{
                                data: [data.kpis.positiveFeedbacks, data.kpis.negativeFeedbacks],
                                backgroundColor: ['#22c55e', '#ef4444']
                            }]
                        };

                        if (feedbackSentimentChart) {
                            feedbackSentimentChart.data = sentimentData;
                            feedbackSentimentChart.update();
                        } else {
                            feedbackSentimentChart = new Chart(ctxSentiment, {
                                type: 'pie',
                                data: sentimentData,
                                options: {
                                    responsive: true
                                }
                            });
                        }

                    } catch (error) {
                        console.error('Error fetching monthly feedback:', error);
                    }
                }

                // Month navigation
                document.getElementById('prevMonthFeedback').addEventListener('click', () => {
                    currentDate.setMonth(currentDate.getMonth() - 1);
                    fetchMonthlyFeedback(currentDate.getFullYear(), currentDate.getMonth() + 1);
                });
                document.getElementById('nextMonthFeedback').addEventListener('click', () => {
                    currentDate.setMonth(currentDate.getMonth() + 1);
                    fetchMonthlyFeedback(currentDate.getFullYear(), currentDate.getMonth() + 1);
                });

                // Initial fetch
                fetchMonthlyFeedback(currentDate.getFullYear(), currentDate.getMonth() + 1);
            });

            // Feedback Analysis PDF 
            document.addEventListener("DOMContentLoaded", () => {
                const {
                    jsPDF
                } = window.jspdf;

                document.getElementById("exportFeedbackReportBtn").addEventListener("click", () => {
                    const pdf = new jsPDF("p", "mm", "a4");

                    // ===== Page Border =====
                    pdf.setDrawColor(0, 0, 0);
                    pdf.setLineWidth(0.8);
                    pdf.rect(10, 10, 190, 277);

                    // ===== Header Background =====
                    pdf.setFillColor(161, 0, 0);
                    pdf.rect(10, 10, 190, 15, "F");

                    // ===== Header Text =====
                    pdf.setFont("helvetica", "bold");
                    pdf.setFontSize(18);
                    pdf.setTextColor(255, 255, 255);
                    pdf.text("PULSEONE", 105, 20, {
                        align: "center"
                    });

                    // ===== Report Title =====
                    pdf.setFont("helvetica", "bold");
                    pdf.setFontSize(16);
                    pdf.setTextColor(0, 0, 0);
                    pdf.text("Feedback Analysis Report", 105, 40, {
                        align: "center"
                    });

                    // ===== Generated Date =====
                    pdf.setFontSize(11);
                    pdf.setFont("helvetica", "normal");
                    pdf.text("Generated on: " + new Date().toLocaleDateString(), 14, 50);

                    // ===== Summary Cards =====
                    pdf.setFont("helvetica", "bold");
                    pdf.setFontSize(14);
                    pdf.text("Feedback Summary", 14, 65);

                    const startY = 75;
                    const cardWidth = 50; // 3 cards per row
                    const cardHeight = 20;
                    const gapX = 8;
                    const gapY = 10;
                    const startX = 20;

                    function drawCard(x, y, bgColor, title, value) {
                        pdf.setFillColor(...bgColor);
                        pdf.roundedRect(x, y, cardWidth, cardHeight, 3, 3, "F");

                        pdf.setDrawColor(200, 200, 200);
                        pdf.roundedRect(x, y, cardWidth, cardHeight, 3, 3);

                        pdf.setFont("helvetica", "bold");
                        pdf.setFontSize(10);
                        pdf.setTextColor(255, 255, 255);
                        pdf.text(title, x + 3, y + 8);

                        pdf.setFont("helvetica", "normal");
                        pdf.setFontSize(11);
                        pdf.text(value, x + 3, y + 16);
                    }

                    const cards = [{
                            title: "Average Rating",
                            value: document.getElementById("avgRating").innerText,
                            color: [220, 53, 69]
                        },
                        {
                            title: "Total Feedbacks",
                            value: document.getElementById("totalFeedbacks").innerText,
                            color: [0, 123, 255]
                        },
                        {
                            title: "Positive Feedback",
                            value: document.getElementById("positiveFeedbacks").innerText,
                            color: [40, 167, 69]
                        },
                        {
                            title: "Negative Feedback",
                            value: document.getElementById("negativeFeedbacks").innerText,
                            color: [255, 193, 7]
                        },
                        {
                            title: "Most Mentioned Type",
                            value: document.getElementById("mostMentionedType").innerText,
                            color: [111, 66, 193]
                        }
                    ];

                    for (let i = 0; i < cards.length; i++) {
                        const col = i % 3;
                        const row = Math.floor(i / 3);
                        const x = startX + col * (cardWidth + gapX);
                        const y = startY + row * (cardHeight + gapY);
                        drawCard(x, y, cards[i].color, cards[i].title, cards[i].value);
                    }

                    // ===== Add First Chart (Monthly Rating Trend) =====
                    const trendCanvas = document.getElementById("feedbackTrendChart");
                    const trendImage = trendCanvas.toDataURL("image/png");

                    const chartWidth = 160;
                    const chartHeight = 70;
                    const chartX = 20;
                    const chartY = startY + Math.ceil(cards.length / 3) * (cardHeight + gapY) + 15;

                    pdf.setFont("helvetica", "bold");
                    pdf.setFontSize(14);
                    pdf.setTextColor(0, 0, 0);
                    pdf.text("Monthly Rating Trend", chartX, chartY - 8);
                    pdf.addImage(trendImage, "PNG", chartX, chartY, chartWidth, chartHeight);

                    // ===== Save PDF =====
                    pdf.save("Feedback_Analysis_Report.pdf");
                });
            });

            // View sections
            const tabs = document.querySelectorAll('.analytics-tab');
            const contents = document.querySelectorAll('.analytics-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const target = tab.dataset.tab;

                    contents.forEach(c => c.classList.add('hidden'));
                    document.getElementById(target).classList.remove('hidden');

                    tabs.forEach(t => {
                        t.classList.remove('bg-red-500', 'text-white');
                        t.classList.add('bg-white', 'text-gray-700');
                    });

                    tab.classList.add('bg-red-500', 'text-white');
                    tab.classList.remove('bg-white', 'text-gray-700');
                });
            });
        </script>
    @endpush
@endsection
