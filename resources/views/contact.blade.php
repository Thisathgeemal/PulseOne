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
    <section class="relative bg-cover bg-center h-[400px]" style="background-image: url('{{ asset('images/contact-hero.jpg') }}');" data-aos="fade-up">
        <div class="absolute inset-0 bg-black bg-opacity-50 flex flex-col justify-center items-center text-white px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-4">Get in Touch</h1>
            <p class="text-lg md:text-xl max-w-3xl leading-relaxed">
                We're here to support your fitness journey. Have questions, feedback, or need assistance? Reach out to us today!
            </p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-20 px-4 max-w-7xl mx-auto bg-gray-50" data-aos="fade-up">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            <!-- Contact Form -->
            <div class="relative bg-white rounded-xl shadow-xl p-8 md:p-10 transform hover:shadow-2xl transition duration-300">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-red-600 to-red-800 rounded-t-xl"></div>
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-6 mt-4">Send Us a Message</h2>
                <form action="#" method="POST" class="space-y-5">
                    @csrf
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700">Your Name</label>
                        <input type="text" name="name" required
                            class="w-full mt-1 px-4 py-3 border border-gray-300 rounded-md focus:ring-red-600 focus:border-red-600 transition bg-gray-50 hover:bg-white">
                        <i class="fas fa-user absolute right-3 top-10 text-red-600"></i>
                    </div>
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" name="email" required
                            class="w-full mt-1 px-4 py-3 border border-gray-300 rounded-md focus:ring-red-600 focus:border-red-600 transition bg-gray-50 hover:bg-white">
                        <i class="fas fa-envelope absolute right-3 top-10 text-red-600"></i>
                    </div>
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700">Subject</label>
                        <input type="text" name="subject"
                            class="w-full mt-1 px-4 py-3 border border-gray-300 rounded-md focus:ring-red-600 focus:border-red-600 transition bg-gray-50 hover:bg-white">
                        <i class="fas fa-tag absolute right-3 top-10 text-red-600"></i>
                    </div>
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700">Your Message</label>
                        <textarea name="message" rows="5" required
                            class="w-full mt-1 px-4 py-3 border border-gray-300 rounded-md focus:ring-red-600 focus:border-red-600 transition bg-gray-50 hover:bg-white"></textarea>
                        <i class="fas fa-comment absolute right-3 top-10 text-red-600"></i>
                    </div>
                    <button type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-md transition font-semibold text-sm hover:scale-105 duration-200">
                        Send Message
                    </button>
                </form>
            </div>

            <!-- Contact Info -->
            <div class="relative bg-white rounded-xl shadow-xl p-8 md:p-10 transform hover:shadow-2xl transition duration-300">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-red-600 to-red-800 rounded-t-xl"></div>
                <div class="flex justify-center md:justify-start mb-6 mt-4">
                    <img src="{{ asset('images/logo.png') }}" alt="PulseOne Logo" class="w-36 h-auto">
                </div>
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-6">Contact Information</h2>
                <ul class="space-y-6 text-gray-700">
                    <li class="flex items-center gap-4">
                        <i class="fas fa-map-marker-alt text-red-600 text-2xl"></i>
                        <span class="text-[15px]">No. 25, Kandy Road, Colombo 07, Sri Lanka</span>
                    </li>
                    <li class="flex items-center gap-4">
                        <i class="fas fa-envelope text-red-600 text-2xl"></i>
                        <span class="text-[15px]">support@pulseone.fit</span>
                    </li>
                    <li class="flex items-center gap-4">
                        <i class="fas fa-phone-alt text-red-600 text-2xl"></i>
                        <span class="text-[15px]">+94 71 234 5678</span>
                    </li>
                </ul>
                <div class="mt-8">
                    <h3 class="text-lg font-semibold mb-4">Follow Us</h3>
                    <div class="flex gap-6 text-red-600 text-2xl">
                        <a href="#" class="hover:text-red-700 transform hover:scale-110 transition"><i class="fab fa-facebook-square"></i></a>
                        <a href="#" class="hover:text-red-700 transform hover:scale-110 transition"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="hover:text-red-700 transform hover:scale-110 transition"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="py-16 px-4" data-aos="fade-up">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-2xl md:text-3xl font-bold mb-6 text-center text-gray-900">Find Us</h2>
            <iframe class="w-full h-96 rounded-xl shadow-lg grayscale-[30%]" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
                src="https://maps.google.com/maps?q=colombo%20sri%20lanka&t=&z=13&ie=UTF8&iwloc=&output=embed">
            </iframe>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 px-4 md:px-6 bg-red-50" data-aos="fade-up">
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

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js')
                .then(reg => console.log('Service Worker registered:', reg))
                .catch(err => console.error('Service Worker registration failed:', err));
        }
    </script>
</body>
</html>