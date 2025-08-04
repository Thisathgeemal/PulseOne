@extends('adminDashboard.layout')

@section('content')

    {{-- Filters + Attendance Table --}}
    <div class="w-full max-w-xs md:max-w-7xl p-8 bg-white rounded-lg mt-4 mb-8 text-center shadow-md mx-auto animate-fade-in" x-data>

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <h2 class="text-xl sm:text-2xl font-bold">Attendance Summary</h2>

            <div class="w-full sm:w-auto flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                {{-- Filter/Search Form --}}
                <form method="GET" action="{{ route('admin.attendance') }}" class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 w-full">
                    {{-- By Role --}}
                    <select name="role"
                        class="w-full sm:w-40 md:w-40 h-9 px-4 border rounded-full text-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                        <option value="">All Roles</option>
                        <option value="Member" {{ request('role') == 'Member' ? 'selected' : '' }}>Members Only</option>
                        <option value="Trainer" {{ request('role') == 'Trainer' ? 'selected' : '' }}>Trainers Only</option>
                    </select>

                    {{-- By Date --}}
                    <input
                        type="date"
                        name="date"
                        value="{{ request('date')}}"
                        class="w-full sm:w-40 md:w-40 h-9 px-4 border rounded-full text-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                    />

                    {{-- Search Button --}}
                    <button type="submit"
                            class="bg-red-500 hover:bg-red-600 text-white flex items-center justify-center rounded-md w-8 h-8">
                        <i class="fas fa-search text-sm"></i>
                    </button>
                </form>

                {{-- Export Form --}}
                <form method="POST" action="{{ route('attendance.report') }}" target="_blank"
                    onsubmit="setCurrentDateTime()" class="relative group flex items-center">
                    @csrf
                    <input type="hidden" name="date" id="currentDatetime">
                    <input type="hidden" name="role" value="{{ request('role') }}">

                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white flex items-center justify-center rounded-md w-8 h-8">
                        <i class="fas fa-download text-sm"></i>
                    </button>

                    {{-- Tooltip --}}
                    <span class="absolute hidden group-hover:block bg-gray-700 text-white text-xs rounded py-1 px-2 -bottom-8 left-1/2 transform -translate-x-1/2">
                        Export
                    </span>
                </form>
            </div>

        </div>

        <!-- Attendance Table -->
        <div class="md:overflow-x-auto mt-6 overflow-x-scroll ">
            <table class="min-w-full bg-white shadow-md rounded-lg border text-base border-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-3 px-4 border-b border-gray-300">No</th>
                        <th class="py-3 px-4 border-b border-gray-300">User Name</th>
                        <th class="py-3 px-4 border-b border-gray-300">Role</th>
                        <th class="py-3 px-4 border-b border-gray-300">Date</th>
                        <th class="py-3 px-4 border-b border-gray-300">Check-In</th>
                        <th class="py-3 px-4 border-b border-gray-300">Check-Out</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $index => $attendance)
                        <tr class="hover:bg-gray-200 transition duration-200">
                            <td class="px-4 py-3 border-b border-gray-200">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 border-b border-gray-200">{{ $attendance->user->first_name ?? 'N/A' }} {{ $attendance->user->last_name ?? '' }}</td>
                            @php
                                $filteredRole = $attendance->user->roles->firstWhere('role_name', 'Trainer') 
                                                ?? $attendance->user->roles->firstWhere('role_name', 'Member');
                            @endphp
                            <td class="px-4 py-3 border-b border-gray-200">
                               {{ $filteredRole->role_name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 border-b border-gray-200">{{ \Carbon\Carbon::parse($attendance->check_in_time)->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 border-b border-gray-200">{{ \Carbon\Carbon::parse($attendance->check_in_time)->format('h:i A') }}</td>
                            <td class="px-4 py-3 border-b border-gray-200">
                                {{ $attendance->check_out_time 
                                    ? \Carbon\Carbon::parse($attendance->check_out_time)->format('h:i A') 
                                    : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-gray-500 font-medium">No attendance records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $attendances->links() }}
        </div>
    </div>

    {{-- Manual Check-In --}}
    <div class="w-full max-w-xs md:max-w-7xl p-8 bg-white rounded-lg text-center shadow-md mx-auto animate-fade-in" x-data>
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-cente">
            Manual Check-In
        </h2>

        {{-- Manual Entry Section --}}
        <form action="{{ route('admin.attendance.manual') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @csrf

            {{-- Live Name Search --}}
            <div>
                <label class="block text-left text-sm font-semibold text-gray-600 mb-1">Search Member or Trainer</label>
                <input type="text" id="user_search" placeholder="Type name..." autocomplete="off"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 shadow focus:outline-none focus:ring-red-500 focus:border-red-500">
                <ul id="user_results"
                    class="bg-white border border-gray-300 rounded-md shadow mt-1 max-h-60 overflow-y-auto hidden z-10">
                </ul>
            </div>

            <div class="hidden">
                <label for="user_id" class="block text-sm font-semibold text-gray-600 mb-1">User ID</label>
                <input type="number" name="user_id" id="user_id" required>
            </div>

            {{-- Date --}}
            <div>
                <label class="block text-left text-sm font-semibold text-gray-600 mb-1">Date</label>
                <input type="date" name="date" value="{{ now()->timezone('Asia/Colombo')->toDateString() }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 shadow focus:outline-none focus:ring-red-500 focus:border-red-500" required>
            </div>

            {{-- Time --}}
            <div>
                <label class="block text-left text-sm font-semibold text-gray-600 mb-1">Time</label>
                <input type="time" name="time"
                    value="{{ now()->timezone('Asia/Colombo')->format('H:i') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 shadow focus:outline-none focus:ring-red-500 focus:border-red-500" required>
            </div>

            {{-- Submit Button aligned right --}}
            <div class="col-span-1 md:col-span-3 flex justify-end">
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-save mr-2"></i>Submit Entry
                </button>
            </div>
        </form>

    </div>

    {{-- Alpine Animations --}}
    <style>
        [x-cloak] { display: none; }

        .animate-fade-in {
            animation: fadeIn 0.8s ease-in-out;
        }

        .animate-slide-in {
            animation: slideIn 0.6s ease-in-out;
        }
 
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>

    {{-- Live Search Script --}}
    @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('user_search');
            const results = document.getElementById('user_results');

            input.addEventListener('input', function () {
                let query = this.value;
                if (query.length < 2) {
                    results.classList.add('hidden');
                    return;
                }

                fetch("{{ route('admin.search.users') }}?q=" + encodeURIComponent(query))
                    .then(res => res.json())
                    .then(data => {
                        results.innerHTML = '';
                        const filtered = data.filter(user => ['Member', 'Trainer'].includes(user.role_name));

                        if (filtered.length === 0) {
                            results.innerHTML = '<li class="px-4 py-2 text-gray-500">No users found</li>';
                        } else {
                            filtered.forEach(user => {
                                const li = document.createElement('li');
                                li.className = "px-4 py-2 hover:bg-gray-100 cursor-pointer";
                                li.textContent = `${user.first_name} ${user.last_name} (${user.role_name})`;
                                li.setAttribute('data-user-id', user.id); // <-- important!
                                results.appendChild(li);
                            });
                        }
                        results.classList.remove('hidden');
                    });
            });

            // Use event delegation for clicks on list items
            results.addEventListener('click', function(e) {
                if(e.target && e.target.matches('li')) {
                    const selectedUserId = e.target.getAttribute('data-user-id');
                    const selectedUserName = e.target.textContent;

                    input.value = selectedUserName;
                    document.getElementById('user_id').value = selectedUserId;

                    results.classList.add('hidden');
                }
            });

            // Hide results when clicking outside
            document.addEventListener('click', function (event) {
                if (!input.contains(event.target) && !results.contains(event.target)) {
                    results.classList.add('hidden');
                }
            });
        });

        function setCurrentDateTime() {
            const now = new Date();
            const formattedDateTime = now.toISOString(); 
            document.getElementById('currentDatetime').value = formattedDateTime;
        }
        </script>
    @endpush
@endsection
