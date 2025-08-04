@extends('memberDashboard.layout')

@section('content')
    <div class="w-full max-w-xs md:max-w-7xl p-8 bg-white rounded-lg my-4 text-center shadow-md mx-auto">
        <h2 class="text-xl sm:text-2xl font-bold">My Attendance</h2>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-800 px-5 py-3 rounded mb-6 shadow-sm animate-fade-in">
                {{ session('success') }}
            </div>
        @endif

        {{--  Member Attendance Date Filter --}}
        <form method="GET" class="mb-6 flex flex-col sm:flex-row items-center gap-4 sm:gap-2">
            <div class="flex items-end gap-2 w-full sm:w-auto">
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">By Date</label>
                    <input type="date" name="date"
                        value="{{ request('date', now()->timezone('Asia/Colombo')->toDateString()) }}"
                        class="border border-gray-300 rounded-lg px-4 py-2 shadow focus:ring-2 focus:ring-yellow-400">
                </div>

                <button type="submit"
                        class="h-10 mt-1 bg-blue-600 hover:bg-yellow-600 text-white font-semibold px-4 py-2 rounded-lg transition whitespace-nowrap">
                    <i class="fas fa-search mr-1"></i>Filter
                </button>
            </div>
        </form>

        {{-- Attendance Table --}}
        <div class="overflow-x-auto border border-gray-200 rounded-xl shadow-sm bg-white animate-fade-in-up">
            <table class="w-full text-sm text-left table-auto border-collapse">
                <thead class="bg-black text-white text-sm">
                    <tr>
                        <th class="px-4 py-3"></th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Check-In</th>
                        <th class="px-4 py-3">Check-Out</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white text-gray-700">
                    @forelse($attendances as $index => $attendance)
                        <tr class="border-t hover:bg-green-50 transition">
                            <td class="px-4 py-3 font-semibold">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">
                                {{ \Carbon\Carbon::parse($attendance->check_in_time)->timezone('Asia/Colombo')->format('Y-m-d') }}
                            </td>
                            <td class="px-4 py-3">
                                {{ \Carbon\Carbon::parse($attendance->check_in_time)->timezone('Asia/Colombo')->format('h:i A') }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $attendance->check_out_time 
                                    ? \Carbon\Carbon::parse($attendance->check_out_time)->timezone('Asia/Colombo')->format('h:i A') 
                                    : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold 
                                    {{ $attendance->status === 'Present' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                    {{ $attendance->status }}
                                </span>
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

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $attendances->links() }}
        </div>
    </div>

    {{-- Custom Fade-In Animation --}}
    <style>
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection
