@extends('memberDashboard.layout')

@section('content')
    {{-- Header --}}
    <div class="bg-[#1E1E1E] text-white px-8 py-6 rounded-lg shadow mb-6 mt-4">
        <h2 class="text-2xl font-bold">My Sessions</h2>
        <p class="text-sm text-gray-300 mt-1">Assigned and approved sessions with your trainer.</p>
    </div>

    {{-- UPCOMING --}}
    <div class="w-full max-w-xs md:max-w-7xl mx-auto">
        <div class="bg-white text-black px-8 py-6 rounded-lg shadow mb-6 mt-4">
            <h2 class="text-xl font-bold">Upcoming Sessions</h2>
        </div>

        @forelse($upcoming as $s)
            @php
                $trainerFirst = optional($s->trainer)->first_name ?? '';
                $trainerLast = optional($s->trainer)->last_name ?? '';
                $initial = mb_substr($trainerFirst ?: 'T', 0, 1);

                // Use model helpers (start_at first, legacy fallback)
                $dateLabel = $s->start_date_label;
                $timeLabel = $s->start_time_label;

                // Use start_at primarily to decide if cancel is allowed
                $start = $s->start_at
                    ? \Carbon\Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $s->getRawOriginal('start_at'),
                        'UTC',
                    )->setTimezone('Asia/Colombo')
                    : \Carbon\Carbon::parse(
                        (\Carbon\Carbon::parse($s->date)->toDateString() ?? now()->toDateString()) .
                            ' ' .
                            ($s->time ?? '00:00:00'),
                        'Asia/Colombo',
                    );

                $canCancel = in_array($s->status, ['pending', 'approved']) && $start->isFuture();
            @endphp

            <div
                class="group bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-lg hover:scale-[1.02] transition-all duration-300 p-6 mb-4 overflow-hidden">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-4 min-w-0">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 text-white rounded-full flex items-center justify-center font-bold text-lg shadow">
                            {{ $initial }}
                        </div>

                        <div class="min-w-0">
                            <h3 class="text-lg font-bold text-gray-800 mb-1 truncate">
                                {{ trim($trainerFirst . ' ' . $trainerLast) ?: 'Trainer' }}
                            </h3>
                            <p class="text-sm text-gray-600 leading-snug">
                                <span class="inline-flex items-center mr-3">
                                    <i class="far fa-calendar-alt mr-1"></i>{{ $dateLabel }}
                                </span>
                                <span class="inline-flex items-center mr-3">
                                    <i class="far fa-clock mr-1"></i>{{ $timeLabel }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <span
                        class="inline-block text-xs font-semibold px-3 py-1 rounded-full bg-green-100 text-green-700 shrink-0">
                        Approved
                    </span>
                </div>

                @if ($canCancel)
                    <div class="mt-4 flex justify-end">
                        <form method="POST" action="{{ route('member.bookings.cancel', $s->booking_id) }}"
                            class="cancel-booking-form">
                            @csrf
                            <button type="submit"
                                class="cancel-btn px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                                Cancel
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 text-center text-gray-500">
                No upcoming sessions.
            </div>
        @endforelse
    </div>

    {{-- PAST --}}
    <div class="w-full max-w-xs md:max-w-7xl mx-auto mt-8">
        <div class="bg-white text-black px-8 py-6 rounded-lg shadow mb-6 mt-4">
            <h2 class="text-xl font-bold">Past Sessions</h2>
        </div>

        @forelse($past as $s)
            @php
                $trainerFirst = optional($s->trainer)->first_name ?? '';
                $trainerLast = optional($s->trainer)->last_name ?? '';
                $initial = mb_substr($trainerFirst ?: 'T', 0, 1);

                $dateLabel = $s->start_date_label;
                $timeLabel = $s->start_time_label;
                $status = strtolower($s->status);

                // Show "Completed" for approved sessions that are in the past
                $displayStatus = $status === 'approved' ? 'completed' : ($status === 'expired' ? 'expired' : $status);

                $pill = match ($displayStatus) {
                    'completed' => 'bg-blue-100 text-blue-700',
                    'cancelled' => 'bg-red-100 text-red-700',
                    'declined' => 'bg-yellow-100 text-yellow-700',
                    'expired' => 'bg-red-50 text-red-600',
                    default => 'bg-gray-100 text-gray-700',
                };
            @endphp

            <div
                class="group bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-lg hover:scale-[1.02] transition-all duration-300 p-6 mb-4 overflow-hidden">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-4 min-w-0">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 text-white rounded-full flex items-center justify-center font-bold text-lg shadow">
                            {{ $initial }}
                        </div>

                        <div class="min-w-0">
                            <h3 class="text-lg font-bold text-gray-800 mb-1 truncate">
                                {{ trim($trainerFirst . ' ' . $trainerLast) ?: 'Trainer' }}
                            </h3>
                            <p class="text-sm text-gray-600 leading-snug">
                                <span class="inline-flex items-center mr-3">
                                    <i class="far fa-calendar-alt mr-1"></i>{{ $dateLabel }}
                                </span>
                                <span class="inline-flex items-center mr-3">
                                    <i class="far fa-clock mr-1"></i>{{ $timeLabel }}
                                </span>
                            </p>
                        </div>
                    </div>

                    @if($displayStatus === 'declined')
                                        <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full capitalize shrink-0"
                                            style="background-color:#fee2e2 !important; color:#dc2626 !important; border:1px solid rgba(220,38,38,0.12) !important; position:relative; z-index:10001;">
                                                        {{ $displayStatus }}
                                                </span>
                    @else
                        <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full {{ $pill }} capitalize shrink-0">
                            {{ $displayStatus }}
                        </span>
                    @endif
                </div>

                @if ($s->status === 'declined' && $s->decline_reason)
                    <!-- use utility class to keep reason above decorative fills -->
                    <div class="mt-2 text-sm booking-reason-on-top">
                        <span class="font-medium text-red-700">Decline Reason:</span>
                        <span class="text-red-600">{{ $s->decline_reason }}</span>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 text-center text-gray-500">
                No past sessions.
            </div>
        @endforelse
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
