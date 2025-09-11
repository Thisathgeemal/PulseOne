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
    @scroll.window="handleScroll()"
    class="relative"
>

    <!-- Topbar -->
    <div class="flex items-center justify-between bg-[#1E1E1E] text-white px-6 py-3 shadow-sm">
        <!-- Left: Welcome Message -->
        <div class="flex items-center gap-4 text-lg font-semibold">
            <i class="fas fa-user-circle text-2xl"></i>
            <span>Welcome, {{ auth()->user()->first_name }}!</span>
        </div>

        <!-- Right: Icons -->
        <div class="flex items-center gap-4">
            <!-- Settings Gear -->
            <button @click="showSettings = !showSettings" class="relative text-xl hover:text-gray-300 cursor-pointer">
                <i class="fas fa-cog"></i>
            </button>

            <!-- Notification Bell -->
            <button @click="showNotifications = !showNotifications" class="relative text-xl hover:text-gray-300 cursor-pointer">
                <i class="fas fa-bell"></i>
                @if($notifications->where('is_read', false)->count() > 0)
                    <span id="unread-badge" class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">
                        {{ $notifications->where('is_read', false)->count() }}
                    </span>
                @endif
            </button>

            <!-- Profile Menu -->
            <button @click="showProfile = !showProfile" class="relative">
                @if(auth()->user()->image)
                    <img src="{{ asset('storage/profile_images/' . auth()->user()->image) }}" 
                         alt="Profile" class="w-8 h-8 rounded-full object-cover border-2 border-gray-300">
                @else
                    <div class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}
                    </div>
                @endif
            </button>
        </div>
    </div>

    <!-- Settings Panel -->
    <div 
        x-show="showSettings"
        x-transition
        @click.away="showSettings = false"
        class="fixed right-0 top-16 bottom-0 w-[500px] bg-white text-black shadow-lg z-100 overflow-y-auto rounded-md"
    >
        <div class="p-7">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-800">Settings</h2>
                <button @click="showSettings = false" class="text-gray-500 hover:text-red-600 text-xl">&times;</button>
            </div>

            <!-- Simple Theme Controls -->
            <div class="py-4 border-t pt-4">
                <h3 class="text-lg font-semibold mb-2">Quick Themes</h3>
                <div class="flex items-center gap-2 mb-3">
                    <button onclick="document.documentElement.classList.remove('theme-dark')" class="px-3 py-2 bg-white border rounded">Light</button>
                    <button onclick="document.documentElement.classList.add('theme-dark')" class="px-3 py-2 bg-gray-800 text-white rounded">Dark</button>
                </div>
                
                <div class="mb-3">
                    <div class="text-sm mb-2">Colors</div>
                    <div class="flex gap-2">
                        <button onclick="document.documentElement.style.setProperty('--accent-color', '#ef4444')" class="w-8 h-8 rounded" style="background:#ef4444"></button>
                        <button onclick="document.documentElement.style.setProperty('--accent-color', '#3b82f6')" class="w-8 h-8 rounded" style="background:#3b82f6"></button>
                        <button onclick="document.documentElement.style.setProperty('--accent-color', '#10b981')" class="w-8 h-8 rounded" style="background:#10b981"></button>
                        <button onclick="document.documentElement.style.setProperty('--accent-color', '#f59e0b')" class="w-8 h-8 rounded" style="background:#f59e0b"></button>
                    </div>
                </div>
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
    </div>

    <!-- Profile Panel -->
    <div 
        x-show="showProfile"
        x-transition
        @click.away="showProfile = false"
        class="fixed right-0 top-16 w-80 bg-white text-black shadow-lg z-100 rounded-md"
    >
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">Profile</h2>
                <button @click="showProfile = false" class="text-gray-500 hover:text-red-600 text-xl">&times;</button>
            </div>

            <div class="text-center mb-4">
                @if(auth()->user()->image)
                    <img src="{{ asset('storage/profile_images/' . auth()->user()->image) }}" 
                         alt="Profile" class="w-20 h-20 rounded-full mx-auto object-cover border-4 border-gray-300 mb-3">
                @else
                    <div class="w-20 h-20 bg-gray-500 rounded-full flex items-center justify-center text-white font-bold text-2xl mx-auto mb-3">
                        {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}
                    </div>
                @endif
                <h3 class="text-lg font-semibold">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h3>
                <p class="text-gray-600 text-sm">{{ auth()->user()->email }}</p>
            </div>

            <div class="space-y-2">
                <a href="{{ route('member.profile') }}" class="block w-full text-left px-4 py-2 hover:bg-gray-100 rounded">
                    <i class="fas fa-user mr-2"></i> My Profile
                </a>
                
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100 rounded text-red-600">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </div>
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
                    'Membership'   => '🏷️',
                    'Chat'         => '💬',
                    'Payment'      => '💳',
                    'Attendance'   => '🗓️',
                    'Feedback'     => '⭐',
                    'Settings'     => '⚙️',
                    'Profile'      => '👤',
                    'Workout Plan' => '🏋️‍♂️',
                    'Diet Plan'    => '🥗',
                    'Request'      => '📩',
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
                            @click="markAsRead(notification)"
                            class="border rounded-lg p-3 shadow-md hover:bg-gray-50 cursor-pointer"
                        >
                            <div class="flex items-start gap-3">
                                <span class="text-2xl" x-text="getIcon(notification.type)"></span>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800" x-text="notification.title"></h4>
                                    <p class="text-gray-600 text-sm mt-1" x-text="notification.message"></p>
                                    <p class="text-gray-400 text-xs mt-2" x-text="notification.time"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Show Read Toggle -->
                <div class="mt-6 text-center" x-show="readNotifications.length > 0">
                    <button @click="showRead = !showRead" 
                            class="text-blue-600 hover:text-blue-800 text-sm">
                        <span x-text="showRead ? 'Hide Read' : 'Show Read'"></span>
                        (<span x-text="readNotifications.length"></span>)
                    </button>
                </div>

                <!-- Read Notifications -->
                <div x-show="showRead" class="mt-4 space-y-4">
                    <template x-for="notification in readNotifications" :key="notification.id">
                        <div class="border rounded-lg p-3 shadow-md opacity-75">
                            <div class="flex items-start gap-3">
                                <span class="text-2xl" x-text="getIcon(notification.type)"></span>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800" x-text="notification.title"></h4>
                                    <p class="text-gray-600 text-sm mt-1" x-text="notification.message"></p>
                                    <p class="text-gray-400 text-xs mt-2" x-text="notification.time"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <script>
        function notificationPanel() {
            return {
                notifications: @json($notificationsArray),
                unreadNotifications: [],
                readNotifications: [],
                
                init() {
                    this.separateNotifications();
                    this.updateBadge();
                    this.startPolling();
                },

                separateNotifications() {
                    this.unreadNotifications = this.notifications.filter(n => !n.is_read);
                    this.readNotifications = this.notifications.filter(n => n.is_read);
                    this.unreadNotifications.sort((a, b) => new Date(b.time) - new Date(a.time));
                    this.readNotifications.sort((a, b) => new Date(b.time) - new Date(a.time));
                },

                getIcon(type) {
                    const icons = @json($icons);
                    return icons[type] || '📢';
                },

                async markAsRead(notification) {
                    try {
                        const response = await fetch(`/notifications/${notification.id}/read`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                        });

                        if (response.ok) {
                            this.unreadNotifications = this.unreadNotifications.filter(n => n.id !== notification.id);
                            notification.is_read = true;
                            this.readNotifications.push(notification);
                            this.readNotifications.sort((a, b) => new Date(b.time) - new Date(a.time));
                            this.updateBadge();
                        }
                    } catch (err) {
                        console.error(err);
                    }
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
                    }
                },

                startPolling() {
                    setInterval(async () => {
                        try {
                            const response = await fetch('/notifications');
                            const newNotifications = await response.json();
                            // Update logic here
                        } catch (err) {
                            console.error('Error fetching notifications:', err);
                        }
                    }, 30000); 
                }
            }
        }
    </script>
</div>
