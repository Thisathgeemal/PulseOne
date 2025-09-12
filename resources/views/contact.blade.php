<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>PULSEONE | Contact Us</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

    <!-- AOS CSS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css" />

    <!-- Fontawesome -->
    <script src="https://kit.fontawesome.com/bc9b460555.js" crossorigin="anonymous"></script>

    <!-- Favicons -->
    <link rel="icon" type="image/png" href="{{ asset('favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}" />

    <!-- PWA Meta -->
    <meta name="apple-mobile-web-app-title" content="PulseOne" />
    <meta name="theme-color" content="#ffffff" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes" />

    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-gray-800">

    <!-- Header -->
    @include('components.header')

    <!-- Hero -->
    <section class="relative bg-gradient-to-br from-black via-gray-900 to-red-900 text-white py-32 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-red-600/30 to-transparent"></div>
        <div class="absolute top-10 right-10 w-72 h-72 bg-red-600/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-10 left-10 w-96 h-96 bg-red-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: -3s;"></div>
        
        <div class="relative max-w-6xl mx-auto text-center px-4" data-aos="fade-up">
            <h1 class="text-5xl md:text-7xl font-black mb-6 tracking-tight">
                GET IN <span class="bg-gradient-to-r from-red-400 to-red-600 bg-clip-text text-transparent">TOUCH</span>
            </h1>
            <p class="text-xl md:text-2xl text-gray-300 mb-8 max-w-4xl mx-auto leading-relaxed">
                We're here to support your fitness journey. Have questions, feedback, or need assistance? Reach out to us today!
            </p>
            
            <!-- Quick Contact Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mt-16" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center">
                    <div class="text-4xl font-bold text-red-400 mb-2">24/7</div>
                    <div class="text-sm text-gray-300">Support Available</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-red-400 mb-2">&lt;1hr</div>
                    <div class="text-sm text-gray-300">Response Time</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-red-400 mb-2">5+</div>
                    <div class="text-sm text-gray-300">Contact Methods</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-red-400 mb-2">100%</div>
                    <div class="text-sm text-gray-300">Satisfaction Rate</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-24 px-4 max-w-7xl mx-auto bg-white" data-aos="fade-up">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-black text-gray-900 mb-4">
                Let's <span class="bg-gradient-to-r from-red-600 to-red-700 bg-clip-text text-transparent">Connect</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Choose your preferred way to reach out - we're here to help you succeed
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
            <!-- Enhanced Contact Form -->
            <div class="relative bg-white rounded-2xl shadow-2xl p-8 md:p-12 transform hover:shadow-3xl transition duration-300 border border-gray-100">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-red-600 to-red-800 rounded-t-2xl"></div>
                <div class="absolute -top-6 left-8 bg-red-600 p-4 rounded-full shadow-lg">
                    <i class="fas fa-paper-plane text-white text-xl"></i>
                </div>
                
                <h3 class="text-2xl md:text-3xl font-black text-gray-900 mb-8 mt-4">Send Us a Message</h3>
                
                <form action="#" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="relative group">
                            <label class="block text-sm font-bold text-gray-700 mb-2">First Name *</label>
                            <input type="text" name="first_name" required
                                class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-600 focus:border-red-600 transition bg-gray-50 hover:bg-white group-hover:border-red-300">
                            <i class="fas fa-user absolute right-4 top-12 text-red-600 opacity-50"></i>
                        </div>
                        <div class="relative group">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Last Name *</label>
                            <input type="text" name="last_name" required
                                class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-600 focus:border-red-600 transition bg-gray-50 hover:bg-white group-hover:border-red-300">
                            <i class="fas fa-user absolute right-4 top-12 text-red-600 opacity-50"></i>
                        </div>
                    </div>
                    
                    <div class="relative group">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Email Address *</label>
                        <input type="email" name="email" required
                            class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-600 focus:border-red-600 transition bg-gray-50 hover:bg-white group-hover:border-red-300">
                        <i class="fas fa-envelope absolute right-4 top-12 text-red-600 opacity-50"></i>
                    </div>
                    
                    <div class="relative group">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" name="phone"
                            class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-600 focus:border-red-600 transition bg-gray-50 hover:bg-white group-hover:border-red-300">
                        <i class="fas fa-phone absolute right-4 top-12 text-red-600 opacity-50"></i>
                    </div>
                    
                    <div class="relative group">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Subject *</label>
                        <select name="subject" required
                            class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-600 focus:border-red-600 transition bg-gray-50 hover:bg-white group-hover:border-red-300">
                            <option value="">Select a subject</option>
                            <option value="general">General Inquiry</option>
                            <option value="support">Technical Support</option>
                            <option value="membership">Membership Questions</option>
                            <option value="feedback">Feedback</option>
                            <option value="partnership">Partnership</option>
                        </select>
                        <i class="fas fa-tag absolute right-4 top-12 text-red-600 opacity-50"></i>
                    </div>
                    
                    <div class="relative group">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Your Message *</label>
                        <textarea name="message" rows="6" required placeholder="Tell us how we can help you..."
                            class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-600 focus:border-red-600 transition bg-gray-50 hover:bg-white group-hover:border-red-300 resize-none"></textarea>
                        <i class="fas fa-comment absolute right-4 top-12 text-red-600 opacity-50"></i>
                    </div>
                    
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white py-4 rounded-xl transition font-bold text-lg hover:scale-105 duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Send Message
                    </button>
                </form>
            </div>

            <!-- Enhanced Contact Info -->
            <div class="space-y-8">
                <!-- Contact Information Card -->
                <div class="relative bg-white rounded-2xl shadow-2xl p-8 md:p-12 transform hover:shadow-3xl transition duration-300 border border-gray-100">
                    <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-red-600 to-red-800 rounded-t-2xl"></div>
                    <div class="absolute -top-6 left-8 bg-red-600 p-4 rounded-full shadow-lg">
                        <i class="fas fa-info-circle text-white text-xl"></i>
                    </div>
                    
                    <div class="flex justify-center md:justify-start mb-8 mt-4">
                        <img src="{{ asset('images/logo.png') }}" alt="PulseOne Logo" class="w-40 h-auto">
                    </div>
                    
                    <h3 class="text-2xl md:text-3xl font-black text-gray-900 mb-8">Contact Information</h3>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-red-50 border border-red-100 hover:border-red-200 transition-colors duration-300">
                            <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-white text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 mb-1">Address</h4>
                                <span class="text-gray-700">No. 25, Kandy Road, Colombo 07, Sri Lanka</span>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-red-50 border border-red-100 hover:border-red-200 transition-colors duration-300">
                            <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope text-white text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 mb-1">Email</h4>
                                <a href="mailto:support@pulseone.fit" class="text-red-600 hover:text-red-700 transition-colors">support@pulseone.fit</a>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-red-50 border border-red-100 hover:border-red-200 transition-colors duration-300">
                            <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone-alt text-white text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 mb-1">Phone</h4>
                                <a href="tel:+94712345678" class="text-red-600 hover:text-red-700 transition-colors">+94 71 234 5678</a>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-red-50 border border-red-100 hover:border-red-200 transition-colors duration-300">
                            <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-clock text-white text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 mb-1">Business Hours</h4>
                                <div class="text-gray-700 text-sm">
                                    <div>Mon - Fri: 6:00 AM - 10:00 PM</div>
                                    <div>Sat - Sun: 7:00 AM - 9:00 PM</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Media Card -->
                <div class="relative bg-white rounded-2xl shadow-2xl p-8 transform hover:shadow-3xl transition duration-300 border border-gray-100">
                    <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-red-600 to-red-800 rounded-t-2xl"></div>
                    
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Follow Us</h3>
                    <div class="flex gap-4">
                        <a href="#" class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center hover:bg-red-700 transform hover:scale-110 transition duration-300">
                            <i class="fab fa-facebook-f text-white"></i>
                        </a>
                        <a href="#" class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center hover:bg-red-700 transform hover:scale-110 transition duration-300">
                            <i class="fab fa-instagram text-white"></i>
                        </a>
                        <a href="#" class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center hover:bg-red-700 transform hover:scale-110 transition duration-300">
                            <i class="fab fa-twitter text-white"></i>
                        </a>
                        <a href="#" class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center hover:bg-red-700 transform hover:scale-110 transition duration-300">
                            <i class="fab fa-linkedin-in text-white"></i>
                        </a>
                        <a href="#" class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center hover:bg-red-700 transform hover:scale-110 transition duration-300">
                            <i class="fab fa-youtube text-white"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Enhanced Map & Location Section -->
    <section class="py-24 bg-gradient-to-br from-gray-50 to-white relative overflow-hidden">
        <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>
        <div class="max-w-7xl mx-auto px-4 relative z-10">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-black text-gray-900 mb-4">
                    Find Our <span class="bg-gradient-to-r from-red-600 to-red-700 bg-clip-text text-transparent">Location</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Located in the heart of Colombo - easily accessible and ready to welcome you
                </p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                <!-- Map Container -->
                <div class="lg:col-span-2 relative" data-aos="fade-right">
                    <div class="relative bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-100">
                        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-red-600 to-red-800"></div>
                        
                        <!-- Google Maps Embed showing Kandy Gyms -->
                        <div class="w-full h-96 lg:h-[500px] relative overflow-hidden">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m16!1m12!1m3!1d31685.736547982147!2d80.62059895!3d7.2944043!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!2m1!1sgyms%20in%20kandy%20sri%20lanka!5e0!3m2!1sen!2slk!4v1694345678901!5m2!1sen!2slk" 
                                width="100%" 
                                height="100%" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade"
                                class="rounded-lg">
                            </iframe>
                            
                            <!-- Floating location markers -->
                            <div class="absolute top-4 left-4 bg-white rounded-lg shadow-lg p-3 border border-gray-200 backdrop-blur-sm bg-opacity-95">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 bg-red-600 rounded-full animate-pulse"></div>
                                    <span class="text-sm font-semibold text-gray-900">Gyms in Kandy</span>
                                </div>
                            </div>
                            
                            <!-- Map overlay controls -->
                            <div class="absolute bottom-4 right-4 space-y-2">
                                <a href="https://www.google.com/maps/search/gyms+in+kandy+sri+lanka" 
                                   target="_blank"
                                   class="block bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition font-semibold text-sm shadow-lg">
                                    <i class="fas fa-external-link-alt mr-2"></i>
                                    Open in Google Maps
                                </a>
                                <button onclick="refreshMap()" 
                                        class="block bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg transition font-semibold text-sm shadow-lg border border-gray-200 w-full">
                                    <i class="fas fa-redo-alt mr-2"></i>
                                    Refresh Map
                                </button>
                            </div>
                        </div>
                        
                        <!-- Quick directions with Kandy gym references -->
                        <div class="p-6 bg-white border-t border-gray-100">
                            <h4 class="font-bold text-gray-900 mb-3">Popular Gyms in Kandy Area</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div class="flex items-center gap-2 text-gray-700">
                                    <i class="fas fa-dumbbell text-red-600"></i>
                                    <span>Revolution Gym - Kandy City</span>
                                </div>
                                <div class="flex items-center gap-2 text-gray-700">
                                    <i class="fas fa-map-marker-alt text-red-600"></i>
                                    <span>Near Kandy City Center</span>
                                </div>
                                <div class="flex items-center gap-2 text-gray-700">
                                    <i class="fas fa-car text-red-600"></i>
                                    <span>Accessible by main roads</span>
                                </div>
                                <div class="flex items-center gap-2 text-gray-700">
                                    <i class="fas fa-users text-red-600"></i>
                                    <span>Multiple fitness centers</span>
                                </div>
                            </div>
                            
                            <!-- Additional gym locations -->
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <h5 class="font-semibold text-gray-800 mb-2">Other Fitness Centers Nearby:</h5>
                                <div class="space-y-2 text-xs text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                        <span>Fitness First - Temple Street</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                        <span>Power Gym - Peradeniya Road</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                                        <span>Gold's Gym - Kandy Central</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Location Details -->
                <div class="space-y-6" data-aos="fade-left">
                    <!-- Address Card -->
                    <div class="relative bg-white rounded-2xl shadow-2xl p-8 transform hover:shadow-3xl transition duration-300 border border-gray-100">
                        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-red-600 to-red-800 rounded-t-2xl"></div>
                        <div class="absolute -top-6 left-6 bg-red-600 p-4 rounded-full shadow-lg">
                            <i class="fas fa-location-arrow text-white text-xl"></i>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-gray-900 mb-6 mt-4">Find Gyms in Kandy</h3>
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-map-marked-alt text-red-600 text-lg mt-1"></i>
                                <div>
                                    <p class="font-semibold text-gray-900">Kandy Fitness Hub</p>
                                    <p class="text-gray-700">Multiple gyms & fitness centers</p>
                                    <p class="text-gray-700">Central Province, Sri Lanka</p>
                                </div>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-4">
                                <div class="space-y-2">
                                    <div class="flex items-center gap-3 text-gray-700">
                                        <i class="fas fa-dumbbell text-red-600"></i>
                                        <span class="text-sm">Revolution Gym</span>
                                    </div>
                                    <div class="flex items-center gap-3 text-gray-700">
                                        <i class="fas fa-dumbbell text-red-600"></i>
                                        <span class="text-sm">Fitness First Kandy</span>
                                    </div>
                                    <div class="flex items-center gap-3 text-gray-700">
                                        <i class="fas fa-dumbbell text-red-600"></i>
                                        <span class="text-sm">Power Gym</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Transportation Card -->
                    <div class="relative bg-white rounded-2xl shadow-2xl p-8 transform hover:shadow-3xl transition duration-300 border border-gray-100">
                        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-red-600 to-red-800 rounded-t-2xl"></div>
                        
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Getting Here</h3>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 p-3 rounded-lg bg-red-50 border border-red-100">
                                <div class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-car text-white"></i>
                                </div>
                                <div class="text-sm">
                                    <p class="font-semibold text-gray-900">By Car</p>
                                    <p class="text-gray-600">Free parking for members</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3 p-3 rounded-lg bg-red-50 border border-red-100">
                                <div class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-bus text-white"></i>
                                </div>
                                <div class="text-sm">
                                    <p class="font-semibold text-gray-900">Public Transport</p>
                                    <p class="text-gray-600">Multiple bus routes available</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3 p-3 rounded-lg bg-red-50 border border-red-100">
                                <div class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-taxi text-white"></i>
                                </div>
                                <div class="text-sm">
                                    <p class="font-semibold text-gray-900">Ride Services</p>
                                    <p class="text-gray-600">Uber, PickMe available</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Enhanced CTA Section -->
    <section class="py-24 bg-gradient-to-br from-red-600 via-red-700 to-red-800 relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0">
            <div class="absolute top-0 left-0 w-full h-full bg-black opacity-10"></div>
            <div class="absolute top-10 left-10 w-32 h-32 bg-white opacity-5 rounded-full blur-xl animate-pulse"></div>
            <div class="absolute bottom-10 right-10 w-40 h-40 bg-white opacity-5 rounded-full blur-xl animate-pulse" style="animation-delay: 1s;"></div>
            <div class="absolute top-1/2 left-1/4 w-24 h-24 bg-white opacity-5 rounded-full blur-xl animate-pulse" style="animation-delay: 2s;"></div>
        </div>
        
        <div class="max-w-6xl mx-auto px-4 text-center relative z-10">
            <div data-aos="zoom-in" data-aos-duration="800">
                <!-- Icon -->
                <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full mx-auto mb-8 flex items-center justify-center backdrop-blur-sm shadow-2xl">
                    <i class="fas fa-rocket text-white text-3xl"></i>
                </div>
                
                <!-- Main Heading -->
                <h2 class="text-4xl md:text-6xl font-black text-white mb-6 leading-tight">
                    Ready to Start Your
                    <span class="block bg-gradient-to-r from-yellow-300 to-orange-300 bg-clip-text text-transparent">Fitness Journey?</span>
                </h2>
                
                <!-- Subheading -->
                <p class="text-xl md:text-2xl text-red-100 mb-12 max-w-4xl mx-auto leading-relaxed">
                    Join thousands of satisfied members who have transformed their lives with PulseOne. 
                    <span class="font-semibold text-white">Your transformation starts with a single message.</span>
                </p>
                
                <!-- Stats Row -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12" data-aos="fade-up" data-aos-delay="200">
                    <div class="bg-white bg-opacity-10 rounded-2xl p-6 backdrop-blur-sm border border-white border-opacity-20">
                        <div class="text-3xl md:text-4xl font-black text-white mb-2">24/7</div>
                        <div class="text-red-100 text-sm md:text-base">Support Available</div>
                    </div>
                    <div class="bg-white bg-opacity-10 rounded-2xl p-6 backdrop-blur-sm border border-white border-opacity-20">
                        <div class="text-3xl md:text-4xl font-black text-white mb-2">&lt;24h</div>
                        <div class="text-red-100 text-sm md:text-base">Response Time</div>
                    </div>
                    <div class="bg-white bg-opacity-10 rounded-2xl p-6 backdrop-blur-sm border border-white border-opacity-20">
                        <div class="text-3xl md:text-4xl font-black text-white mb-2">100%</div>
                        <div class="text-red-100 text-sm md:text-base">Member Satisfaction</div>
                    </div>
                    <div class="bg-white bg-opacity-10 rounded-2xl p-6 backdrop-blur-sm border border-white border-opacity-20">
                        <div class="text-3xl md:text-4xl font-black text-white mb-2">5★</div>
                        <div class="text-red-100 text-sm md:text-base">Average Rating</div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-8" data-aos="fade-up" data-aos-delay="400">
                    <a href="#contact-form" 
                        class="group bg-white text-red-600 px-10 py-5 rounded-2xl font-black text-lg hover:bg-red-50 transform hover:scale-105 transition duration-300 shadow-2xl hover:shadow-3xl flex items-center gap-3">
                        <i class="fas fa-paper-plane group-hover:translate-x-1 transition-transform duration-300"></i>
                        Send Message Now
                    </a>
                    <a href="tel:+94712345678" 
                        class="group bg-transparent border-2 border-white text-white px-10 py-5 rounded-2xl font-black text-lg hover:bg-white hover:text-red-600 transform hover:scale-105 transition duration-300 flex items-center gap-3">
                        <i class="fas fa-phone group-hover:rotate-12 transition-transform duration-300"></i>
                        Call Us Directly
                    </a>
                </div>
                
                <!-- Quick Contact Options -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto" data-aos="fade-up" data-aos-delay="600">
                    <div class="bg-white bg-opacity-10 rounded-xl p-6 backdrop-blur-sm border border-white border-opacity-20 hover:bg-opacity-20 transition duration-300 group">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full mx-auto mb-4 flex items-center justify-center group-hover:scale-110 transition duration-300">
                            <i class="fas fa-calendar-alt text-white text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Schedule a Tour</h3>
                        <p class="text-red-100 text-sm">Book a free facility tour</p>
                    </div>
                    
                    <div class="bg-white bg-opacity-10 rounded-xl p-6 backdrop-blur-sm border border-white border-opacity-20 hover:bg-opacity-20 transition duration-300 group">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full mx-auto mb-4 flex items-center justify-center group-hover:scale-110 transition duration-300">
                            <i class="fas fa-user-plus text-white text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Join Today</h3>
                        <p class="text-red-100 text-sm">Start your membership</p>
                    </div>
                    
                    <div class="bg-white bg-opacity-10 rounded-xl p-6 backdrop-blur-sm border border-white border-opacity-20 hover:bg-opacity-20 transition duration-300 group">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full mx-auto mb-4 flex items-center justify-center group-hover:scale-110 transition duration-300">
                            <i class="fas fa-question-circle text-white text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Ask Questions</h3>
                        <p class="text-red-100 text-sm">Get expert advice</p>
                    </div>
                </div>
                
                <!-- Trust Indicators -->
                <div class="mt-12 pt-8 border-t border-white border-opacity-20" data-aos="fade-up" data-aos-delay="800">
                    <p class="text-red-100 text-sm mb-4">Trusted by fitness enthusiasts across Sri Lanka</p>
                    <div class="flex justify-center items-center gap-8">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-shield-alt text-yellow-300 text-xl"></i>
                            <span class="text-white text-sm font-semibold">Secure & Private</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-clock text-yellow-300 text-xl"></i>
                            <span class="text-white text-sm font-semibold">Quick Response</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-heart text-yellow-300 text-xl"></i>
                            <span class="text-white text-sm font-semibold">Member Focused</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Legacy CTA Section for Reference -->
    <section class="py-16 px-4 md:px-6 bg-red-50" data-aos="fade-up" style="display: none;">
        <div class="max-w-7xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden flex flex-col md:flex-row items-center">
            <div class="w-full md:w-1/2 p-8">
                <h3 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-4 leading-tight">
                    Ready to Start Your Fitness Journey?
                </h3>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Join PulseOne today and connect with expert trainers, personalized plans, and a community dedicated to your success.
                </p>
                <a href="{{ route('register') }}">
                    <button class="bg-red-600 text-white px-6 py-3 rounded-md hover:bg-red-700 transition text-sm font-semibold hover:scale-105">
                        Sign Up Now
                    </button>
                </a>
            </div>
            <div class="w-full md:w-1/2">
                <img src="{{ asset('images/gym-red.jpg') }}" alt="Gym" class="w-full h-full object-cover">
            </div>
        </div>
    </section>

    <!-- Footer -->
    @include('components.footer')

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init();

        // Map refresh functionality
        function refreshMap() {
            const iframe = document.querySelector('iframe[src*="google.com/maps"]');
            if (iframe) {
                const currentSrc = iframe.src;
                iframe.src = '';
                setTimeout(() => {
                    iframe.src = currentSrc;
                }, 100);
            }
        }

        // Add smooth scroll to contact form when clicking "Send Message Now"
        document.addEventListener('DOMContentLoaded', function() {
            const contactFormLink = document.querySelector('a[href="#contact-form"]');
            if (contactFormLink) {
                contactFormLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    const contactSection = document.querySelector('section .space-y-6');
                    if (contactSection) {
                        contactSection.scrollIntoView({ 
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            }
        });

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js')
                .then(reg => console.log('Service Worker registered:', reg))
                .catch(err => console.error('Service Worker registration failed:', err));
        }
    </script>
</body>
</html>