<div 
    x-data="{
        showSettings: false,
        showProfile: false,
        showNotifications: false,
        showRead: false,
        lastScroll: 0,
        handleScroll() {
            const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
            if (currentScroll > this.lastScroll) {
                this.showSettings = false;
                this.showProfile = false;
                this.showNotifications = false;
            }
            this.lastScroll = currentScroll <= 0 ? 0 : currentScroll;
        }
    }"
    x-init="window.addEventListener('scroll', () => handleScroll())"
    class="relative"
>

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
            <button @click=" showSettings = !showSettings; if (showSettings) { showProfile = false; showNotifications = false } ">
                <i class="fas fa-cog text-white text-xl cursor-pointer hover:text-yellow-400"></i>
            </button>               

            <!-- Notifications -->
            @php
                $unreadCount = $notifications->where('is_read', false)->count();
            @endphp

            <button class="relative cursor-pointer"
                @click="
                    showNotifications = !showNotifications;
                    if(showNotifications) { showSettings = false; showProfile = false; }
                ">
                <i class="fas fa-bell text-white text-xl hover:text-yellow-400"></i>

                <!-- Only show badge if there are unread notifications -->
                @if($unreadCount > 0)
                    <span id="unread-badge" class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">
                        {{ $unreadCount }}
                    </span>
                @endif
            </button>

            <!-- Profile -->
            <button @click="showProfile = !showProfile; if (showProfile) { showSettings = false; showNotifications = false }">
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

    <!-- Notification Panel -->
    <div 
        x-show="showNotifications"
        x-transition
        @click.away="showNotifications = false"
        class="fixed right-0 top-16 bottom-0 w-[400px] bg-white text-black shadow-lg z-100 overflow-y-auto rounded-md"
     >
        <div class="p-7">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-800">Notifications</h2>
                <button @click="showNotifications = false" class="text-gray-500 hover:text-red-600 text-xl">&times;</button>
            </div>

            @php
                $icons = [
                    'Membership' => '🏷️',
                    'Chat'       => '💬',
                    'Payment'    => '💳',
                    'Attendance' => '🗓️',
                    'Feedback'   => '⭐',
                    'Settings'   => '⚙️',
                ];

                $notificationsArray = $notifications->map(function($n) {
                    return [
                        'id' => $n->id,
                        'type' => $n->type,
                        'title' => $n->title,
                        'message' => $n->message,
                        'time' => $n->created_at->diffForHumans(),
                        'is_read' => $n->is_read,
                    ];
                })->values(); 
            @endphp

            <div x-data="notificationPanel()">
                <!-- Unread Notifications -->
                <div class="space-y-4">
                    <template x-for="notification in unreadNotifications" :key="notification.id">
                        <div 
                            x-data="{ offset: 0, start: 0 }"
                            x-on:touchstart="start = $event.touches[0].clientX"
                            x-on:touchmove="offset = $event.touches[0].clientX - start"
                            x-on:touchend="handleSwipe(notification)"
                            x-on:mousedown="start = $event.clientX"
                            x-on:mousemove="if($event.buttons == 1) offset = $event.clientX - start"
                            x-on:mouseup="handleSwipe(notification)"
                            @click="markAsRead(notification)"
                            class="border rounded-lg p-3 shadow-md hover:bg-gray-50 cursor-pointer transform transition-transform duration-200"
                            :style="`translateX(${offset}px)`"
                        >
                            <div class="flex justify-between items-center mb-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-blue-500 text-lg" x-text="icons[notification.type] ?? '🔔'"></span>
                                    <span class="text-sm font-semibold text-blue-600" x-text="notification.type"></span>
                                </div>
                                <span class="text-xs text-gray-500" x-text="notification.time"></span>
                            </div>
                            <div class="ml-6">
                                <h3 class="text-sm font-bold text-gray-800" x-text="notification.title"></h3>
                                <p class="text-sm text-gray-700" x-text="notification.message"></p>
                            </div>
                        </div>
                    </template>
                    <template x-if="unreadNotifications.length === 0 && readNotifications.length > 0">
                        <p class="text-center text-gray-500">No unread notifications.</p>
                    </template>
                </div>

                <!-- Show Read Notifications Button -->
                <div class="text-right mt-4" x-show="readNotifications.length > 0">
                    <button @click="showRead = !showRead" class="text-sm text-green-600 hover:underline">
                        <span x-text="showRead ? 'Hide Read Notifications' : 'Show Read Notifications'"></span>
                    </button>
                </div>

                <!-- Read Notifications -->
                <div x-show="showRead" x-transition class="space-y-4 mt-4">
                    <template x-if="readNotifications.length === 0">
                        <p class="text-center text-gray-500">No read notifications yet.</p>
                    </template>
                    <template x-for="notification in readNotifications" :key="notification.id">
                        <div class="border rounded-lg p-3 shadow-md bg-gray-50 cursor-pointer">
                            <div class="flex justify-between items-center mb-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-blue-500 text-lg" x-text="icons[notification.type] ?? '🔔'"></span>
                                    <span class="text-sm font-semibold text-blue-600" x-text="notification.type"></span>
                                </div>
                                <span class="text-xs text-gray-500" x-text="notification.time"></span>
                            </div>
                            <div class="ml-6">
                                <h3 class="text-sm font-bold text-gray-800" x-text="notification.title"></h3>
                                <p class="text-sm text-gray-700" x-text="notification.message"></p>
                            </div>
                        </div>
                    </template>
                </div>

                <template x-if="unreadNotifications.length === 0 && readNotifications.length === 0">
                    <p class="text-center text-gray-500 mt-6">No notifications</p>
                </template>
            </div>
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

        function notificationPanel() {
            return {
                notifications: @json($notificationsArray),
                icons: @json($icons),
                showRead: false,
                unreadNotifications: [],
                readNotifications: [],

                init() {
                    this.unreadNotifications = this.notifications.filter(n => !n.is_read);
                    this.readNotifications = this.notifications.filter(n => n.is_read);
                    this.readNotifications.sort((a, b) => new Date(b.time) - new Date(a.time));
                    this.startPolling();
                },

                markAsRead(notification) {
                    fetch(`/notifications/${notification.id}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                    }).then(response => {
                        if (response.ok) {
                            this.unreadNotifications = this.unreadNotifications.filter(n => n.id !== notification.id);
                            notification.is_read = true;
                            this.readNotifications.push(notification);
                            this.readNotifications.sort((a, b) => new Date(b.time) - new Date(a.time));
                            this.updateBadge();
                        } else {
                            console.error('Failed to mark as read');
                        }
                    }).catch(err => console.error(err));
                },

                handleSwipe(notification) {
                    if (this.offset > 100) {
                        this.markAsRead(notification);
                    }
                    this.offset = 0;
                },

                updateBadge() {
                    const badge = document.getElementById('unread-badge');
                    const newCount = this.unreadNotifications.length;
                    if (badge) {
                        if (newCount > 0) {
                            badge.textContent = newCount;
                        } else {
                            badge.remove();
                        }
                    } else if (newCount > 0) {
                        const bellButton = document.querySelector('button.relative.cursor-pointer');
                        const newBadge = document.createElement('span');
                        newBadge.id = 'unread-badge';
                        newBadge.className = 'absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-full';
                        newBadge.textContent = newCount;
                        bellButton.appendChild(newBadge);
                    }
                },

                startPolling() {
                    setInterval(async () => {
                        try {
                            const response = await fetch('/notifications', {
                                method: 'GET',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                            });
                            const newNotifications = await response.json();
                            newNotifications.forEach(newNotif => {
                                if (!this.notifications.some(n => n.id === newNotif.id)) {
                                    this.notifications.push(newNotif);
                                    if (!newNotif.is_read) {
                                        this.unreadNotifications.push(newNotif);
                                        this.unreadNotifications.sort((a, b) => new Date(b.time) - new Date(a.time));
                                        this.updateBadge();
                                    } else {
                                        this.readNotifications.push(newNotif);
                                        this.readNotifications.sort((a, b) => new Date(b.time) - new Date(a.time));
                                    }
                                }
                            });
                        } catch (err) {
                            console.error('Error fetching notifications:', err);
                        }
                    }, 2000); 
                }
            }
        }
    </script>
@endpush