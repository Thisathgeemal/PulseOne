<footer class="bg-[#0D1117] text-white pt-16 px-6">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12 border-b border-gray-700 pb-12">

        <!-- Left: Logo + Contact -->
        <div class="space-y-6">
            <img src="{{ asset('images/footer-logo.png') }}" alt="PulseOne Logo" class="h-14">
            <ul class="space-y-3 text-sm text-gray-300">
                <li class="flex items-start gap-3">
                    <i class="fas fa-map-marker-alt mt-0.5 text-xl text-white"></i>
                    <span class="hover:text-white transition">No. 25, Kandy Road, Colombo 07, Sri Lanka</span>
                </li>
                <li class="flex items-start gap-3">
                    <i class="fas fa-phone-alt mt-0.5 text-lg text-white"></i>
                    <span class="hover:text-white transition">(+94) 11 234 5678</span>
                </li>
                <li class="flex items-start gap-3">
                    <i class="fas fa-envelope mt-0.5 text-lg text-white"></i>
                    <span class="hover:text-white transition">info@pulseone.lk</span>
                </li>
            </ul>
        </div>

        <!-- Middle: Links -->
        <div class="space-y-3">
            <h4 class="text-white font-semibold">Quick Links</h4>
            <ul class="text-sm text-gray-300 space-y-2">
                <li>
                    <a href="{{ route('home') }}" class="hover:text-white">
                        Home
                    </a>
                </li>
                <li>
                    <a href="{{ route('about') }}" class="hover:text-white">
                        About
                    </a>
                </li>
                <li>
                    <a href="{{ route('features') }}" class="hover:text-white">
                        Features
                    </a>
                </li>
                <li>
                    <a href="{{ route('challenges') }}" class="hover:text-white">
                        Challenges
                    </a>
                </li>
                <li>
                    <a href="{{ route('contact') }}" class="hover:text-white">
                        Contact Us
                    </a>
                </li>
            </ul>
        </div>

        <!-- Right: CTA + Social -->
        <div class="flex flex-col justify-between space-y-4">
            <div>
                <h4 class="text-white font-medium leading-snug">
                    Have questions or want to get started with <strong>PulseOne</strong>?
                </h4>
                <a href="#" class="mt-4 inline-block bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded transition text-mb">
                    Get in touch →
                </a>
            </div>
            <div class="flex gap-4 mt-4">
                <a href="#" class="text-gray-400 hover:text-white text-xl"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-gray-400 hover:text-white text-xl"><i class="fab fa-facebook"></i></a>
                <a href="#" class="text-gray-400 hover:text-white text-xl"><i class="fab fa-linkedin"></i></a>
                <a href="#" class="text-gray-400 hover:text-white text-xl"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </div>

    <!-- Bottom -->
    <div class="text-sm text-gray-500 text-center py-6 hover:text-white transition">
        © {{ date('Y') }} PulseOne, Inc. All rights reserved.
    </div>
</footer>
