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

            <!-- Appearance Settings -->
            <div class="py-4 border-t pt-4">
                <h3 class="text-lg font-semibold mb-2">Appearance Settings</h3>
                
                <!-- Light/Dark Mode -->
                <div class="mb-4">
                    <div class="text-sm font-medium mb-2">Theme Mode</div>
                    <div class="flex items-center gap-2">
                        <button onclick="document.documentElement.classList.remove('theme-dark'); localStorage.setItem('themeMode', 'light')" class="px-3 py-2 bg-white border rounded hover:bg-gray-50">
                            <i class="fas fa-sun mr-1"></i> Light
                        </button>
                        <button onclick="document.documentElement.classList.add('theme-dark'); localStorage.setItem('themeMode', 'dark')" class="px-3 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">
                            <i class="fas fa-moon mr-1"></i> Dark
                        </button>
                        <button onclick="toggleAutoMode()" class="px-3 py-2 bg-gray-100 border rounded hover:bg-gray-200">
                            <i class="fas fa-adjust mr-1"></i> Auto
                        </button>
                    </div>
                </div>

                <!-- Accent Colors -->
                <div class="mb-4">
                    <div class="text-sm font-medium mb-2">Accent Colors</div>
                    <div class="flex gap-2 flex-wrap">
                        <button onclick="changeAccentColor('#ef4444')" class="w-8 h-8 rounded border-2 border-transparent hover:border-gray-300" style="background:#ef4444" title="Red"></button>
                        <button onclick="changeAccentColor('#3b82f6')" class="w-8 h-8 rounded border-2 border-transparent hover:border-gray-300" style="background:#3b82f6" title="Blue"></button>
                        <button onclick="changeAccentColor('#10b981')" class="w-8 h-8 rounded border-2 border-transparent hover:border-gray-300" style="background:#10b981" title="Green"></button>
                        <button onclick="changeAccentColor('#f59e0b')" class="w-8 h-8 rounded border-2 border-transparent hover:border-gray-300" style="background:#f59e0b" title="Orange"></button>
                        <button onclick="changeAccentColor('#8b5cf6')" class="w-8 h-8 rounded border-2 border-transparent hover:border-gray-300" style="background:#8b5cf6" title="Purple"></button>
                        <button onclick="changeAccentColor('#ec4899')" class="w-8 h-8 rounded border-2 border-transparent hover:border-gray-300" style="background:#ec4899" title="Pink"></button>
                    </div>
                </div>

                <!-- Sidebar Controls -->
                <div class="mb-4">
                    <div class="text-sm font-medium mb-2">Sidebar Layout</div>
                    <div class="space-y-2">
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="sidebarPos" value="left" onchange="setSidebarPosition('left')" checked class="text-red-500">
                                <span class="text-sm">Left Side</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="sidebarPos" value="right" onchange="setSidebarPosition('right')" class="text-red-500">
                                <span class="text-sm">Right Side</span>
                            </label>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" onchange="toggleSidebarCompact(this)" class="rounded text-red-500">
                            <span class="text-sm">Compact Sidebar (Icons Only)</span>
                        </label>
                    </div>
                </div>

                <!-- Save Settings Button -->
                <div class="mt-4">
                    <button onclick="saveThemeSettings()" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition-colors">
                        <i class="fas fa-save mr-2"></i>Save Theme Settings
                    </button>
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

    <!-- Profile Panel (UNCHANGED FROM ORIGINAL) -->
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

    <!-- Theme Control Scripts -->
    <script>
        // Theme control functions
        function changeAccentColor(color) {
            document.documentElement.style.setProperty('--accent-color', color);
            localStorage.setItem('accentColor', color);
            
            // Update all accent color elements
            const elements = document.querySelectorAll('.bg-red-500, .text-red-500, .border-red-500');
            elements.forEach(el => {
                if (el.classList.contains('bg-red-500')) {
                    el.style.backgroundColor = color;
                }
                if (el.classList.contains('text-red-500')) {
                    el.style.color = color;
                }
                if (el.classList.contains('border-red-500')) {
                    el.style.borderColor = color;
                }
            });
        }

        function toggleAutoMode() {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (prefersDark) {
                document.documentElement.classList.add('theme-dark');
            } else {
                document.documentElement.classList.remove('theme-dark');
            }
            localStorage.setItem('themeMode', 'auto');
        }

        function setSidebarPosition(position) {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            
            if (position === 'right') {
                document.body.style.flexDirection = 'row-reverse';
            } else {
                document.body.style.flexDirection = 'row';
            }
            localStorage.setItem('sidebarPosition', position);
        }

        function toggleSidebarCompact(checkbox) {
            const sidebar = document.querySelector('.sidebar, .w-64');
            const sidebarLinks = document.querySelectorAll('.sidebar a, .w-64 a');
            
            if (checkbox.checked) {
                if (sidebar) {
                    sidebar.classList.remove('w-64');
                    sidebar.classList.add('w-16');
                }
                sidebarLinks.forEach(link => {
                    const text = link.querySelector('span:not(.fas):not(.fa)');
                    if (text) text.style.display = 'none';
                });
            } else {
                if (sidebar) {
                    sidebar.classList.remove('w-16');
                    sidebar.classList.add('w-64');
                }
                sidebarLinks.forEach(link => {
                    const text = link.querySelector('span:not(.fas):not(.fa)');
                    if (text) text.style.display = 'inline';
                });
            }
            localStorage.setItem('sidebarCompact', checkbox.checked);
        }

        function saveThemeSettings() {
            // Show success message
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            notification.textContent = 'Theme settings saved successfully!';
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Load saved settings on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Load theme mode
            const savedTheme = localStorage.getItem('themeMode');
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('theme-dark');
            } else if (savedTheme === 'auto') {
                toggleAutoMode();
            }
            
            // Load accent color
            const savedColor = localStorage.getItem('accentColor');
            if (savedColor) {
                changeAccentColor(savedColor);
            }
            
            // Load sidebar settings
            const savedPosition = localStorage.getItem('sidebarPosition');
            if (savedPosition === 'right') {
                document.querySelector('input[value="right"]').checked = true;
                setSidebarPosition('right');
            }
            
            const savedCompact = localStorage.getItem('sidebarCompact') === 'true';
            if (savedCompact) {
                document.querySelector('input[type="checkbox"]').checked = true;
                toggleSidebarCompact(document.querySelector('input[type="checkbox"]'));
            }
        });

        // Notification panel function
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
