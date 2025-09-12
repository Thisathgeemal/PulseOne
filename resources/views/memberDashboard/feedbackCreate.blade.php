@extends('memberDashboard.layout')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-gradient-to-r from-gray-800 to-black rounded-xl shadow-lg p-8 mb-8">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-white mb-2">Submit Feedback</h2>
                    <p class="text-gray-200">Share your experience with our trainers and dietitians</p>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <form method="POST" action="{{ route('member.feedback.store') }}" class="space-y-8">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Type Selection -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Type</label>
                            <select name="type" id="type"
                                class="w-full px-4 py-3 border border-gray-400 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500 transition-colors bg-white">
                                <option value="Trainer">Trainer</option>
                                <option value="Dietitian">Dietitian</option>
                                <option value="System">System</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Person Selection -->
                        <div id="personRow">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Select Person</label>
                            <select name="to_user_id" id="to_user_id"
                                class="w-full px-4 py-3 border border-gray-400 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500 transition-colors bg-white">
                                <!-- Options will be populated by JavaScript -->
                            </select>
                            @error('to_user_id')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Rating with Stars -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Rating</label>
                        <div class="flex items-center space-x-2">
                            <div class="flex space-x-1" id="star-rating">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button"
                                        class="star-btn text-gray-300 hover:text-yellow-400 transition-colors"
                                        data-rating="{{ $i }}">
                                        <svg class="w-8 h-8 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </button>
                                @endfor
                            </div>
                            <span id="rating-text" class="text-sm font-medium text-gray-600 ml-4">Click to rate</span>
                        </div>
                        <input type="hidden" name="rate" id="rate" required />
                        @error('rate')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Feedback Content -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Your Feedback</label>
                        <textarea name="content" required rows="6" placeholder="Share your thoughts and experiences..."
                            class="w-full px-4 py-3 border border-gray-400 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500 transition-colors resize-none"></textarea>
                        @error('content')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end gap-4 pt-6">
                        <a href="{{ route('member.feedback') }}"
                            class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors">
                            Submit Feedback
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Data from PHP
            const trainers = @json($trainers);
            const dietitians = @json($dietitians);

            // Elements
            const typeSelect = document.getElementById('type');
            const personRow = document.getElementById('personRow');
            const personSelect = document.getElementById('to_user_id');
            const starButtons = document.querySelectorAll('.star-btn');
            const ratingInput = document.getElementById('rate');
            const ratingText = document.getElementById('rating-text');

            // Initialize
            togglePersonDropdown();

            // Type change handler
            typeSelect.addEventListener('change', togglePersonDropdown);

            function togglePersonDropdown() {
                const selectedType = typeSelect.value;

                if (selectedType === 'System') {
                    personRow.style.display = 'none';
                    personSelect.required = false;
                } else {
                    personRow.style.display = 'block';
                    personSelect.required = true;

                    // Clear existing options
                    personSelect.innerHTML = '<option value="">Choose...</option>';

                    // Add appropriate options based on type
                    if (selectedType === 'Trainer') {
                        trainers.forEach(trainer => {
                            const option = document.createElement('option');
                            option.value = trainer.id;
                            option.textContent = `${trainer.first_name} ${trainer.last_name}`;
                            personSelect.appendChild(option);
                        });
                    } else if (selectedType === 'Dietitian') {
                        dietitians.forEach(dietitian => {
                            const option = document.createElement('option');
                            option.value = dietitian.id;
                            option.textContent = `${dietitian.first_name} ${dietitian.last_name}`;
                            personSelect.appendChild(option);
                        });
                    }
                }
            }

            // Star rating functionality
            starButtons.forEach((button, index) => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const rating = parseInt(this.dataset.rating);
                    setRating(rating);
                });

                button.addEventListener('mouseenter', function() {
                    const rating = parseInt(this.dataset.rating);
                    highlightStars(rating);
                });
            });

            // Reset star highlighting on mouse leave
            document.getElementById('star-rating').addEventListener('mouseleave', function() {
                const currentRating = parseInt(ratingInput.value) || 0;
                highlightStars(currentRating);
            });

            function setRating(rating) {
                ratingInput.value = rating;
                highlightStars(rating);
                updateRatingText(rating);
            }

            function highlightStars(rating) {
                starButtons.forEach((button, index) => {
                    if (index < rating) {
                        button.classList.remove('text-gray-300');
                        button.classList.add('text-yellow-400');
                    } else {
                        button.classList.remove('text-yellow-400');
                        button.classList.add('text-gray-300');
                    }
                });
            }

            function updateRatingText(rating) {
                const ratingTexts = {
                    1: 'Poor',
                    2: 'Fair',
                    3: 'Good',
                    4: 'Very Good',
                    5: 'Excellent'
                };
                ratingText.textContent = `${rating}/5 - ${ratingTexts[rating]}`;
            }
        </script>
    @endpush
@endsection
