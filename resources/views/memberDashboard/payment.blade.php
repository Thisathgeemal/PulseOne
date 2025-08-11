@extends('memberDashboard.layout')

@section('content')
    <div class="w-full max-w-xs md:max-w-7xl p-8 bg-white rounded-lg my-4 text-center shadow-md mx-auto animate-fade-in" x-data>
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <h2 class="text-xl sm:text-2xl font-bold">Payment Summary</h2>

            <div class="flex justify-between items-center space-x-3 sm:space-x-4">
                <div class="relative w-full sm:w-auto flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-0 sm:space-y-0">
                    <form method="GET" action="{{ route('member.payment') }}" class="flex flex-col sm:flex-row sm:items-center sm:space-x-4">
                        
                        {{-- Filter by Payment Date --}}
                        <input
                            type="date"
                            name="date"
                            value="{{ request('date') }}"
                            class="p-1.5 border rounded-full text-sm focus:outline-none focus:ring-red-500 focus:border-red-500 mb-3 sm:mb-0 md:mb-0"
                        >

                        {{-- Global Search Input --}}
                        <div class="relative">
                            <input
                                type="text"
                                id="searchBar"
                                name="search"
                                placeholder="Search Payments"
                                value="{{ request('search') }}"
                                class="p-1.5 pl-4 border md:w-48 rounded-full text-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                            >
                        </div>

                        {{-- Search Button --}}
                        <button type="submit"
                                class="bg-red-500 hover:bg-red-600 text-white flex items-center justify-center rounded-md w-8 h-8">
                            <i class="fas fa-search text-sm"></i>
                        </button>
                    </form>
                </div>

                <div class="relative group">
                    <form method="POST" action="{{ route('member.payment.report') }}" target="_blank" class="relative group" onsubmit="setCurrentDateTime()">
                        @csrf
                        <input type="hidden" name="datetime" id="currentDatetime">

                        <button type="submit"
                            class="bg-blue-500 text-white p-1.5 rounded hover:bg-blue-600 flex items-center justify-center w-8 h-8">
                            <i class="fas fa-download text-white text-sm"></i>
                        </button>

                        <span 
                            class="absolute hidden group-hover:block bg-gray-700 text-white text-xs rounded py-1 px-2 -bottom-8 left-1/2 transform -translate-x-1/2">Export
                        </span>
                    </form>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="md:overflow-x-auto mt-6 overflow-x-scroll">
            <table class="min-w-full bg-white shadow-md rounded-lg border text-base border-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-3 px-4 border-b border-gray-300">No</th>
                        <th class="py-3 text-left px-4 border-b border-gray-300">Membership Type</th>
                        <th class="py-3 text-left px-4 border-b border-gray-300">Amount</th>
                        <th class="py-3 text-left px-4 border-b border-gray-300">Payment Method</th>
                        <th class="py-3 text-left px-4 border-b border-gray-300">Payment Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $index => $payment)
                        <tr class="hover:bg-gray-200 transition duration-200">
                            <td class="px-4 py-3 border-b border-gray-200">{{ $index + 1 }}</td>
                            <td class="py-3 text-left px-4 border-b border-gray-200">
                                {{ $payment->membershipType->type_name ?? '' }}
                            </td>
                            <td class="py-3 text-left px-4 border-b border-gray-200">
                                Rs. {{ number_format($payment->amount, 2) }}
                            </td>
                            <td class="py-3 text-left px-4 border-b border-gray-200">
                                {{ $payment->payment_method }}
                            </td>
                            <td class="py-3 text-left px-4 border-b border-gray-200">
                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-gray-500 font-medium">No payment records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $payments->links() }}
        </div>

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

        <script>
            function setCurrentDateTime() {
                const now = new Date();
                const formattedDateTime = now.toISOString(); 
                document.getElementById('currentDatetime').value = formattedDateTime;
            }
        </script>
    @endpush

@endsection
