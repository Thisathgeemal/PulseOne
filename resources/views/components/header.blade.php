<!-- Header -->
<nav class="sticky top-0 z-50 flex justify-between items-center px-10 py-3 bg-white/80 backdrop-blur-sm border-b border-gray-200 shadow-sm">

    <!-- Logo -->
    <a href="{{ route('home') }}">
        <img src="{{ asset('images/logo.png') }}" alt="PulseOne Logo" class="h-12 w-auto">
    </a>

    <!-- Navigation Links -->
    <ul class="flex space-x-16 text-[16px] font-medium">
        <li>
            <a href="{{ route('home') }}"
               class="{{ request()->routeIs('home') ? 'text-red-600 font-bold' : 'text-gray-800 hover:text-red-600 transition' }}">
               Home
            </a>
        </li>
        <li>
            <a href="{{ route('about') }}"
               class="{{ request()->routeIs('about') ? 'text-red-600 font-bold' : 'text-gray-800 hover:text-red-600 transition' }}">
               About
            </a>
        </li>
        <li>
            <a href="{{ route('features') }}"
               class="{{ request()->routeIs('features') ? 'text-red-600 font-bold' : 'text-gray-800 hover:text-red-600 transition' }}">
               Features
            </a>
        </li>
        <li>
            <a href="{{ route('challenges') }}"
               class="{{ request()->routeIs('challenges') ? 'text-red-600 font-bold' : 'text-gray-800 hover:text-red-600 transition' }}">
               Challenges
            </a>
        </li>
        <li>
            <a href="{{ route('contact') }}"
               class="{{ request()->routeIs('contact') ? 'text-red-600 font-bold' : 'text-gray-800 hover:text-red-600 transition' }}">
               Contact Us
            </a>
        </li>
    </ul>

    <!-- Action Buttons -->
    <div class="flex space-x-3">
        <a href="{{ route('login') }}">
            <button class="text-sm font-semibold border border-red-600 text-red-600 px-4 py-1.5 rounded hover:bg-red-600 hover:text-white transition">
                Sign in
            </button>
        </a>
        <a href="{{ route('register') }}">
            <button class="text-sm font-semibold bg-black text-white px-4 py-1.5 rounded hover:bg-gray-900 transition">
                Sign up
            </button>
        </a>
    </div>

</nav>
