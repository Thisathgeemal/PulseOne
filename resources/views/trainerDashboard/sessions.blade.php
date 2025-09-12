@extends('trainerDashboard.layout')

@section('content')
    {{-- Header banner --}}
    <div class="bg-[#1E1E1E] text-white px-8 py-6 rounded-lg shadow mb-6 mt-4">
        <h2 class="text-2xl font-bold">My Sessions</h2>
        <p class="text-sm text-gray-300 mt-1">Approved sessions with your members.</p>
    </div>

    {{-- UPCOMING --}}
    <div class="w-full max-w-7xl mx-auto mb-8">

        <div class="bg-white text-black px-8 py-6 rounded-lg shadow mb-6 mt-4">
            <h2 class="text-xl font-bold">Upcoming Sessions</h2>
        </div>

        @if ($upcoming->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                @foreach ($upcoming as $s)
                    @php
                        $first = optional($s->member)->first_name ?? '';
                        $last = optional($s->member)->last_name ?? '';
                        $initial = mb_substr($first ?: 'M', 0, 1);

                        // Prefer start_at labels; fall back to legacy date/time safely
                        $dateLbl = $s->start_date_label ?? \Carbon\Carbon::parse($s->date)->format('Y-m-d');
                        $timeLbl =
                            $s->start_time_label ??
                            ($s->time
                                ? \Carbon\Carbon::parse(strlen($s->time) === 5 ? $s->time . ':00' : $s->time)->format(
                                    'H:i',
                                )
                                : '—');

                        // Determine if cancellation is allowed
                        $start = $s->start_at
                            ? \Carbon\Carbon::createFromFormat(
                                'Y-m-d H:i:s',
                                $s->getRawOriginal('start_at'),
                                'UTC',
                            )->setTimezone('Asia/Colombo')
                            : \Carbon\Carbon::parse(
                                ($s->date ?? now()->toDateString()) . ' ' . ($s->time ?? '00:00:00'),
                                'Asia/Colombo',
                            );

                        $canCancel = in_array($s->status, ['pending', 'approved']) && $start->isFuture();
                    @endphp

                    <div
                        class="group bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-lg hover:scale-[1.01] transition-all duration-300 p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-4 min-w-0">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 text-white rounded-full flex items-center justify-center font-bold text-lg shadow">
                                    {{ $initial }}
                                </div>

                                <div class="min-w-0">
                                    <h3 class="text-lg font-bold text-gray-800 mb-0.5 truncate">
                                        {{ trim($first . ' ' . $last) ?: 'Member' }}
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        <span class="inline-flex items-center mr-3">
                                            <i class="far fa-calendar-alt mr-1"></i>{{ $dateLbl }}
                                        </span>
                                        <span class="inline-flex items-center">
                                            <i class="far fa-clock mr-1"></i>{{ $timeLbl }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <span
                                class="inline-block text-xs font-semibold px-3 py-1 rounded-full bg-green-100 text-green-700">
                                Approved
                            </span>
                        </div>

                        {{-- Cancel button (only if eligible) --}}
                        @if ($canCancel)
                            <div class="mt-4 flex justify-end">
                                <form method="POST" action="{{ route('trainer.bookings.cancel', $s->booking_id) }}"
                                    class="cancel-booking-form">
                                    @csrf
                                    <button type="submit"
                                        class="cancel-btn px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition">
                                        Cancel
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 text-center text-gray-500">
                No upcoming sessions.
            </div>
        @endif
    </div>

    {{-- PAST --}}
    <div class="w-full max-w-7xl mx-auto">

        <div class="bg-white text-black px-8 py-6 rounded-lg shadow mb-6 mt-4">
            <h2 class="text-xl font-bold">Past Sessions</h2>
        </div>


        @if ($past->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                @foreach ($past as $s)
                    @php
                        $first = optional($s->member)->first_name ?? '';
                        $last = optional($s->member)->last_name ?? '';
                        $initial = mb_substr($first ?: 'M', 0, 1);

                        $dateLbl = $s->start_date_label ?? \Carbon\Carbon::parse($s->date)->format('Y-m-d');
                        $timeLbl =
                            $s->start_time_label ??
                            ($s->time
                                ? \Carbon\Carbon::parse(strlen($s->time) === 5 ? $s->time . ':00' : $s->time)->format(
                                    'H:i',
                                )
                                : '—');

                        $status = strtolower($s->status);
                        $displayStatus = $status === 'approved' ? 'completed' : $status;

                        $pill = match ($displayStatus) {
                            'completed' => 'bg-blue-100 text-blue-700',
                            'cancelled' => 'bg-red-100 text-red-700',
                            'declined' => 'bg-yellow-100 text-yellow-700',
                            'expired' => 'bg-red-200 text-red-700',
                            default => 'bg-gray-100 text-gray-700',
                        };
                        $statusText = ucfirst($displayStatus);
                    @endphp

                    <div
                        class="group bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-lg hover:scale-[1.01] transition-all duration-300 p-6 mb-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-4 min-w-0">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 text-white rounded-full flex items-center justify-center font-bold text-lg shadow">
                                    {{ $initial }}
                                </div>

                                <div class="min-w-0">
                                    <h3 class="text-lg font-bold text-gray-800 mb-0.5 truncate">
                                        {{ trim($first . ' ' . $last) ?: 'Member' }}
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        <span class="inline-flex items-center mr-3">
                                            <i class="far fa-calendar-alt mr-1"></i>{{ $dateLbl }}
                                        </span>
                                        <span class="inline-flex items-center">
                                            <i class="far fa-clock mr-1"></i>{{ $timeLbl }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full {{ $pill }}">
                                {{ $statusText }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 text-center text-gray-500">
                No past sessions.
            </div>
        @endif

    </div>

    @push('scripts')
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: @json(session('success')),
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d32f2f'
                });
            </script>
        @endif

        @if (session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: @json(session('error')),
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d32f2f'
                });
            </script>
        @endif

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.cancel-btn').forEach(function(button) {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        let form = this.closest('.cancel-booking-form');

                        Swal.fire({
                            title: 'Are you sure?',
                            text: "Do you want to cancel this booking?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Yes',
                            cancelButtonText: 'No'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
@endsection
