@extends('memberDashboard.layout')

@section('content')
    <div class="w-full max-w-xs md:max-w-7xl p-8 bg-white rounded-lg my-4 text-center shadow-md mx-auto animate-fade-in" x-data>
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <h2 class="text-xl sm:text-2xl font-bold">Membership Summary</h2>

            <div class="flex justify-between items-center space-x-3 sm:space-x-4">
                <div class="relative w-full sm:w-auto flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-0 sm:space-y-0">
                    <form method="GET" action="{{ route('member.membership') }}" class="flex flex-col sm:flex-row sm:items-center sm:space-x-4">
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
                        {{-- Global Search Input --}}
                        <div class="relative">
                            <input
                                type="text"
                                id="searchBar"
                                name="search"
                                placeholder="Search Memberships"
                                value="{{ request('search') }}"
                                class="p-1.5 pl-4 border md:w-48 rounded-full text-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                            >
                        </div>

                        {{-- Search Button --}}
                        <button type="submit"
                                class="bg-red-500 hover:bg-red-600 text-white flex items-center justify-center rounded-md w-8 h-8">
                            <i class="fas fa-search text-sm"></i>
                        </button>
                    </form>
                </div>

                <div class="relative group">
                    <form method="POST" action="{{ route('member.membership.report') }}" target="_blank" class="relative group" onsubmit="setCurrentDateTime()">
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

        <!-- Add Buttons -->
        <form method="POST">
            @csrf
            <div class="flex flex-col sm:flex-row justify-start mt-6 space-x-3">
                <button type="button" onclick="openModal()"
                    class="w-full sm:w-40 btn-primary py-2 px-4 mb-2 md:mb-0 rounded-lg">
                    Buy Membership
                </button>
            </div>

            <!-- Data Table -->
            <div class="md:overflow-x-auto mt-6 overflow-x-scroll">
                <table class="min-w-full bg-white shadow-md rounded-lg border text-base border-gray-200">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-3 px-4 border-b border-gray-300">No</th>
                            <th class="py-3 px-4 border-b border-gray-300">Membership Type</th>
                            <th class="py-3 text-left px-4 border-b border-gray-300">Start Date</th>
                            <th class="py-3 text-left px-4 border-b border-gray-300">End Date</th>
                            <th class="py-3 text-left px-4 border-b border-gray-300">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($memberships as $index => $membership)
                            <tr class="hover:bg-gray-200 transition duration-200">
                                <td class="px-4 py-3 border-b border-gray-200">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 border-b border-gray-200">{{ $membership->membershipType->type_name }}</td>
                                <td class="py-3 text-left px-4 border-b border-gray-200">{{ \Carbon\Carbon::parse($membership->start_date)->format('Y-m-d') }}</td>
                                <td class="py-3 text-left px-4 border-b border-gray-200">{{ \Carbon\Carbon::parse($membership->end_date)->format('Y-m-d') }}</td>
                                <td class="py-3 text-left px-4 border-b border-gray-200">
                                    <span class="{{ 
                                        $membership->status === 'Active' ? 'text-green-600 font-semibold' : 
                                        ($membership->status === 'Pending' ? 'text-yellow-600 font-semibold' : 'text-red-600 font-semibold') 
                                    }}">
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
        <div id="addMembershipModel" role="dialog" aria-modal="true" class="fixed inset-0 backdrop-blur-sm bg-white/20 hidden z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-[0_0_15px_4px_rgba(241,30,19,0.5)]">
                <!-- Steps -->
                <div class="flex items-center mb-4 mt-1">
                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-white step" id="step1">1</div>
                    <div class="flex-1 h-1 bg-gray-300" id="line"></div>
                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-white step" id="step2">2</div>
                </div>

                <!-- Step 1: Membership -->
                <div id="membership-section" class="form-section">
                    <h2 class="text-md md:text-2xl font-bold text-center mb-4">Buy Membership</h2>
                    <form method="POST" id="membershipForm">
                        @csrf

                        <!-- Membership Type -->
                        <div>
                            <label for="membership_type" class="block text-sm text-left font-medium text-gray-700 mt-5">
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
                        </div>

                        <!-- Price -->
                        <div>
                            <label for="membership_price" class="block text-left text-sm font-medium text-gray-700 mt-4">
                                <i class="fa-solid fa-dollar-sign text-sm mr-1.5 ml-1"></i>
                                Price
                            </label>
                            <input id="membership_price" type="text" name="membership_price" required readonly
                                placeholder="Membership Price"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 cursor-not-allowed focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        </div>

                        <div class="flex justify-end space-x-2 mt-5">
                            <button type="button" class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded" onclick="closeModal()">Cancel</button>
                            <button type="button" class="btn-primary px-4 py-2 rounded" onclick="nextStep()">Next</button>
                        </div>
                    </form>
                </div>
                
                <!-- Step 2: Payment -->
                <div id="payment-section" class="form-section hidden">   
                    <h2 class="text-md md:text-2xl font-bold text-center mb-4" id="modalHeader">Complete Payment</h2>
                    <form method="POST" class="space-y-6" action="{{ route('member.membership.buy') }}">
                        @csrf

                         <input type="hidden" name="membership_type" id="hidden_membership_type">
                        <input type="hidden" name="membership_price" id="hidden_membership_price">

                        <!-- Accepted Cards -->
                        <div class="flex flex-col items-start">
                            <label class="block text-sm font-medium mb-1">Accepted Cards:</label>
                            <div class="flex space-x-4 mt-2">
                                <img src="{{ asset('images/card-1.webp') }}" alt="Visa" class="h-8 border-1 border-black transition-transform duration-200 hover:scale-110 hover:shadow-lg cursor-pointer"
                                    onclick="selectCardType('visa')">
                                <img src="{{ asset('images/card-2.webp') }}" alt="MasterCard" class="h-8 border-1 border-black transition-transform duration-200 hover:scale-110 hover:shadow-lg cursor-pointer"
                                    onclick="selectCardType('mastercard')">
                                <img src="{{ asset('images/card-3.webp') }}" alt="American Express" class="h-8 border-1 border-black transition-transform duration-200 hover:scale-110 hover:shadow-lg cursor-pointer"
                                    onclick="selectCardType('amex')">
                                <img src="{{ asset('images/card-4.webp') }}" alt="Discover" class="h-8 border-1 border-black transition-transform duration-200 hover:scale-110 hover:shadow-lg cursor-pointer"
                                    onclick="selectCardType('discover')">
                            </div>
                        </div>

                        <!-- Card Type -->
                        <div>
                            <label for="card_type" class="block text-sm text-left font-medium text-gray-700 mt-4">
                                <i class="fa-solid fa-credit-card text-sm mr-1.5 ml-1"></i>
                                Card Type
                            </label>
                            <select id="card_type" name="card_type" required autofocus
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                <option value="" disabled selected>Select card type</option>
                                <option value="visa" {{ old('card_type') == 'visa' ? 'selected' : '' }}>Visa</option>
                                <option value="mastercard" {{ old('card_type') == 'mastercard' ? 'selected' : '' }}>MasterCard</option>
                                <option value="amex" {{ old('card_type') == 'amex' ? 'selected' : '' }}>American Express</option>
                                <option value="discover" {{ old('card_type') == 'discover' ? 'selected' : '' }}>Discover</option>
                            </select>
                            @error('card_type')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Name on Card -->
                        <div class="mb-0">
                            <label for="card_name" class="block text-left text-sm font-medium text-gray-700 mt-3">
                                <i class="fa-solid fa-user text-sm mr-1.5 ml-1"></i>
                                Name on Card
                            </label>
                            <input id="card_name" type="text" name="card_name" required autofocus
                                placeholder="Enter name on card"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                                value="{{ old('card_name') }}">
                            @error('card_name')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Card Number -->
                        <div class="mb-0">
                            <label for="card_number" class="block text-left text-sm font-medium text-gray-700 mt-3">
                                <i class="fa-solid fa-hashtag text-sm mr-1.5 ml-1"></i>
                                Card Number
                            </label>
                            <input id="card_number" type="text" name="card_number" required autofocus maxlength="16"
                                placeholder="1234 5678 9012 3456"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                                value="{{ old('card_number') }}">
                            @error('card_number')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- CVV -->
                        <div class="mb-0">
                            <label for="cvv" class="block text-sm text-left font-medium text-gray-700 mt-3">
                                <i class="fa-solid fa-lock text-sm mr-1.5 ml-1"></i>
                                CVV
                            </label>
                            <input id="cvv" type="text" name="cvv" required autofocus maxlength="3"
                                placeholder="CVV"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                                value="{{ old('cvv') }}">
                            @error('cvv')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Expiry Month and Year -->
                        <div class="grid grid-cols-2 gap-4 mt-3 mb-0">
                            <div>
                                <label for="expiry_month" class="block text-left text-sm font-medium text-gray-700">
                                    <i class="fa-regular fa-calendar text-sm mr-1.5 ml-1"></i>
                                    Expiry Month
                                </label>
                                <select id="expiry_month" name="expiry_month" required autofocus
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                    <option value="" disabled selected>MM</option>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ sprintf('%02d', $m) }}" {{ old('expiry_month') == sprintf('%02d', $m) ? 'selected' : '' }}>
                                            {{ sprintf('%02d', $m) }}
                                        </option>
                                    @endfor
                                </select>
                                @error('expiry_month')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="expiry_year" class="block text-left text-sm font-medium text-gray-700">
                                    <i class="fa-regular fa-calendar-days text-sm mr-1.5 ml-1"></i>
                                    Expiry Year
                                </label>
                                <select id="expiry_year" name="expiry_year" required autofocus
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                    <option value="" disabled selected>YYYY</option>
                                    @for ($y = date('Y'); $y <= date('Y') + 15; $y++)
                                        <option value="{{ $y }}" {{ old('expiry_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                                @error('expiry_year')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-2 mt-5">
                            <button type="button" class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded" onclick="backStep()">Previous</button>
                            <button type="submit" class="btn-primary px-4 py-2 rounded">Buy Now</button>
                        </div>
                    </form>
                </div>
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

        <script>
            function openModal() {
                document.getElementById('addMembershipModel').classList.remove('hidden');
                highlightStep(1);
            }

            function closeModal() {
                document.getElementById('addMembershipModel').classList.add('hidden');
                resetSteps();
                document.getElementById('membership-section').classList.remove('hidden');
                document.getElementById('payment-section').classList.add('hidden');
            }

            function nextStep() {
                const membershipType = document.getElementById('membership_type').value;
                const membershipPrice = document.getElementById('membership_price').value;

                if (!membershipType) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please select a membership type before proceeding.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d32f2f'
                    });
                    return;
                }

                document.getElementById('hidden_membership_type').value = membershipType;
                document.getElementById('hidden_membership_price').value = membershipPrice;

                document.getElementById('membership-section').classList.add('hidden');
                document.getElementById('payment-section').classList.remove('hidden');
                highlightStep(2);
            }

            function backStep() {
                document.getElementById('payment-section').classList.add('hidden');
                document.getElementById('membership-section').classList.remove('hidden');
                highlightStep(1);
            }

            function highlightStep(step) {
                const step1 = document.getElementById('step1');
                const step2 = document.getElementById('step2');
                const line = document.getElementById('line');

                step1.classList.remove('bg-blue-500');
                step1.classList.add('bg-gray-300');
                step2.classList.remove('bg-blue-500');
                step2.classList.add('bg-gray-300');
                line.classList.remove('bg-blue-500');
                line.classList.add('bg-gray-300');

                if (step === 1) {
                    step1.classList.remove('bg-gray-300');
                    step1.classList.add('bg-blue-500');
                } else if (step === 2) {
                    step1.classList.remove('bg-gray-300');
                    step2.classList.remove('bg-gray-300');
                    line.classList.remove('bg-gray-300');
                    step1.classList.add('bg-blue-500');
                    step2.classList.add('bg-blue-500');
                    line.classList.add('bg-blue-500');
                }
            }

            function resetSteps() {
                const step1 = document.getElementById('step1');
                const step2 = document.getElementById('step2');
                const line = document.getElementById('line');
                step1.classList.remove('bg-blue-500');
                step2.classList.remove('bg-blue-500');
                line.classList.remove('bg-blue-500');

                const membershipForm = document.getElementById('membershipForm');
                if (membershipForm) {
                    membershipForm.reset();
                }

                const paymentForm = document.querySelector('#payment-section form');
                if (paymentForm) {
                    paymentForm.reset();
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
