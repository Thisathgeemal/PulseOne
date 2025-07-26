@extends('adminDashboard.layout')

@section('content')

    <div class="w-full max-w-xs md:max-w-7xl p-5 bg-white rounded-lg my-4 text-center shadow-md mx-auto">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <h2 class="text-xl sm:text-2xl font-bold">Membership Type Management</h2>

            <div class="flex justify-between items-center space-x-3 sm:space-x-4">
                <div class="relative sm:w-auto">
                    <form method="GET" action="{{ route('admin.membertype') }}">
                        <input
                            type="text"
                            id="searchBar"
                            name="search"
                            placeholder="Search Membership types"
                            value="{{ request('search') }}"
                            class="p-1.5 pl-8 border rounded-full w-56 text-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                        >
                        <button type="submit" class="absolute left-2.5 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <div class="relative group">
                    <form method="POST" action="{{ route('membertype.report') }}" target="_blank" class="relative group" onsubmit="setCurrentDateTime()">
                        @csrf
                        <input type="hidden" name="datetime" id="currentDatetime">

                        <button type="submit"
                            class="bg-blue-500 text-white p-1.5 rounded hover:bg-blue-600 flex items-center justify-center w-8 h-8">
                            <i class="fas fa-download text-white text-sm"></i>
                        </button>

                        <span 
                            class="absolute hidden group-hover:block bg-gray-700 text-white text-xs rounded py-1 px-2 -bottom-8 left-1/2 transform -translate-x-1/2">Export
                        </span>
                    </form>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <form method="POST" action="{{ route('membertype.delete') }}">
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
            </div>

            <!-- Data Table -->
            <div class="md:overflow-x-auto mt-6 overflow-x-scroll">
                <table class="min-w-full bg-white shadow-md rounded-lg border text-base border-gray-200">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-3 px-4 text-left border-b border-gray-300">
                                <input type="checkbox" id="select-all" class="h-4 w-4">
                            </th>
                            <th class="py-3 text-left px-4 border-b border-gray-300">Type Name</th>
                            <th class="py-3 text-left px-4 border-b border-gray-300">Duration (Days)</th>
                            <th class="py-3 text-left px-4 border-b border-gray-300">Amount</th>
                            <th class="py-3 text-left px-4 border-b border-gray-300">Discount (%)</th>
                            <th class="py-3 text-left px-4 border-b border-gray-300">Price</th>
                            <th class="py-3 text-left px-4 border-b border-gray-300">Edit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($membershipType as $type)
                            <tr class="hover:bg-gray-200 transition duration-200">
                                <td class="py-3 px-4 text-left border-b border-gray-200">
                                    <input type="checkbox" name="selector[]" class="h-4 w-4" value="{{ $type->type_id }}">
                                </td>
                                <td class="py-3 text-left px-4 border-b border-gray-200">{{ $type->type_name }}</td>
                                <td class="py-3 text-left px-4 border-b border-gray-200">{{ $type->duration }}</td>
                                <td class="py-3 text-left px-4 border-b border-gray-200">{{ $type->amount }}</td>
                                <td class="py-3 text-left px-4 border-b border-gray-200">
                                    {{ intval($type->discount) == $type->discount ? intval($type->discount) : number_format($type->discount, 2) }}%
                                </td>
                                <td class="py-3 text-left px-4 border-b border-gray-200">{{ $type->price }}</td>
                                <td class="py-3 text-left px-4 border-b border-gray-200">
                                    <a href="javascript:void(0);" onclick="openEditModal(this)" data-type_id="{{ $type->type_id }}" data-type_name="{{ $type->type_name }}"
                                        data-duration="{{ $type->duration }}" data-amount="{{ $type->amount }}" data-discount="{{ $type->discount }}"
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
            {{ $membershipType->links() }}
        </div>

        <!-- Modal -->
        <div id="addTypeModle" role="dialog" aria-modal="true" class="fixed inset-0 backdrop-blur-sm bg-white/20 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-[0_0_15px_4px_rgba(241,30,19,0.5)]">
                <h2 class="text-md md:text-3xl font-bold text-center mb-4" id="modalHeader">Create Membership Type</h2>                

                <form method="POST" id="typeForm">
                    @csrf
                    <input type="hidden" id="type_id" name="type_id">
                    <!-- Membership Type -->
                    <div>
                        <label for="type_name" class="block text-left text-sm font-medium text-gray-700 items-center gap-2">
                            <i class="fa-solid fa-user text-sm mr-1.5 ml-1"></i>
                            Membership Type
                        </label>
                        <input id="type_name" type="text" name="type_name" required autofocus
                            placeholder="Enter membership type"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            value="{{ old('type_name') }}">
                        @error('type_name')
                            <span class="text-red-600 text-sm text-left">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Duration (Days) -->
                    <div class="mt-4">
                        <label for="duration" class="block text-left text-sm font-medium text-gray-700 items-center gap-2">
                            <i class="fa-solid fa-clock text-sm mr-1.5 ml-1"></i>
                            Duration (Days)
                        </label>
                        <input id="duration" type="number" name="duration" required
                            placeholder="Enter duration in days"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            value="{{ old('duration') }}">
                        @error('duration')
                            <span class="text-red-600 text-sm text-left">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Amount -->
                    <div class="mt-4">
                        <label for="amount" class="block text-left text-sm font-medium text-gray-700 items-center gap-2">
                            <i class="fa-solid fa-money-bill-wave text-sm mr-1.5 ml-1"></i>
                            Amount
                        </label>
                        <input id="amount" type="number" name="amount" required
                            placeholder="Enter amount"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            value="{{ old('amount') }}">
                        @error('amount')
                            <span class="text-red-600 text-sm text-left">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Discount -->
                    <div class="mt-4">
                        <label for="discount" class="block text-left text-sm font-medium text-gray-700 items-center gap-2">
                            <i class="fa-solid fa-percent text-sm mr-1.5 ml-1"></i>
                            Discount (%)
                        </label>
                        <input id="discount" type="number" name="discount" required step="0.01" min="0" max="100"
                            placeholder="Enter discount percentage"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            value="{{ old('discount') }}">
                        @error('discount')
                            <span class="text-red-600 text-sm text-left">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded" onclick="closeModal()">Cancel</button>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded" id="modalSubmitButton">Save</button>
                    </div>
                </form>
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
        document.querySelector('form[action="{{ route('admin.bulkAction') }}"]').addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('input[name="selector[]"]:checked');
            const submitter = e.submitter;

            if (checkboxes.length === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'No selection',
                    text: 'Please select at least one dietitian.',
                    confirmButtonColor: '#d32f2f'
                });
                return; 
            }

            if (submitter && submitter.name === 'action' && submitter.value === 'delete') {
                e.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this deletion!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d32f2f',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Delete',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Add hidden input for the action
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'action';
                        hiddenInput.value = 'delete';
                        e.target.appendChild(hiddenInput);

                        e.target.submit(); 
                    }
                });
            }
        });

        function openModal() {
            document.getElementById('type_id').value = '';
            document.getElementById('type_name').value = '';
            document.getElementById('amount').value = '';
            document.getElementById('duration').value = '';

            document.getElementById('typeForm').action = "{{ route('membertype.create') }}";
            document.getElementById('modalSubmitButton').textContent = 'Create';
            document.getElementById('modalHeader').textContent = 'Create Membership Type';
            const modal = document.getElementById('addTypeModle');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function openEditModal(button) {
            const id = button.getAttribute('data-type_id');
            const membershipType = button.getAttribute('data-type_name');
            const amount = button.getAttribute('data-amount');
            const duration = button.getAttribute('data-duration');
            const discount = button.getAttribute('data-discount');

            document.getElementById('type_id').value = id;
            document.getElementById('type_name').value = membershipType;
            document.getElementById('amount').value = amount;
            document.getElementById('duration').value = duration;
            document.getElementById('discount').value = discount;

            const form = document.getElementById('typeForm');
            form.action = "{{ route('membertype.update') }}"; 

            document.getElementById('modalSubmitButton').textContent = 'Update';
            document.getElementById('modalHeader').textContent = 'Update Membership Type';
            const modal = document.getElementById('addTypeModle');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function setCurrentDateTime() {
            const now = new Date();
            const formattedDateTime = now.toISOString(); 
            document.getElementById('currentDatetime').value = formattedDateTime;
        }

        function closeModal() {
            document.getElementById('addTypeModle').classList.add('hidden');
        }
        </script>

    @endpush
@endsection
