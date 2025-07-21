<div class="flex items-center justify-between bg-[#1E1E1E] text-white px-6 py-3 shadow-sm">
    <!-- Left: Welcome Message -->
    <div class="flex items-center gap-4 text-lg font-semibold">
        <i class="fa-solid fa-user-tie text-white text-xl"></i>
        <span class="font-semibold">
            Welcome, {{ Auth::user()->first_name }}
        </span>
    </div>

    <!-- Right: Icons -->
    <div class="flex items-center gap-6">
        <!-- Settings (Clickable) -->
        <a href="{{ route('Member.settings') }}">
            <i class="fas fa-cog text-white text-xl cursor-pointer hover:text-yellow-400"></i>
        </a>

        <!-- Notifications -->
        <div class="relative cursor-pointer">
            <i class="fas fa-bell text-white text-xl"></i>
            <span class="absolute -top-1 -right-2 bg-red-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">3</span>
        </div>

        <!-- Avatar with fallback and cache-busting -->
        <a href="{{ route('Member.settings') }}">
            @if(Auth::user()->profile_image)
                <img src="{{ asset(Auth::user()->profile_image) }}?v={{ time() }}"
                    alt="User Avatar"
                    class="w-10 h-10 rounded-full border-2 border-white object-cover hover:ring-2 ring-yellow-400 transition">
            @else
                <div class="w-10 h-10 rounded-full bg-orange-300 text-orange-900 flex items-center justify-center font-bold uppercase border-2 border-white hover:ring-2 ring-yellow-400 transition">
                    {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}
                </div>
            @endif
        </a>

    </div>
</div>
