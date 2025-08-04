@extends('memberDashboard.layout')

@section('content')
    @php
        $checkinToken = session()->pull('checkin_token');
    @endphp

    <div class="max-w-xl mx-auto px-6 py-10 bg-white rounded-xl shadow-lg animate-fade-in mt-10" x-data>
        <h2 class="text-2xl font-bold text-gray-700 mb-6 flex items-center gap-2">
            <i class="fas fa-qrcode text-blue-500"></i> Scan QR to Check In
        </h2>

        {{-- QR Form --}}
        <form action="{{ route('checkin') }}" method="POST" class="space-y-6" id="qrCheckinForm">
            @csrf
            <div>
                <label for="qr_code" class="block text-sm font-semibold text-gray-700 mb-1">QR Code Token</label>
                <input 
                    type="text" 
                    id="qr_code" 
                    name="qr_code" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 transition" 
                    placeholder="Enter or scan QR code..."
                    value="{{ $checkinToken }}"
                    required
                >
            </div>

            <p class="text-sm text-gray-500 flex items-center gap-1 mt-2">
                <i class="fas fa-info-circle text-red-400"></i> Please scan the QR code shown at the gym screen.
            </p>

            <button type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-2 rounded-lg shadow-md">
                <i class="fas fa-sign-in-alt mr-2"></i>Check In
            </button>
        </form>
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