@extends('memberDashboard.layout')

@section('content')

    <div class="w-full max-w-7xl p-8 bg-white rounded-lg my-4 shadow-md mx-auto"
        x-data="bookingForm()" x-init="init()">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold">My Bookings</h2>
            </div>
            <button @click="checkWorkoutPlanAndOpenModal()"
                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                + New Booking
            </button>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto mt-6">
            <table class="min-w-full bg-white shadow-md rounded-lg border text-base border-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                    <th class="py-3 px-4 text-left border-b border-gray-300">Trainer</th>
                    <th class="py-3 px-4 text-left border-b border-gray-300">Date</th>
                    <th class="py-3 px-4 text-left border-b border-gray-300">Time</th>
                    <th class="py-3 px-4 text-left border-b border-gray-300">Notes</th>
                    <th class="py-3 px-4 text-left border-b border-gray-300">Status</th>
                    <th class="py-3 px-4 text-left border-b border-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $b)
                    <tr class="hover:bg-gray-100 transition duration-150">
                        <td class="py-3 px-4 border-b border-gray-200">
                        {{ optional($b->trainer)->first_name }} {{ optional($b->trainer)->last_name }}
                        </td>
                        <td class="py-3 px-4 border-b border-gray-200">
                        {{ $b->start_date_label }}
                        </td>
                        <td class="py-3 px-4 border-b border-gray-200">
                        {{ $b->start_time_label }}
                        </td>
                        <td class="py-3 px-4 border-b border-gray-200">
                        {{ $b->description ?? '—' }}
                        </td>
                        <td class="py-3 px-4 border-b border-gray-200">
                        @php
                            $badges = [
                            'pending'  => 'bg-yellow-100 text-yellow-800 ring-yellow-200',
                            'approved' => 'bg-green-100  text-green-800  ring-green-200',
                            'declined' => 'bg-red-100    text-red-800    ring-red-200',
                            'cancelled'=> 'bg-gray-100   text-gray-700   ring-gray-200',
                            'completed'=> 'bg-blue-100   text-blue-800   ring-blue-200',
                            'expired'  => 'bg-gray-100   text-gray-700   ring-gray-200',
                            ];
                        @endphp
                        <div class="flex flex-col">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs ring-1 w-fit h-8 {{ $badges[$b->status] ?? 'bg-gray-100 text-gray-700 ring-gray-200' }}">
                            {{ ucfirst($b->status) }}
                            </span>
                            @if($b->status === 'declined' && $b->decline_reason)
                            <span class="text-xs text-red-600 mt-1 font-medium">
                                Reason: {{ $b->decline_reason }}
                            </span>
                            @endif
                        </div>
                        </td>
                        <td class="py-3 px-4 border-b border-gray-200">
                        @if($b->status === 'pending')
                             <form method="POST" action="{{ route('member.bookings.cancel', $b->booking_id) }}" class="cancel-booking-form">
                                @csrf
                                <button type="button" class="px-3 py-1 rounded bg-red-500 hover:bg-red-600 text-white cancel-btn">
                                    Cancel
                                </button>
                            </form>
                        @else
                            <span class="text-gray-400">No Available</span>
                        @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-6 px-4 text-center text-gray-500">No bookings yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $bookings->links() }}
        </div>

        {{-- Modal: New Booking --}}
        <div x-show="openNew" x-cloak
            class="fixed inset-0 flex items-center justify-center backdrop-blur-sm bg-white/20 z-50">
            <div @click.away="openNew=false"
                class="bg-white rounded-lg p-6 w-full max-w-md shadow-[0_0_15px_4px_rgba(241,30,19,0.5)]">
            <h2 class="text-md md:text-3xl font-bold text-center mb-5">New Booking</h2>

            <form method="POST" action="{{ route('member.bookings.store') }}" class="space-y-4">
                @csrf

                {{-- Trainer --}}
                <div>
                <label class="block text-left text-sm font-medium text-gray-700 items-center gap-2">
                    <i class="fa-solid fa-user-group text-sm mr-1.5 ml-1"></i>
                    Select Trainer
                </label>
                <select name="trainer_id" x-model="trainerId" @change="loadSlots()"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                                focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm" required>
                    <option value="" disabled selected>Choose trainer…</option>
                    @foreach($trainers as $t)
                    <option value="{{ $t->id }}">{{ $t->first_name }} {{ $t->last_name }}</option>
                    @endforeach
                </select>
                </div>

                {{-- Date --}}
                <div>
                <label class="block text-left text-sm font-medium text-gray-700">Preferred Date</label>
                <input type="date" name="preferred_date" x-model="date" @change="loadSlots()"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                                focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                        min="{{ now()->toDateString() }}" required>
                </div>

                {{-- Time --}}
                <div>
                <label class="block text-left text-sm font-medium text-gray-700">Preferred Time (optional)</label>
                <select name="preferred_time" x-ref="timeSelect"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                                focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    <option value="">— Select a time (optional) —</option>
                </select>
                <p class="text-xs text-gray-500 mt-1" x-text="hint"></p>
                </div>

                {{-- Notes --}}
                <div>
                <label class="block text-left text-sm font-medium text-gray-700">Notes (optional)</label>
                <input type="text" name="description" placeholder="Injuries, goals…"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                                focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>

                {{-- Actions --}}
                <div class="flex justify-end space-x-2 pt-2">
                <button type="button" @click="openNew=false"
                        class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded">
                    Cancel
                </button>
                <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                    Submit
                </button>
                </div>
            </form>
            </div>
        </div>

        {{-- Modal: Workout Plan Required --}}
        <div x-show="showWorkoutPlanModal" x-cloak
            class="fixed inset-0 flex items-center justify-center backdrop-blur-sm bg-black/50 z-50">
            <div @click.away="showWorkoutPlanModal=false"
                class="bg-white rounded-lg p-6 w-full max-w-md shadow-[0_0_15px_4px_rgba(241,30,19,0.5)]">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-dumbbell text-red-500 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Workout Plan Required</h3>
                    <p class="text-gray-600 mb-6">
                        You need to have an active workout plan before booking training sessions. 
                        Please request a workout plan first.
                    </p>
                    <div class="flex gap-3">
                        <button @click="showWorkoutPlanModal=false" 
                                class="flex-1 bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded">
                            Cancel
                        </button>
                        <a href="{{ route('member.workoutplan.request') }}" 
                            class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-center">
                            Request Workout Plan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

            function bookingForm() {
                return {
                    openNew: false,
                    showWorkoutPlanModal: false,
                    trainerId: '',
                    date: '',
                    hint: '',
                    init() {},
                    async checkWorkoutPlanAndOpenModal() {
                    try {
                        const response = await fetch('{{ route("member.workoutplan.check") }}', {
                        headers: {'X-Requested-With': 'XMLHttpRequest'}
                        });
                        const data = await response.json();
                        
                        if (data.hasWorkoutPlan) {
                        this.openNew = true;
                        } else {
                        this.showWorkoutPlanModal = true;
                        }
                    } catch(e) {
                        console.error('Error checking workout plan:', e);
                        this.showWorkoutPlanModal = true;
                    }
                    },
                    async loadSlots() {
                    this.hint = '';
                    const t = this.trainerId, d = this.date;
                    const sel = this.$refs.timeSelect;
                    sel.innerHTML = '<option value="">— Select a time (optional) —</option>';
                    if (!t || !d) return;

                    try {
                        const url = `{{ route('member.bookings.slots') }}?trainer_id=${encodeURIComponent(t)}&date=${encodeURIComponent(d)}`;
                        console.log('Loading slots from:', url);
                        
                        const res = await fetch(url, { 
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                        },
                        credentials: 'same-origin'
                        });
                        
                        console.log('Response status:', res.status);
                        
                        if (!res.ok) {
                        throw new Error(`HTTP ${res.status}`);
                        }
                        
                        const json = await res.json();
                        console.log('Slots response:', json);
                        
                        if (json.slots && json.slots.length) {
                        json.slots.forEach(hhmm => {
                            const opt = document.createElement('option');
                            opt.value = hhmm;
                            opt.textContent = hhmm;
                            sel.appendChild(opt);
                        });
                        } else {
                        this.hint = 'No free times on this day. Try another date.';
                        }
                    } catch(e) {
                        console.error('Slots loading error:', e);
                        this.hint = 'Could not load slots. Check browser console for details.';
                    }
                    }
                }
            }
        </script>
    @endpush
@endsection
