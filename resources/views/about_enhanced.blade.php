<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>PULSEONE | About Us</title>

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
        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .stats-counter {
            transition: all 0.3s ease;
        }
        .stats-counter:hover {
            transform: scale(1.05);
        }
        .parallax-bg {
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>

</head>
<body class="bg-white text-gray-800">

    <!-- Header -->
    @include('components.header')
    
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-gray-900 via-black to-gray-800 text-white py-32 px-6 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-red-600/20 to-transparent"></div>
        <div class="absolute top-10 right-10 w-72 h-72 bg-red-600/10 rounded-full blur-3xl floating-animation"></div>
        <div class="absolute bottom-10 left-10 w-96 h-96 bg-red-500/5 rounded-full blur-3xl floating-animation" style="animation-delay: -3s;"></div>
        
        <div class="relative max-w-6xl mx-auto text-center" data-aos="fade-up">
            <h1 class="text-5xl md:text-7xl font-black mb-6 tracking-tight">
                ABOUT <span class="gradient-text">PULSEONE</span>
            </h1>
            <p class="text-xl md:text-2xl text-gray-300 mb-8 max-w-4xl mx-auto leading-relaxed">
                Your trusted partner for smarter fitness and health tracking. We simplify your journey, whether you're a beginner or a pro.
            </p>
            <div class="flex flex-wrap justify-center gap-8 mt-12" data-aos="fade-up" data-aos-delay="200">
                <div class="stats-counter text-center">
                    <div class="text-4xl font-bold text-red-500">500+</div>
                    <div class="text-sm text-gray-400">Active Members</div>
                </div>
                <div class="stats-counter text-center">
                    <div class="text-4xl font-bold text-red-500">50+</div>
                    <div class="text-sm text-gray-400">Expert Trainers</div>
                </div>
                <div class="stats-counter text-center">
                    <div class="text-4xl font-bold text-red-500">1000+</div>
                    <div class="text-sm text-gray-400">Workouts Completed</div>
                </div>
                <div class="stats-counter text-center">
                    <div class="text-4xl font-bold text-red-500">99%</div>
                    <div class="text-sm text-gray-400">Satisfaction Rate</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Who We Are -->
    <section class="py-24 bg-white px-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-red-50 to-transparent rounded-full transform translate-x-48 -translate-y-48"></div>
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div data-aos="fade-right" class="relative">
                    <div class="absolute -top-4 -left-4 w-24 h-24 bg-red-100 rounded-full"></div>
                    <h2 class="text-4xl md:text-5xl font-black mb-6 text-gray-900 relative">
                        WHO <span class="gradient-text">WE ARE</span>
                    </h2>
                    <p class="text-lg text-gray-600 leading-relaxed mb-6">
                        PULSEONE is more than just a fitness platform. We're a technology-powered ecosystem that connects members, trainers, and dietitians through personalized dashboards, goal-oriented plans, and smart tracking tools.
                    </p>
                    <p class="text-lg text-gray-600 leading-relaxed mb-8">
                        Whether it's achieving physical goals or maintaining diet discipline, our platform adapts to your needs. We're built to serve real gyms, real people, and real goals.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center gap-3 bg-red-50 px-4 py-2 rounded-full">
                            <i class="fas fa-check-circle text-red-600"></i>
                            <span class="font-medium">AI-Powered Plans</span>
                        </div>
                        <div class="flex items-center gap-3 bg-red-50 px-4 py-2 rounded-full">
                            <i class="fas fa-check-circle text-red-600"></i>
                            <span class="font-medium">24/7 Support</span>
                        </div>
                        <div class="flex items-center gap-3 bg-red-50 px-4 py-2 rounded-full">
                            <i class="fas fa-check-circle text-red-600"></i>
                            <span class="font-medium">Real-time Tracking</span>
                        </div>
                    </div>
                </div>
                <div data-aos="fade-left" class="relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-600/20 to-transparent rounded-2xl transform rotate-3"></div>
                    <img src="{{ asset('images/about/team-fitness.jpg') }}" alt="Fitness Team" class="relative rounded-2xl shadow-2xl w-full h-96 object-cover">
                    <div class="absolute -bottom-6 -right-6 bg-white p-6 rounded-xl shadow-xl">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">5+</div>
                            <div class="text-sm text-gray-600">Years Experience</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Mission -->
    <section class="py-24 bg-gradient-to-br from-gray-50 to-red-50 px-6 relative parallax-bg" style="background-image: url('{{ asset('images/about/mission-bg.jpg') }}');">
        <div class="absolute inset-0 bg-white/90"></div>
        <div class="relative max-w-4xl mx-auto text-center" data-aos="fade-up">
            <div class="inline-block p-3 bg-red-100 rounded-full mb-6">
                <i class="fas fa-bullseye text-3xl text-red-600"></i>
            </div>
            <h2 class="text-4xl md:text-5xl font-black text-gray-900 mb-6">
                Our <span class="gradient-text">Mission</span>
            </h2>
            <p class="text-xl text-gray-700 leading-relaxed mb-8">
                To empower fitness communities with technology-driven solutions, making health management seamless, personalized, and effective for everyone.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
                <div class="text-center" data-aos="zoom-in" data-aos-delay="100">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-heart text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Health First</h3>
                    <p class="text-gray-600">Prioritizing member wellness above all</p>
                </div>
                <div class="text-center" data-aos="zoom-in" data-aos-delay="200">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-rocket text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Innovation</h3>
                    <p class="text-gray-600">Cutting-edge technology solutions</p>
                </div>
                <div class="text-center" data-aos="zoom-in" data-aos-delay="300">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Community</h3>
                    <p class="text-gray-600">Building stronger fitness communities</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="py-24 bg-white px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-black text-gray-900 mb-4">
                    Why Choose <span class="gradient-text">PULSEONE</span>?
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    We combine cutting-edge technology with human expertise to deliver unmatched fitness experiences
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <div class="group relative bg-white p-8 rounded-2xl border border-gray-100 hover:border-red-200 transition-all duration-300 hover:shadow-2xl" data-aos="zoom-in">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-2xl"></div>
                    <div class="relative">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-user-shield text-2xl text-red-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-gray-900">Secure & Scalable</h3>
                        <p class="text-gray-600 leading-relaxed">Built with modern architecture ensuring data safety, scalability, and consistent uptime for all your fitness needs.</p>
                    </div>
                </div>
                <div class="group relative bg-white p-8 rounded-2xl border border-gray-100 hover:border-red-200 transition-all duration-300 hover:shadow-2xl" data-aos="zoom-in" data-aos-delay="100">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-2xl"></div>
                    <div class="relative">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-chart-line text-2xl text-red-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-gray-900">Performance-Driven</h3>
                        <p class="text-gray-600 leading-relaxed">Track workouts, diet plans, and member feedback with detailed reporting and comprehensive dashboards.</p>
                    </div>
                </div>
                <div class="group relative bg-white p-8 rounded-2xl border border-gray-100 hover:border-red-200 transition-all duration-300 hover:shadow-2xl" data-aos="zoom-in" data-aos-delay="200">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-2xl"></div>
                    <div class="relative">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-users text-2xl text-red-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-gray-900">User-Friendly</h3>
                        <p class="text-gray-600 leading-relaxed">Intuitive design with smooth navigation for members, trainers, admins, and dietitians alike.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-24 bg-gray-50 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-black text-gray-900 mb-4">
                    Meet Our <span class="gradient-text">Team</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Passionate professionals dedicated to revolutionizing your fitness journey
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center group" data-aos="fade-up" data-aos-delay="100">
                    <div class="relative mb-6">
                        <img src="{{ asset('images/about/team-member-1.jpg') }}" alt="Team Member" class="w-48 h-48 rounded-full mx-auto object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-red-600/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Sarah Johnson</h3>
                    <p class="text-red-600 font-medium">CEO & Founder</p>
                    <p class="text-gray-600 mt-2">10+ years in fitness tech</p>
                </div>
                <div class="text-center group" data-aos="fade-up" data-aos-delay="200">
                    <div class="relative mb-6">
                        <img src="{{ asset('images/about/team-member-2.jpg') }}" alt="Team Member" class="w-48 h-48 rounded-full mx-auto object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-red-600/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Mike Chen</h3>
                    <p class="text-red-600 font-medium">CTO</p>
                    <p class="text-gray-600 mt-2">Expert in AI & ML</p>
                </div>
                <div class="text-center group" data-aos="fade-up" data-aos-delay="300">
                    <div class="relative mb-6">
                        <img src="{{ asset('images/about/team-member-3.jpg') }}" alt="Team Member" class="w-48 h-48 rounded-full mx-auto object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-red-600/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Emma Wilson</h3>
                    <p class="text-red-600 font-medium">Head of Design</p>
                    <p class="text-gray-600 mt-2">UX/UI specialist</p>
                </div>
                <div class="text-center group" data-aos="fade-up" data-aos-delay="400">
                    <div class="relative mb-6">
                        <img src="{{ asset('images/about/team-member-4.jpg') }}" alt="Team Member" class="w-48 h-48 rounded-full mx-auto object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-red-600/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">David Rodriguez</h3>
                    <p class="text-red-600 font-medium">Fitness Director</p>
                    <p class="text-gray-600 mt-2">Certified trainer & nutritionist</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Enhanced Testimonials -->
    <section class="py-24 bg-white px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-black text-gray-900 mb-4">
                    What Our <span class="gradient-text">Users Say</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Real stories from real people who transformed their lives with PULSEONE
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-gradient-to-br from-white to-red-50 p-8 rounded-2xl shadow-lg border border-red-100" data-aos="fade-right">
                    <div class="flex items-center mb-6">
                        <img src="{{ asset('images/testimonials/user-1.jpg') }}" alt="User" class="w-16 h-16 rounded-full object-cover mr-4">
                        <div>
                            <h4 class="font-bold text-gray-900">Ayeshi R.</h4>
                            <p class="text-gray-600 text-sm">Member</p>
                            <div class="flex text-yellow-400 mt-1">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-700 italic leading-relaxed">"PULSEONE made my diet tracking a breeze! I can access my plans and talk to my dietitian all in one place. Lost 15kg in 6 months!"</p>
                </div>
                <div class="bg-gradient-to-br from-white to-red-50 p-8 rounded-2xl shadow-lg border border-red-100" data-aos="fade-up">
                    <div class="flex items-center mb-6">
                        <img src="{{ asset('images/testimonials/user-2.jpg') }}" alt="User" class="w-16 h-16 rounded-full object-cover mr-4">
                        <div>
                            <h4 class="font-bold text-gray-900">Kanishka S.</h4>
                            <p class="text-gray-600 text-sm">Trainer</p>
                            <div class="flex text-yellow-400 mt-1">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-700 italic leading-relaxed">"Finally a system that respects trainers! Assigning plans and tracking progress has never been easier. My efficiency increased by 200%!"</p>
                </div>
                <div class="bg-gradient-to-br from-white to-red-50 p-8 rounded-2xl shadow-lg border border-red-100" data-aos="fade-left">
                    <div class="flex items-center mb-6">
                        <img src="{{ asset('images/testimonials/user-3.jpg') }}" alt="User" class="w-16 h-16 rounded-full object-cover mr-4">
                        <div>
                            <h4 class="font-bold text-gray-900">Hafsa M.</h4>
                            <p class="text-gray-600 text-sm">Admin</p>
                            <div class="flex text-yellow-400 mt-1">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-700 italic leading-relaxed">"As an admin, I love how clean the dashboard is. I can manage members and view reports in seconds. Best investment we made!"</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 bg-gradient-to-r from-red-600 to-red-700 text-white px-6">
        <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-black mb-6">
                Ready to Transform Your Fitness Journey?
            </h2>
            <p class="text-xl mb-8 opacity-90">
                Join thousands of satisfied users who've already revolutionized their health with PULSEONE
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-red-600 font-bold rounded-full hover:bg-gray-100 transition-all duration-300 hover:scale-105">
                    Start Your Journey Today
                </a>
                <a href="{{ route('contact') }}" class="px-8 py-4 border-2 border-white text-white font-bold rounded-full hover:bg-white hover:text-red-600 transition-all duration-300">
                    Learn More
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
