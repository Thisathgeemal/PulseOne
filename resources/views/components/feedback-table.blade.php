@php
    $theme = $theme ?? 'trainer';
    $isTrainer = $theme === 'trainer';
    $isDietitian = $theme === 'dietitian';

    $bgGradient = $isTrainer ? 'from-blue-50 via-white to-indigo-50' : 'from-emerald-50 via-white to-teal-50';
    $titleGradient = $isTrainer ? 'from-blue-600 to-indigo-600' : 'from-emerald-600 to-teal-600';
    $cardGradient = $isTrainer ? 'from-blue-500 to-indigo-600' : 'from-emerald-500 to-teal-600';
    $badgeColor = $isTrainer ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800';
    $textColor = $isTrainer ? 'text-blue-100' : 'text-emerald-100';
    $initialsColor = $isTrainer ? 'text-blue-600' : 'text-emerald-600';
    $statColor1 = $isTrainer ? 'bg-blue-100 text-blue-600' : 'bg-emerald-100 text-emerald-600';
    $statColor3 = $isTrainer ? 'bg-green-100 text-green-600' : 'bg-teal-100 text-teal-600';
    $feedbackType = $isTrainer ? 'Trainer Feedback' : 'Dietitian Feedback';
    $description = $isTrainer ? 'Review feedback from your members' : 'Review feedback from your members';
    $emptyIcon = $isTrainer
        ? 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'
        : 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z';
    $emptyTitle = $isTrainer ? 'No feedback yet' : 'No feedback yet';
    $emptyDesc = $isTrainer
        ? 'You haven\'t received any feedback from members yet. Keep up the great work and feedback will start coming in!'
        : 'You haven\'t received any feedback from members yet. Keep providing excellent guidance and feedback will start coming in!';
@endphp

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Box -->
        <div class="bg-black rounded-xl p-6 mb-8 shadow-lg">
            <h1 class="text-3xl font-bold text-white mb-2">My Feedbacks</h1>
            <p class="text-white opacity-90">{{ $description }}</p>
        </div>

        @if ($items->count() > 0)
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <div class="flex items-center">
                        @if ($isTrainer)
                            <div class="p-3 rounded-full bg-blue-100">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 8h10m0 0V6a2 2 0 00-2-2H9a2 2 0 00-2 2v2m10 0v10a2 2 0 01-2 2H9a2 2 0 01-2-2V8m10 0H7">
                                    </path>
                                </svg>
                            </div>
                        @else
                            <div class="p-3 rounded-full bg-emerald-100">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 8h10m0 0V6a2 2 0 00-2-2H9a2 2 0 00-2 2v2m10 0v10a2 2 0 01-2 2H9a2 2 0 01-2-2V8m10 0H7">
                                    </path>
                                </svg>
                            </div>
                        @endif
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Reviews</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $items->total() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100">
                            <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Average Rating</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ number_format($items->where('rate', '!=', null)->avg('rate'), 1) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <div class="flex items-center">
                        @if ($isTrainer)
                            <div class="p-3 rounded-full bg-green-100">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        @else
                            <div class="p-3 rounded-full bg-teal-100">
                                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        @endif
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">This Month</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $items->where('created_at', '>=', now()->startOfMonth())->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feedback Cards Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach ($items as $fb)
                    <div
                        class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 overflow-hidden">
                        <!-- Card Header -->
                        <div class="bg-gradient-to-r {{ $cardGradient }} p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 rounded-full overflow-hidden ring-2 ring-white shadow-lg">
                                        @if (!empty($fb->fromUser->profile_image))
                                            @php
                                                $img = $fb->fromUser->profile_image;
                                                // If stored value is just a filename (like "profile_xxx.jpg"),
                                                // prepend the storage path. If it already contains 'storage/' or
                                                // looks like a URL, leave it as-is.
                                                if (strpos($img, 'storage/') === false && strpos($img, 'http') === false && strpos($img, '/') === false) {
                                                    $img = 'storage/profile_images/' . $img;
                                                }
                                            @endphp
                                            <img src="{{ asset($img) }}" alt="{{ $fb->fromUser->first_name }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-white flex items-center justify-center">
                                                <span class="{{ $initialsColor }} font-bold text-lg">
                                                    {{ substr($fb->fromUser->first_name, 0, 1) }}{{ substr($fb->fromUser->last_name, 0, 1) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="text-white font-semibold text-lg">{{ $fb->fromUser->first_name }}
                                            {{ $fb->fromUser->last_name }}</h3>
                                        <p class="{{ $textColor }} text-sm">
                                            {{ \Carbon\Carbon::parse($fb->created_at)->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if ($fb->rate)
                                        <div class="flex items-center space-x-1">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $fb->rate)
                                                    <svg class="w-5 h-5 text-yellow-400 drop-shadow-sm"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                        </path>
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5 text-gray-300" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                        </path>
                                                    </svg>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="text-white text-sm font-medium">{{ $fb->rate }}/5</span>
                                    @else
                                        <span class="text-blue-200 text-sm">No rating</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-6">
                            <p class="text-gray-700 leading-relaxed">{{ $fb->content }}</p>

                            <div class="flex items-center justify-between pt-4 mt-4 border-t border-gray-100">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $badgeColor }}">
                                    {{ $feedbackType }}
                                </span>
                                <span
                                    class="text-gray-500 text-sm">{{ \Carbon\Carbon::parse($fb->created_at)->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $items->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $emptyIcon }}">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">{{ $emptyTitle }}</h3>
                <p class="text-gray-500 max-w-md mx-auto">{{ $emptyDesc }}</p>
            </div>
        @endif
    </div>
</div>
