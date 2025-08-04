@extends('memberDashboard.layout')

@section('content')
    @php
        $checkinToken = session()->pull('checkin_token');
    @endphp

    <div class="max-w-xl mx-auto px-6 py-10 bg-white rounded-xl shadow-lg animate-fade-in mt-10" x-data>
        <h2 class="text-3xl font-bold text-blue-700 mb-4 flex items-center gap-2">
            <i class="fas fa-qrcode text-blue-500"></i> Scan QR to Check In
        </h2>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded mb-4 shadow-sm animate-slide-in">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded mb-4 shadow-sm animate-slide-in">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        {{-- QR Form --}}
        <form action="{{ route('checkin') }}" method="POST" class="space-y-6" id="qrCheckinForm">
            @csrf
            <div>
                <label for="qr_code" class="block text-sm font-semibold text-gray-700 mb-1">QR Code Token</label>
                <input 
                    type="text" 
                    id="qr_code" 
                    name="qr_code" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" 
                    placeholder="Enter or scan QR code..."
                    value="{{ $checkinToken }}"
                    required
                >
            </div>

            <p class="text-sm text-gray-500 flex items-center gap-1 mt-2">
                <i class="fas fa-info-circle text-blue-400"></i> Please scan the QR code shown at the gym screen.
            </p>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg shadow-md transition duration-200 transform hover:scale-105">
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
@endsection