<!-- Sidebar -->
<div class="bg-white min-h-screen w-64 border-r shadow-sm flex flex-col py-6"
    x-data="{ 
        openUsers: {{ request()->routeIs('member.qr') || request()->routeIs('member.attendance') ? 'true' : 'false' }},
        openWorkout: {{ request()->routeIs('member.workoutplan.*') ? 'true' : 'false' }},
        openDiet: {{ request()->routeIs('member.dietplan.*') ? 'true' : 'false' }},
    }"
>

    <!-- Logo -->
    <div class="px-6 mb-5">
        <a href="{{ route('Member.dashboard') }}">
            <img src="{{ asset('images/logo - side.png') }}" alt="PulseOne Logo" class="h-12 -mt-3">
        </a>
    </div>

    <!-- Navigation -->
    <ul class="flex-grow space-y-4 px-4 text-sm text-gray-800 font-medium">
        <!-- Dashboard -->
        <li>
            <a href="{{ route('Member.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg 
                      {{ request()->routeIs('Member.dashboard') ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                <i class="fas fa-house"></i> Dashboard
            </a>
        </li>

        <!-- QR Dropdown -->
        <li @click.away="openUsers = false">
            <button @click="openUsers = !openUsers"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg focus:outline-none 
                        {{ request()->routeIs('member.qr') || request()->routeIs('member.attendance') 
                            ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                <i class="fas fa-qrcode"></i> QR
                
                <!-- Toggle icon -->
                <i :class="openUsers ? 'fa fa-chevron-circle-up' : 'fa fa-chevron-circle-down'" class="ml-auto transition-all duration-300"></i>
            </button>

            <!-- Dropdown menu -->
            <ul x-show="openUsers" x-transition x-cloak class="mt-2 space-y-1 pl-6">
                @foreach ([
                    'member.qr' => ['icon' => 'fas fa-qrcode', 'label' => 'QR Scanner'],
                    'member.attendance' => ['icon' => 'fas fa-calendar-check', 'label' => 'Attendance'],
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

        @php
            $dropdowns = [
                'member.workoutplan' => [
                    'icon' => 'fas fa-dumbbell',
                    'label' => 'Workout Plan',
                    'openVar' => 'openWorkout',
                    'routes' => [
                        'member.workoutplan.request' => 'Request',
                        'member.workoutplan.myplan' => 'My Plan',
                        'member.workoutplan.progress' => 'Progress Tracking',
                    ],
                ],
                'member.dietplan' => [
                    'icon' => 'fas fa-utensils',
                    'label' => 'Diet Plan',
                    'openVar' => 'openDiet',
                    'routes' => [
                        'member.dietplan.request' => 'Request',
                        'member.dietplan.myplan' => 'My Plan',
                        'member.dietplan.progress' => 'Progress Tracking',
                    ],
                ],
            ];
        @endphp

        <!-- Workout Plan & Diet Plan Dropdowns -->
        @foreach ($dropdowns as $key => $dropdown)
            <li @click.away="{{ $dropdown['openVar'] }} = false">
                <button @click="{{ $dropdown['openVar'] }} = !{{ $dropdown['openVar'] }}"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg focus:outline-none
                    {{ collect(array_keys($dropdown['routes']))->contains(fn($route) => request()->routeIs($route)) 
                        ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="{{ $dropdown['icon'] }}"></i> {{ $dropdown['label'] }}
                    <i :class="{{ $dropdown['openVar'] }} ? 'fa fa-chevron-circle-up' : 'fa fa-chevron-circle-down'" class="ml-auto transition-all duration-300"></i>
                </button>
                <ul x-show="{{ $dropdown['openVar'] }}" x-transition x-cloak class="mt-2 space-y-1 pl-6">
                    @foreach ($dropdown['routes'] as $route => $label)
                        <li>
                            <a href="{{ route($route) }}"
                               class="flex items-center gap-3 px-3 py-2 rounded-lg
                               {{ request()->routeIs($route) ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                                {{ $label }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endforeach

        <!-- Other Links -->
        @foreach ([
            'member.booking' => ['icon' => 'fas fa-calendar-check', 'label' => 'Booking'],
            'member.membership' => ['icon' => 'fas fa-id-card', 'label' => 'Membership'],
            'member.payment' => ['icon' => 'fas fa-credit-card', 'label' => 'Payment'],
            'member.feedback' => ['icon' => 'fas fa-comment-dots', 'label' => 'Feedback'],
            'member.message' => ['icon' => 'fas fa-comment-alt', 'label' => 'Message'],
            'member.leaderboard' => ['icon' => 'fas fa-trophy', 'label' => 'Leaderboard'],
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
