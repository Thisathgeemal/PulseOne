@extends('memberDashboard.layout')

@section('content')
<div class="max-w-4xl mx-auto mt-10 p-6 bg-white shadow rounded">
    <h2 class="text-2xl font-bold mb-6">QR Code Attendance</h2>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @elseif(session('info'))
        <div class="bg-yellow-100 text-yellow-700 px-4 py-2 rounded mb-4">
            {{ session('info') }}
        </div>
    @endif

    {{-- QR image and scan button --}}
    <form action="{{ route('mark.attendance') }}" method="POST" class="text-center mb-8">
        @csrf
        <img src="{{ asset('images/qr-placeholder.png') }}" alt="QR Code" class="mx-auto mb-4 w-40 h-40" />
        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded">
            SCAN
        </button>
    </form>

    <hr class="my-6">

    {{-- Attendance History --}}
    <h3 class="text-xl font-semibold mb-2">Your Attendance History</h3>
@include('memberDashboard.attendance', ['attendances' => $attendances])

</div>
@endsection
