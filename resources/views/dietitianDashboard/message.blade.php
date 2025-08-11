@extends('dietitianDashboard.layout')

@section('content')

    @livewire('chat')

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

@endsection
