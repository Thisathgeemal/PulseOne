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

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Fontawesome CDN -->
    <script src="https://kit.fontawesome.com/bc9b460555.js" crossorigin="anonymous"></script>

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
        <div class="w-full max-w-md py-10 px-6 bg-white rounded-lg shadow-md">
            <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">Send Reset Link</h2>
            
            <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-4">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 items-center gap-2 mb-2">
                        <i class="fa-solid fa-envelope text-sm mr-1.5 ml-1"></i>
                        Email
                    </label>
                    <input type="email" name="email" id="email" 
                           class="block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 ease-in-out" 
                           placeholder="Enter your email" required>
                    @error('email')
                        <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" 
                        class="w-full px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Send Reset Link
                </button>
            </form>

            <div class="text-center mt-6">
                <p class="text-sm text-gray-600">
                    Remember your password? 
                    <a href="{{ route('login') }}" class="text-red-600 hover:text-red-500 font-medium underline transition-colors duration-200">Back to login</a>
                </p>
            </div>
        </div>
    </section>

    @if (session('status'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('status') }}",
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'rounded-xl'
                }
            });
        </script>
    @endif
</body>
</html>