{{-- <div class="flex items-center justify-between bg-[#1E1E1E] text-white px-6 py-3 shadow-sm">
    <!-- Left: Welcome Message -->
    <div class="flex items-center gap-4 text-lg font-semibold">
        <i class="fa-solid fa-user-tie text-white text-xl"></i>
        <span class="font-semibold">
            Welcome, {{ Auth::user()->first_name }}
        </span>
    </div>

    <!-- Right: Icons -->
    <div class="flex items-center gap-6">
        <!-- Settings -->
        <a href="{{ route('admin.settings') }}">
            <i class="fas fa-cog text-white text-xl cursor-pointer hover:text-yellow-400"></i>
        </a>

        <!-- Notifications -->
        <div class="relative cursor-pointer">
            <i class="fas fa-bell text-white text-xl"></i>
            <span class="absolute -top-1 -right-2 bg-red-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">3</span>
        </div>

        <!-- Avatar with fallback and cache-busting -->
        <a href="{{ route('admin.profile') }}">
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
</div> --}}


<div x-data="{ showSettings: false }" class="relative">

    <!-- Topbar -->
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
            <!-- Settings -->
            <button @click="showSettings = !showSettings">
                <i class="fas fa-cog text-white text-xl cursor-pointer hover:text-yellow-400"></i>
            </button>

            <!-- Notifications -->
            <div class="relative cursor-pointer">
                <i class="fas fa-bell text-white text-xl"></i>
                <span class="absolute -top-1 -right-2 bg-red-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">3</span>
            </div>

            <!-- Avatar -->
            <a href="{{ route('admin.profile') }}">
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

    <!-- Slide-in Settings Panel -->
    <div 
        x-show="showSettings"
        x-transition
        @click.away="showSettings = false"
        class="absolute right-0 top-16 w-[600px] bg-white text-black rounded-md shadow-lg z-50 p-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Settings</h2>

        <!-- Notification Settings -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2 text-gray-700">Notification Settings</h3>

            <div class="space-y-3 text-sm text-gray-600">
                <label class="flex items-center gap-2">
                    <input type="checkbox" checked class="accent-yellow-500 rounded">
                    Receive email notifications for upcoming sessions
                </label>

                <label class="flex items-center gap-2">
                    <input type="checkbox" checked class="accent-yellow-500 rounded">
                    Notify me when a trainer updates my workout plan
                </label>

                <label class="flex items-center gap-2">
                    <input type="checkbox" class="accent-yellow-500 rounded">
                    Receive promotional emails
                </label>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="mb-6 border-t pt-4">
            <h3 class="text-lg font-semibold mb-2 text-gray-700">Security Settings</h3>

            <div class="mb-3 text-sm text-gray-600">
                <p>Last Password Change: <span class="font-medium text-gray-800">2025-06-01</span></p>
                <p>Two-Factor Authentication: 
                    <span class="font-semibold text-green-600 ml-1">Enabled</span>
                </p>
            </div>

            <!-- Active Sessions -->
            <div class="bg-gray-100 rounded p-3 text-sm space-y-2">
                <p class="text-gray-700 font-medium">Active Sessions:</p>
                <ul class="list-disc ml-5 text-gray-600">
                    <li>Windows, Chrome – Today, 9:45 AM</li>
                    <li>Mobile App – Yesterday, 5:22 PM</li>
                </ul>
            </div>

            <!-- Logout from all devices -->
            <div class="mt-4">
                <form method="POST" action="#">
                    @csrf
                    <button type="submit" class="mt-2 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-sm font-medium">
                        Log out from all devices
                    </button>
                </form>
            </div>
        </div>

        <!-- Language & Region -->
        <div class="mb-6 border-t pt-4">
            <h3 class="text-lg font-semibold mb-2 text-gray-700">Language & Region</h3>
            <div class="space-y-2 text-sm text-gray-600">
                <p>Language: <span class="font-medium text-gray-800">English (UK)</span></p>
                <p>Timezone: <span class="font-medium text-gray-800">Asia/Colombo (GMT+5:30)</span></p>
            </div>
        </div>

        <!-- Optional Save Button -->
        <div class="border-t pt-4 flex justify-end">
            <button type="button" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 text-sm font-semibold">
                Save Preferences
            </button>
        </div>
    </div>

</div>
