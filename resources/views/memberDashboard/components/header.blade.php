<style>
    :root {
        --accent-color: #ef4444;
    }

    .accent-bg {
        background-color: var(--accent-color) !important;
        color: white !important;
    }

    .accent-text {
        color: var(--accent-color) !important;
    }

    .accent-border {
        border-color: var(--accent-color) !important;
    }

    .accent-bg-hover:hover {
        background-color: var(--accent-color) !important;
        color: white !important;
    }

    /* Focus styles for accent color support */
    input:focus,
    select:focus,
    textarea:focus {
        --tw-ring-color: var(--accent-color) !important;
        border-color: var(--accent-color) !important;
    }

    /* Radio and checkbox accent color support */
    .accent-radio:checked {
        background-color: var(--accent-color) !important;
        border-color: var(--accent-color) !important;
    }

    .accent-checkbox:checked {
        background-color: var(--accent-color) !important;
        border-color: var(--accent-color) !important;
    }

    /* Text accent color support */
    .accent-text-color {
        color: var(--accent-color) !important;
    }

    /* Override existing red focus styles */
    .focus\:ring-red-500:focus {
        --tw-ring-color: var(--accent-color) !important;
    }

    .focus\:border-red-500:focus {
        border-color: var(--accent-color) !important;
    }

    .text-red-500.accent-override {
        color: var(--accent-color) !important;
    }

    /* Hover state for accent text */
    .accent-text-hover:hover {
        color: var(--accent-color) !important;
        filter: brightness(0.9);
    }

    /* Protect profile images from accent color changes */
    .lb-member-avatar,
    .lb-member-avatar img,
    .lb-member-avatar div,
    .lb-podium-avatar,
    .lb-podium-avatar img,
    .lb-podium-avatar div,
    .lb-ring-1,
    .lb-ring-2,
    .lb-champion-avatar {
        background-color: inherit !important;
    }

    .lb-member-avatar.bg-gradient-to-br,
    .lb-podium-avatar div.bg-gradient-to-br {
        background: linear-gradient(to bottom right, #f3f4f6, #e5e7eb) !important;
    }

    /* Force red text for profile initials */
    .lb-member-avatar div.text-red-600,
    .lb-podium-avatar div.text-red-600 {
        color: #dc2626 !important;
    }

    /* Button styles */
    .btn-primary {
        background-color: var(--accent-color) !important;
        color: white !important;
    }

    .btn-primary:hover {
        background-color: var(--accent-color) !important;
        color: white !important;
        filter: brightness(0.9) !important;
    }

    /* Ensure hardcoded colored buttons maintain their text */
    .bg-red-500,
    .bg-blue-500,
    .bg-green-500,
    .bg-yellow-500,
    .bg-purple-500,
    .bg-orange-500 {
        color: white !important;
    }

    /* Override hardcoded hover states for accent color buttons */
    .bg-red-500:hover,
    .bg-red-600:hover {
        background-color: var(--accent-color) !important;
        filter: brightness(0.9) !important;
        color: white !important;
    }

    /* Keep Start buttons (yellow) and Cancel buttons (red) with fixed colors */
    .bg-green-600,
    .bg-green-700,
    a[href*="progress"].bg-green-600,
    a[href*="progress"].bg-green-700 {
        background-color: #eab308 !important;
        /* Fixed yellow */
        color: white !important;
    }

    .bg-green-600:hover,
    .bg-green-700:hover,
    a[href*="progress"].bg-green-600:hover,
    a[href*="progress"].bg-green-700:hover {
        background-color: #ca8a04 !important;
        /* Fixed darker yellow on hover */
        color: white !important;
    }

    a[href*="cancel"].bg-red-600,
    a[href*="cancel"].bg-red-700,
    .cancel-btn {
        background-color: #dc2626 !important;
        /* Keep red */
        color: white !important;
    }

    a[href*="cancel"].bg-red-600:hover,
    a[href*="cancel"].bg-red-700:hover,
    .cancel-btn:hover {
        background-color: #b91c1c !important;
        /* Keep darker red on hover */
        color: white !important;
    }

    /* Prevent other colored buttons from changing when they shouldn't */
    .bg-blue-500:hover,
    .bg-blue-600:hover,
    .bg-yellow-500:hover,
    .bg-yellow-600:hover,
    .bg-purple-500:hover,
    .bg-purple-600:hover,
    .bg-orange-500:hover,
    .bg-orange-600:hover {
        color: white !important;
    }
</style>

<div x-data="{
        showSettings: false,
        showProfile: false,
        showNotifications: false,
        showRead: false,
        showSidebar: false,
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
    }" x-init="window.addEventListener('scroll', () => handleScroll())" class="relative">

    <!-- Topbar -->
    <div class="flex items-center justify-between bg-[#1E1E1E] text-white px-6 py-3 shadow-sm">
        <!-- Left: Hamburger + Welcome Message -->
        <div class="flex items-center gap-4 text-lg font-semibold">
            <!-- Hamburger Icon (Mobile Only) -->
            <button @click="showSidebar = !showSidebar"
                class="md:hidden text-white text-2xl hover:text-yellow-400 focus:outline-none">
                <i class="fas fa-bars"></i>
            </button>

            <i class="fa-solid fa-user-tie text-white text-xl"></i>
            <span class="font-semibold">
                Welcome, {{ Auth::user()->first_name }}
            </span>
        </div>

        <!-- Right: Icons -->
        <div class="flex items-center gap-6">
            <!-- Settings -->
            <button
                @click=" showSettings = !showSettings; if (showSettings) { showProfile = false; showNotifications = false } "
                dusk="settings-button">
                <i class="fas fa-cog text-white text-xl cursor-pointer hover:text-yellow-400"></i>
            </button>

            <!-- Notifications -->
            @php
                $unreadCount = $notifications->where('is_read', false)->count();
            @endphp

            <button class="relative cursor-pointer" dusk="notifications-button"
                @click="
                    showNotifications = !showNotifications;
                    if(showNotifications) { showSettings = false; showProfile = false; }
                ">
                <i class="fas fa-bell text-white text-xl hover:text-yellow-400"></i>

                <!-- Only show badge if there are unread notifications -->
                @if ($unreadCount > 0)
                    <span id="unread-badge"
                        class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">
                        {{ $unreadCount }}
                    </span>
                @endif
            </button>

            <!-- Profile -->
            <button
                @click="showProfile = !showProfile; if (showProfile) { showSettings = false; showNotifications = false }"
                dusk="profile-avatar">
                @if (Auth::user()->profile_image)
                    <img src="{{ asset(Auth::user()->profile_image) }}?v={{ time() }}" alt="User Avatar"
                        class="w-10 h-10 rounded-full border-2 border-white object-cover hover:ring-2 ring-yellow-400 transition">
                @else
                    <div
                        class="w-10 h-10 rounded-full bg-orange-300 text-orange-900 flex items-center justify-center font-bold uppercase border-2 border-white hover:ring-2 ring-yellow-400 transition">
                        {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}
                    </div>
                @endif
            </button>
        </div>
    </div>

    <!-- Sidebar Panel (Mobile Only) -->
    <div x-show="showSidebar" x-transition @click.away="showSidebar = false"
        class="fixed inset-y-0 left-0 w-64 bg-white text-black shadow-lg z-50 overflow-y-auto transform transition-transform duration-300 -translate-x-full lg:hidden"
        :class="{'translate-x-0': showSidebar}">
        
        <div x-data="{
            openUsers: {{ request()->routeIs('member.qr') || request()->routeIs('member.attendance') ? 'true' : 'false' }},
            openWorkout: {{ request()->routeIs('member.workoutplan.*') ? 'true' : 'false' }},
            openDiet: {{ request()->routeIs('member.dietplan.*') ? 'true' : 'false' }},
            openBooking: {{ request()->routeIs('member.bookings.*') ? 'true' : 'false' }},
        }" class="flex flex-col min-h-full">

            <!-- Logo -->
            <div class="px-6 mb-5 mt-10 flex justify-between items-center">
                <a href="{{ route('Member.dashboard') }}">
                    <img src="{{ asset('images/logo - side.png') }}" alt="PulseOne Logo" class="h-12 -mt-3">
                </a>
                <button @click="showSidebar = false" class="text-gray-500 hover:text-red-600 text-2xl">&times;</button>
            </div>

            <!-- Navigation -->
            <ul class="flex-grow space-y-4 px-4 text-sm text-gray-800 font-medium">

                <!-- Dashboard -->
                <li>
                    <a href="{{ route('Member.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg 
                    {{ request()->routeIs('Member.dashboard') ? 'accent-bg text-white font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fas fa-house"></i> Dashboard
                    </a>
                </li>

                <!-- QR Dropdown -->
                <li @click.away="openUsers = false">
                    <button @click="openUsers = !openUsers"
                            class="w-full flex items-center gap-3 px-3 py-2 rounded-lg focus:outline-none
                            {{ request()->routeIs('member.qr') || request()->routeIs('member.attendance') ? 'accent-bg text-white font-semibold' : 'hover:bg-gray-100' }}">
                        <i class="fas fa-qrcode"></i> QR
                        <i :class="openUsers ? 'fa fa-chevron-circle-up' : 'fa fa-chevron-circle-down'" class="ml-auto transition-all duration-300"></i>
                    </button>

                    <ul x-show="openUsers" x-transition x-cloak class="mt-2 space-y-1 pl-6">
                        @foreach ([
                            'member.qr' => ['icon' => 'fas fa-qrcode', 'label' => 'QR Scanner'],
                            'member.attendance' => ['icon' => 'fas fa-calendar-check', 'label' => 'Attendance'],
                        ] as $route => $data)
                            <li>
                                <a href="{{ route($route) }}"
                                class="flex items-center gap-3 px-3 py-2 rounded-lg
                                {{ request()->routeIs($route) ? 'accent-bg text-white font-semibold' : 'hover:bg-gray-100' }}">
                                <i class="{{ $data['icon'] }}"></i> {{ $data['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>

                <!-- Workout, Diet, Booking Dropdowns -->
                @php
                    $dropdowns = [
                        'member.workoutplan' => [
                            'icon' => 'fas fa-dumbbell', 'label' => 'Workout Plan', 'openVar' => 'openWorkout',
                            'routes' => [
                                'member.workoutplan.request' => ['icon' => 'fas fa-paper-plane', 'label' => 'Request'],
                                'member.workoutplan.myplan' => ['icon' => 'fas fa-file-alt', 'label' => 'My Plan'],
                                'member.workoutplan.progress' => ['icon' => 'fas fa-chart-line', 'label' => 'Progress Tracking'],
                            ],
                        ],
                        'member.dietplan' => [
                            'icon' => 'fas fa-utensils', 'label' => 'Diet Plan', 'openVar' => 'openDiet',
                            'routes' => [
                                'member.dietplan.request' => ['icon' => 'fas fa-paper-plane', 'label' => 'Request'],
                                'member.dietplan.myplan' => ['icon' => 'fas fa-file-alt', 'label' => 'My Plan'],
                                'member.dietplan.progress' => ['icon' => 'fas fa-chart-line', 'label' => 'Progress Tracking', 'url' => $activeDietPlan ? route('member.dietplan.progress', $activeDietPlan->dietplan_id) : null],
                            ],
                        ],
                        'member.bookings' => [
                            'icon' => 'fas fa-calendar-check', 'label' => 'Booking', 'openVar' => 'openBooking',
                            'routes' => [
                                'member.bookings.index' => ['icon' => 'fas fa-calendar', 'label' => 'Sessions'],
                                'member.bookings.sessions' => ['icon' => 'fas fa-user-clock', 'label' => 'My Sessions'],
                            ],
                        ],
                    ];
                @endphp

                @foreach ($dropdowns as $key => $dropdown)
                    <li @click.away="{{ $dropdown['openVar'] }} = false">
                        <button @click="{{ $dropdown['openVar'] }} = !{{ $dropdown['openVar'] }}"
                                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg focus:outline-none
                                {{ collect(array_keys($dropdown['routes']))->contains(fn($route) => request()->routeIs($route)) ? 'accent-bg text-white font-semibold' : 'hover:bg-gray-100' }}">
                            <i class="{{ $dropdown['icon'] }}"></i> {{ $dropdown['label'] }}
                            <i :class="{{ $dropdown['openVar'] }} ? 'fa fa-chevron-circle-up' : 'fa fa-chevron-circle-down'" class="ml-auto transition-all duration-300"></i>
                        </button>
                        <ul x-show="{{ $dropdown['openVar'] }}" x-transition x-cloak class="mt-2 space-y-1 pl-6">
                            @foreach ($dropdown['routes'] as $route => $data)
                                <li>
                                    @if ($route === 'member.dietplan.progress' && is_null($data['url']))
                                        <button onclick="Swal.fire({icon: 'warning', title: 'No Active Diet Plan', text: 'Please start an active diet plan first!', confirmButtonColor: '#d32f2f'})"
                                                class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 w-full">
                                            <i class="{{ $data['icon'] }}"></i> {{ $data['label'] }}
                                        </button>
                                    @else
                                        <a href="{{ $route === 'member.dietplan.progress' ? $data['url'] : route($route) }}"
                                        class="flex items-center gap-3 px-3 py-2 rounded-lg
                                        {{ request()->routeIs($route) ? 'accent-bg text-white font-semibold' : 'hover:bg-gray-100' }}">
                                        <i class="{{ $data['icon'] }}"></i> {{ $data['label'] }}
                                        </a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach

                <!-- Health Assessment & Other Links (Membership, Payment, Message, etc.) -->
                @foreach ([
                    'member.health-assessment' => ['icon' => 'fas fa-heartbeat', 'label' => 'Health Assessment'],
                    'member.membership' => ['icon' => 'fas fa-id-card', 'label' => 'Membership'],
                    'member.payment' => ['icon' => 'fas fa-credit-card', 'label' => 'Payment'],
                    'member.message' => ['icon' => 'fas fa-comment-alt', 'label' => 'Message'],
                    'member.feedback' => ['icon' => 'fas fa-comment-dots', 'label' => 'Feedback'],
                    'member.leaderboard' => ['icon' => 'fas fa-trophy', 'label' => 'Leaderboard'],
                ] as $route => $data)
                    <li>
                        <a href="{{ route($route) }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg
                        {{ request()->routeIs($route) ? 'accent-bg text-white font-semibold' : 'hover:bg-gray-100' }}">
                        <i class="{{ $data['icon'] }}"></i> {{ $data['label'] }}
                        </a>
                    </li>
                @endforeach

                <!-- Logout -->
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-left flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 accent-text font-semibold">
                            <i class="fa fa-power-off"></i> Log out
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <!-- Slide-in Settings Panel -->
    <div x-show="showSettings" x-transition if (showSettings) showProfile=false; @click.away="showSettings = false"
        class="fixed right-0 top-16 bottom-0 w-[400px] bg-white text-black rounded-md shadow-lg z-100 p-7 overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Settings</h2>
            <button @click="showSettings = false" class="text-gray-500 hover:text-red-600 text-xl">&times;</button>
        </div>

        <!-- Appearance Settings -->
        <div class="py-4 border-t pt-4">
            <h3 class="text-lg font-semibold mb-4">Appearance Settings</h3>

            <!-- Modern Light/Dark Mode Toggle -->
            <div class="mb-6">
                <div class="text-sm font-medium mb-3">Theme Mode</div>
                <div class="flex items-center justify-between bg-gray-100 rounded-full p-1 w-48">
                    <button onclick="setThemeMode('light')" id="lightBtn"
                        class="flex-1 py-2 px-4 rounded-full text-sm font-medium transition-all duration-200 bg-white text-gray-900 shadow-sm">
                        <i class="fas fa-sun mr-2"></i>Light
                    </button>
                    <button onclick="setThemeMode('dark')" id="darkBtn"
                        class="flex-1 py-2 px-4 rounded-full text-sm font-medium transition-all duration-200 text-gray-600 hover:text-gray-900">
                        <i class="fas fa-moon mr-2"></i>Dark
                    </button>
                </div>
            </div>

            <!-- Modern Accent Colors -->
            <div class="mb-6">
                <div class="text-sm font-medium mb-3">Accent Colors</div>
                <div class="flex gap-3 flex-wrap">
                    <button onclick="changeAccentColor('#ef4444')"
                        class="accent-color-btn w-10 h-10 rounded-full border-3 border-transparent hover:border-gray-300 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
                        style="background:#ef4444" title="Red" data-color="#ef4444">
                        <i class="fas fa-check text-white text-sm opacity-0"></i>
                    </button>
                    <button onclick="changeAccentColor('#3b82f6')"
                        class="accent-color-btn w-10 h-10 rounded-full border-3 border-transparent hover:border-gray-300 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
                        style="background:#3b82f6" title="Blue" data-color="#3b82f6">
                        <i class="fas fa-check text-white text-sm opacity-0"></i>
                    </button>
                    <button onclick="changeAccentColor('#10b981')"
                        class="accent-color-btn w-10 h-10 rounded-full border-3 border-transparent hover:border-gray-300 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
                        style="background:#10b981" title="Green" data-color="#10b981">
                        <i class="fas fa-check text-white text-sm opacity-0"></i>
                    </button>
                    <button onclick="changeAccentColor('#f59e0b')"
                        class="accent-color-btn w-10 h-10 rounded-full border-3 border-transparent hover:border-gray-300 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
                        style="background:#f59e0b" title="Orange" data-color="#f59e0b">
                        <i class="fas fa-check text-white text-sm opacity-0"></i>
                    </button>
                    <button onclick="changeAccentColor('#8b5cf6')"
                        class="accent-color-btn w-10 h-10 rounded-full border-3 border-transparent hover:border-gray-300 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
                        style="background:#8b5cf6" title="Purple" data-color="#8b5cf6">
                        <i class="fas fa-check text-white text-sm opacity-0"></i>
                    </button>
                    <button onclick="changeAccentColor('#ec4899')"
                        class="accent-color-btn w-10 h-10 rounded-full border-3 border-transparent hover:border-gray-300 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
                        style="background:#ec4899" title="Pink" data-color="#ec4899">
                        <i class="fas fa-check text-white text-sm opacity-0"></i>
                    </button>
                </div>
            </div>

            <!-- Save Settings Button -->
            <div class="mt-4">
                <button onclick="saveThemeSettings()" class="w-full btn-primary px-4 py-2 rounded transition-colors">
                    <i class="fas fa-save mr-2"></i>Save Theme Settings
                </button>
            </div>
        </div>

        <!-- MFA Security -->
        <div class="py-4">
            <h2 class="text-xl font-semibold mb-4">MFA Security</h2>
            <form action="{{ route('settings.mfa-toggle') }}" method="POST" class="border rounded-lg p-4 shadow-md">
                @csrf
                <p class="mb-2">Two-Factor Authentication is currently
                    <strong>{{ auth()->user()->mfa_enabled ? 'Enabled' : 'Disabled' }}</strong>.
                </p>
                @if (auth()->user()->mfa_enabled)
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Disable Two-Factor Authentication
                    </button>
                @else
                    <button type="submit"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
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
                                <button dusk="logout-device" type="submit"
                                    class="ml-4 mt-2 px-4 py-2 bg-red-500 text-white font-bold rounded hover:bg-red-700">
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
                    <button dusk="logout-all-devices" type="submit"
                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Logout from all other devices
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Notification Panel -->
    <div x-show="showNotifications" x-transition @click.away="showNotifications = false"
        class="fixed right-0 top-16 bottom-0 w-[400px] bg-white text-black shadow-lg z-100 overflow-y-auto rounded-md">
        <div class="p-7">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-800">Notifications</h2>
                <button @click="showNotifications = false"
                    class="text-gray-500 hover:text-red-600 text-xl">&times;</button>
            </div>

            @php
                $icons = [
                    'Membership' => '🏷️',
                    'Chat' => '💬',
                    'Payment' => '💳',
                    'Attendance' => '🗓️',
                    'Feedback' => '⭐',
                    'Settings' => '⚙️',
                    'Profile' => '👤',
                    'Workout Plan' => '🏋️‍♂️',
                    'Diet Plan' => '🥗',
                    'Request' => '📩',
                ];

                $notificationsArray = $notifications
                    ->map(function ($n) {
                        return [
                            'id' => $n->id,
                            'type' => $n->type,
                            'title' => $n->title,
                            'message' => $n->message,
                            'time' => $n->created_at->diffForHumans(),
                            'is_read' => $n->is_read,
                        ];
                    })
                    ->values();
            @endphp

            <div x-data="notificationPanel()">
                <!-- Unread Notifications -->
                <div class="space-y-4">
                    <template x-for="notification in unreadNotifications" :key="notification.id">
                        <div x-data="{ offset: 0, start: 0 }" x-on:touchstart="start = $event.touches[0].clientX"
                            x-on:touchmove="offset = $event.touches[0].clientX - start"
                            x-on:touchend="handleSwipe(notification)" x-on:mousedown="start = $event.clientX"
                            x-on:mousemove="if($event.buttons == 1) offset = $event.clientX - start"
                            x-on:mouseup="handleSwipe(notification)" @click="markAsRead(notification)"
                            class="border rounded-lg p-3 shadow-md hover:bg-gray-50 cursor-pointer transform transition-transform duration-200"
                            :style="`translateX(${offset}px)`">
                            <div class="flex justify-between items-center mb-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-blue-500 text-lg"
                                        x-text="icons[notification.type] ?? '🔔'"></span>
                                    <span class="text-sm font-semibold text-blue-600"
                                        x-text="notification.type"></span>
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
                                    <span class="text-blue-500 text-lg"
                                        x-text="icons[notification.type] ?? '🔔'"></span>
                                    <span class="text-sm font-semibold text-blue-600"
                                        x-text="notification.type"></span>
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
                    @if (Auth::user()->profile_image)
                        <img src="{{ asset(Auth::user()->profile_image) }}?v={{ time() }}"
                            class="w-20 h-20 rounded-full object-cover border-2 border-gray-300" />
                    @else
                        <div
                            class="w-20 h-20 rounded-full bg-orange-200 text-orange-800 flex items-center justify-center text-2xl font-bold uppercase border-2 border-gray-300">
                            {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}
                        </div>
                    @endif
                    <div
                        class="absolute bottom-0 right-0 bg-white w-6 h-6 flex items-center justify-center rounded-full border">
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
            <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data"
                class="space-y-6">
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
                        <input type="date" name="dob"
                            value="{{ Auth::user()->dob ? Auth::user()->dob->format('Y-m-d') : '' }}"
                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>

                <!-- Password Update -->
                <div class="pt-2">
                    <div class="flex justify-between items-center mb-1">
                        <label for="current_password" class="text-sm font-medium text-gray-700">Change
                            Password</label>
                        <button type="button" id="verifyPasswordBtn"
                            class="text-sm text-green-600 hover:underline">Verify</button>
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
                    <button type="submit"
                        class="bg-red-500 text-white px-6 py-2 rounded hover:bg-red-600">Save</button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
    @if (session('success'))
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
                    @if (session('error'))
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
        document.addEventListener('alpine:init', () => {
            Alpine.data('headerState', () => ({
                showSidebar: false
            }))
        })

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
                    body: JSON.stringify({
                        password
                    })
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

        document.getElementById('profile_image')?.addEventListener('change', function(e) {
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
                            this.unreadNotifications = this.unreadNotifications.filter(n => n.id !== notification
                                .id);
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
                        newBadge.className =
                            'absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-full';
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
                                        this.unreadNotifications.sort((a, b) => new Date(b.time) -
                                            new Date(a.time));
                                        this.updateBadge();
                                    } else {
                                        this.readNotifications.push(newNotif);
                                        this.readNotifications.sort((a, b) => new Date(b.time) -
                                            new Date(a.time));
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

        // Theme control functions (Global definitions)
        window.setThemeMode = function(mode) {
            const lightBtn = document.getElementById('lightBtn');
            const darkBtn = document.getElementById('darkBtn');

            if (mode === 'light') {
                document.documentElement.classList.remove('theme-dark');

                // Update button styles
                lightBtn.className =
                    'flex-1 py-2 px-4 rounded-full text-sm font-medium transition-all duration-200 bg-white text-gray-900 shadow-sm';
                darkBtn.className =
                    'flex-1 py-2 px-4 rounded-full text-sm font-medium transition-all duration-200 text-gray-600 hover:text-gray-900';
            } else if (mode === 'dark') {
                document.documentElement.classList.add('theme-dark');

                // Update button styles
                darkBtn.className =
                    'flex-1 py-2 px-4 rounded-full text-sm font-medium transition-all duration-200 bg-gray-800 text-white shadow-sm';
                lightBtn.className =
                    'flex-1 py-2 px-4 rounded-full text-sm font-medium transition-all duration-200 text-gray-600 hover:text-gray-900';
            }

            // Store temporarily for saving later
            localStorage.setItem('themeMode', mode);
            window.tempThemeMode = mode;
        };

        window.changeAccentColor = function(color) {
            document.documentElement.style.setProperty('--accent-color', color);
            document.documentElement.style.setProperty('--lb-accent', color);

            // Update elements that should change with accent color
            const accentElements = document.querySelectorAll(
                '.accent-bg, .accent-text, .accent-border, .btn-primary, #unread-badge, .accent-text-color');
            accentElements.forEach(el => {
                // Skip profile-related elements
                if (el.closest('.lb-member-avatar') ||
                    el.closest('.lb-podium-avatar') ||
                    el.closest('.lb-ring-1') ||
                    el.closest('.lb-ring-2') ||
                    el.closest('.lb-champion-avatar')) {
                    return;
                }

                if (el.classList.contains('accent-bg') || el.classList.contains('btn-primary') || el.id ===
                    'unread-badge') {
                    el.style.backgroundColor = color;
                    el.style.color = 'white';
                }
                if (el.classList.contains('accent-text') || el.classList.contains('accent-text-color')) {
                    el.style.color = color;
                }
                if (el.classList.contains('accent-border')) {
                    el.style.borderColor = color;
                }
            });

            // Update red buttons to use accent color BUT exclude Start/Cancel buttons
            const redButtons = document.querySelectorAll('.bg-red-500, .bg-red-600');
            redButtons.forEach(el => {
                // Skip profile-related elements
                if (el.classList.contains('lb-member-avatar') ||
                    el.classList.contains('lb-podium-avatar') ||
                    el.querySelector('img') ||
                    el.closest('.lb-member-avatar') ||
                    el.closest('.lb-podium-avatar') ||
                    el.closest('.lb-ring-1') ||
                    el.closest('.lb-ring-2') ||
                    el.closest('.lb-champion-avatar')) {
                    return;
                }

                // Skip Cancel buttons and Start buttons
                if (el.classList.contains('cancel-btn') ||
                    (el.href && el.href.includes('cancel')) ||
                    (el.href && el.href.includes('progress'))) {
                    return;
                }

                el.style.backgroundColor = color;
                el.style.color = 'white';
                el.style.transition = 'all 0.3s ease';

                // Remove existing listeners to prevent duplicates
                el.removeEventListener('mouseenter', el._accentHoverIn);
                el.removeEventListener('mouseleave', el._accentHoverOut);

                // Create new listeners
                el._accentHoverIn = function() {
                    this.style.backgroundColor = color;
                    this.style.filter = 'brightness(0.9)';
                    this.style.color = 'white';
                };
                el._accentHoverOut = function() {
                    this.style.backgroundColor = color;
                    this.style.filter = 'brightness(1)';
                    this.style.color = 'white';
                };

                el.addEventListener('mouseenter', el._accentHoverIn);
                el.addEventListener('mouseleave', el._accentHoverOut);
            });

            // Update accent color picker selection
            document.querySelectorAll('.accent-color-btn').forEach(btn => {
                const icon = btn.querySelector('i');
                if (btn.dataset.color === color) {
                    icon.style.opacity = '1';
                    btn.style.borderColor = '#374151';
                    btn.style.borderWidth = '3px';
                } else {
                    icon.style.opacity = '0';
                    btn.style.borderColor = 'transparent';
                    btn.style.borderWidth = '3px';
                }
            });

            // Store temporarily for saving later
            window.tempAccentColor = color;
        };

        window.saveThemeSettings = function() {
            // Save the temporarily stored settings to localStorage
            if (window.tempThemeMode) {
                localStorage.setItem('themeMode', window.tempThemeMode);
            }
            if (window.tempAccentColor) {
                localStorage.setItem('accentColor', window.tempAccentColor);
            }

            // Show success message
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            notification.textContent = 'Theme settings saved successfully!';
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        };

        // Initialize saved settings and other functionality on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Load theme mode
            const savedTheme = localStorage.getItem('themeMode') || 'light';
            window.setThemeMode(savedTheme);
            window.tempThemeMode = savedTheme;

            // Load accent color
            const savedColor = localStorage.getItem('accentColor') || '#ef4444';
            window.changeAccentColor(savedColor);
            window.tempAccentColor = savedColor;
        });
    </script>
@endpush
