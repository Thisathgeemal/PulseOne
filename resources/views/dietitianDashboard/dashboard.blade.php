@extends('dietitianDashboard.layout')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-2xl font-bold text-gray-900">Dietitian Dashboard</h1>
            <p class="text-gray-600 mt-1">Welcome back! Here's an overview of your nutrition consultation activities.</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <i class="fas fa-users text-emerald-500 text-2xl mr-4"></i>
                    <div>
                        <p class="text-sm text-gray-600">Active Members</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['active_members'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <i class="fas fa-utensils text-green-500 text-2xl mr-4"></i>
                    <div>
                        <p class="text-sm text-gray-600">Meals</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['meals'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <i class="fas fa-apple-alt text-teal-500 text-2xl mr-4"></i>
                    <div>
                        <p class="text-sm text-gray-600">Diet Plans</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['diet_plans'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <i class="fas fa-chart-pie text-orange-500 text-2xl mr-4"></i>
                    <div>
                        <p class="text-sm text-gray-600">Health Assessments</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['nutrition_assessments'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Member Nutrition Assessments -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-chart-pie text-orange-500 mr-2"></i>
                        Member Health Assessments
                    </h2>
                    <a href="{{ route('dietitian.member.health-assessments') }}" 
                    class="text-orange-500 hover:text-orange-700 text-sm font-medium">
                        View All →
                    </a>
                </div>
                
                <p class="text-gray-600 mb-4">Review member nutrition data before creating diet plans or consultation bookings.</p>
                
                <div class="space-y-3">
                    @forelse($recent_assessments ?? [] as $assessment)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-orange-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">
                                        {{ $assessment->member->first_name }} {{ $assessment->member->last_name }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        @if($assessment->completed_at)
                                            Completed {{ $assessment->completed_at->format('M j, Y') }}
                                        @else
                                            Updated {{ optional($assessment->updated_at)->format('M j, Y') }}
                                        @endif
                                        @if(isset($assessment->needs_update) && $assessment->completed_at && $assessment->needs_update)
                                            <span class="text-yellow-600">• Needs Update</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('dietitian.member.health-assessment', $assessment->member_id) }}" 
                            class="px-3 py-1 bg-orange-500 text-white text-sm rounded-lg hover:bg-orange-600">
                                View
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-chart-pie text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500">No nutrition assessments yet</p>
                            <p class="text-sm text-gray-400">Member nutrition assessments will appear here once completed</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Feedback (compact) -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-star text-yellow-500 mr-2"></i>
                        Recent Feedback
                    </h2>
                    <a href="{{ route('dietitian.feedback') }}" 
                    class="text-emerald-500 hover:text-emerald-700 text-sm font-medium">
                        View All →
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($recent_feedback->take(4) as $feedback)
                        <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center mt-1">
                                <i class="fas fa-star text-emerald-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="font-medium text-gray-900">
                                        {{ $feedback->fromUser->first_name }} {{ $feedback->fromUser->last_name }}
                                    </p>
                                    <div class="flex items-center">
                                        @if($feedback->rate)
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $feedback->rate ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @endfor
                                        @endif
                                    </div>
                                </div>
                                <p class="text-gray-700 mt-1 text-sm">{{ Str::limit($feedback->content, 80) }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $feedback->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-star text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500">No feedback yet</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    <!-- full-width Recent Feedback removed (compact version is displayed in the right column) -->

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-activity text-green-500 mr-2"></i>
                Recent Activity
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
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
