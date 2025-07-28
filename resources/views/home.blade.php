<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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

        <!-- Hero Banner -->
        <section class="relative bg-cover bg-center h-[500px]" style="background-image: url('{{ asset('images/hero.jpg') }}');">
            <div class="absolute inset-0 bg-black bg-opacity-40 flex flex-col justify-center items-center text-white px-4 text-center" data-aos="fade-up">
                <h1 class="text-5xl font-bold mb-4">WORK MORE!</h1>
                <p class="max-w-xl mb-6">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quis nostrae exercitatio ullamcorper suscipit lobortis nisi ut aliquip.</p>
                <div class="space-x-4">
                    <button class="bg-red-600 px-5 py-2 rounded hover:scale-105">Join us now</button>
                    <button class="border px-5 py-2 rounded hover:scale-105">Learn more</button>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section class="py-20 px-3 max-w-7xl mx-auto space-y-20" data-aos="fade-up">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-14 items-center">
                <div>
                    <h2 class="text-[26px] font-bold mb-4 text-gray-900">Book sessions with our top trainers</h2>
                    <p class="text-gray-600 mb-5 text-[15px] leading-relaxed">
                        Get matched with certified trainers who guide you toward your fitness goals.<br>
                        Choose your preferred trainer, time slot, and session type all in just a few <br>clicks.
                    </p>
                    <div class="flex space-x-4">
                        <button class="bg-red-600 text-white px-5 py-2 rounded hover:bg-red-700 hover:scale-105 transition font-medium text-sm">Try now</button>
                        <button class="border border-gray-400 px-5 py-2 rounded hover:bg-gray-100 hover:scale-105 transition font-medium text-sm">Learn more</button>
                    </div>
                </div>
                <div class="relative flex justify-center md:justify-end">
                    <div class="absolute right-[30px] hidden md:block md:right-[126px] top-[37px] z-0">
                        <img src="{{ asset('images/dot.jpg') }}" alt="Dots" class="w-full h-60 opacity-70">
                    </div>
                    <img src="{{ asset('images/session.jpg') }}" alt="Trainer Session" class="shadow-xl w-full max-w-md relative z-10">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-14 items-center">
                <div class="flex justify-center md:justify-start order-2 md:order-1">
                    <img src="{{ asset('images/plan.png') }}" alt="Diet Plan" class="shadow-xl w-full max-w-md">
                </div>
                <div class="order-1 md:order-2">
                    <h2 class="text-[26px] font-bold mb-4 text-gray-900">Personalized Workout & Diet Plans</h2>
                    <p class="text-gray-600 mb-5 text-[15px] leading-relaxed">
                        Receive custom workout and diet plans created by professionals based on your body type and goals.
                        Whether you want to build muscle, lose weight, or eat cleaner <br>we’ve got you covered.
                    </p>
                    <div class="flex space-x-4">
                        <button class="bg-red-600 text-white px-5 py-2 rounded hover:bg-red-700 hover:scale-105 transition font-medium text-sm">Try now</button>
                        <button class="border border-gray-400 px-5 py-2 rounded hover:bg-gray-100 hover:scale-105 transition font-medium text-sm">Learn more</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Trainers Section -->
        <section class="py-20 px-3 max-w-7xl mx-auto space-y-12" data-aos="fade-up">
            <h2 class="text-[40px] font-bold text-center">Meet Our Personal Trainers</h2>

            <div class="relative">
                <!-- Navigation Buttons -->
                <div class="swiper-button-prev !left-[-40px] text-black"></div>
                <div class="swiper-button-next !right-[-40px] text-black"></div>

                <!-- Swiper Main -->
                <div class="swiper trainerSwiper pb-16"> 
                <div class="swiper-wrapper">
                    @foreach ([
                        ['name' => 'Omar Ali', 'role' => 'Fitness Trainer', 'img' => 'trainer1.jpg'],
                        ['name' => 'Ravindu Perera', 'role' => 'Fitness Instructor & Wellness Coach', 'img' => 'trainer2.jpg'],
                        ['name' => 'Natalie Fernando', 'role' => 'Fitness Trainer & Sports Messiah', 'img' => 'trainer3.jpg'],
                        ['name' => 'John Smith', 'role' => 'Yoga Expert', 'img' => 'trainer4.jpg'],
                        ['name' => 'Ayesha Silva', 'role' => 'Pilates Instructor', 'img' => 'trainer5.jpg'],
                        ['name' => 'Kavin Raj', 'role' => 'CrossFit Coach', 'img' => 'trainer6.jpg'],
                    ] as $trainer)
                    <div class="swiper-slide">
                    <div class="bg-white shadow-lg rounded-xl overflow-hidden text-center">
                        <img src="{{ asset('images/' . $trainer['img']) }}" alt="Trainer" class="w-full h-[330px] object-cover">
                        <div class="py-4 px-2">
                        <h3 class="text-[18px] font-bold">{{ $trainer['name'] }}</h3>
                        <p class="text-gray-500 text-sm">{{ $trainer['role'] }}</p>
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
        <section class="py-16 px-6 bg-red-50 text-center" data-aos="fade-up">
            <h2 class="text-xl font-bold mb-6">Reviews</h2>
            <p class="max-w-2xl mx-auto text-gray-700 italic mb-4">
                “PulseOne made my fitness journey so much easier. Booking is quick, everything is personalized, and it’s like having a coach in my pocket!”
            </p>
            <div class="flex items-center justify-center gap-3">
                <img src="{{ asset('images/reviewer.jpg') }}" alt="Reviewer" class="w-12 h-12 rounded-full">
                <p class="font-medium text-sm">Kenneth Dias</p>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-16 px-4 md:px-6" data-aos="fade-up">
            <div class="max-w-7xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden flex flex-col md:flex-row items-center">
                
                <!-- Left Text -->
                <div class="w-full md:w-1/2 p-8">
                    <h3 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-4 leading-tight">
                        Ready to Transform Your<br>Fitness Journey?
                    </h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Join a smarter fitness platform built for real results. From personalized plans to seamless session booking and expert guidance — PulseOne helps you stay consistent, accountable, and in control.
                    </p>
                    <a href="{{ route('register') }}">
                        <button class="bg-red-600 text-white px-6 py-3 rounded-md hover:bg-red-700 transition text-sm font-semibold">
                            Sign up Now
                        </button>
                    </a>
                </div>

                <!-- Right Image -->
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

            const swiper = new Swiper(".trainerSwiper", {
                slidesPerView: 1,
                spaceBetween: 20,
                loop: true,
                navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
                },
                pagination: {
                el: ".swiper-pagination",
                clickable: true,
                },
                breakpoints: {
                768: { slidesPerView: 2 },
                1024: { slidesPerView: 3 },
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
