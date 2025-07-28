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
        <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-md">
            <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">Two-Factor Authentication</h2>
            <form method="POST" action="{{ route('2fa.verify') }}" class="flex flex-col gap-4">
                @csrf
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Enter the 6-digit code sent to your email:</label>
                    <input type="text" name="code" id="code" class="block w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors" placeholder="123456" maxlength="6" required>
                    @error('code')
                        <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Verify Code</button>
            </form>

            <form method="POST" action="{{ route('2fa.resend') }}" class="text-center mt-4">
                @csrf
                <button type="submit" class="text-sm text-gray-600">
                    Didn't receive a code? 
                    <span class="text-red-600 hover:text-red-500 underline">Resend</span>
                </button>
            </form>

        </div>
    </section>

</body>
</html>
