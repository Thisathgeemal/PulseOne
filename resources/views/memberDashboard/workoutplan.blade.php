@extends('memberDashboard.layout')

@section('content')
    <div class="w-full max-w-xs md:max-w-7xl p-8 bg-white rounded-lg my-4 text-center shadow-md mx-auto">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <h2 class="text-xl sm:text-2xl font-bold">My Workout Plan Request</h2>
            <button onclick="openRequestModal()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                + Request Plan
            </button>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto mt-6">
            <table class="min-w-full bg-white shadow-md rounded-lg border text-base border-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left border-b border-gray-300">Trainer Name</th>
                        <th class="py-3 px-4 text-left border-b border-gray-300">Plan Description</th>
                        <th class="py-3 px-4 text-left border-b border-gray-300">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $request)
                        <tr class="hover:bg-gray-100 transition duration-150">
                            <td class="py-3 px-4 text-left border-b border-gray-200">
                                {{ $request->trainer ? $request->trainer->first_name . ' ' . $request->trainer->last_name : 'N/A' }}
                            </td>
                            <td class="py-3 px-4 text-left border-b border-gray-200">
                                {{ $request->description ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-left border-b border-gray-200">
                                <span class="{{ $request->status === 'Approved' ? 'text-green-600 font-semibold' : ($request->status === 'Rejected' ? 'text-red-600 font-semibold' : 'text-yellow-600 font-semibold') }}">
                                    {{ $request->status }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination links -->
        <div class="mt-4">
            {{ $requests->links() }}
        </div>

        <!-- Request Workout Plan Modal -->
        <div id="requestModal" role="dialog" aria-modal="true" class="fixed inset-0 flex backdrop-blur-sm bg-white/20 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-[0_0_15px_4px_rgba(241,30,19,0.5)]">
                <h2 class="text-md md:text-3xl font-bold text-center mb-5">Request a Workout Plan</h2>
                
                <!-- Form -->
                <form action="{{ route('member.workout.request') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Trainer Selection -->
                    <div>
                        <label for="trainer_id" class="block text-left text-sm font-medium text-gray-700 items-center gap-2">
                            <i class="fa-solid fa-dumbbell text-sm mr-1.5 ml-1"></i>
                            Select Trainer
                        </label>
                        <select id="trainer_id" name="trainer_id" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            <option value="" disabled selected>Select Trainer</option>
                            @foreach($trainers as $trainer)
                                <option value="{{ $trainer->id }}">{{ $trainer->first_name }} {{ $trainer->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Plan Description -->
                    <div>
                        <label for="plan_dis" class="block text-left text-sm font-medium text-gray-700 items-center gap-2">
                            <i class="fa-solid fa-clipboard-list text-sm mr-1.5 ml-1"></i>
                            Plan Description
                        </label>
                        <input 
                            type="text" 
                            id="plan_dis" 
                            name="plan_dis" 
                            placeholder="Fat loss, Build muscle..." 
                            required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                        />
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" onclick="closeRequestModal()" class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded">
                            Cancel
                        </button>
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- Assigned Plan View -->
    <div class="w-full max-w-xs md:max-w-7xl p-8 bg-white rounded-lg mb-4 text-center shadow-md mx-auto mt-10">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-left">My Workout Plan</h2>
                <p class="text-sm text-gray-500">Assigned by your personal trainer</p>
            </div>
        </div>

         @if(isset($plan))

        @else
            <div class="bg-yellow-50 text-yellow-700 border border-yellow-300 p-4 rounded">
                <p>No workout plan has been assigned to you yet. Please contact your trainer for assistance.</p>
            </div>
        @endif

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
            function openRequestModal() {
                document.getElementById('requestModal').classList.remove('hidden');
            }
            function closeRequestModal() {
                document.getElementById('requestModal').classList.add('hidden');
            }
        </script>
    @endpush

@endsection
