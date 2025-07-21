<div class="bg-white min-h-screen w-64 border-r shadow-sm flex flex-col py-6">
    <div class="flex-grow">

        <!-- Logo -->
        <div class="px-6 mb-5">
            <a href="{{ route('Member.dashboard') }}">
                <img src="{{ asset('images/logo - side.png') }}" alt="PulseOne Logo" class="h-12 -mt-3">
            </a>
        </div>

        <!-- Navigation -->
        <ul class="space-y-4 px-4 text-sm text-gray-800 font-medium">
            <li>
                <a href="{{ route('Member.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('Member.dashboard') ? 'bg-red-500 text-black font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fas fa-th-large text-md"></i>
                    Dashboard
                </a>
            </li>

            <li>
                <a href="{{ route('member.qrscanner') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('member.qrscanner') ? 'bg-red-500 text-black font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fas fa-qrcode text-md"></i>
                    QR Scanner
                </a>
            </li>

            <li>
                <a href="{{ route('member.qrscanner') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('member.qrscanner') ? 'bg-red-500 text-black font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fas fa-qrcode text-md"></i>
                    Attendance
                </a>
            </li>

            <li>
                <a href="{{ route('Member.workoutplan') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('Member.workoutplan') ? 'bg-red-500 text-black font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fas fa-dumbbell text-md"></i>
                    Workout Plan
                </a>
            </li>
            <li>
                <a href="{{ route('Member.dietplan') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('Member.dietplan') ? 'bg-red-500 text-black font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fas fa-utensils text-md"></i>
                    Diet Plan
                </a>
            </li>
            <li>
                <a href="{{ route('Member.bookings') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('Member.bookings') ? 'bg-red-500 text-black font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fas fa-calendar-alt text-md"></i>
                    Bookings
                </a>
            </li>
            <li>
                <a href="{{ route('Member.message') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('Member.message') ? 'bg-red-500 text-black font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fas fa-comment-alt text-md"></i>
                    Message
                </a>
            </li>
            <li>
                <a href="{{ route('Member.leaderboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('Member.leaderboard') ? 'bg-red-500 text-black font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fas fa-trophy text-md"></i>
                    Leaderboard
                </a>
            </li>
            <li>
                <a href="{{ route('Member.payment') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('Member.payment') ? 'bg-red-500 text-black font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fas fa-credit-card text-md"></i>
                    Payment
                </a>
            </li>
        </ul>
    </div>

    <!-- Logout -->
    <div class="px-6 mt-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-red-600 font-semibold hover:underline w-full text-center text-lg">
                Log out
            </button>
        </form>
    </div>
</div>