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
        class="absolute right-0 top-16 w-[400px] bg-white text-black rounded-md shadow-lg z-50 p-6">
        <h2 class="text-2xl font-bold text-gray-800 border-b-2 border-gray-300 pb-2 mb-2">Settings</h2>

        <!-- MFA Security -->
        <div class="py-4">
            <h2 class="text-xl font-semibold mb-4">MFA Security</h2>
            <form action="{{ route('settings.mfa-toggle') }}" method="POST" class="border rounded-lg p-4 shadow-md">
                @csrf
                <p class="mb-2">Two-Factor Authentication is currently <strong>{{ auth()->user()->mfa_enabled ? 'Enabled' : 'Disabled' }}</strong>.</p>
                @if(auth()->user()->mfa_enabled)
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Disable Two-Factor Authentication
                    </button>
                @else
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Enable Two-Factor Authentication
                    </button>
                @endif
            </form>
        </div>

        <!-- Active Sessions -->
        <div class="py-4">
            <h2 class="text-xl font-semibold mb-4">Browser Sessions</h2>
            @foreach ($sessions as $session)
                <div class="border rounded-lg p-4 shadow-md mb-4">
                    <div><strong>IP:</strong> {{ $session['ip_address'] }}</div>
                    <div><strong>Device:</strong> {{ $session['device'] }}</div>
                    <div><strong>Last Active:</strong> {{ $session['last_activity'] }}</div>
                    <div>
                        @if ($session['is_current'])
                            <span class="text-green-600 font-semibold">Current Session</span>
                        @else
                            <form action="{{ route('security.logout.device') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="session_id" value="{{ $session['id'] }}">
                                <button type="submit" class="ml-4 mt-2 px-4 py-2 bg-red-500 text-white font-bold rounded hover:bg-red-700">
                                    Log out
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach

            @if ($sessions->count() > 1)
                <form action="{{ route('security.logout.all') }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Logout from all other devices
                    </button>
                </form>
            @endif
        </div>
    </div>

</div>

@push('scripts')
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: "{{ session('success') }}",
            confirmButtonText: 'OK',
            confirmButtonColor: '#d32f2f'
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "{{ session('error') }}",
            confirmButtonText: 'OK',
            confirmButtonColor: '#d32f2f'
        });
    </script>
    @endif
@endpush