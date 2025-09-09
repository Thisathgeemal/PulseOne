<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PULSEONE</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (function () {
            try {
                var mode = localStorage.getItem('themeMode') || 'auto';
                var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                var shouldDark = mode === 'dark' || (mode === 'auto' && prefersDark);
                if (shouldDark) document.documentElement.classList.add('theme-dark');
            } catch (_) {}
        })();
    </script>
    
    <!-- Theme Variables -->
    <link rel="stylesheet" href="{{ asset('css/theme-variables.css') }}">

    {{-- <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script> --}}

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

    <!-- AOS CSS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css" />

    <!-- FontAwesome -->
    <script src="https://kit.fontawesome.com/bc9b460555.js" crossorigin="anonymous"></script>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Choices.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <!-- Choices.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <style>
        .choices__inner {
            min-height: 44px;
            border-radius: 0.5rem; /* Tailwind rounded-lg */
            border-color: #d1d5db; /* Tailwind gray-300 */
            box-shadow: none;
            transition: box-shadow 0.2s ease;
        }
        .choices__inner:focus-within {
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.5); /* Tailwind ring-red-500 */
            border-color: #ef4444;
        }
        /* Optional: Adjust padding inside multi-select input */
        .choices__input {
            padding: 0.375rem 0.75rem;
        }
    </style>
    
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}

    @livewireStyles

</head>
<body class="bg-gray-100 text-gray-900">

    <div class="flex">
        <!-- Sidebar -->
        @include('trainerDashboard.components.sidebar')

        <!-- Main Content -->
        <div class="flex-1 min-h-screen">
            <!-- Header -->
            @include('trainerDashboard.components.header')

            <!-- Page Content -->
            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>

</body>
</html>