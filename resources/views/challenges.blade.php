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

    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

</head>
<body class="bg-white text-gray-800">

    <!-- Header -->
    @include('components.header')
    
    <!-- Hero Section -->
    <section class="bg-black text-white py-20">
        <div class="container mx-auto text-center px-4 animate-fade-in">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">CHALLENGES WE TACKLE</h1>
            <p class="text-lg text-gray-200">Addressing real-world fitness & wellness barriers with smart digital solutions.</p>
        </div>
    </section>

    <!-- Challenges Grid -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-12">

            <!-- Challenge 1 -->
            <div class="relative bg-white shadow-lg rounded-lg p-6 border group overflow-hidden hover:shadow-xl transition min-h-[250px]">
                <!-- Front Content -->
                <div class="absolute inset-0 flex flex-col justify-center items-center text-center transition-all duration-300 ease-in-out
                            group-hover:opacity-0 group-hover:pointer-events-none">
                    <div class="text-red-600 text-5xl mb-4">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Lack of Time</h3>
                    <p class="text-gray-600">Busy schedules make fitness a challenge.</p>
                </div>
                <!-- Hover Back Content -->
                <div class="absolute inset-0 bg-red-600 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-center items-center text-center p-4 rounded-lg">
                    <p>Many users struggle to find time for workouts. PULSEONE's personalized plans and home routines solve this.</p>
                </div>
            </div>

            <!-- Challenge 2 -->
            <div class="relative bg-white shadow-lg rounded-lg p-6 border group overflow-hidden hover:shadow-xl transition min-h-[250px]">
                <div class="absolute inset-0 flex flex-col justify-center items-center text-center transition-all duration-300 ease-in-out
                            group-hover:opacity-0 group-hover:pointer-events-none">
                    <div class="text-red-600 text-5xl mb-4">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Motivation Drops</h3>
                    <p class="text-gray-600">Staying motivated is tough.</p>
                </div>
                <div class="absolute inset-0 bg-red-600 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-center items-center text-center p-4 rounded-lg">
                    <p>PULSEONE uses progress tracking and community features to keep you inspired and on track.</p>
                </div>
            </div>

            <!-- Challenge 3 -->
            <div class="relative bg-white shadow-lg rounded-lg p-6 border group overflow-hidden hover:shadow-xl transition min-h-[250px]">
                <div class="absolute inset-0 flex flex-col justify-center items-center text-center transition-all duration-300 ease-in-out
                            group-hover:opacity-0 group-hover:pointer-events-none">
                    <div class="text-red-600 text-5xl mb-4">
                        <i class="fas fa-apple-alt"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Diet Discipline</h3>
                    <p class="text-gray-600">Sticking to a diet is hard.</p>
                </div>
                <div class="absolute inset-0 bg-red-600 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-center items-center text-center p-4 rounded-lg">
                    <p>Custom diet plans and reminders help users maintain healthy eating habits.</p>
                </div>
            </div>

            <!-- Challenge 4 -->
            <div class="relative bg-white shadow-lg rounded-lg p-6 border group overflow-hidden hover:shadow-xl transition min-h-[250px]">
                <div class="absolute inset-0 flex flex-col justify-center items-center text-center transition-all duration-300 ease-in-out
                            group-hover:opacity-0 group-hover:pointer-events-none">
                    <div class="text-red-600 text-5xl mb-4">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Tracking Progress</h3>
                    <p class="text-gray-600">Hard to measure improvement.</p>
                </div>
                <div class="absolute inset-0 bg-red-600 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-center items-center text-center p-4 rounded-lg">
                    <p>PULSEONE provides clear analytics and visual progress charts for every user.</p>
                </div>
            </div>

            <!-- Challenge 5 -->
            <div class="relative bg-white shadow-lg rounded-lg p-6 border group overflow-hidden hover:shadow-xl transition min-h-[250px]">
                <div class="absolute inset-0 flex flex-col justify-center items-center text-center transition-all duration-300 ease-in-out
                            group-hover:opacity-0 group-hover:pointer-events-none">
                    <div class="text-red-600 text-5xl mb-4">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Community Support</h3>
                    <p class="text-gray-600">Feeling alone in the journey.</p>
                </div>
                <div class="absolute inset-0 bg-red-600 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-center items-center text-center p-4 rounded-lg">
                    <p>Connect with trainers, dietitians, and peers for encouragement and advice.</p>
                </div>
            </div>

            <!-- Challenge 6 -->
            <div class="relative bg-white shadow-lg rounded-lg p-6 border group overflow-hidden hover:shadow-xl transition min-h-[250px]">
                <div class="absolute inset-0 flex flex-col justify-center items-center text-center transition-all duration-300 ease-in-out
                            group-hover:opacity-0 group-hover:pointer-events-none">
                    <div class="text-red-600 text-5xl mb-4">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Privacy & Security</h3>
                    <p class="text-gray-600">Worried about data safety.</p>
                </div>
                <div class="absolute inset-0 bg-red-600 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-center items-center text-center p-4 rounded-lg">
                    <p>PULSEONE uses secure authentication and privacy controls for peace of mind.</p>
                </div>
            </div>

        </div>
    </section>

    <!-- Call-to-Action -->
    <section class="bg-[#1E1E1E] text-white py-12 text-center px-6">
        <h2 class="text-3xl font-bold">WE’RE NOT JUST SOLVING PROBLEMS, WE'RE TRANSFORMING LIVES.</h2>
        <p class="mt-3 text-lg">PULSEONE bridges the gap between intention and action, guiding every step of your wellness journey.</p>
    </section>

    <!-- Footer -->
    @include('components.footer')

    <script>
        AOS.init();
    </script>

</body>
</html>
