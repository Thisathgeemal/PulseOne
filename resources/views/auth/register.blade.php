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
<body class="bg-white text-gray-800">

    <!-- Header -->
    <nav class="sticky top-0 z-50 flex justify-between items-center px-10 py-3 bg-white/80 backdrop-blur-sm border-b border-gray-200 shadow-sm">
        <!-- Logo -->
        <a href="{{ route('home') }}">
            <img src="{{ asset('images/logo.png') }}" alt="PulseOne Logo" class="h-12 w-auto">
        </a>
    </nav>

    <!-- Main Content -->
    <section class="min-h-screen bg-cover bg-center overflow-y-auto" style="background-image: url('{{ url('images/gym-red.jpg') }}');">
        <div class="flex flex-col md:flex-row justify-center items-center gap-6 w-full max-w-5xl mx-auto px-4 py-12">
            <!-- Registration Form -->
            <div id="create-form" class="w-full max-w-sm md:max-w-md bg-white rounded-xl shadow-xl p-8">
                <h2 class="text-3xl md:text-4xl font-bold text-center mb-3">Create Account</h2>                

                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <!-- Member Details Section -->
                    <p class="text-sm text-gray-600 mb-6 text-center border-b-2 pb-2 border-red-500">Sign up for your PulseOne account</p>
                    <!-- First Name -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 items-center gap-2">
                            <i class="fa-solid fa-user text-sm mr-1.5 ml-1"></i>
                            First Name
                        </label>
                        <input id="first_name" type="text" name="first_name" required autofocus
                                placeholder="Enter your first name"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                                value="{{ old('first_name') }}">
                        @error('first_name')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 items-center gap-2 mt-4">
                            <i class="fa-solid fa-user text-sm mr-1.5 ml-1"></i>
                            Last Name
                        </label>
                        <input id="last_name" type="text" name="last_name" required
                                placeholder="Enter your last name"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                                value="{{ old('last_name') }}">
                        @error('last_name')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 items-center gap-2 mt-4">
                            <i class="fa-solid fa-envelope text-sm mr-1.5 ml-1"></i>
                            Email
                        </label>
                        <input id="email" type="email" name="email" required autofocus
                            placeholder="Enter your email"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            value="{{ old('email') }}">
                        @error('email')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 items-center gap-2 mt-4">
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
                        @error('password')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Contact Number -->
                    <div>
                        <label for="contact_number" class="block text-sm font-medium text-gray-700 items-center gap-2 mt-4">
                            <i class="fa-solid fa-phone text-sm mr-1.5 ml-1"></i>
                            Contact Number
                        </label>
                        <input id="contact_number" type="text" name="contact_number" required
                                placeholder="Enter your contact number"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                                value="{{ old('contact_number') }}">
                        @error('contact_number')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Membership Type -->
                    <div>
                        <label for="membership_type" class="block text-sm font-medium text-gray-700 items-center gap-2 mt-4">
                            <i class="fa-solid fa-id-card text-sm mr-1.5 ml-1"></i>
                            Membership Type
                        </label>
                        <select id="membership_type" name="membership_type" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            <option value="" disabled selected>Select membership type</option>
                            <option value="basic" {{ old('membership_type') == 'basic' ? 'selected' : '' }}>Basic</option>
                            <option value="premium" {{ old('membership_type') == 'premium' ? 'selected' : '' }}>Premium</option>
                            <option value="vip" {{ old('membership_type') == 'vip' ? 'selected' : '' }}>VIP</option>
                        </select>
                        @error('membership_type')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Next Button -->
                    <div class="mt-6">
                        <button type="submit" onclick="goToPaymentSection()"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm font-medium text-white bg-red-600 hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Proceed to Payment
                        </button>
                    </div>

                    <!-- link login -->
                    <div class="mt-4 text-center">
                        <p class="text-sm text-gray-600">
                            Already have an account? 
                            <a href="{{ route('login') }}" class="text-red-600 hover:text-red-500 font-semibold">Sign in</a>
                        </p>
                    </div>

                </form>
            </div>

            <!-- Payment Form -->
            <div id="payment-form" class="w-full max-w-sm md:max-w-md bg-white rounded-xl shadow-xl p-8 hidden">
                <h2 class="text-3xl md:text-4xl font-bold text-center mb-3">Complete Payment</h2>                

                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <!-- Payment Details Section -->
                    <p class="text-sm text-gray-600 mb-5 text-center pb-2 border-b-2 border-red-500">Complete payment to activate your PulseOne account</p>
                    <!-- Accepted Cards -->
                    <div class="flex flex-col items-start">
                        <label class="block text-sm font-medium mb-1">Accepted Cards:</label>
                        <div class="flex space-x-4 mt-2">
                            <img src="{{ asset('images/card-1.webp') }}" alt="Visa" class="h-8 border-1 border-black transition-transform duration-200 hover:scale-110 hover:shadow-lg cursor-pointer"
                                onclick="selectCardType('visa')">
                            <img src="{{ asset('images/card-2.webp') }}" alt="MasterCard" class="h-8 border-1 border-black transition-transform duration-200 hover:scale-110 hover:shadow-lg cursor-pointer"
                                onclick="selectCardType('mastercard')">
                            <img src="{{ asset('images/card-3.webp') }}" alt="American Express" class="h-8 border-1 border-black transition-transform duration-200 hover:scale-110 hover:shadow-lg cursor-pointer"
                                onclick="selectCardType('amex')">
                            <img src="{{ asset('images/card-4.webp') }}" alt="Discover" class="h-8 border-1 border-black transition-transform duration-200 hover:scale-110 hover:shadow-lg cursor-pointer"
                                onclick="selectCardType('discover')">
                        </div>
                    </div>

                    <!-- Card Type -->
                    <div>
                        <label for="card_type" class="block text-sm font-medium text-gray-700 mt-4">
                            <i class="fa-solid fa-credit-card text-sm mr-1.5 ml-1"></i>
                            Card Type
                        </label>
                        <select id="card_type" name="card_type" required autofocus
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            <option value="" disabled selected>Select card type</option>
                            <option value="visa" {{ old('card_type') == 'visa' ? 'selected' : '' }}>Visa</option>
                            <option value="mastercard" {{ old('card_type') == 'mastercard' ? 'selected' : '' }}>MasterCard</option>
                            <option value="amex" {{ old('card_type') == 'amex' ? 'selected' : '' }}>American Express</option>
                            <option value="discover" {{ old('card_type') == 'discover' ? 'selected' : '' }}>Discover</option>
                        </select>
                        @error('card_type')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Name on Card -->
                    <div>
                        <label for="card_name" class="block text-sm font-medium text-gray-700 mt-4">
                            <i class="fa-solid fa-user text-sm mr-1.5 ml-1"></i>
                            Name on Card
                        </label>
                        <input id="card_name" type="text" name="card_name" required autofocus
                            placeholder="Enter name on card"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            value="{{ old('card_name') }}">
                        @error('card_name')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Card Number -->
                    <div>
                        <label for="card_number" class="block text-sm font-medium text-gray-700 mt-4">
                            <i class="fa-solid fa-hashtag text-sm mr-1.5 ml-1"></i>
                            Card Number
                        </label>
                        <input id="card_number" type="text" name="card_number" required autofocus maxlength="16"
                            placeholder="1234 5678 9012 3456"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            value="{{ old('card_number') }}">
                        @error('card_number')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- CVV -->
                    <div>
                        <label for="cvv" class="block text-sm font-medium text-gray-700 mt-4">
                            <i class="fa-solid fa-lock text-sm mr-1.5 ml-1"></i>
                            CVV
                        </label>
                        <input id="cvv" type="text" name="cvv" required autofocus maxlength="3"
                            placeholder="CVV"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            value="{{ old('cvv') }}">
                        @error('cvv')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Expiry Month and Year -->
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="expiry_month" class="block text-sm font-medium text-gray-700">
                                <i class="fa-regular fa-calendar text-sm mr-1.5 ml-1"></i>
                                Expiry Month
                            </label>
                            <select id="expiry_month" name="expiry_month" required autofocus
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                <option value="" disabled selected>MM</option>
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ sprintf('%02d', $m) }}" {{ old('expiry_month') == sprintf('%02d', $m) ? 'selected' : '' }}>
                                        {{ sprintf('%02d', $m) }}
                                    </option>
                                @endfor
                            </select>
                            @error('expiry_month')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="expiry_year" class="block text-sm font-medium text-gray-700">
                                <i class="fa-regular fa-calendar-days text-sm mr-1.5 ml-1"></i>
                                Expiry Year
                            </label>
                            <select id="expiry_year" name="expiry_year" required autofocus
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                <option value="" disabled selected>YYYY</option>
                                @for ($y = date('Y'); $y <= date('Y') + 15; $y++)
                                    <option value="{{ $y }}" {{ old('expiry_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                            @error('expiry_year')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6">
                        <button type="submit" 
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm font-medium text-white bg-red-600 hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Sign Up
                        </button>
                    </div>

                    <!-- link login -->
                    <div class="mt-4 text-center">
                        <p class="text-sm text-gray-600">
                            Already have an account? 
                            <a href="{{ route('login') }}" class="text-red-600 hover:text-red-500 font-semibold">Sign in</a>
                        </p>
                    </div>
                    
                </form>
            </div>
        </div>
    </section>

</body>
</html>
