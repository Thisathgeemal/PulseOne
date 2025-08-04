@extends('adminDashboard.layout')

@section('content')
    <div class="w-full max-w-xs md:max-w-7xl p-8 bg-white rounded-lg my-4 text-center shadow-md mx-auto animate-fade-in" x-data>
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <h2 class="text-xl sm:text-2xl font-bold">Membership Summary</h2>

            <div class="flex justify-between items-center space-x-3 sm:space-x-4">
                <div class="relative w-full sm:w-auto flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-0 sm:space-y-0">
                    <form method="GET" action="{{ route('admin.membership') }}" class="flex flex-col sm:flex-row sm:items-center sm:space-x-4">
                        <input
                            type="date"
                            name="start_date"
                            value="{{ request('start_date') }}"
                            class="p-1.5 border rounded-full text-sm focus:outline-none focus:ring-red-500 focus:border-red-500 mb-3 sm:mb-0 md:mb-0"
                        >
                        <input
                            type="date"
                            name="end_date"
                            value="{{ request('end_date') }}"
                            class="p-1.5 border rounded-full text-sm focus:outline-none focus:ring-red-500 focus:border-red-500 mb-3 sm:mb-0 md:mb-0"
                        >
                        <div class="relative">
                            <input
                                type="text"
                                id="searchBar"
                                name="search"
                                placeholder="Search Memberships"
                                value="{{ request('search') }}"
                                class="p-1.5 pl-8 pr-10 border md:w-56 rounded-full text-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                            >
                            <button type="submit" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="relative group">
                    <form method="POST" action="{{ route('membership.report') }}" target="_blank" class="relative group" onsubmit="setCurrentDateTime()">
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

        <!-- Add & Cancel Buttons -->
        <form method="POST" action="{{ route('membership.cancel') }}">
            @csrf
            <input type="hidden" name="action" value="">
            <div class="flex flex-col sm:flex-row justify-start mt-6 space-x-3">
                <button type="button" onclick="openModal()"
                    class="w-full sm:w-32 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 mb-2 md:mb-0 rounded-lg">
                    Add
                </button>
                <button type="submit" name="action" value="cancel"
                    class="w-full sm:w-32 bg-red-500 hover:bg-red-600 text-white py-2 px-4 mb-2 md:mb-0 rounded-lg">
                    Cancel
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
                            <th class="py-3 text-left px-4 border-b border-gray-300">Member Name</th>
                            <th class="py-3 text-left px-4 border-b border-gray-300">Membership Type</th>
                            <th class="py-3 text-left px-4 border-b border-gray-300">Start Date</th>
                            <th class="py-3 text-left px-4 border-b border-gray-300">End Date</th>
                            <th class="py-3 text-left px-4 border-b border-gray-300">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($memberships as $membership)
                            <tr class="hover:bg-gray-200 transition duration-200">
                                <td class="py-3 px-4 text-left border-b border-gray-200">
                                    <input type="checkbox" name="selector[]" class="h-4 w-4" value="{{ $membership->membership_id }}">
                                </td>
                                <td class="py-3 text-left px-4 border-b border-gray-200">
                                    {{ $membership->user->first_name }} {{ $membership->user->last_name }}
                                </td>
                                <td class="py-3 text-left px-4 border-b border-gray-200">{{ $membership->membershipType->type_name }}</td>
                                <td class="py-3 text-left px-4 border-b border-gray-200">{{ \Carbon\Carbon::parse($membership->start_date)->format('Y-m-d') }}</td>
                                <td class="py-3 text-left px-4 border-b border-gray-200">{{ \Carbon\Carbon::parse($membership->end_date)->format('Y-m-d') }}</td>
                                <td class="py-3 text-left px-4 border-b border-gray-200">
                                    <span class="{{ $membership->status === 'Active' ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold' }}">
                                        {{ $membership->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $memberships->links() }}
        </div>

        <!-- Modal -->
        <div id="addMembershipModel" role="dialog" aria-modal="true" class="fixed inset-0 backdrop-blur-sm bg-white/20 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-[0_0_15px_4px_rgba(241,30,19,0.5)]">
                <h2 class="text-md md:text-3xl font-bold text-center mb-4" id="modalHeader">Create Membership</h2>

                <form method="POST" id="membershipForm" action="{{ route('membership.create') }}">
                    @csrf

                    <!-- Hidden Member ID -->
                    <input type="hidden" name="member_id" id="member_id">

                    <!-- Member Name (readonly) -->
                    <div>
                        <label for="member_name" class="block text-left text-sm font-medium text-gray-700 mt-2">
                            <i class="fa-solid fa-user text-sm mr-1.5 ml-1"></i>
                            Member Name
                        </label>
                        <input type="text" id="member_name" name="member_name"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            placeholder="Enter Member First Name"
                            {{ session('member_id') && session('member_name') ? 'readonly' : '' }}>
                        @error('member_name')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Membership Type -->
                    <div>
                        <label for="membership_type" class="block text-sm text-left font-medium text-gray-700 items-center gap-2 mt-4">
                            <i class="fa-solid fa-id-card text-sm mr-1.5 ml-1"></i>
                            Membership Type
                        </label>
                        <select id="membership_type" name="membership_type" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            onchange="setPriceFromSelect('membership_type', 'membership_price')">
                            <option value="" disabled selected>Select Membership Type</option>
                            @foreach($membershipType as $type)
                                <option value="{{ $type->type_id }}" data-price="{{ $type->price }}">
                                    {{ $type->type_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('membership_type')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="membership_price" class="block text-left text-sm font-medium text-gray-700 items-center gap-2 mt-4">
                            <i class="fa-solid fa-dollar-sign text-sm mr-1.5 ml-1"></i>
                            Price
                        </label>
                        <input id="membership_price" type="text" name="membership_price" required readonly
                            placeholder="Membership Price"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 cursor-not-allowed focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        @error('membership_price')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded" onclick="closeModal()">Cancel</button>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    {{-- Alpine Animations --}}
    <style>
        [x-cloak] { display: none; }

        .animate-fade-in {
            animation: fadeIn 0.8s ease-in-out;
        }

        .animate-slide-in {
            animation: slideIn 0.6s ease-in-out;
        }
 
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>
    
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

        @if(session('member_id') && session('member_name'))
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    openModal('{{ session('member_id') }}', '{{ session('member_name') }}');
                });
            </script>
        @endif

        <script>
            document.querySelector('form[action="{{ route('membership.cancel') }}"]').addEventListener('submit', function(e) {
                const checkboxes = document.querySelectorAll('input[name="selector[]"]:checked');
                const submitter = e.submitter;

                if (checkboxes.length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'No selection',
                        text: 'Please select at least one membership.',
                        confirmButtonColor: '#d32f2f'
                    });
                    return;
                }

                if (submitter && submitter.name === 'action' && submitter.value === 'cancel') {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this cancelation!",
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
                            hiddenInput.value = 'cancel';
                            e.target.appendChild(hiddenInput);
                            e.target.submit();
                        }
                    });
                }
            });

            function openModal(memberId = '', memberName = '') {
                const memberIdInput = document.getElementById('member_id');
                const memberNameInput = document.getElementById('member_name');

                memberIdInput.value = memberId;
                memberNameInput.value = memberName;

                if (memberId && memberName) {
                    memberNameInput.readOnly = true;
                    memberNameInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                } else {
                    memberNameInput.readOnly = false;
                    memberNameInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
                    memberNameInput.value = '';
                    memberIdInput.value = '';
                }

                document.getElementById('membership_type').selectedIndex = 0;
                document.getElementById('membership_price').value = '';

                const modal = document.getElementById('addMembershipModel');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function setCurrentDateTime() {
                const now = new Date();
                const formattedDateTime = now.toISOString();
                document.getElementById('currentDatetime').value = formattedDateTime;
            }

            function closeModal() {
                document.getElementById('addMembershipModel').classList.add('hidden');
            }
        </script>
    @endpush
@endsection
