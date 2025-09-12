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
        (function() {
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

    <!-- Alpine.js -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script> --}}

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

    <!-- AOS CSS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css" />

    <!-- FontAwesome -->
    <script src="https://kit.fontawesome.com/bc9b460555.js" crossorigin="anonymous"></script>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}

    @livewireStyles

</head>

<body class="bg-gray-100 text-gray-900">

    <div class="flex">
        <!-- Sidebar -->
        @include('adminDashboard.components.sidebar')

        <!-- Main Content -->
        <div class="main-content flex-1 min-h-screen">
            <!-- Header -->
            @include('adminDashboard.components.header')

            <!-- Page Content -->
            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')

    <script>
        // Ensure admin pages always show the full sidebar width.
        // Some dashboards store a 'sidebar-compact' preference in localStorage
        // or scripts may add it; remove it on admin pages so the admin sidebar
        // doesn't collapse unexpectedly (e.g., on the Feedback page).
        document.addEventListener('DOMContentLoaded', function() {
            try {
                document.documentElement.classList.remove('sidebar-compact');
                localStorage.setItem('sidebarCompact', 'false');

                const sidebar = document.querySelector('.sidebar');
                if (sidebar) {
                    sidebar.classList.remove('w-16');
                    sidebar.classList.add('w-64');
                }
            } catch (e) {
                // noop
                console.error('admin layout sidebar fix error', e);
            }
        });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

</body>

</html>
