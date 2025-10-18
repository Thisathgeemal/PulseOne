<!-- Header -->
<nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-sm border-b border-gray-200 shadow-sm">
    <div class="flex justify-between items-center px-4 sm:px-6 lg:px-10 py-3">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="flex-shrink-0">
            <img src="{{ asset('images/logo.png') }}" alt="PulseOne Logo" class="h-8 sm:h-10 lg:h-12 w-auto">
        </a>

        <!-- Mobile Menu Button -->
        <button class="md:hidden p-2 rounded-md text-gray-700 hover:text-red-600 transition" id="mobile-menu-button">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                </path>
            </svg>
        </button>

        <!-- Navigation Links -->
        <ul class="hidden md:flex lg:flex xl:flex space-x-16 text-[16px] font-medium">
            <li class="mr-0">
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

        <!-- Desktop Action Buttons -->
        <div class="hidden md:flex space-x-2 lg:space-x-3">
            <a href="{{ route('login') }}">
                <button
                    class="text-xs lg:text-sm font-semibold border border-red-600 text-red-600 px-3 lg:px-4 py-1.5 rounded hover:bg-red-600 hover:text-white transition">
                    Sign in
                </button>
            </a>
            <a href="{{ route('register') }}">
                <button
                    class="text-xs lg:text-sm font-semibold bg-black text-white px-3 lg:px-4 py-1.5 rounded hover:bg-gray-900 transition">
                    Sign up
                </button>
            </a>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="md:hidden hidden bg-white border-t border-gray-200" id="mobile-menu">
        <div class="px-4 py-2 space-y-1">
            <!-- Mobile Navigation Links -->
            <a href="{{ route('home') }}"
                class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('home') ? 'text-red-600 bg-red-50' : 'text-gray-700 hover:text-red-600 hover:bg-gray-50' }} transition">
                Home
            </a>
            <a href="{{ route('about') }}"
                class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('about') ? 'text-red-600 bg-red-50' : 'text-gray-700 hover:text-red-600 hover:bg-gray-50' }} transition">
                About
            </a>
            <a href="{{ route('features') }}"
                class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('features') ? 'text-red-600 bg-red-50' : 'text-gray-700 hover:text-red-600 hover:bg-gray-50' }} transition">
                Features
            </a>
            <a href="{{ route('challenges') }}"
                class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('challenges') ? 'text-red-600 bg-red-50' : 'text-gray-700 hover:text-red-600 hover:bg-gray-50' }} transition">
                Challenges
            </a>
            <a href="{{ route('contact') }}"
                class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('contact') ? 'text-red-600 bg-red-50' : 'text-gray-700 hover:text-red-600 hover:bg-gray-50' }} transition">
                Contact Us
            </a>

            <!-- Mobile Action Buttons -->
            <div class="pt-4 pb-2 space-y-2">
                <a href="{{ route('login') }}" class="block">
                    <button
                        class="w-full text-sm font-semibold border border-red-600 text-red-600 px-4 py-2 rounded hover:bg-red-600 hover:text-white transition">
                        Sign in
                    </button>
                </a>
                <a href="{{ route('register') }}" class="block">
                    <button
                        class="w-full text-sm font-semibold bg-black text-white px-4 py-2 rounded hover:bg-gray-900 transition">
                        Sign up
                    </button>
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');

            // Toggle hamburger/close icon
            const icon = mobileMenuButton.querySelector('svg path');
            if (mobileMenu.classList.contains('hidden')) {
                icon.setAttribute('d', 'M4 6h16M4 12h16M4 18h16');
            } else {
                icon.setAttribute('d', 'M6 18L18 6M6 6l12 12');
            }
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                mobileMenu.classList.add('hidden');
                const icon = mobileMenuButton.querySelector('svg path');
                icon.setAttribute('d', 'M4 6h16M4 12h16M4 18h16');
            }
        });
    });
</script>
