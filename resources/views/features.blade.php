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

    <link rel="icon" href="{{ asset('images/favicon.png') }}">

    <script src="https://kit.fontawesome.com/bc9b460555.js" crossorigin="anonymous"></script>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .feature-card:hover .icon-glow {
            transform: scale(1.1);
            filter: drop-shadow(0 0 10px rgba(239, 68, 68, 0.5));
        }

        .feature-card:hover .hover-info {
            opacity: 1;
            transform: translateY(0);
        }
    </style>

</head>
<body class="bg-white text-gray-800">

    <!-- Header -->
    @include('components.header')
    
    <!-- Hero Section -->
    <section class="bg-black text-white py-20">
        <div class="container mx-auto text-center px-4">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">CORE FEATURES OF PULSEONE</h1>
            <p class="text-lg text-gray-200">Empowering fitness journeys with smart, integrated solutions.</p>
        </div>
    </section>

    <!-- Features Grid -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-12">
            @php
                $features = [
                    ['icon' => 'fa-user-check', 'title' => 'QR Attendance System', 'short' => 'Check-in to the gym securely.', 'full' => 'Scan a unique QR daily for contactless check-in and precise attendance tracking.'],
                    ['icon' => 'fa-dumbbell', 'title' => 'Personalized Workout Plans', 'short' => 'Plans made for you.', 'full' => 'Trainers create weekly goal-based workout plans that adapt to your progress.'],
                    ['icon' => 'fa-apple-alt', 'title' => 'Diet Plan Integration', 'short' => 'Track your meals easily.', 'full' => 'Dietitians assign custom diet plans with tracking and updates based on your goals.'],
                    ['icon' => 'fa-user-cog', 'title' => 'Role-Based Dashboards', 'short' => 'Tailored interfaces.', 'full' => 'Separate dashboards for Admins, Trainers, Dietitians & Members with relevant tools.'],
                    ['icon' => 'fa-chart-line', 'title' => 'Progress Tracking', 'short' => 'See your results.', 'full' => 'Track performance metrics across your training and meal journey with ease.'],
                    ['icon' => 'fa-shield-alt', 'title' => 'Security & 2FA', 'short' => 'Stay protected.', 'full' => 'Secure accounts with two-factor authentication and active session management.']
                ];
            @endphp

            @foreach($features as $feature)
                <div class="relative group bg-white border shadow-md rounded-lg p-6 transition duration-300 overflow-hidden hover:z-10">
                    <!-- Front Content -->
                    <div class="transition-all duration-300 group-hover:opacity-0 group-hover:scale-95">
                        <div class="text-red-600 text-5xl mb-4 transition-transform duration-300 group-hover:scale-110">
                            <i class="fas {{ $feature['icon'] }}"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">{{ $feature['title'] }}</h3>
                        <p class="text-gray-600">{{ $feature['short'] }}</p>
                    </div>

                    <!-- Hover Back Content -->
                    <div class="absolute inset-0 bg-white flex flex-col justify-center items-center text-center p-6 rounded-lg opacity-0 group-hover:opacity-100 transition-all duration-300 ease-in-out">
                        <h4 class="text-lg font-bold mb-2">{{ $feature['title'] }}</h4>
                        <p class="text-sm text-gray-700">{{ $feature['full'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- CTA Banner -->
    <section class="bg-red-600 text-white py-12 text-center">
        <h2 class="text-3xl font-bold uppercase">Experience fitness redefined with PULSEONE.</h2>
        <p class="mt-2 text-lg">Join us and take control of your health and well-being today!</p>
    </section>

    <!-- Footer -->
    @include('components.footer')

</body>
</html>
