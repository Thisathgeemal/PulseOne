<div 
    x-data="{
        showSettings: false,
        showProfile: false,
        lastScroll: 0,
        handleScroll() {
            const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
            if (currentScroll > this.lastScroll) {
                // User is scrolling down
                this.showSettings = false;
                this.showProfile = false;
            }
            this.lastScroll = currentScroll <= 0 ? 0 : currentScroll;
        }
    }"
    x-init="window.addEventListener('scroll', () => handleScroll())"
    class="relative" >

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
            <button @click=" showSettings = !showSettings; if (showSettings) showProfile = false; ">
                <i class="fas fa-cog text-white text-xl cursor-pointer hover:text-yellow-400"></i>
            </button>               

            <!-- Notifications -->
            <div class="relative cursor-pointer">
                <i class="fas fa-bell text-white text-xl"></i>
                <span class="absolute -top-1 -right-2 bg-red-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">3</span>
            </div>

            <!-- Profile -->
            <button @click="showProfile = !showProfile; if (showProfile) showSettings = false;">
                @if(Auth::user()->profile_image)
                    <img src="{{ asset(Auth::user()->profile_image) }}?v={{ time() }}"
                         alt="User Avatar"
                         class="w-10 h-10 rounded-full border-2 border-white object-cover hover:ring-2 ring-yellow-400 transition">
                @else
                    <div class="w-10 h-10 rounded-full bg-orange-300 text-orange-900 flex items-center justify-center font-bold uppercase border-2 border-white hover:ring-2 ring-yellow-400 transition">
                        {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}
                    </div>
                @endif
            </button>
        </div>
    </div>

    <!-- Slide-in Settings Panel -->
    <div 
        x-show="showSettings"
        x-transition
        if (showSettings) showProfile = false;
        @click.away="showSettings = false"
        class="fixed right-0 top-16 bottom-0 w-[400px] bg-white text-black rounded-md shadow-lg z-100 p-7 overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Settings</h2>
            <button @click="showSettings = false" class="text-gray-500 hover:text-red-600 text-xl">&times;</button>
        </div>

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

    <!-- Profile Sidebar -->
    <div x-show="showProfile" x-transition
        class="fixed right-0 top-16 bottom-0 w-[400px] bg-white text-black shadow-lg z-100 overflow-y-auto">
        <div class="p-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Edit Profile</h2>
                <button @click="showProfile = false" class="text-gray-500 hover:text-red-600 text-xl">&times;</button>
            </div>

            <!-- Avatar -->
            <div class="relative text-center mb-4">
                <label for="profile_image" class="relative cursor-pointer inline-block">
                    @if(Auth::user()->profile_image)
                        <img src="{{ asset(Auth::user()->profile_image) }}?v={{ time() }}"
                            class="w-20 h-20 rounded-full object-cover border-2 border-gray-300" />
                    @else
                        <div class="w-20 h-20 rounded-full bg-orange-200 text-orange-800 flex items-center justify-center text-2xl font-bold uppercase border-2 border-gray-300">
                            {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="absolute bottom-0 right-0 bg-white w-6 h-6 flex items-center justify-center rounded-full border">
                        <i class="fas fa-pen text-sm text-gray-500"></i>
                    </div>
                </label>
                <form action="{{ route('admin.profile.removeImage') }}" method="POST" class="mt-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-red-500 hover:underline mt-1">Remove Image</button>
                </form>
            </div>

            <!-- Profile Form -->
            <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <input type="file" id="profile_image" name="profile_image" class="hidden" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    <div>
                        <label class="text-sm font-medium text-gray-700">First Name</label>
                        <input type="text" name="first_name" value="{{ Auth::user()->first_name }}"
                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Last Name</label>
                        <input type="text" name="last_name" value="{{ Auth::user()->last_name }}"
                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Email</label>
                        <input type="email" value="{{ Auth::user()->email }}" disabled
                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 bg-gray-100 text-gray-600">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Mobile Number</label>
                        <input type="text" name="mobile_number" value="{{ Auth::user()->mobile_number }}"
                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Address</label>
                        <input type="text" name="address" value="{{ Auth::user()->address }}"
                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">DOB</label>
                        <input type="date" name="dob" value="{{ Auth::user()->dob ? Auth::user()->dob->format('Y-m-d') : '' }}"
                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>

                <!-- Password Update -->
                <div class="pt-2">
                    <div class="flex justify-between items-center mb-1">
                        <label for="current_password" class="text-sm font-medium text-gray-700">Change Password</label>
                        <button type="button" id="verifyPasswordBtn" class="text-sm text-green-600 hover:underline">Verify</button>
                    </div>
                    <input type="password" name="current_password" id="current_password"
                        placeholder="Current password"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-red-500 focus:border-red-500">
                </div>

                <div class="flex items-center gap-2 mt-2" id="checkResult" style="display: none;">
                    <span id="checkIcon"></span>
                    <span id="checkMessage" class="text-sm"></span>
                </div>

                <div id="passwordFields" class="space-y-4 hidden mt-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" name="password"
                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Confirm Password</label>
                        <input type="password" name="password_confirmation"
                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>

                <div class="flex justify-end gap-4 pt-1">
                    <button type="submit" class="bg-red-500 text-white px-6 py-2 rounded hover:bg-red-600">Save</button>
                </div>
            </form>
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

    @if (session('error') || $errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: `
                    @if(session('error'))
                        {{ session('error') }}
                    @else
                        {!! implode('<br>', $errors->all()) !!}
                    @endif
                `,
                confirmButtonText: 'OK',
                confirmButtonColor: '#d32f2f'
            });
        </script>
    @endif

    <script>
        document.getElementById('verifyPasswordBtn')?.addEventListener('click', async () => {
            const password = document.getElementById('current_password').value;
            const icon = document.getElementById('checkIcon');
            const msg = document.getElementById('checkMessage');
            const result = document.getElementById('checkResult');

            result.style.display = 'flex';
            msg.textContent = 'Checking...';
            icon.innerHTML = `<i class="fas fa-spinner fa-spin text-gray-500"></i>`;

            try {
                const res = await fetch("{{ route('admin.profile.checkPassword') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ password })
                });

                const data = await res.json();

                if (data.success) {
                    icon.innerHTML = `<i class="fas fa-check-circle text-green-500"></i>`;
                    msg.textContent = 'Password confirmed.';
                    document.getElementById('passwordFields').classList.remove('hidden');
                } else {
                    icon.innerHTML = `<i class="fas fa-times-circle text-red-500"></i>`;
                    msg.textContent = 'Incorrect password.';
                }
            } catch (err) {
                icon.innerHTML = `<i class="fas fa-exclamation-triangle text-yellow-500"></i>`;
                msg.textContent = 'Error validating password.';
            }
        });

        document.getElementById('profile_image')?.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            const previewContainer = document.querySelector('label[for="profile_image"]');
            const existingImg = previewContainer.querySelector('img');
            const letterDiv = previewContainer.querySelector('div');

            if (existingImg) {
                existingImg.src = URL.createObjectURL(file);
            } else if (letterDiv) {
                const newImg = document.createElement('img');
                newImg.src = URL.createObjectURL(file);
                newImg.className = 'w-20 h-20 rounded-full object-cover border-2 border-gray-300';
                previewContainer.replaceChild(newImg, letterDiv);
            }
        });
    </script>
@endpush