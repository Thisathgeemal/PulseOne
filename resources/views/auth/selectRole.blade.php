<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>PULSEONE</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
     <script src="https://kit.fontawesome.com/bc9b460555.js" crossorigin="anonymous"></script>

    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/bc9b460555.js" crossorigin="anonymous"></script>

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-white text-gray-800 overflow-hidden">
    <!-- Header -->
    <nav class="sticky top-0 z-50 flex justify-between items-center px-10 py-3 bg-white/80 backdrop-blur-sm border-b border-gray-200 shadow-sm">
        <!-- Logo -->
        <a href="{{ route('home') }}">
            <img src="{{ asset('images/logo.png') }}" alt="PulseOne Logo" class="h-12 w-auto">
        </a>
    </nav>

    <!-- Main Content -->
    <section class="h-[calc(100vh-64px)] bg-cover bg-center flex items-center justify-center min-h-full" style="background-image: url('{{ url('images/gym-red.jpg') }}');">
        <div class="w-full max-w-xl p-8 bg-white rounded-lg shadow-md">
            <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">Which dashboard would you like to access?</h2>
            @if(session('user_roles'))
                <form action="{{ route('selectRole.submit') }}" method="POST">
                    @csrf
                    @php
                        $icons = [
                            'admin' => 'fa-solid fa-user-tie',
                            'trainer' => 'fa-solid fa-dumbbell',
                            'dietitian' => 'fa-solid fa-apple-whole',
                            'member' => 'fa-solid fa-user',
                        ];

                        $labels = [
                            'admin' => 'Admin',
                            'trainer' => 'Trainer',
                            'dietitian' => 'Dietitian',
                            'member' => 'Member',
                        ];

                        $userRoles = session('user_roles') ?? [];
                    @endphp

                    <div class="flex justify-center gap-6 flex-wrap sm:flex-nowrap">
                        @foreach ($userRoles as $role)
                            @php
                                $lowerRole = strtolower($role);
                                $isActive = session('selected_role') == $role;
                                $cardClasses = $isActive
                                    ? 'bg-red-600 text-white scale-105'
                                    : 'bg-white text-gray-800 border border-red-300 hover:bg-red-50 hover:scale-105';
                            @endphp

                            <button type="submit" name="selected_role" value="{{ $role }}"
                                class="w-36 h-36 flex flex-col items-center justify-center gap-3 p-4 rounded-xl shadow-md transition-all duration-300 transform
                                       focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $cardClasses }}">
                                <i class="{{ $icons[$lowerRole] ?? 'fa-solid fa-user' }} text-3xl"></i>
                                <span class="text-sm font-semibold">{{ $labels[$lowerRole] ?? ucfirst($role) }}</span>
                            </button>
                        @endforeach
                    </div>
                </form>
            @else
                <div class="text-red-600 text-center">
                    No roles found in session.
                </div>
            @endif
        </div>
    </section>

    @if (session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '{{ session('error') }}',
            confirmButtonColor: '#d32f2f'
        });
    </script>
    @endif
</body>
</html>
