<!-- Sidebar -->
<div class="bg-white min-h-screen w-64 border-r shadow-sm flex flex-col py-6"
     x-data="{ 
        openUsers: @json(request()->routeIs('admin.admin') || request()->routeIs('admin.trainer') || request()->routeIs('admin.dietitian') || request()->routeIs('admin.member') || request()->routeIs('admin.role')) 
     }">
    
    <!-- Logo -->
    <div class="px-6 mb-5">
        <a href="{{ route('Admin.dashboard') }}">
            <img src="{{ asset('images/logo - side.png') }}" alt="PulseOne Logo" class="h-12 -mt-3">
        </a>
    </div>

    <!-- Navigation -->
    <ul class="flex-grow space-y-4 px-4 text-sm text-gray-800 font-medium">
        <!-- Dashboard -->
        <li>
            <a href="{{ route('Admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg 
                      {{ request()->routeIs('Admin.dashboard') ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                <i class="fa fa-th-large"></i> Dashboard
            </a>
        </li>

        <!-- Users Dropdown -->
        <li @click.away="openUsers = false">
            <button @click="openUsers = !openUsers"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg focus:outline-none 
                           {{ request()->routeIs('admin.admin') || request()->routeIs('admin.trainer') || request()->routeIs('admin.dietitian') || request()->routeIs('admin.member') || request()->routeIs('admin.role') 
                              ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                <i class="fa fa-users"></i> Users
                <i class="fa fa-chevron-circle-down ml-auto"></i>
            </button>

            <!-- Dropdown menu -->
            <ul x-show="openUsers" x-transition x-cloak class="mt-2 space-y-1 pl-6">
                @foreach ([
                    'admin.admin' => ['icon' => 'fa-solid fa-user-tie', 'label' => 'Admin'],
                    'admin.dietitian' => ['icon' => 'fa-solid fa-user', 'label' => 'Dietitian'],
                    'admin.trainer' => ['icon' => 'fa-solid fa-user', 'label' => 'Trainer'],                    
                    'admin.member' => ['icon' => 'fa-solid fa-user', 'label' => 'Member'],
                    'admin.role' => ['icon' => 'fa fa-user-plus', 'label' => 'UserRole'],
                ] as $route => $data)
                    <li>
                        <a href="{{ route($route) }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg 
                                  {{ request()->routeIs($route) ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                            <i class="{{ $data['icon'] }}"></i> {{ $data['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>

        <!-- Other Links -->
        @foreach ([
            'admin.attendance' => ['icon' => 'fas fa-qrcode', 'label' => 'Attendance'],
            'admin.membership' => ['icon' => 'fas fa-id-card', 'label' => 'Membership'],
            'admin.payment' => ['icon' => 'fas fa-credit-card', 'label' => 'Payment'],
            'admin.message' => ['icon' => 'fas fa-comment-alt', 'label' => 'Message'],
            'admin.feedback' => ['icon' => 'fas fa-comment-dots', 'label' => 'Feedback'],
            'admin.report' => ['icon' => 'fa fa-line-chart', 'label' => 'Report'],
        ] as $route => $data)
            <li>
                <a href="{{ route($route) }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg 
                          {{ request()->routeIs($route) ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="{{ $data['icon'] }}"></i> {{ $data['label'] }}
                </a>
            </li>
        @endforeach

        <!-- Logout -->
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full text-left flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 text-red-600 font-semibold">
                    <i class="fa fa-power-off"></i> Log out
                </button>
            </form>
        </li>
    </ul>
</div>
