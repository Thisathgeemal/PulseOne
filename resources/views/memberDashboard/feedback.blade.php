@extends('memberDashboard.layout')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Box -->
            <div class="bg-black rounded-xl p-6 mb-8 shadow-lg">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">My Feedback</h1>
                        <p class="text-white opacity-90">Track and manage your submitted feedback</p>
                    </div>
                    <a href="{{ route('member.feedback.create') }}"
                        class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">+
                        New Feedback</a>
                </div>
            </div>

            @if ($items->count() > 0)
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 8h10m0 0V6a2 2 0 00-2-2H9a2 2 0 00-2 2v2m10 0v10a2 2 0 01-2 2H9a2 2 0 01-2-2V8m10 0H7">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Feedback</p>
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
                                    {{ number_format($items->where('rate', '!=', null)->avg('rate') ?: 0, 1) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Visible Feedback</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $items->where('is_visible', true)->count() }}
                                </p>
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
                            <div class="bg-gradient-to-r from-red-500 to-red-600 p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 rounded-full overflow-hidden ring-2 ring-white shadow-lg">
                                            @if ($fb->toUser && $fb->toUser->profile_image)
                                                <img src="{{ asset('storage/profile_images/' . $fb->toUser->profile_image) }}"
                                                    alt="{{ $fb->toUser->first_name ?? 'System' }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full bg-white flex items-center justify-center">
                                                    <span class="text-red-600 font-bold text-lg">
                                                        @if ($fb->toUser)
                                                            {{ substr($fb->toUser->first_name, 0, 1) }}{{ substr($fb->toUser->last_name, 0, 1) }}
                                                        @else
                                                            S
                                                        @endif
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h3 class="text-white font-semibold text-lg">
                                                @if ($fb->toUser)
                                                    {{ $fb->toUser->first_name }} {{ $fb->toUser->last_name }}
                                                @else
                                                    System
                                                @endif
                                            </h3>
                                            <p class="text-red-100 text-sm">
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
                                            <span class="text-red-200 text-sm">No rating</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-6">
                                <p class="text-gray-700 leading-relaxed">{{ $fb->content }}</p>

                                <div class="flex items-center justify-between pt-4 mt-4 border-t border-gray-100">
                                    <div class="flex items-center space-x-3">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                            {{ $fb->type }}
                                        </span>
                                        <span
                                            class="px-2 py-1 rounded text-sm {{ $fb->is_visible ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                            {{ $fb->is_visible ? 'Visible' : 'Hidden by Admin' }}
                                        </span>
                                    </div>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 8h10m0 0V6a2 2 0 00-2-2H9a2 2 0 00-2 2v2m10 0v10a2 2 0 01-2 2H9a2 2 0 01-2-2V8m10 0H7">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">No feedback yet</h3>
                    <p class="text-gray-500 max-w-md mx-auto">You haven't submitted any feedback yet. Share your thoughts
                        and experiences with our trainers and dietitians!</p>
                    <div class="mt-6">
                        <a href="{{ route('member.feedback.create') }}"
                            class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">Submit
                            Your First Feedback</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
