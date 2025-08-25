@extends('trainerDashboard.layout')

@section('content')

    <div x-data="{
        declineOpen: false,
        declineId: null,
        declineName: '',
        openDecline(id, name) {
            this.declineId = id;
            this.declineName = name;
            this.declineOpen = true
        },
        closeDecline() {
            this.declineOpen = false;
            this.declineId = null;
            this.declineName = ''
        }
    }">

        {{-- Header banner --}}
        <div class="bg-[#1E1E1E] text-white px-8 py-6 rounded-lg shadow mb-6 mt-4">
            <h2 class="text-2xl font-bold">Booking Requests</h2>
            <p class="text-sm text-gray-300 mt-1">Pending requests from your members.</p>
        </div>

        @forelse($requests as $r)
            @php
                // From controller: $reqMeta[$bookingId] = ['slots'=>['06:00','07:00',...], 'preferred'=>'HH:MM'|null]
                $meta = $reqMeta[$r->booking_id] ?? ['slots' => [], 'preferred' => null];
                $slots = $meta['slots'];
                $pref = $meta['preferred']; // 'HH:MM' or null
                $prefAvailable = $pref && in_array($pref, $slots, true);

                $first = $r->member->first_name ?? '';
                $last = $r->member->last_name ?? '';
                $name = trim($first . ' ' . $last) ?: 'Member';
                $initial = mb_substr($first ?: 'M', 0, 1);

                // For pending requests we still display the legacy date
                $dateLbl = \Carbon\Carbon::parse($r->date)->format('Y-m-d');
            @endphp

            <div
                class="group bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-lg hover:scale-[1.01] transition-all duration-300 p-6 mb-4">
                {{-- Top row: avatar + info + requested-time badge --}}
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-4 min-w-0">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 text-white rounded-full flex items-center justify-center font-bold text-lg shadow">
                            {{ $initial }}
                        </div>

                        <div class="min-w-0">
                            <h3 class="text-lg font-bold text-gray-800 mb-0.5 truncate">{{ $name }}</h3>
                            <p class="text-sm text-gray-600">
                                <span class="inline-flex items-center">
                                    <i class="far fa-calendar-alt mr-1"></i>{{ $dateLbl }}
                                </span>
                            </p>
                            <p class="text-sm text-gray-500 mt-1">
                                <span class="font-medium text-gray-600">Notes:</span> {{ $r->description ?: '—' }}
                            </p>
                        </div>
                    </div>

                    <div class="shrink-0">
                        @if ($pref)
                            <span
                                class="inline-block text-xs font-semibold px-3 py-1 rounded-full 
                                    {{ $prefAvailable ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                Member Requested: {{ \Carbon\Carbon::createFromFormat('H:i', $pref)->format('h:i A') }}
                                @if (!$prefAvailable)
                                    <span class="block text-[10px] mt-0.5">(Time conflict - showing alternatives)</span>
                                @endif
                            </span>
                        @else
                            <span
                                class="inline-block text-xs font-semibold px-3 py-1 rounded-full bg-blue-100 text-blue-700">
                                Any Available Time
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Actions row: slot select + Approve + Decline --}}
                <div class="mt-4 flex flex-col md:flex-row md:items-center gap-3">
                    {{-- Approve --}}
                    <form method="POST" action="{{ route('trainer.bookings.approve', $r->booking_id) }}"
                        class="flex items-center gap-3">
                        @csrf

                        <select name="time"
                            class="w-44 md:w-52 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                            {{ empty($slots) ? 'disabled' : '' }} required>
                            @if (empty($slots))
                                <option disabled selected>No free times available</option>
                            @else
                                <option value="" disabled {{ !$prefAvailable ? 'selected' : '' }}>
                                    Choose available time...
                                </option>
                                @foreach ($slots as $t)
                                    @php
                                        $isPreferred = $prefAvailable && $pref === $t;
                                        $displayTime = \Carbon\Carbon::createFromFormat('H:i', $t)->format('h:i A');
                                    @endphp
                                    <option value="{{ $t }}" @selected($isPreferred)
                                        {{ $isPreferred ? 'style=background-color:#dcfce7;font-weight:600;' : '' }}>
                                        {{ $displayTime }}
                                        @if ($isPreferred)
                                            ★ (Member's Choice)
                                        @endif
                                    </option>
                                @endforeach
                            @endif
                        </select>

                        <button type="submit"
                            class="px-4 py-2 rounded-md text-white bg-green-600 hover:bg-green-700 disabled:opacity-50"
                            {{ empty($slots) ? 'disabled' : '' }}>
                            Approve
                        </button>
                    </form>

                    <div class="flex-1"></div>

                    {{-- Decline (opens single modal) --}}
                    <button type="button" @click="openDecline({{ $r->booking_id }}, {{ json_encode($name) }})"
                        class="px-4 py-2 rounded-md text-white bg-red-600 hover:bg-red-700 self-start md:self-auto">
                        Decline
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm text-center p-6 text-gray-600">
                No pending requests.
            </div>
        @endforelse

        {{-- Single Decline Modal (prevents stacking/lag) --}}
        <div x-show="declineOpen" x-cloak
            class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50" x-transition.opacity>
            <div @click.away="closeDecline()"
                class="bg-white rounded-lg p-6 w-full max-w-md shadow-[0_0_15px_4px_rgba(0,0,0,0.08)]" x-transition.scale>
                <h3 class="text-lg font-semibold mb-1">Decline request</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Please provide a short reason. The member will see this message.
                </p>

                <form method="POST" :action="`{{ url('/trainer/bookings') }}/${declineId}/decline`" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                        <input type="text" name="reason" required placeholder="Reason (required)"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" @click="closeDecline()"
                            class="px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-md text-white bg-red-600 hover:bg-red-700">
                            Decline
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        @if (session('success'))
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

        @if (session('error'))
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
