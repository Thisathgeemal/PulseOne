<!-- Sidebar -->
<div class="hidden md:flex bg-white min-h-screen w-64 border-r shadow-sm flex flex-col py-6" x-data="{
    openUsers: {{ request()->routeIs('trainer.qr') || request()->routeIs('trainer.attendance') ? 'true' : 'false' }},
    openBooking: {{ request()->routeIs('trainer.bookings.*') ? 'true' : 'false' }},
    openWorkoutPlan: {{ request()->routeIs('trainer.workoutplan') || request()->routeIs('trainer.request') ? 'true' : 'false' }},
}">

    <!-- Logo -->
    <div class="px-6 mb-5">
        <a href="{{ route('Trainer.dashboard') }}">
            <img src="{{ asset('images/logo - side.png') }}" alt="PulseOne Logo" class="h-12 -mt-3">
        </a>
    </div>

    <!-- Navigation -->
    <ul class="flex-grow space-y-4 px-4 text-sm text-gray-800 font-medium">

        <!-- Dashboard -->
        <li>
            <a href="{{ route('Trainer.dashboard') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg 
                      {{ request()->routeIs('Trainer.dashboard') ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                <i class="fas fa-house"></i> Dashboard
            </a>
        </li>

        <!-- QR Dropdown -->
        <li @click.away="openUsers = false">
            <button @click="openUsers = !openUsers"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg focus:outline-none 
                        {{ request()->routeIs('trainer.qr') || request()->routeIs('trainer.attendance')
                            ? 'bg-red-500 text-white font-semibold'
                            : 'hover:bg-gray-100' }}">
                <i class="fas fa-qrcode"></i> QR
                <i :class="openUsers ? 'fa fa-chevron-circle-up' : 'fa fa-chevron-circle-down'"
                    class="ml-auto transition-all duration-300"></i>
            </button>

            <ul x-show="openUsers" x-transition x-cloak class="mt-2 space-y-1 pl-6">
                @foreach ([
        'trainer.qr' => ['icon' => 'fas fa-qrcode', 'label' => 'QR Scanner'],
        'trainer.attendance' => ['icon' => 'fas fa-calendar-check', 'label' => 'Attendance'],
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

        <!-- Workout Plan Dropdown -->
        <li @click.away="openWorkoutPlan = false">
            <button @click="openWorkoutPlan = !openWorkoutPlan"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg focus:outline-none
                {{ request()->routeIs('trainer.workoutplan') || request()->routeIs('trainer.request') ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                <i class="fas fa-dumbbell"></i> Workout Plan
                <i :class="openWorkoutPlan ? 'fa fa-chevron-circle-up' : 'fa fa-chevron-circle-down'"
                    class="ml-auto transition-all duration-300"></i>
            </button>

            <ul x-show="openWorkoutPlan" x-transition x-cloak class="mt-2 space-y-1 pl-6">
                <li>
                    <a href="{{ route('trainer.request') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg
                        {{ request()->routeIs('trainer.request') ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                        <i class="fas fa-paper-plane"></i> Requests
                    </a>
                </li>
                <li>
                    <a href="{{ route('trainer.workoutplan') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg
                        {{ request()->routeIs('trainer.workoutplan') ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                        <i class="fas fa-dumbbell"></i> My Plans
                    </a>
                </li>
            </ul>
        </li>

        <!-- Booking Dropdown -->
        <li @click.away="openBooking = false">
            <button @click="openBooking = !openBooking"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg focus:outline-none
                {{ request()->routeIs('trainer.bookings.*') ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                <i class="fas fa-calendar-check"></i> Booking
                <i :class="openBooking ? 'fa fa-chevron-circle-up' : 'fa fa-chevron-circle-down'"
                    class="ml-auto transition-all duration-300"></i>
            </button>

            <ul x-show="openBooking" x-transition x-cloak class="mt-2 space-y-1 pl-6">
                <li>
                    <a href="{{ route('trainer.bookings.requests') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg
                        {{ request()->routeIs('trainer.bookings.requests') ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                        <i class="fas fa-paper-plane"></i> Requests
                    </a>
                </li>
                <li>
                    <a href="{{ route('trainer.bookings.sessions') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg
                                {{ request()->routeIs('trainer.bookings.sessions') ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                        <i class="fas fa-calendar-alt"></i> My Sessions
                    </a>
                </li>
            </ul>
        </li>

        <!-- Other Links -->
        @foreach ([
        'trainer.exercises' => ['icon' => 'fas fa-running', 'label' => 'Exercises'],
        'trainer.member.health-assessments' => ['icon' => 'fas fa-notes-medical', 'label' => 'Health Assessments'],
        'trainer.message' => ['icon' => 'fas fa-comment-alt', 'label' => 'Message'],
        'trainer.feedback' => ['icon' => 'fas fa-comment-dots', 'label' => 'Feedback'],
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
                    <i class="fas fa-power-off"></i> Log out
                </button>
            </form>
        </li>
    </ul>
</div>
