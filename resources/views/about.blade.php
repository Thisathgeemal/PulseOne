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

    <!-- AOS Animations -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

</head>
<body class="bg-white text-gray-800">

    <!-- Header -->
    @include('components.header')
    
    <!-- Hero Section -->
    <section class="bg-[#1E1E1E] text-white py-20 px-6 text-center">
        <div class="max-w-4xl mx-auto" data-aos="fade-down">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">ABOUT PULSEONE</h1>
            <p class="text-lg text-gray-300 whitespace-nowrap">Your trusted partner for smarter fitness and health tracking. We simplify your journey, whether you're a beginner or a pro.</p>
        </div>
    </section>

    <!-- Who We Are -->
    <section class="py-20 bg-white px-6">
        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div data-aos="fade-right">
                <h2 class="text-3xl font-bold mb-4 text-gray-800">WHO WE ARE</h2>
                <p class="text-gray-600 leading-relaxed mb-4 text-justify">
                    PULSEONE is more than just a fitness platform. We're a technology-powered ecosystem that connects members, trainers, and dietitians through personalized dashboards, goal-oriented plans, and smart tracking tools.
                </p>
                <p class="text-gray-600">
                    Whether it's achieving physical goals or maintaining diet discipline, our platform adapts to your needs. We’re built to serve real gyms, real people, and real goals.
                </p>
            </div>
            <div data-aos="fade-left">
                <img src="{{ asset('images/about-fitness.jpg') }}" alt="Fitness Team" class="rounded-lg shadow-lg">
            </div>
        </div>
    </section>

    <!-- Our Mission -->
    <section class="py-20 bg-gray-100 px-6">
        <div class="max-w-3xl mx-auto text-center" data-aos="fade-up">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Our Mission</h2>
            <p class="text-gray-700 text-lg">
                To empower fitness communities with technology-driven solutions, making health management seamless, personalized, and effective for everyone.
            </p>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="py-20 bg-white px-6">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-800" data-aos="fade-down">Why Choose PULSEONE?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <div class="text-center" data-aos="zoom-in">
                    <i class="fas fa-user-shield text-5xl text-red-500 mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">Secure & Scalable</h3>
                    <p class="text-gray-600">Built with modern architecture ensuring data safety, scalability, and consistent uptime.</p>
                </div>
                <div class="text-center" data-aos="zoom-in" data-aos-delay="100">
                    <i class="fas fa-chart-line text-5xl text-red-500 mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">Performance-Driven</h3>
                    <p class="text-gray-600">Track workouts, diet plans, and member feedback with detailed reporting and dashboards.</p>
                </div>
                <div class="text-center" data-aos="zoom-in" data-aos-delay="200">
                    <i class="fas fa-users text-5xl text-red-500 mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">User-Friendly</h3>
                    <p class="text-gray-600">Intuitive design with smooth navigation for members, trainers, admins, and dietitians alike.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="bg-gray-100 py-20 px-6">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-800" data-aos="fade-up">What Our Users Say</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow" data-aos="fade-right">
                    <p class="text-gray-700 italic mb-4">"PULSEONE made my diet tracking a breeze! I can access my plans and talk to my dietitian all in one place."</p>
                    <div class="font-semibold text-red-500">- Ayeshi R., Member</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow" data-aos="fade-up">
                    <p class="text-gray-700 italic mb-4">"Finally a system that respects trainers! Assigning plans and tracking progress has never been easier."</p>
                    <div class="font-semibold text-red-500">- Kanishka S., Trainer</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow" data-aos="fade-left">
                    <p class="text-gray-700 italic mb-4">"As an admin, I love how clean the dashboard is. I can manage members and view reports in seconds."</p>
                    <div class="font-semibold text-red-500">- Hafsa M., Admin</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    @include('components.footer')

    <!-- AOS Script -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => AOS.init());
    </script>
</body>
</html>
