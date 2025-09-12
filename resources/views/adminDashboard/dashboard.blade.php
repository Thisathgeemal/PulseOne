@extends('adminDashboard.layout')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="text-gray-600 mt-1">Welcome back! Here's an overview of your system management activities.</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <i class="fas fa-users text-blue-500 text-2xl mr-4"></i>
                    <div>
                        <p class="text-sm text-gray-600">Total Members</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_members'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <i class="fas fa-dumbbell text-green-500 text-2xl mr-4"></i>
                    <div>
                        <p class="text-sm text-gray-600">Total Trainers</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_trainers'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <i class="fas fa-apple-alt text-teal-500 text-2xl mr-4"></i>
                    <div>
                        <p class="text-sm text-gray-600">Total Dietitians</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_dietitians'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <i class="fas fa-money-bill-wave text-orange-500 text-2xl mr-4"></i>
                    <div>
                        <p class="text-sm text-gray-600">Today's Revenue</p>
                        <p class="text-2xl font-bold text-gray-900">Rs.
                            {{ number_format($stats['total_payments'] ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Recent Member Registrations -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-user-plus text-blue-500 mr-2"></i>
                        Recent Registrations
                    </h2>
                    <a href="{{ route('admin.member') }}" class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                        View All →
                    </a>
                </div>

                <p class="text-gray-600 mb-4">Monitor new member registrations and manage user accounts.</p>

                <div class="space-y-3">
                    @forelse($recent_registrations ?? [] as $member)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">
                                        {{ $member->first_name }} {{ $member->last_name }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Registered {{ $member->created_at->format('M j, Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                    Active
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-user-plus text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500">No recent registrations</p>
                            <p class="text-sm text-gray-400">New member registrations will appear here</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Today's Payments -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-credit-card text-orange-500 mr-2"></i>
                        Today's Payments
                    </h2>
                    <a href="{{ route('admin.payment') }}"
                        class="text-orange-500 hover:text-orange-700 text-sm font-medium">
                        View All →
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($todays_payments ?? [] as $payment)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-money-bill text-orange-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">
                                        {{ $payment->user->first_name }} {{ $payment->user->last_name }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        {{ $payment->membershipType->type_name ?? 'N/A' }} -
                                        {{ $payment->payment_method }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">Rs. {{ number_format($payment->amount, 2) }}</p>
                                <p class="text-xs text-gray-600">
                                    {{ Carbon\Carbon::parse($payment->payment_date)->format('h:i A') }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-credit-card text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500">No payments today</p>
                            <p class="text-sm text-gray-400">Payment transactions will appear here</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- System Feedback Overview -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-star text-yellow-500 mr-2"></i>
                    Recent System Feedback
                </h2>
                <a href="{{ route('admin.feedback') }}" class="text-yellow-500 hover:text-yellow-700 text-sm font-medium">
                    Manage All →
                </a>
            </div>

            <div class="space-y-4">
                @forelse($recent_feedback as $feedback)
                    <div class="flex items-start space-x-3 p-3 border-l-4 border-yellow-200 bg-yellow-50 rounded-r-lg">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-star text-yellow-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <p class="font-medium text-gray-900">
                                    {{ $feedback->fromUser->first_name }} {{ $feedback->fromUser->last_name }}
                                    <span class="text-sm text-gray-600">
                                        →
                                        {{ $feedback->toUser ? $feedback->toUser->first_name . ' ' . $feedback->toUser->last_name : 'System' }}
                                    </span>
                                </p>
                                <div class="flex items-center">
                                    <span
                                        class="px-2 py-1 bg-{{ $feedback->type === 'Trainer' ? 'green' : ($feedback->type === 'Dietitian' ? 'blue' : 'gray') }}-100 text-{{ $feedback->type === 'Trainer' ? 'green' : ($feedback->type === 'Dietitian' ? 'blue' : 'gray') }}-800 text-xs rounded-full mr-2">
                                        {{ $feedback->type }}
                                    </span>
                                    @if ($feedback->rate)
                                        @for ($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $feedback->rate ? 'text-yellow-400' : 'text-gray-300' }}"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                </path>
                                            </svg>
                                        @endfor
                                    @endif
                                </div>
                            </div>
                            <p class="text-gray-700 mt-1">{{ Str::limit($feedback->content, 100) }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $feedback->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-star text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">No recent feedback</p>
                        <p class="text-sm text-gray-400">System feedback will appear here for monitoring</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent System Activity -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-activity text-green-500 mr-2"></i>
                Recent System Activity
            </h2>

            <div class="space-y-4">
                @forelse($recent_activities ?? [] as $activity)
                    <div class="flex items-start space-x-3 p-3 border-l-4 border-gray-200 bg-gray-50 rounded-r-lg">
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fas {{ $activity['icon'] }} text-gray-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-gray-900">{{ $activity['message'] }}</p>
                            <p class="text-sm text-gray-600">{{ $activity['time'] }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-history text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">No recent activity</p>
                        <p class="text-sm text-gray-400">System activity will appear here</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
