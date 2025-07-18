<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>PULSEONE</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" href="{{ asset('images/favicon.png') }}">
    <script src="https://kit.fontawesome.com/bc9b460555.js" crossorigin="anonymous"></script>

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
    <section class="h-[calc(100vh-64px)] bg-cover bg-center" style="background-image: url('{{ url('images/gym-red.jpg') }}');">
        <div class="relative z-10 flex flex-col items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-md">
                <h2 class="text-3xl md:text-4xl font-bold text-center mb-3">Welcome back!</h2>
                <p class="text-sm text-gray-600 mb-4 text-center">Sign in to your PulseOne account</p>
                <div class="flex justify-center items-center w-full">
                    @error('email')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 items-center gap-2">
                            <i class="fa-solid fa-envelope text-sm mr-1.5 ml-1"></i>
                            Email
                        </label>
                        <input id="email" type="email" name="email" required autofocus
                               placeholder="Enter your email"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                               value="{{ old('email') }}">
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 items-center gap-2">
                            <i class="fa-solid fa-lock text-sm mr-1.5 ml-1"></i>
                            Password
                        </label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required
                                   placeholder="Enter your password"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm pr-10">
                            <button type="button" tabindex="-1"
                                class="absolute inset-y-0 right-0 flex px-3 text-gray-500 top-3"
                                onclick="togglePasswordVisibility()">
                                <i id="password-toggle-icon" class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me and Forgot Password -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember_me" type="checkbox" name="remember"
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <label for="remember_me" class="ml-2 block text-sm font-medium text-gray-700">Remember me</label>
                        </div>
                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-gray-700 hover:text-red-500">Forgot password</a>
                    </div>

                    <!-- Login Button -->
                    <div>
                        <button type="submit"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm font-medium text-white bg-red-600 hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Sign in
                        </button>
                    </div>

                    <!-- Register Link -->
                    <div class="text-sm text-center">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="text-red-600 font-semibold hover:text-red-500"> Sign up</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</body>
</html>
