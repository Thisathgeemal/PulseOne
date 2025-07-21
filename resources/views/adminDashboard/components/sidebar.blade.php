<div class="bg-white min-h-screen w-64 border-r shadow-sm flex flex-col py-6">
    <div class="flex-grow">

        <!-- Logo -->
        <div class="px-6 mb-5">
            <a href="{{ route('Admin.dashboard') }}">
                <img src="{{ asset('images/logo - side.png') }}" alt="PulseOne Logo" class="h-12 -mt-3">
            </a>
        </div>

        <!-- Navigation -->
        <ul class="space-y-4 px-4 text-sm text-gray-800 font-medium">
            <li>
                <a href="{{ route('Admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('Admin.dashboard') ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fa fa-th-large" aria-hidden="true"></i>        
                    Dashboard
                </a>
            </li>

            <li>
                <a href="{{ route('admin.admin') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.admin') ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fas fa-user-shield text-md"></i>
                    Admin
                </a>
            </li>

            <li>
                <a href="{{ route('admin.trainer') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.trainer') ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fa-solid fa-user text-md"></i>
                    Trainer
                </a>
            </li>

            <li>
                <a href="{{ route('admin.dietitian') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.dietitian') ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fa-solid fa-user-tie text-md"></i>
                    Dietitian
                </a>
            </li>

            <li>
                <a href="{{ route('admin.member') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.member') ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fa-solid fa-users text-md"></i>
                    Member
                </a>
            </li>

            <li>
                <a href="{{ route('admin.attendance') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.attendance') ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fas fa-qrcode text-md"></i>
                    Attendance
                </a>
            </li>

            <li>
                <a href="{{ route('admin.message') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.message') ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fas fa-comment-alt text-md"></i>
                    Message
                </a>
            </li>

            <li>
                <a href="{{ route('admin.payment') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.payment') ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fas fa-credit-card text-md"></i>
                    Payment
                </a>
            </li>

            <li>
                <a href="{{ route('admin.payment') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.payment') ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                    <i class='fa fa-drivers-license'></i>
                    Membership
                </a>
            </li>

            <li>
                <a href="{{ route('admin.feedback') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.feedback') ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fas fa-comment-dots text-md"></i>
                    Feedback
                </a>
            </li>

            <li>
                <a href="{{ route('admin.feedback') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.feedback') ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                    <i class="fa fa-line-chart" aria-hidden="true"></i>  
                    Report
                </a>
            </li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 text-red-600 font-semibold">
                        <i class="fa fa-power-off text-md"></i>
                        Log out
                    </button>
                </form>
            </li>
        </ul>

    </div>

    <!-- Logout -->
    <div class="px-6 mt-4">

    </div>
</div>