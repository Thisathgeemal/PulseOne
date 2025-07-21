<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PULSEONE</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

    <!-- AOS CSS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css" />

    <!-- FontAwesome -->
    <script src="https://kit.fontawesome.com/bc9b460555.js" crossorigin="anonymous"></script>
</head>
<body class="bg-white text-gray-800 font-sans">

    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        @include('memberDashboard.components.sidebar')

        <!-- Main Content -->
        <div class="flex flex-col flex-1">

            <!-- Header -->
            @include('memberDashboard.components.header')

            {{-- <!-- Dashboard Cards -->
            <main class="flex-1 p-6 bg-white">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Attendance -->
                    <div class="bg-blue-100 p-5 rounded-lg shadow">
                        <h2 class="text-lg font-semibold text-gray-700">Attendance</h2>
                        <p class="text-xl mt-2 font-bold text-blue-800">Present</p>
                        <a href="#" class="text-sm text-blue-700 mt-2 inline-block">View detail</a>
                    </div>

                    <!-- Workout Plan -->
                    <div class="bg-green-100 p-5 rounded-lg shadow">
                        <h2 class="text-lg font-semibold text-gray-700">Active Workout Plan</h2>
                        <p class="text-xl mt-2 font-bold text-green-800">Push-Pull-Legs Split</p>
                        <a href="#" class="text-sm text-green-700 mt-2 inline-block">View detail</a>
                    </div>

                    <!-- Upcoming Session -->
                    <div class="bg-purple-100 p-5 rounded-lg shadow">
                        <h2 class="text-lg font-semibold text-gray-700">Upcoming Session</h2>
                        <p class="text-xl mt-2 font-bold text-purple-800">Today, 01</p>
                        <a href="#" class="text-sm text-purple-700 mt-2 inline-block">View detail</a>
                    </div>

                    <!-- Leaderboard -->
                    <div class="bg-yellow-100 p-5 rounded-lg shadow">
                        <h2 class="text-lg font-semibold text-gray-700">Leaderboard Rank</h2>
                        <p class="text-xl mt-2 font-bold text-yellow-800">#2 out of 20</p>
                        <a href="#" class="text-sm text-yellow-700 mt-2 inline-block">View detail</a>
                    </div>

                    <!-- Unread Messages -->
                    <div class="bg-red-100 p-5 rounded-lg shadow">
                        <h2 class="text-lg font-semibold text-gray-700">Unread Messages</h2>
                        <p class="text-xl mt-2 font-bold text-red-800">3</p>
                        <a href="#" class="text-sm text-red-700 mt-2 inline-block">View detail</a>
                    </div>
                </div>
            </main> --}}

        </div>
    </div>

</body>
</html>