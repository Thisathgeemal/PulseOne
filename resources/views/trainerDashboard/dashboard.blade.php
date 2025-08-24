@extends('trainerDashboard.layout')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-2xl font-bold text-gray-900">Trainer Dashboard</h1>
            <p class="text-gray-600 mt-1">Welcome back! Here's an overview of your training activities.</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <i class="fas fa-users text-blue-500 text-2xl mr-4"></i>
                    <div>
                        <p class="text-sm text-gray-600">Active Members</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['active_members'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <i class="fas fa-calendar-check text-green-500 text-2xl mr-4"></i>
                    <div>
                        <p class="text-sm text-gray-600">Today's Sessions</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['todays_sessions'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <i class="fas fa-dumbbell text-purple-500 text-2xl mr-4"></i>
                    <div>
                        <p class="text-sm text-gray-600">Workout Plans</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['workout_plans'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <i class="fas fa-heart text-red-500 text-2xl mr-4"></i>
                    <div>
                        <p class="text-sm text-gray-600">Health Assessments</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['health_assessments'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Member Health Assessments -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-notes-medical text-red-500 mr-2"></i>
                        Member Health Assessments
                    </h2>
                    <a href="{{ route('trainer.member.health-assessments') }}" 
                    class="text-red-500 hover:text-red-700 text-sm font-medium">
                        View All →
                    </a>
                </div>
                
                <p class="text-gray-600 mb-4">Review member health data before creating workout plans or approving bookings.</p>
                
                <div class="space-y-3">
                    @forelse($recent_assessments ?? [] as $assessment)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-red-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">
                                        {{ $assessment->member->first_name }} {{ $assessment->member->last_name }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Completed {{ $assessment->completed_at->format('M j, Y') }}
                                        @if($assessment->needs_update)
                                            <span class="text-yellow-600">• Needs Update</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('trainer.member.health-assessment', $assessment->member_id) }}" 
                            class="px-3 py-1 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600">
                                View
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-notes-medical text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500">No health assessments yet</p>
                            <p class="text-sm text-gray-400">Member assessments will appear here once completed</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Today's Schedule -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                        Today's Schedule
                    </h2>
                    <a href="{{ route('trainer.bookings.sessions') ?? '#' }}" 
                    class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                        View All →
                    </a>
                </div>
                
                <div class="space-y-3">
                    @forelse($todays_bookings ?? [] as $booking)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-clock text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">
                                        {{ $booking->member->first_name }} {{ $booking->member->last_name }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        {{ $booking->session_date->format('g:i A') }} - {{ $booking->session_type }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                    Confirmed
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-calendar text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500">No sessions today</p>
                            <p class="text-sm text-gray-400">Your schedule is clear for today</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

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
