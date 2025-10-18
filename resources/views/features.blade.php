<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>PULSEONE | Features</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800,900" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" href="{{ asset('images/favicon.png') }}">

    <script src="https://kit.fontawesome.com/bc9b460555.js" crossorigin="anonymous"></script>

    <script src="https://cdn.tailwindcss.com"></script>

    <!-- AOS Animations -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        .gradient-text {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .feature-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }
        .feature-card:hover {
            transform: translateY(-10px) scale(1.02);
        }
        .feature-card:hover .icon-glow {
            transform: scale(1.2);
            filter: drop-shadow(0 0 20px rgba(239, 68, 68, 0.6));
        }
        .feature-card:hover .hover-info {
            opacity: 1;
            transform: translateY(0);
        }
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.05));
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .feature-card:hover::before {
            opacity: 1;
        }
        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>

</head>
<body class="bg-white text-gray-800">

    <!-- Header -->
    @include('components.header')
    
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-black via-gray-900 to-red-900 text-white py-16 sm:py-24 lg:py-32 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-red-600/30 to-transparent"></div>
        <div class="absolute top-5 sm:top-10 right-5 sm:right-10 w-48 sm:w-72 h-48 sm:h-72 bg-red-600/20 rounded-full blur-3xl floating-animation"></div>
        <div class="absolute bottom-5 sm:bottom-10 left-5 sm:left-10 w-64 sm:w-96 h-64 sm:h-96 bg-red-500/10 rounded-full blur-3xl floating-animation" style="animation-delay: -3s;"></div>
        
        <div class="relative container mx-auto text-center px-4" data-aos="fade-up">
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-7xl font-black mb-4 sm:mb-6 tracking-tight leading-tight">
                CORE <span class="gradient-text">FEATURES</span>
            </h1>
            <p class="text-base sm:text-lg md:text-xl lg:text-2xl text-gray-300 mb-6 sm:mb-8 max-w-4xl mx-auto leading-relaxed">
                Empowering fitness journeys with smart, integrated solutions that revolutionize how you train, eat, and achieve your goals.
            </p>
            
            <!-- Feature Stats -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8 mt-8 sm:mt-16" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center">
                    <div class="text-2xl sm:text-3xl lg:text-4xl font-bold text-red-400 mb-1 sm:mb-2">10+</div>
                    <div class="text-xs sm:text-sm text-gray-300">Core Features</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl sm:text-3xl lg:text-4xl font-bold text-red-400 mb-1 sm:mb-2">24/7</div>
                    <div class="text-xs sm:text-sm text-gray-300">System Access</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl sm:text-3xl lg:text-4xl font-bold text-red-400 mb-1 sm:mb-2">Personalized</div>
                    <div class="text-xs sm:text-sm text-gray-300">Plans</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl sm:text-3xl lg:text-4xl font-bold text-red-400 mb-1 sm:mb-2">Real-time</div>
                    <div class="text-xs sm:text-sm text-gray-300">Tracking</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section class="py-12 sm:py-16 lg:py-24 bg-white relative overflow-hidden">
        <div class="absolute top-0 left-0 w-64 sm:w-96 h-64 sm:h-96 bg-gradient-to-br from-red-50 to-transparent rounded-full transform -translate-x-32 sm:-translate-x-48 -translate-y-32 sm:-translate-y-48"></div>
        <div class="absolute bottom-0 right-0 w-64 sm:w-96 h-64 sm:h-96 bg-gradient-to-tl from-red-50 to-transparent rounded-full transform translate-x-32 sm:translate-x-48 translate-y-32 sm:translate-y-48"></div>
        
        <div class="relative container mx-auto px-4 sm:px-6">
            <div class="text-center mb-12 sm:mb-16" data-aos="fade-up">
                <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black text-gray-900 mb-4">
                    Powerful <span class="gradient-text">Features</span>
                </h2>
                <p class="text-base sm:text-lg lg:text-xl text-gray-600 max-w-3xl mx-auto">
                    Discover the comprehensive suite of tools designed to transform your fitness experience
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @php
                    $features = [
                        [
                            'icon' => 'fa-qrcode', 
                            'title' => 'QR Attendance System', 
                            'short' => 'Contactless check-in solution', 
                            'full' => 'Scan a unique QR code daily for contactless check-in and precise attendance tracking. Completely secure and hygienic.',
                            'color' => 'blue'
                        ],
                        [
                            'icon' => 'fa-dumbbell', 
                            'title' => 'Personalized Workout Plans', 
                            'short' => 'Personlaized fitness routines', 
                            'full' => 'Trainers create weekly goal-based workout plans that adapt to your progress and fitness level dynamically.',
                            'color' => 'green'
                        ],
                        [
                            'icon' => 'fa-apple-alt', 
                            'title' => 'Diet Plan Integration', 
                            'short' => 'Smart nutrition tracking', 
                            'full' => 'Dietitians assign custom diet plans with real-time tracking and updates based on your specific health goals.',
                            'color' => 'orange'
                        ],
                        [
                            'icon' => 'fa-user-cog', 
                            'title' => 'Role-Based Dashboards', 
                            'short' => 'Tailored user interfaces', 
                            'full' => 'Separate dashboards for Admins, Trainers, Dietitians & Members with relevant tools and analytics.',
                            'color' => 'purple'
                        ],
                        [
                            'icon' => 'fa-chart-line', 
                            'title' => 'Progress Tracking', 
                            'short' => 'Comprehensive analytics', 
                            'full' => 'Track performance metrics across your training and meal journey with detailed charts and insights.',
                            'color' => 'teal'
                        ],
                        [
                            'icon' => 'fa-shield-alt', 
                            'title' => 'Security & 2FA', 
                            'short' => 'Enterprise-grade protection', 
                            'full' => 'Secure accounts with two-factor authentication and active session management for complete peace of mind.',
                            'color' => 'red'
                        ],
                        [
                            'icon' => 'fa-calendar-check', 
                            'title' => 'Session Booking', 
                            'short' => 'Smart scheduling system', 
                            'full' => 'Book training sessions with preferred trainers, manage schedules, and receive automated reminders.',
                            'color' => 'indigo'
                        ],
                        [
                            'icon' => 'fa-comments', 
                            'title' => 'Feedback System', 
                            'short' => 'Interactive communication', 
                            'full' => 'Rate and review trainers, dietitians, and services. Build better relationships through transparent feedback.',
                            'color' => 'pink'
                        ],
                        [
                            'icon' => 'fa-mobile-alt', 
                            'title' => 'Mobile Responsive', 
                            'short' => 'Access anywhere, anytime', 
                            'full' => 'Fully responsive design that works seamlessly across all devices - desktop, tablet, and mobile.',
                            'color' => 'gray'
                        ]
                    ];
                @endphp

                @foreach($features as $index => $feature)
                    <div class="feature-card group bg-white border border-gray-100 shadow-lg rounded-2xl p-4 sm:p-6 lg:p-8 transition-all duration-300 hover:shadow-2xl" 
                         data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                        
                        <!-- Front Content -->
                        <div class="relative z-10">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 bg-{{ $feature['color'] }}-100 rounded-full flex items-center justify-center mb-4 sm:mb-6 icon-glow transition-all duration-300">
                                <i class="fas {{ $feature['icon'] }} text-lg sm:text-xl lg:text-2xl text-{{ $feature['color'] }}-600"></i>
                            </div>
                            <h3 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3 text-gray-900 group-hover:text-{{ $feature['color'] }}-600 transition-colors duration-300">
                                {{ $feature['title'] }}
                            </h3>
                            <p class="text-sm sm:text-base text-gray-600 mb-3 sm:mb-4">{{ $feature['short'] }}</p>
                            
                            <!-- Detailed description on hover/mobile always visible -->
                            <div class="hover-info opacity-100 sm:opacity-0 transform translate-y-0 sm:translate-y-4 transition-all duration-300 sm:group-hover:opacity-100 sm:group-hover:translate-y-0">
                                <div class="border-t border-gray-200 pt-3 sm:pt-4">
                                    <p class="text-xs sm:text-sm text-gray-700 leading-relaxed">{{ $feature['full'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Feature Showcase -->
    <section class="py-24 bg-gradient-to-br from-gray-50 to-red-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-black text-gray-900 mb-4">
                    See It In <span class="gradient-text">Action</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Experience how our features work together to create the ultimate fitness platform
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div data-aos="fade-right">
                    <h3 class="text-3xl font-bold text-gray-900 mb-6">
                        Complete Fitness <span class="gradient-text">Ecosystem</span>
                    </h3>
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check text-red-600"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 mb-2">Integrated Tracking</h4>
                                <p class="text-gray-600">All your fitness data in one place - workouts, nutrition, progress, and more.</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check text-red-600"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 mb-2">Expert Guidance</h4>
                                <p class="text-gray-600">Connect with certified trainers and nutritionists for personalized advice.</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check text-red-600"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 mb-2">Smart Analytics</h4>
                                <p class="text-gray-600">Insights help you understand your progress and optimize your routine.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div data-aos="fade-left" class="relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-600/20 to-transparent rounded-2xl transform rotate-3"></div>
                    <img src="{{ asset('images/dashboard-showcase.png') }}" alt="Dashboard" class="relative rounded-2xl shadow-2xl w-full">
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Banner -->
    <section class="py-20 text-center relative overflow-hidden" style="background: linear-gradient(90deg,#dc2626 0%,#ef4444 50%,#dc2626 100%);">
        <div class="relative container mx-auto px-4" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-black mb-6 text-white">
                Experience Fitness <span class="text-black">Redefined</span>
            </h2>
            <p class="text-xl mb-8 max-w-3xl mx-auto text-white/90">
                Join us and take control of your health and well-being today with our comprehensive fitness platform!
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-red-600 font-bold rounded-full hover:bg-gray-100 transition-all duration-300 hover:scale-105 shadow">
                    Get Started Free
                </a>
                <a href="{{ route('contact') }}" class="px-8 py-4 border-2 border-white text-white font-bold rounded-full hover:bg-white hover:text-red-600 transition-all duration-300 shadow">
                    Request Demo
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    @include('components.footer')

    <!-- AOS Script -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            AOS.init({
                duration: 800,
                once: true,
                offset: 100
            });
        });
    </script>
</body>
</html>
