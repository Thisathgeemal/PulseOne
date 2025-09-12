<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>PULSEONE | Challenges We Solve</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800,900" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" href="{{ asset('images/favicon.png') }}">

    <script src="https://kit.fontawesome.com/bc9b460555.js" crossorigin="anonymous"></script>

    <script src="https://cdn.tailwindcss.com"></script>

    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        .gradient-text {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .challenge-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }
        .challenge-card:hover {
            transform: translateY(-15px) scale(1.03);
        }
        .challenge-card::before {
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
        .challenge-card:hover::before {
            opacity: 1;
        }
        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .stats-animation {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>

</head>
<body class="bg-white text-gray-800">

    <!-- Header -->
    @include('components.header')
    
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-black via-gray-900 to-red-900 text-white py-32 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-red-600/30 to-transparent"></div>
        <div class="absolute top-10 right-10 w-72 h-72 bg-red-600/20 rounded-full blur-3xl floating-animation"></div>
        <div class="absolute bottom-10 left-10 w-96 h-96 bg-red-500/10 rounded-full blur-3xl floating-animation" style="animation-delay: -3s;"></div>
        
        <div class="relative container mx-auto text-center px-4" data-aos="fade-up">
            <h1 class="text-5xl md:text-7xl font-black mb-6 tracking-tight">
                CHALLENGES <span class="gradient-text">WE TACKLE</span>
            </h1>
            <p class="text-xl md:text-2xl text-gray-300 mb-8 max-w-4xl mx-auto leading-relaxed">
                Addressing real-world fitness & wellness barriers with smart digital solutions that actually work.
            </p>
            
            <!-- Challenge Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mt-16" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center stats-animation">
                    <div class="text-4xl font-bold text-red-400 mb-2">90%</div>
                    <div class="text-sm text-gray-300">Motivation Issues</div>
                </div>
                <div class="text-center stats-animation" style="animation-delay: 0.5s;">
                    <div class="text-4xl font-bold text-red-400 mb-2">75%</div>
                    <div class="text-sm text-gray-300">Time Constraints</div>
                </div>
                <div class="text-center stats-animation" style="animation-delay: 1s;">
                    <div class="text-4xl font-bold text-red-400 mb-2">85%</div>
                    <div class="text-sm text-gray-300">Diet Struggles</div>
                </div>
                <div class="text-center stats-animation" style="animation-delay: 1.5s;">
                    <div class="text-4xl font-bold text-red-400 mb-2">100%</div>
                    <div class="text-sm text-gray-300">PULSEONE Solutions</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Challenges Grid -->
        <!-- Challenges Grid -->
    <section class="py-24 bg-white relative overflow-hidden">
        <div class="absolute top-0 left-0 w-96 h-96 bg-gradient-to-br from-red-50 to-transparent rounded-full transform -translate-x-48 -translate-y-48"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-gradient-to-tl from-red-50 to-transparent rounded-full transform translate-x-48 translate-y-48"></div>
        
        <div class="relative container mx-auto px-6">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-black text-gray-900 mb-4">
                    Common <span class="gradient-text">Fitness Barriers</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    We understand the real challenges people face in their fitness journey and provide targeted solutions
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- Challenge 1 -->
                <div class="challenge-card group bg-white shadow-xl rounded-2xl p-8 border border-gray-100 hover:border-red-200 transition-all duration-300 min-h-[300px]" data-aos="fade-up" data-aos-delay="100">
                    <!-- Front Content -->
                    <div class="relative z-10 h-full flex flex-col">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-user-clock text-2xl text-red-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-gray-900 group-hover:text-red-600 transition-colors duration-300">Lack of Time</h3>
                        <p class="text-gray-600 mb-6 flex-grow">Busy schedules make consistent fitness routines nearly impossible for modern professionals.</p>
                        
                        <!-- Solution on hover -->
                        <div class="opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-4 group-hover:translate-y-0">
                            <div class="border-t border-red-200 pt-4">
                                <h4 class="font-bold text-red-600 mb-2">Our Solution:</h4>
                                <p class="text-sm text-gray-700">Flexible workout plans and home routines that fit into any schedule. Quick 15-30 minute sessions available.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Challenge 2 -->
                <div class="challenge-card group bg-white shadow-xl rounded-2xl p-8 border border-gray-100 hover:border-red-200 transition-all duration-300 min-h-[300px]" data-aos="fade-up" data-aos-delay="200">
                    <div class="relative z-10 h-full flex flex-col">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-battery-empty text-2xl text-red-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-gray-900 group-hover:text-red-600 transition-colors duration-300">Motivation Drops</h3>
                        <p class="text-gray-600 mb-6 flex-grow">Staying motivated without proper support system leads to eventual burnout and giving up.</p>
                        
                        <div class="opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-4 group-hover:translate-y-0">
                            <div class="border-t border-red-200 pt-4">
                                <h4 class="font-bold text-red-600 mb-2">Our Solution:</h4>
                                <p class="text-sm text-gray-700">Progress tracking, achievement badges, community features, and personal trainer support to keep you inspired.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Challenge 3 -->
                <div class="challenge-card group bg-white shadow-xl rounded-2xl p-8 border border-gray-100 hover:border-red-200 transition-all duration-300 min-h-[300px]" data-aos="fade-up" data-aos-delay="300">
                    <div class="relative z-10 h-full flex flex-col">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-apple-alt text-2xl text-red-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-gray-900 group-hover:text-red-600 transition-colors duration-300">Diet Discipline</h3>
                        <p class="text-gray-600 mb-6 flex-grow">Maintaining healthy eating habits is one of the biggest challenges in any fitness journey.</p>
                        
                        <div class="opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-4 group-hover:translate-y-0">
                            <div class="border-t border-red-200 pt-4">
                                <h4 class="font-bold text-red-600 mb-2">Our Solution:</h4>
                                <p class="text-sm text-gray-700">Custom diet plans, meal tracking, nutritionist consultations, and automated reminders for healthy eating.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Challenge 4 -->
                <div class="challenge-card group bg-white shadow-xl rounded-2xl p-8 border border-gray-100 hover:border-red-200 transition-all duration-300 min-h-[300px]" data-aos="fade-up" data-aos-delay="400">
                    <div class="relative z-10 h-full flex flex-col">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-chart-line text-2xl text-red-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-gray-900 group-hover:text-red-600 transition-colors duration-300">Tracking Progress</h3>
                        <p class="text-gray-600 mb-6 flex-grow">Without proper measurement tools, it's impossible to know if your efforts are paying off.</p>
                        
                        <div class="opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-4 group-hover:translate-y-0">
                            <div class="border-t border-red-200 pt-4">
                                <h4 class="font-bold text-red-600 mb-2">Our Solution:</h4>
                                <p class="text-sm text-gray-700">Comprehensive analytics with visual charts, body metrics tracking, and performance insights.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Challenge 5 -->
                <div class="challenge-card group bg-white shadow-xl rounded-2xl p-8 border border-gray-100 hover:border-red-200 transition-all duration-300 min-h-[300px]" data-aos="fade-up" data-aos-delay="500">
                    <div class="relative z-10 h-full flex flex-col">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-users text-2xl text-red-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-gray-900 group-hover:text-red-600 transition-colors duration-300">Lack of Guidance</h3>
                        <p class="text-gray-600 mb-6 flex-grow">Going solo in fitness often leads to ineffective routines and potential injuries.</p>
                        
                        <div class="opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-4 group-hover:translate-y-0">
                            <div class="border-t border-red-200 pt-4">
                                <h4 class="font-bold text-red-600 mb-2">Our Solution:</h4>
                                <p class="text-sm text-gray-700">Connect with certified trainers, dietitians, and fitness community for expert guidance and support.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Challenge 6 -->
                <div class="challenge-card group bg-white shadow-xl rounded-2xl p-8 border border-gray-100 hover:border-red-200 transition-all duration-300 min-h-[300px]" data-aos="fade-up" data-aos-delay="600">
                    <div class="relative z-10 h-full flex flex-col">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-shield-alt text-2xl text-red-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 text-gray-900 group-hover:text-red-600 transition-colors duration-300">Privacy Concerns</h3>
                        <p class="text-gray-600 mb-6 flex-grow">Sharing personal health data online raises legitimate privacy and security concerns.</p>
                        
                        <div class="opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-4 group-hover:translate-y-0">
                            <div class="border-t border-red-200 pt-4">
                                <h4 class="font-bold text-red-600 mb-2">Our Solution:</h4>
                                <p class="text-sm text-gray-700">Enterprise-grade security with 2FA, encrypted data storage, and complete privacy controls.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Success Stories -->
    <section class="py-24 bg-gradient-to-br from-gray-50 to-red-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-black text-gray-900 mb-4">
                    Real <span class="gradient-text">Success Stories</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    See how PULSEONE has helped people overcome these exact challenges
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-2xl shadow-lg" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center mb-6">
                        <img src="{{ asset('images/success/user-1.jpg') }}" alt="Success Story" class="w-16 h-16 rounded-full object-cover mr-4">
                        <div>
                            <h4 class="font-bold text-gray-900">Maria Santos</h4>
                            <p class="text-gray-600 text-sm">Busy Executive</p>
                        </div>
                    </div>
                    <p class="text-gray-700 italic mb-4">"PULSEONE's 20-minute workouts fit perfectly into my hectic schedule. Lost 12kg in 4 months!"</p>
                    <div class="text-red-600 font-bold">Challenge: Lack of Time → SOLVED</div>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-lg" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center mb-6">
                        <img src="{{ asset('images/success/user-2.jpg') }}" alt="Success Story" class="w-16 h-16 rounded-full object-cover mr-4">
                        <div>
                            <h4 class="font-bold text-gray-900">James Wilson</h4>
                            <p class="text-gray-600 text-sm">Software Developer</p>
                        </div>
                    </div>
                    <p class="text-gray-700 italic mb-4">"The progress tracking kept me motivated. Seeing my improvements daily was a game-changer!"</p>
                    <div class="text-red-600 font-bold">Challenge: Low Motivation → SOLVED</div>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-lg" data-aos="fade-up" data-aos-delay="300">
                    <div class="flex items-center mb-6">
                        <img src="{{ asset('images/success/user-3.jpg') }}" alt="Success Story" class="w-16 h-16 rounded-full object-cover mr-4">
                        <div>
                            <h4 class="font-bold text-gray-900">Lisa Chen</h4>
                            <p class="text-gray-600 text-sm">Marketing Manager</p>
                        </div>
                    </div>
                    <p class="text-gray-700 italic mb-4">"The dietitian's meal plans made healthy eating simple. No more guesswork!"</p>
                    <div class="text-red-600 font-bold">Challenge: Diet Discipline → SOLVED</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call-to-Action -->
    <section class="py-24 bg-gradient-to-r from-red-600 to-red-700 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="relative container mx-auto text-center px-6" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-black mb-6">
                WE'RE NOT JUST SOLVING PROBLEMS,<br>
                <span class="text-red-200">WE'RE TRANSFORMING LIVES</span>
            </h2>
            <p class="text-xl mb-8 opacity-90 max-w-4xl mx-auto">
                PULSEONE bridges the gap between intention and action, guiding every step of your wellness journey with personalized solutions that actually work.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-red-600 font-bold rounded-full hover:bg-gray-100 transition-all duration-300 hover:scale-105">
                    Start Solving Your Challenges
                </a>
                <a href="{{ route('contact') }}" class="px-8 py-4 border-2 border-white text-white font-bold rounded-full hover:bg-white hover:text-red-600 transition-all duration-300">
                    Learn How We Help
                </a>
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
