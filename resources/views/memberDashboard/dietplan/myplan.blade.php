@extends('memberDashboard.layout')

@section('content')
    <div class="w-full max-w-xs md:max-w-7xl p-8 bg-white rounded-lg my-4 text-center shadow-md mx-auto">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <h2 class="text-xl sm:text-2xl font-bold">My Diet Plan Requests</h2>
            <button onclick="openRequestModal()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                + Request Plan
            </button>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto mt-6">
            <table class="min-w-full bg-white shadow-md rounded-lg border text-base border-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left border-b border-gray-300">Dietitian Name</th>
                        <th class="py-3 px-4 text-left border-b border-gray-300">Plan Description</th>
                        <th class="py-3 px-4 text-left border-b border-gray-300">Preferred Start Date</th>
                        <th class="py-3 px-4 text-left border-b border-gray-300">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $request)
                        <tr class="hover:bg-gray-100 transition duration-150">
                            <td class="py-3 px-4 text-left border-b border-gray-200">
                                {{ $request->dietitian ? $request->dietitian->first_name . ' ' . $request->dietitian->last_name : 'N/A' }}
                            </td>
                            <td class="py-3 px-4 text-left border-b border-gray-200">
                                {{ $request->description ?? '-' }}
                            </td>
                             <td class="py-3 px-4 text-left border-b border-gray-200">
                                {{ $request->preferred_start_date ? \Carbon\Carbon::parse($request->preferred_start_date)->format('d M Y') : '-' }}
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

        <!-- Pagination -->
        <div class="mt-4">
            {{ $requests->links() }}
        </div>

        <!-- Request Diet Plan Modal -->
        <div id="requestModal" role="dialog" aria-modal="true" class="fixed inset-0 flex backdrop-blur-sm bg-white/20 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-[0_0_15px_4px_rgba(241,30,19,0.5)]">
                <h2 class="text-md md:text-3xl font-bold text-center mb-5">Request a Diet Plan</h2>

                <!-- Form -->
                <form action="{{ route('member.diet.request') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Dietitian Selection -->
                    <div>
                        <label for="dietitian_id" class="block text-left text-sm font-medium text-gray-700 items-center gap-2">
                            <i class="fa-solid fa-user-nurse text-sm mr-1.5 ml-1"></i>
                            Select Dietitian
                        </label>
                        <select id="dietitian_id" name="dietitian_id" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            <option value="" disabled selected>Select Dietitian</option>
                            @foreach($dietitians as $d)
                                <option value="{{ $d->id }}">{{ $d->first_name }} {{ $d->last_name }}</option>
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
                            placeholder="Keto diet, weight loss..." 
                            required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                        />
                    </div>

                    <!-- Height & Weight -->
                    <div class="flex gap-3">
                        <div class="w-1/2 text-left">
                            <label class="block text-sm font-medium text-gray-700">Height (cm)</label>
                            <input type="number" name="height" placeholder="e.g. 170"
                                class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                        </div>
                        <div class="w-1/2 text-left">
                            <label class="block text-sm font-medium text-gray-700">Weight (kg)</label>
                            <input type="number" name="weight" placeholder="e.g. 65"
                                class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                        </div>
                    </div>

                    <!-- Target Weight -->
                    <div class="text-left">
                        <label class="block text-sm font-medium text-gray-700">Target Weight (kg)</label>
                        <input type="number" name="target_weight" placeholder="e.g. 65"
                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                    </div>

                    <!-- Start Date -->
                    <div class="text-left">
                        <label class="block text-sm font-medium text-gray-700">Preferred Start Date</label>
                        <input type="date" name="preferred_start_date"
                            class="w-full mt-1 px-3 py-2 border rounded-md border-gray-300 text-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                    </div>

                    <!-- Buttons -->
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
        <div class="bg-[#1E1E1E] text-white px-8 py-6 rounded-lg shadow mb-6 mt-4">
            <h2 class="text-2xl font-bold">My Diet Plan</h2>
            <p class="text-sm text-gray-300 mt-1">Assigned by your personal dietitian.</p>
        </div>

        @if($plans->count())
            <div class="grid gap-6 sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-1">
                @foreach($plans as $plan)
                    <div class="group bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-lg hover:scale-[1.02] transition-all duration-300 p-6 overflow-hidden">
                        <div class="flex justify-between items-start">
                            <div class="flex items-start gap-4">
                                <!-- Avatar with Gradient -->
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 text-white rounded-full flex items-center justify-center font-bold text-lg shadow">
                                    {{ strtoupper(substr($plan->dietitian->first_name, 0, 1)) }}
                                </div>

                                <!-- Info -->
                                <div class="text-left">
                                    <h3 class="text-lg font-bold text-gray-800 mb-1">
                                        {{ $plan->plan_name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 leading-snug">
                                        <span class="font-semibold text-gray-700">Dietitian:</span> {{ $plan->dietitian->first_name }} {{ $plan->dietitian->last_name }}<br>
                                        <span class="font-semibold text-gray-700">Duration:</span>
                                        {{ \Carbon\Carbon::parse($plan->start_date)->format('Y-m-d') }} to
                                        {{ \Carbon\Carbon::parse($plan->end_date)->format('Y-m-d') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div class="mt-1">
                                <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full
                                    {{ $plan->status === 'Active' ? 'bg-green-100 text-green-700' :
                                    ($plan->status === 'Cancelled' ? 'bg-red-100 text-red-700' :
                                    'bg-yellow-100 text-yellow-700') }}">
                                    {{ ucfirst($plan->status) }}
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-1 flex gap-3 justify-end opacity-100 transition-opacity duration-300">
                            <a href="{{ route('member.workoutplan.cancel', $plan->workoutplan_id) }}"
                            class="w-[110px] px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition text-center">
                                Cancel
                            </a>
                            <a href="{{ route('member.workoutplan.view', $plan->workoutplan_id) }}"
                            class="w-[110px] px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition text-center">
                                View
                            </a>
                            <a href="{{ route('workout.report', $plan->workoutplan_id) }}"
                            class="w-[110px] px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition text-center">
                                Download 
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <h2 class="text-xl font-semibold text-gray-600">No workout plans have been created yet.</h2>
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
