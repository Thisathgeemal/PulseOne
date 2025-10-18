<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
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

    <style>
        @keyframes floating {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-10px) rotate(1deg);
            }
        }

        .floating-animation {
            animation: floating 6s ease-in-out infinite;
        }

        .gradient-text {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>

<body class="bg-white text-gray-800">

    <!-- Header -->
    @include('components.header')

    {{-- <!-- Hero Banner -->
    <section class="relative bg-cover bg-center h-[400px] sm:h-[500px] lg:h-[600px] overflow-hidden"
        style="background-image: url('{{ asset('images/banner1.jpg') }}');">
        <div class="absolute inset-0 bg-black bg-opacity-35"></div>

        <!-- Floating elements for visual appeal -->
        <div
            class="absolute top-5 sm:top-10 right-5 sm:right-10 w-48 sm:w-72 h-48 sm:h-72 bg-red-600/10 rounded-full blur-3xl floating-animation">
        </div>

        <div class="absolute bottom-5 sm:bottom-10 left-5 sm:left-10 w-64 sm:w-96 h-64 sm:h-96 bg-red-500/5 rounded-full blur-3xl floating-animation"
            style="animation-delay: -3s;"></div>

        <div class="relative flex flex-col justify-center items-center text-white px-4 sm:px-6 text-center h-full"
            data-aos="fade-up">
            <!-- Hero Content -->
            <div class="max-w-4xl mx-auto mb-4 sm:mb-6">

            </div>

            <!-- CTA Buttons repositioned to sit closer to the banner's built-in 'Join Now' graphic -->
            <div class="absolute z-20 left-10 sm:left-16 md:left-24 lg:left-35 top-28 sm:top-32 md:top-60 lg:top-44 translate-y-1 sm:translate-y-2 flex flex-row gap-2 sm:gap-3 items-center"
                data-aos="fade-up" data-aos-delay="250">
                <a href="{{ route('register') }}">
                    <button
                        class="bg-white/90 backdrop-blur text-black font-semibold rounded-md px-4 py-2 text-xs sm:text-sm md:text-base shadow hover:bg-white transition">
                        Join us now
                    </button>
                </a>
                <a href="{{ route('about') }}">
                    <button
                        class="border border-white/80 text-white font-semibold rounded-md px-4 py-2 text-xs sm:text-sm md:text-base hover:bg-white hover:text-black transition">
                        Learn more
                    </button>
                </a>
            </div>
        </div>
    </section> --}}

    <!-- Hero Banner -->
    <section class="relative bg-cover bg-center h-[500px]"
        style="background-image: url('{{ asset('images/banner1.jpg') }}');">
        <div class="absolute inset-0 bg-black bg-opacity-35 flex flex-col justify-center items-center text-white px-4 text-center"
            data-aos="fade-up">
            <div class="absolute bottom-38 left-28 flex space-x-4 z-10" data-aos="fade-up">
                <a href="{{ route('register') }}">
                    <button
                        class="w-38 h-12 bg-white text-black px-5 py-2 rounded-md font-semibold shadow hover:scale-105 transition">Join
                        us now</button>
                </a>
                <a href="{{ route('about') }}">
                    <button
                        class="w-38 h-12 border border-white text-white px-5 py-2 rounded-md font-semibold hover:bg-white hover:text-black transition">Learn
                        more</button>
                </a>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-12 sm:py-16 lg:py-20 px-4 sm:px-6 max-w-7xl mx-auto space-y-12 sm:space-y-16 lg:space-y-20"
        data-aos="fade-up">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 sm:gap-12 lg:gap-14 items-center">
            <div>
                <h2
                    class="text-xl sm:text-2xl lg:text-3xl xl:text-4xl font-bold mb-3 sm:mb-4 text-gray-900 leading-tight">
                    Book sessions with our top trainers</h2>
                <p class="text-gray-600 mb-4 sm:mb-5 text-sm sm:text-base leading-relaxed">
                    Get matched with certified trainers who guide you toward your fitness goals.
                    Choose your preferred trainer, time slot, and session type all in just a few clicks.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                    <button
                        class="bg-red-600 text-white px-4 sm:px-5 py-2 sm:py-3 rounded hover:bg-red-700 hover:scale-105 transition font-medium text-sm sm:text-base">Try
                        now</button>
                    <button
                        class="border border-gray-400 px-4 sm:px-5 py-2 sm:py-3 rounded hover:bg-gray-100 hover:scale-105 transition font-medium text-sm sm:text-base">Learn
                        more</button>
                </div>
            </div>
            <div class="relative flex justify-center lg:justify-end order-first lg:order-last">
                <div class="absolute right-[30px] hidden lg:block lg:right-[126px] top-[37px] z-0">
                    <img src="{{ asset('images/dot.jpg') }}" alt="Dots"
                        class="w-full h-40 sm:h-48 lg:h-60 opacity-70">
                </div>
                <img src="{{ asset('images/session.jpg') }}" alt="Trainer Session"
                    class="shadow-xl w-full max-w-sm sm:max-w-md relative z-10 rounded-lg">
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 sm:gap-12 lg:gap-14 items-center">
            <div class="flex justify-center lg:justify-start">
                <img src="{{ asset('images/plan.png') }}" alt="Diet Plan"
                    class="shadow-xl w-full max-w-sm sm:max-w-md rounded-lg">
            </div>
            <h2 class="text-xl sm:text-2xl lg:text-3xl xl:text-4xl font-bold mb-3 sm:mb-4 text-gray-900 leading-tight">
                Personalized Workout & Diet Plans</h2>
            <p class="text-gray-600 mb-5 text-[15px] leading-relaxed">
                Receive custom workout and diet plans created by professionals based on your body type and goals.
                Whether you want to build muscle, lose weight, or eat cleaner <br>we’ve got you covered.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                <button
                    class="bg-red-600 text-white px-4 sm:px-5 py-2 sm:py-3 rounded hover:bg-red-700 hover:scale-105 transition font-medium text-sm sm:text-base">Try
                    now</button>
                <button
                    class="border border-gray-400 px-4 sm:px-5 py-2 sm:py-3 rounded hover:bg-gray-100 hover:scale-105 transition font-medium text-sm sm:text-base">Learn
                    more</button>
            </div>
        </div>
        </div>
    </section>

    <!-- Trainers Section -->
    <section class="py-12 sm:py-16 lg:py-20 px-4 sm:px-6 max-w-7xl mx-auto space-y-8 sm:space-y-12" data-aos="fade-up">
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-center">Meet Our Personal Trainers</h2>

        <div class="relative">
            <!-- Navigation Buttons - Hidden on mobile -->
            <div class="swiper-button-prev !left-[-40px] text-black hidden sm:block"></div>
            <div class="swiper-button-next !right-[-40px] text-black hidden sm:block"></div>

            <!-- Swiper Main -->
            <div class="swiper trainerSwiper pb-16">
                <div class="swiper-wrapper">
                    @foreach ([['name' => 'Omar Ali', 'role' => 'Fitness Trainer', 'img' => 'trainer1.jpg'], ['name' => 'Ravindu Perera', 'role' => 'Fitness Instructor & Wellness Coach', 'img' => 'trainer2.jpg'], ['name' => 'Natalie Fernando', 'role' => 'Fitness Trainer & Sports Messiah', 'img' => 'trainer3.jpg'], ['name' => 'John Smith', 'role' => 'Yoga Expert', 'img' => 'trainer4.jpg'], ['name' => 'Ayesha Silva', 'role' => 'Pilates Instructor', 'img' => 'trainer5.jpg'], ['name' => 'Kavin Raj', 'role' => 'CrossFit Coach', 'img' => 'trainer6.jpg']] as $trainer)
                        <div class="swiper-slide">
                            <div class="bg-white shadow-lg rounded-xl overflow-hidden text-center mx-2">
                                <img src="{{ asset('images/' . $trainer['img']) }}" alt="Trainer"
                                    class="w-full h-[250px] sm:h-[300px] lg:h-[330px] object-cover">
                                <div class="py-3 sm:py-4 px-2">
                                    <h3 class="text-base sm:text-lg font-bold">{{ $trainer['name'] }}</h3>
                                    <p class="text-gray-500 text-xs sm:text-sm">{{ $trainer['role'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Move pagination inside Swiper with margin -->
                <div class="swiper-pagination mt-2 !relative !bottom-[-2px] text-center"></div>
            </div>
        </div>
    </section>

    <!-- Reviews -->
    <section class="py-12 sm:py-16 px-4 sm:px-6 bg-red-50 text-center" data-aos="fade-up">
        <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold mb-4 sm:mb-6">Reviews</h2>
        <p class="max-w-2xl mx-auto text-gray-700 italic mb-4 sm:mb-6 text-sm sm:text-base leading-relaxed">
            "PulseOne made my fitness journey so much easier. Booking is quick, everything is personalized, and it's
            like having a coach in my pocket!"
        </p>
        <div class="flex items-center justify-center gap-3">
            <img src="{{ asset('images/reviewer.jpg') }}" alt="Reviewer"
                class="w-10 h-10 sm:w-12 sm:h-12 rounded-full">
            <p class="font-medium text-sm sm:text-base">Kenneth Dias</p>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-12 sm:py-16 px-4 sm:px-6" data-aos="fade-up">
        <div
            class="max-w-7xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden flex flex-col lg:flex-row items-center">

            <!-- Left Text -->
            <div class="w-full lg:w-1/2 p-6 sm:p-8">
                <h3 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-gray-900 mb-4 leading-tight">
                    Ready to Transform Your Fitness Journey?
                </h3>
                <p class="text-gray-600 mb-6 leading-relaxed text-sm sm:text-base">
                    Join a smarter fitness platform built for real results. From personalized plans to seamless session
                    booking and expert guidance — PulseOne helps you stay consistent, accountable, and in control.
                </p>
                <a href="{{ route('register') }}">
                    <button
                        class="w-full sm:w-auto bg-red-600 text-white px-6 py-3 rounded-md hover:bg-red-700 transition text-sm font-semibold">
                        Sign up Now
                    </button>
                </a>
            </div>

            <!-- Right Image -->
            <div class="w-full lg:w-1/2 order-first lg:order-last">
                <img src="{{ asset('images/gym-red.jpg') }}" alt="Gym"
                    class="w-full h-48 sm:h-64 lg:h-full object-cover">
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

        const swiper = new Swiper(".trainerSwiper", {
            slidesPerView: 1,
            spaceBetween: 15,
            loop: true,
            centeredSlides: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            breakpoints: {
                480: {
                    slidesPerView: 1.2,
                    spaceBetween: 15,
                    centeredSlides: true,
                },
                640: {
                    slidesPerView: 1.5,
                    spaceBetween: 20,
                    centeredSlides: true,
                },
                768: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                    centeredSlides: false,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 25,
                    centeredSlides: false,
                },
            },
        });

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js')
                .then(reg => console.log('Service Worker registered:', reg))
                .catch(err => console.error('Service Worker registration failed:', err));
        }
    </script>
</body>

</html>
