@extends('memberDashboard.layout')

@section('content')
    <div class="w-full max-w-xs md:max-w-7xl p-5 bg-white rounded-lg my-4 text-center shadow-md mx-auto">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <h2 class="text-xl sm:text-2xl font-bold">My Workout Plan Summary</h2>
            <button onclick="openRequestModal()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                + Request Plan
            </button>
        </div>

        {{-- <!-- Action Buttons -->
        <form method="POST" action="{{ route('admin.bulkAction') }}">
            @csrf
            <input type="hidden" name="action" id="bulkAction" value="">

            <div class="flex flex-col sm:flex-row justify-start mt-6 space-x-3">
                <button type="button" onclick="openModal()" 
                    class="w-full sm:w-32 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 mb-2 md:mb-0 rounded-lg">
                    Add
                </button>

                <button type="submit" name="action" value="delete"
                    class="w-full sm:w-32 bg-red-500 hover:bg-red-600 text-white py-2 px-4 mb-2 md:mb-0 rounded-lg">
                    Delete
                </button>

                <button type="submit" name="action" value="activate" 
                    class="w-full sm:w-32 bg-green-500 hover:bg-green-600 text-white py-2 px-4 mb-2 md:mb-0 rounded-lg">
                    Activate
                </button>

                <button type="submit" name="action" value="deactivate"
                    class="w-full sm:w-32 bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 md:mb-0 rounded-lg">
                    Deactivate
                </button>
            </div>

            <!-- Data Table -->
            <div class="md:overflow-x-auto mt-6 overflow-x-scroll">
                <table class="min-w-full bg-white shadow-md rounded-lg border text-base border-gray-200">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-3 px-4 text-left border-b border-gray-300">
                                <input type="checkbox" id="select-all" class="h-4 w-4">
                            </th>
                            <th class="py-3 text-left px-4 border-b border-gray-300">First Name</th>
                            <th class="py-3 text-left px-4 border-b border-gray-300">Last Name</th>
                            <th class="py-3 text-left px-4 border-b border-gray-300">Email</th>
                            <th class="py-3 text-left px-4 border-b border-gray-300">Mobile</th>
                            <th class="py-3 text-left px-4 border-b border-gray-300">Address</th>
                            <th class="py-3 text-left px-4 border-b border-gray-300">Status</th>
                            <th class="py-3 text-left px-4 border-b border-gray-300">Edit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($admins as $admin)
                            <tr class="hover:bg-gray-200 transition duration-200">
                                <td class="py-3 px-4 text-left border-b border-gray-200">
                                    <input type="checkbox" name="selector[]" class="h-4 w-4" value="{{ $admin->id }}">
                                </td>
                                <td class="py-3 text-left px-4 border-b border-gray-200">{{ $admin->first_name }}</td>
                                <td class="py-3 text-left px-4 border-b border-gray-200">{{ $admin->last_name }}</td>
                                <td class="py-3 text-left px-4 border-b border-gray-200">{{ $admin->email }}</td>
                                <td class="py-3 text-left px-4 border-b border-gray-200">{{ $admin->mobile_number }}</td>
                                <td class="py-3 text-left px-4 border-b border-gray-200">{{ $admin->address }}</td>
                                <td class="py-3 text-left px-4 border-b border-gray-200">
                                    <span class="{{ $admin->roles->first()?->pivot->is_active ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold' }}">
                                        {{ $admin->roles->first()?->pivot->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="py-3 text-left px-4 border-b border-gray-200">
                                    <a href="javascript:void(0);" onclick="openEditModal(this)" data-id="{{ $admin->id }}" data-first_name="{{ $admin->first_name }}"
                                        data-last_name="{{ $admin->last_name }}" data-email="{{ $admin->email }}" data-contact_number="{{ $admin->mobile_number }}"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-red-50 hover:bg-red-100 transition duration-200 text-red-500 hover:text-red-700"
                                        title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $admins->links() }}
        </div> --}}

        {{-- <!-- Request Workout Plan Modal -->
        <div id="requestModal" class="fixed inset-0 bg-white/20 backdrop-blur-sm flex items-center justify-center hidden z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-lg relative shadow-xl border border-gray-300">
                
                <!-- Close Button -->
                <button 
                    onclick="closeRequestModal()" 
                    class="absolute top-2 right-4 text-gray-400 hover:text-red-500 text-xl"
                >&times;</button>

                <!-- Modal Title -->
                <h3 class="text-xl font-bold mb-4 text-center">Request a Workout Plan</h3>

                <!-- Form -->
                <form action="{{ route('member.workout.request') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Trainer Selection -->
                    <div>
                        <label class="block font-medium text-sm mb-1">Select Trainer</label>
                        <select name="trainer_id" required class="w-full px-3 py-2 border rounded">
                            <option value="" disabled selected>Select Trainer</option>
                            @foreach($trainers as $trainer)
                                <option value="{{ $trainer->id }}">{{ $trainer->first_name }} {{ $trainer->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Plan Description -->
                    <div>
                        <label class="block font-medium text-sm mb-1">Plan Description</label>
                        <input 
                            type="text" 
                            name="plan_dis" 
                            placeholder="e.g., Fat loss, Build muscle..." 
                            required
                            class="w-full px-3 py-2 border rounded" 
                        />
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end">
                        <button 
                            type="button" 
                            onclick="closeRequestModal()" 
                            class="bg-gray-300 px-4 py-2 rounded mr-2"
                        >Cancel</button>
                        
                        <button 
                            type="submit" 
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded"
                        >Submit</button>
                    </div>
                </form>
            </div>
        </div> --}}
        
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
