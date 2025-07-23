@extends('adminDashboard.layout')

@section('content')

    <div class="w-full max-w-xs md:max-w-7xl p-5 bg-white rounded-lg my-4 text-center shadow-md mx-auto">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <h2 class="text-xl sm:text-2xl font-bold">Admin Management Summary</h2>

            <div class="flex justify-between items-center space-x-3 sm:space-x-4">
                <div class="relative sm:w-auto">
                    <form method="GET" action="{{ route('admin.admin') }}">
                        <input
                            type="text"
                            id="searchBar"
                            name="search"
                            placeholder="Search Users"
                            value="{{ request('search') }}"
                            class="p-1.5 pl-8 border rounded-full w-56 text-sm"
                        >
                        <button type="submit" class="absolute left-2.5 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <div class="relative group">
                    <form method="POST" action="{{ route('user.report') }}" target="_blank" class="relative group" onsubmit="setCurrentDateTime()">
                        @csrf
                        <input type="hidden" name="datetime" id="currentDatetime">
                        <input type="hidden" name="role" value="Admin">

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
        </div>

        <!-- Modal -->
        <div id="addAdminModle" role="dialog" aria-modal="true" class="fixed inset-0 backdrop-blur-sm bg-white/20 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-[0_0_15px_4px_rgba(241,30,19,0.5)]">
                <h2 class="text-md md:text-3xl font-bold text-center mb-4" id="modalHeader">Create Admin Account</h2>                

                <form method="POST" id="adminForm">
                    @csrf
                    <input type="hidden" id="admin_id" name="admin_id">
                    <div>
                        <label for="first_name" class="block text-left text-sm font-medium text-gray-700 items-center gap-2">
                            <i class="fa-solid fa-user text-sm mr-1.5 ml-1"></i>
                            First Name
                        </label>
                        <input id="first_name" type="text" name="first_name" required autofocus
                                placeholder="Enter your first name"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                                value="{{ old('first_name') }}">
                        @error('first_name')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last_name" class="block text-left text-sm font-medium text-gray-700 items-center gap-2 mt-4">
                            <i class="fa-solid fa-user text-sm mr-1.5 ml-1"></i>
                            Last Name
                        </label>
                        <input id="last_name" type="text" name="last_name" required autofocus
                                placeholder="Enter your last name"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                                value="{{ old('last_name') }}">
                        @error('last_name')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-left text-sm font-medium text-gray-700 items-center gap-2 mt-4">
                            <i class="fa-solid fa-envelope text-sm mr-1.5 ml-1"></i>
                            Email
                        </label>
                        <input id="email" type="email" name="email" required autofocus
                            placeholder="Enter your email"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            value="{{ old('email') }}">
                        @error('email')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Contact Number -->
                    <div>
                        <label for="contact_number" class="block text-left text-sm font-medium text-gray-700 items-center gap-2 mt-4">
                            <i class="fa-solid fa-phone text-sm mr-1.5 ml-1"></i>
                            Mobile Number
                        </label>
                        <input id="contact_number" type="tel" name="contact_number" required autofocus
                                placeholder="Enter your contact number"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                                value="{{ old('contact_number') }}">
                        @error('contact_number')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded"><a href="{{ route('admin.admin') }}">Cancel</a></button>
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
                e.preventDefault(); // Prevent form immediately

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this admin!",
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
            document.getElementById('admin_id').value = '';
            document.getElementById('first_name').value = '';
            document.getElementById('last_name').value = '';
            document.getElementById('email').value = '';
            document.getElementById('contact_number').value = '';

            document.getElementById('adminForm').action = "{{ route('admin.create') }}";
            document.getElementById('modalSubmitButton').textContent = 'Create';
            document.getElementById('modalHeader').textContent = 'Create Admin Account';
            const modal = document.getElementById('addAdminModle');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function openEditModal(button) {
            const id = button.getAttribute('data-id');
            const firstName = button.getAttribute('data-first_name');
            const lastName = button.getAttribute('data-last_name');
            const email = button.getAttribute('data-email');
            const mobile = button.getAttribute('data-contact_number');

            document.getElementById('admin_id').value = id;
            document.getElementById('first_name').value = firstName;
            document.getElementById('last_name').value = lastName;
            document.getElementById('email').value = email;
            document.getElementById('contact_number').value = mobile;

            const form = document.getElementById('adminForm');
            form.action = "{{ route('admin.update') }}"; 

            document.getElementById('modalSubmitButton').textContent = 'Update';
            document.getElementById('modalHeader').textContent = 'Update Admin Account';
            const modal = document.getElementById('addAdminModle');
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
        </script>

    @endpush
@endsection
