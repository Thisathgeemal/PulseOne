@extends('adminDashboard.layout')

@section('content')
    <div class="max-w-3xl mx-auto px-6 py-8 bg-white rounded-xl shadow-lg mt-10 animate-fade-in" x-data>
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <i class="fas fa-qrcode text-blue-600 text-2xl"></i>
            Today's QR Check-In Code
        </h2>

        <div class="text-gray-600 text-sm mb-6">
            <i class="fas fa-calendar-alt mr-1"></i> 
            Date: <span class="font-medium text-gray-800">{{ \Carbon\Carbon::now()->format('l, d M Y') }}</span>
        </div>

        {{-- QR Display --}}
        <div class="flex justify-center items-center mb-6">
            <div class="bg-white p-6 rounded-lg border shadow-md hover:shadow-lg transition duration-300 transform hover:scale-105">
                {!! $qrCode !!}
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 text-red-800 px-4 py-3 rounded text-center text-sm shadow-sm animate-slide-in">
            <i class="fas fa-info-circle mr-1"></i>
            This QR is valid only for today ({{ \Carbon\Carbon::now()->format('Y-m-d') }}). Members must scan this to check in.
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
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>
@endsection
