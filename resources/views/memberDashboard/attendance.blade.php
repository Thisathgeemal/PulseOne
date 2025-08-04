@extends('memberDashboard.layout')

@section('content')

    <div class="w-full max-w-xs md:max-w-7xl p-8 bg-white rounded-lg my-4 text-center shadow-md mx-auto animate-fade-in">
        <!-- Header and Date Filter -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
            <h2 class="text-xl sm:text-2xl font-bold text-left">My Attendance</h2>

            <form method="GET" class="flex flex-col sm:flex-row sm:items-center sm:space-x-4">
                <input
                    type="date"
                    name="date"
                    value="{{ request('date', now()->timezone('Asia/Colombo')->toDateString()) }}"
                    class="p-1.5 border rounded-full text-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                >
                <button type="submit"
                    class="bg-red-500 hover:bg-red-600 text-white p-1.5 px-4 rounded-md text-sm">
                    <i class="fas fa-search mr-1"></i> Filter
                </button>
            </form>
        </div>

        <!-- Attendance Table -->
        <div class="md:overflow-x-auto mt-2 overflow-x-scroll">
            <table class="min-w-full bg-white shadow-md rounded-lg border text-base border-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-3 px-4 border-b border-gray-300">No</th>
                        <th class="py-3 px-4 border-b border-gray-300">Date</th>
                        <th class="py-3 px-4 border-b border-gray-300">Check-In</th>
                        <th class="py-3 px-4 border-b border-gray-300">Check-Out</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $index => $attendance)
                        <tr class="hover:bg-gray-200 transition duration-200">
                            <td class="py-3 px-4 border-b border-gray-200">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 border-b border-gray-200">
                                {{ \Carbon\Carbon::parse($attendance->check_in_time)->timezone('Asia/Colombo')->format('Y-m-d') }}
                            </td>
                            <td class="py-3 px-4 border-b border-gray-200">
                                {{ \Carbon\Carbon::parse($attendance->check_in_time)->timezone('Asia/Colombo')->format('h:i A') }}
                            </td>
                            <td class="py-3 px-4 border-b border-gray-200">
                                @if ($attendance->check_out_time)
                                    {{ \Carbon\Carbon::parse($attendance->check_out_time)->timezone('Asia/Colombo')->format('h:i A') }}
                                @else
                                    <form action="{{ route('attendance.checkout', $attendance->attendance_id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-3 rounded text-sm">
                                            Check Out
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-gray-500 font-medium">
                                No attendance records found.
                            </td>
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

    {{-- Animations --}}
    <style>
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out both;
        }
        .animate-slide-in {
            animation: slideIn 0.5s ease-in-out both;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>
    
    @push('scripts')
        @if(session('success'))
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

        @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "{{ session('error') }}",
                confirmButtonText: 'OK',
                confirmButtonColor: '#d32f2f'
            });
        </script>
        @endif
    @endpush

@endsection
