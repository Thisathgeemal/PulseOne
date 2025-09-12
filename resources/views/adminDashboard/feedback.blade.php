@extends('adminDashboard.layout')

@section('content')
    <div class="min-h-screen bg-gradient-to-br bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Modern Header with Dashboard Stats -->
            <div class="mb-8">
                <div class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r bg-black px-8 py-6">
                        <h1 class="text-2xl font-bold text-white">Feedback Dashboard</h1>
                        <p class="text-sm text-gray-300 mt-1">Real-time feedback monitoring and management</p>
                    </div>

                    <!-- Quick Stats Bar -->
                    <div class="grid grid-cols-2 md:grid-cols-4 border-t border-b border-gray-100">
                        <div class="p-6 text-center border-r border-gray-100">
                            <div class="text-2xl font-bold text-gray-900">{{ $items->total() }}</div>
                            <div class="text-sm text-gray-500 font-medium">Total</div>
                        </div>
                        <div class="p-6 text-center border-r border-gray-100">
                            <div class="text-2xl font-bold text-emerald-600">{{ $items->where('is_visible', 1)->count() }}
                            </div>
                            <div class="text-sm text-gray-500 font-medium">Visible</div>
                        </div>
                        <div class="p-6 text-center border-r border-gray-100">
                            <div class="text-2xl font-bold text-amber-500">
                                {{ number_format($items->where('rate', '!=', null)->avg('rate'), 1) }}</div>
                            <div class="text-sm text-gray-500 font-medium">Avg Rating</div>
                        </div>
                        <div class="p-6 text-center">
                            <div class="text-2xl font-bold text-red-500">{{ $items->where('is_visible', 0)->count() }}</div>
                            <div class="text-sm text-gray-500 font-medium">Hidden</div>
                        </div>
                    </div>

                    <!-- Enhanced Filter Controls -->
                    <div class="p-6">
                        <form method="GET" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                <div class="md:col-span-2">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                        <input name="q" value="{{ request('q') }}"
                                            placeholder="Search feedback content..."
                                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-red-500 focus:border-red-500 transition-all">
                                    </div>
                                </div>
                                <select name="type"
                                    class="px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-red-500 focus:border-red-500 transition-all bg-white">
                                    <option value="">All Types</option>
                                    @foreach (['Trainer', 'Dietitian', 'System'] as $t)
                                        <option value="{{ $t }}" @selected(request('type') === $t)>{{ $t }}
                                        </option>
                                    @endforeach
                                </select>
                                <select name="rate"
                                    class="px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-red-500 focus:border-red-500 transition-all bg-white">
                                    <option value="">All Ratings</option>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" @selected(request('rate') == $i)>{{ $i }}
                                            Star{{ $i > 1 ? 's' : '' }}</option>
                                    @endfor
                                </select>
                                <select name="visibility"
                                    class="px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-red-500 focus:border-red-500 transition-all bg-white">
                                    <option value="">All Status</option>
                                    <option value="1" @selected(request('visibility') === '1')>Visible</option>
                                    <option value="0" @selected(request('visibility') === '0')>Hidden</option>
                                </select>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit"
                                    class="px-6 py-3 bg-gradient-to-r bg-red-500 hover:bg-red-600 text-white font-medium rounded-xl transition-all shadow-lg hover:shadow-xl">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z">
                                        </path>
                                    </svg>
                                    Apply Filters
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Compact Modern Feedback List -->
            <div class="space-y-3">
                @foreach ($items as $fb)
                    <div
                        class="bg-white rounded-xl border border-gray-200 hover:border-gray-300 transition-all hover:shadow-lg group">
                        <div class="p-6">
                            <div class="flex items-start justify-between">

                                <!-- Left: Main Content -->
                                <div class="flex-1 min-w-0 pr-6">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <!-- Type Badge -->
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                    {{ $fb->type === 'Trainer'
                        ? 'bg-blue-100 text-blue-800'
                        : ($fb->type === 'Dietitian'
                            ? 'bg-green-100 text-green-800'
                            : 'bg-purple-100 text-purple-800') }}">
                                            {{ $fb->type }}
                                        </span>

                                        <!-- Rating Stars -->
                                        @if ($fb->rate)
                                            <div class="flex items-center">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $fb->rate ? 'text-yellow-400' : 'text-gray-200' }}"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endfor
                                                <span
                                                    class="ml-2 text-sm text-gray-600 font-medium">{{ $fb->rate }}/5</span>
                                            </div>
                                        @endif

                                        <!-- Date -->
                                        <span
                                            class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($fb->created_at)->format('M d, Y') }}</span>
                                    </div>

                                    <!-- Feedback Content -->
                                    <p class="text-gray-900 text-sm leading-relaxed mb-3 line-clamp-2">{{ $fb->content }}
                                    </p>

                                    <!-- User Info -->
                                    <div class="flex items-center space-x-6 text-xs text-gray-600">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                </path>
                                            </svg>
                                            <span class="font-medium">{{ $fb->fromUser->first_name }}
                                                {{ $fb->fromUser->last_name }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>to
                                                {{ $fb->toUser ? $fb->toUser->first_name . ' ' . $fb->toUser->last_name : 'System' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right: Status & Actions -->
                                <div class="flex flex-col items-end space-y-3">
                                    <!-- Visibility Status -->
                                    <div class="flex items-center space-x-2">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                    {{ $fb->is_visible ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                @if ($fb->is_visible)
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                    </path>
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464m4.242 4.242L9.878 9.878m4.242 4.242c.415.415.415.415 1.061 1.061m-1.061-1.061L13.293 14.05">
                                                    </path>
                                                @endif
                                            </svg>
                                            {{ $fb->is_visible ? 'Visible' : 'Hidden' }}
                                        </span>
                                    </div>

                                    <!-- Toggle Action -->
                                    <form method="POST" action="{{ route('admin.feedback.toggle', $fb->feedback_id) }}"
                                        class="inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="is_visible" value="{{ $fb->is_visible ? 0 : 1 }}">
                                        <button type="submit"
                                            class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-medium transition-all
                    {{ $fb->is_visible
                        ? 'bg-gray-100 hover:bg-gray-200 text-gray-700 hover:text-gray-900'
                        : 'bg-emerald-100 hover:bg-emerald-200 text-emerald-700 hover:text-emerald-900' }}">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                @if ($fb->is_visible)
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464m4.242 4.242L9.878 9.878m4.242 4.242c.415.415.415.415 1.061 1.061m-1.061-1.061L13.293 14.05">
                                                    </path>
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                    </path>
                                                @endif
                                            </svg>
                                            {{ $fb->is_visible ? 'Hide' : 'Show' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8 flex justify-center">
                {{ $items->links() }}
            </div>
        </div>
    </div>
@endsection
