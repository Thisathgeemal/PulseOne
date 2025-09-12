<!-- Sidebar -->
<div class="bg-white min-h-screen w-64 border-r shadow-sm flex flex-col py-6" x-data="{ openUsers: false }">

    <!-- Logo -->
    <div class="px-6 mb-5">
        <a href="{{ route('Dietitian.dashboard') }}">
            <img src="{{ asset('images/logo - side.png') }}" alt="PulseOne Logo" class="h-12 -mt-3">
        </a>
    </div>

    <!-- Navigation -->
    <ul class="flex-grow space-y-4 px-4 text-sm text-gray-800 font-medium">

        <!-- Dashboard -->
        <li>
            <a href="{{ route('Dietitian.dashboard') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg 
                      {{ request()->routeIs('Dietitian.dashboard') ? 'bg-red-500 text-white font-semibold' : 'hover:bg-gray-100' }}">
                <i class="fas fa-house"></i> Dashboard
            </a>
        </li>

        <!-- Other Links -->
        @foreach ([
        'dietitian.request' => ['icon' => 'fas fa-paper-plane', 'label' => 'Request'],
        'dietitian.dietplan' => ['icon' => 'fas fa-apple-alt', 'label' => 'Diet Plan'],
        'dietitian.meals' => ['icon' => 'fas fa-utensils', 'label' => 'Meals'],
        'dietitian.member.health-assessments' => ['icon' => 'fas fa-notes-medical', 'label' => 'Health Assessments'],
        'dietitian.message' => ['icon' => 'fas fa-comment-alt', 'label' => 'Message'],
        'dietitian.feedback' => ['icon' => 'fas fa-comment-dots', 'label' => 'Feedback'],
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
