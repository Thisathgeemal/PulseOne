@extends('adminDashboard.layout')

@section('content')

<div class="w-full max-w-xs md:max-w-7xl p-5 bg-white rounded-lg my-4 text-center shadow-md mx-auto">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <h2 class="text-xl sm:text-2xl font-bold">User Role Management Summary</h2>

        <div class="flex justify-between items-center space-x-3 sm:space-x-4">
            <!-- Search Bar -->
            <div class="relative sm:w-auto">
                <form method="GET" action="{{ route('admin.role') }}">
                    <input type="text" name="search" placeholder="Search Users"
                        value="{{ request('search') }}"
                        class="p-1.5 pl-8 border rounded-full w-56 text-sm">
                    <button type="submit"
                        class="absolute left-2.5 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <!-- Export -->
            <div class="relative group">
                <form method="POST" action="{{ route('role.report') }}" target="_blank" onsubmit="setCurrentDateTime()">
                    @csrf
                    <input type="hidden" name="datetime" id="currentDatetime">
                    <input type="hidden" name="role" value="Trainer">
                    <button type="submit"
                        class="bg-blue-500 text-white p-1.5 rounded hover:bg-blue-600 flex items-center justify-center w-8 h-8">
                        <i class="fas fa-download text-white text-sm"></i>
                    </button>
                    <span class="absolute hidden group-hover:block bg-gray-700 text-white text-xs rounded py-1 px-2 -bottom-8 left-1/2 transform -translate-x-1/2">
                        Export
                    </span>
                </form>
            </div>
        </div>
    </div>

    <!-- Add & Delete Buttons -->
    <form method="POST" action="{{ route('role.delete') }}">
        @csrf
        <input type="hidden" name="action" value="">
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
                        <th class="py-3 text-left px-4 border-b border-gray-300">User Role Id</th>
                        <th class="py-3 text-left px-4 border-b border-gray-300">User Role Name</th>
                        <th class="py-3 text-left px-4 border-b border-gray-300">Total Users</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr class="hover:bg-gray-200 transition duration-200">
                            <td class="py-3 px-4 text-left border-b border-gray-200">
                                <input type="checkbox" name="selector[]" class="h-4 w-4" value="{{ $role->role_id }}">
                            </td>
                            <td class="py-3 text-left px-4 border-b border-gray-200">{{ $role->role_id }}</td>
                            <td class="py-3 text-left px-4 border-b border-gray-200">{{ $role->role_name }}</td>
                            <td class="py-3 text-left px-4 border-b border-gray-200">{{ $role->users_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $roles->links() }}
    </div>

    <!-- Modal for Add -->
    <div id="addRoleModel" role="dialog" aria-modal="true"
        class="fixed inset-0 backdrop-blur-sm bg-white/20 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-[0_0_15px_4px_rgba(241,30,19,0.5)]">
            <h2 class="text-md md:text-3xl font-bold text-center mb-4" id="modalHeader">Create Role Account</h2>
            <form method="POST" id="roleForm" action="{{ route('role.create') }}">
                @csrf
                <div>
                    <label for="role_name" class="block text-left text-sm font-medium text-gray-700">
                        <i class="fa-solid fa-user text-sm mr-1.5 ml-1"></i> Role Name
                    </label>
                    <input id="role_name" type="text" name="role_name" required autofocus
                        placeholder="Enter role name"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                        value="{{ old('role_name') }}">
                    @error('role_name')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button" class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded" onclick="closeModal()">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        Create
                    </button>
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
        document.querySelector('form[action="{{ route('role.delete') }}"]').addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('input[name="selector[]"]:checked');
            const submitter = e.submitter;

            if (checkboxes.length === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'No selection',
                    text: 'Please select at least one user role.',
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
            document.getElementById('role_name').value = '';
            const modal = document.getElementById('addRoleModel');
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
            document.getElementById('addRoleModel').classList.add('hidden');
        }
    </script>
@endpush

@endsection
