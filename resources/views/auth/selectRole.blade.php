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
        <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-md">
            <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">Which dashboard would you like to access?</h2>
            @if(session('user_roles'))
                <form action="{{ route('selectRole.submit') }}" method="POST" class="flex flex-col gap-4">
                    @csrf
                    @foreach (session('user_roles') as $role)
                        <button type="submit" name="selected_role" value="{{ $role }}"
                                class="w-full px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            {{ ucfirst($role) }}
                        </button>
                    @endforeach
                </form>
            @else
                <div class="text-red-600 text-center">
                    No roles found in session.
                </div>
            @endif
        </div>
    </section>

</body>
</html>
