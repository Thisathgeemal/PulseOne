@extends('memberDashboard.layout')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-2xl font-bold text-gray-900">Member Dashboard</h1>
            <p class="text-gray-600 mt-1">Welcome back! Here's an overview of your fitness journey and progress.</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <i class="fas fa-dumbbell text-blue-500 text-2xl mr-4"></i>
                    <div>
                        <p class="text-sm text-gray-600">Total Workouts</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_workouts'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <i class="fas fa-calendar-check text-green-500 text-2xl mr-4"></i>
                    <div>
                        <p class="text-sm text-gray-600">This Month Attendance</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['this_month_attendance'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <i class="fas fa-apple-alt text-teal-500 text-2xl mr-4"></i>
                    <div>
                        <p class="text-sm text-gray-600">Active Diet Plans</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['active_diet_plans'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <i class="fas fa-star text-orange-500 text-2xl mr-4"></i>
                    <div>
                        <p class="text-sm text-gray-600">Feedback Given</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_feedback_given'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Upcoming Sessions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                        Upcoming Sessions
                    </h2>
                    <a href="{{ route('member.bookings.sessions') }}"
                        class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                        View All →
                    </a>
                </div>

                <p class="text-gray-600 mb-4">Your scheduled training sessions and appointments.</p>

                <div class="space-y-3">
                    @forelse($upcoming_bookings ?? [] as $booking)
                        @php
                            // Handle both new start_at field and legacy date/time fields
                            // Prefer legacy date + time if present (more reliable)
                            if ($booking->date && $booking->time) {
                                // Normalize date (it may already include time '00:00:00')
                                $rawDate =
                                    $booking->date instanceof Carbon\Carbon
                                        ? $booking->date->format('Y-m-d')
                                        : substr((string) $booking->date, 0, 10); // take YYYY-MM-DD
                                $rawTime = preg_match('/^\d{2}:\d{2}(:\d{2})?$/', (string) $booking->time)
                                    ? (strlen($booking->time) === 5
                                        ? $booking->time . ':00'
                                        : $booking->time)
                                    : '00:00:00';
                                $sessionDateTime = Carbon\Carbon::createFromFormat(
                                    'Y-m-d H:i:s',
                                    $rawDate . ' ' . $rawTime,
                                    config('app.timezone'),
                                );
                            } elseif ($booking->date) {
                                $sessionDateTime = Carbon\Carbon::parse($booking->date, config('app.timezone'));
                            } elseif ($booking->start_at) {
                                $sessionDateTime = Carbon\Carbon::createFromFormat(
                                    'Y-m-d H:i:s',
                                    $booking->getRawOriginal('start_at'),
                                    'UTC',
                                )->setTimezone(config('app.timezone'));
                            } else {
                                $sessionDateTime = Carbon\Carbon::now(config('app.timezone'));
                            }

                            $now = Carbon\Carbon::now('Asia/Colombo');
                            $diffInMinutes = $now->diffInMinutes($sessionDateTime, false);
                            $diffInHours = $now->diffInHours($sessionDateTime, false);

                            $isToday = $sessionDateTime->isToday();
                            $isTomorrow = $sessionDateTime->isTomorrow();
                            $isThisWeek = $sessionDateTime->isCurrentWeek();

                            // Determine urgency level
                            $urgencyClass = 'text-blue-600';
                            $urgencyBg = 'bg-blue-50 border-blue-200';
                            $urgencyBorderColor = 'border-blue-500';

                            if ($diffInMinutes <= 60 && $diffInMinutes > 0) {
                                $urgencyClass = 'text-red-600';
                                $urgencyBg = 'bg-red-50 border-red-200';
                                $urgencyBorderColor = 'border-red-500';
                            } elseif ($diffInHours <= 3 && $diffInHours > 0) {
                                $urgencyClass = 'text-orange-600';
                                $urgencyBg = 'bg-orange-50 border-orange-200';
                                $urgencyBorderColor = 'border-orange-500';
                            } elseif ($isToday) {
                                $urgencyClass = 'text-amber-600';
                                $urgencyBg = 'bg-amber-50 border-amber-200';
                                $urgencyBorderColor = 'border-amber-500';
                            }
                        @endphp
                        <div class="p-4 {{ $urgencyBg }} rounded-xl border-2 {{ $urgencyBorderColor }} transition-all hover:shadow-lg"
                            data-session-timestamp="{{ $sessionDateTime->timestamp * 1000 }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center flex-1">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-4 shadow-sm">
                                        <i class="fas fa-user-tie text-white text-lg"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900 text-lg">
                                            {{ $booking->trainer ? $booking->trainer->first_name . ' ' . $booking->trainer->last_name : 'Trainer' }}
                                        </p>
                                        <p class="text-sm text-gray-600 mb-2">
                                            <i class="far fa-calendar-alt mr-1"></i>
                                            {{ $sessionDateTime->format('M j, Y') }}
                                            <span class="mx-2">•</span>
                                            <i class="far fa-clock mr-1"></i>
                                            {{ $sessionDateTime->format('h:i A') }}
                                        </p>

                                        <!-- Time Indicator -->
                                        <div class="flex items-center space-x-2">
                                            @if ($diffInMinutes <= 0)
                                                <span
                                                    class="px-3 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full animate-pulse">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>SESSION TIME!
                                                </span>
                                            @elseif($diffInMinutes <= 15)
                                                <span
                                                    class="px-3 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full animate-pulse">
                                                    <i class="fas fa-clock mr-1"></i>{{ $diffInMinutes }}m remaining
                                                </span>
                                            @elseif($diffInMinutes <= 60)
                                                <span
                                                    class="px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
                                                    <i class="fas fa-clock mr-1"></i>{{ $diffInMinutes }}m remaining
                                                </span>
                                            @elseif($diffInHours <= 3)
                                                <span
                                                    class="px-3 py-1 bg-orange-100 text-orange-800 text-xs font-semibold rounded-full">
                                                    <i class="fas fa-clock mr-1"></i>{{ $diffInHours }}h remaining
                                                </span>
                                            @elseif($isToday)
                                                <span
                                                    class="px-3 py-1 bg-amber-100 text-amber-800 text-xs font-semibold rounded-full">
                                                    <i class="fas fa-calendar-day mr-1"></i>Today
                                                </span>
                                            @elseif($isTomorrow)
                                                <span
                                                    class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                                    <i class="fas fa-calendar-plus mr-1"></i>Tomorrow
                                                </span>
                                            @elseif($isThisWeek)
                                                <span
                                                    class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">
                                                    <i
                                                        class="fas fa-calendar-week mr-1"></i>{{ $sessionDateTime->format('l') }}
                                                </span>
                                            @else
                                                <span
                                                    class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">
                                                    <i
                                                        class="fas fa-calendar mr-1"></i>{{ $sessionDateTime->diffForHumans() }}
                                                </span>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                                <div class="text-right flex flex-col items-end space-y-2">
                                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                        <i class="fas fa-check-circle mr-1"></i>Confirmed
                                    </span>
                                    @if ($diffInMinutes <= 60 && $diffInMinutes > 0)
                                        <div class="animate-pulse">
                                            <i class="fas fa-bell text-red-500 text-lg"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-calendar-times text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Upcoming Sessions</h3>
                            <p class="text-gray-500">Your upcoming training sessions will appear here</p>

                        </div>
                    @endforelse
                </div>
                @push('scripts')
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            function pruneSessions() {
                                const now = Date.now();
                                document.querySelectorAll('[data-session-timestamp]').forEach(card => {
                                    const ts = parseInt(card.getAttribute('data-session-timestamp'), 10);
                                    if (ts < now) {
                                        card.classList.add('opacity-0', 'scale-95', 'transition', 'duration-300');
                                        setTimeout(() => {
                                            card.remove();
                                        }, 350);
                                    }
                                });
                            }
                            pruneSessions(); // initial
                            setInterval(pruneSessions, 60000); // every minute
                        });
                    </script>
                @endpush
            </div>

            <!-- Health Assessment Status -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-chart-pie text-orange-500 mr-2"></i>
                        Health Assessment
                    </h2>
                    <a href="{{ route('member.health-assessment') }}"
                        class="text-orange-500 hover:text-orange-700 text-sm font-medium">
                        Update →
                    </a>
                </div>

                @if ($health_assessment)
                    <div class="space-y-3">
                        <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-green-900">Assessment Completed</p>
                                    <p class="text-sm text-green-700">
                                        Last updated: {{ $health_assessment->completed_at->format('M j, Y') }}
                                    </p>
                                </div>
                                <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                            </div>
                            @if ($health_assessment->needs_update)
                                <div class="mt-3 p-2 bg-yellow-50 border border-yellow-200 rounded">
                                    <p class="text-sm text-yellow-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Assessment needs updating for optimal results
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-chart-pie text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">No health assessment yet</p>
                        <p class="text-sm text-gray-400 mb-4">Complete your assessment to get personalized plans</p>
                        <a href="{{ route('member.health-assessment') }}"
                            class="px-4 py-2 bg-orange-500 text-white text-sm rounded-lg hover:bg-orange-600">
                            Complete Assessment
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Workouts -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-dumbbell text-green-500 mr-2"></i>
                    Recent Workouts
                </h2>
                <a href="{{ route('member.workoutplan.progress') }}"
                    class="text-green-500 hover:text-green-700 text-sm font-medium">
                    View All →
                </a>
            </div>

            <div class="space-y-4">
                @forelse($recent_workouts ?? [] as $workout)
                    <div class="flex items-start space-x-3 p-3 border-l-4 border-green-200 bg-green-50 rounded-r-lg">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-dumbbell text-green-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <p class="font-medium text-gray-900">
                                    {{ $workout->exercise ? $workout->exercise->name : 'Workout' }}
                                </p>
                                <span class="text-sm text-gray-600">
                                    {{ Carbon\Carbon::parse($workout->log_date)->format('M j') }}
                                </span>
                            </div>
                            <p class="text-gray-700 mt-1">
                                {{ $workout->sets_completed }} sets × {{ $workout->reps_completed }} reps
                                @if ($workout->weight)
                                    - {{ $workout->weight }}kg
                                @endif
                            </p>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ Carbon\Carbon::parse($workout->log_date)->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-dumbbell text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">No workouts logged yet</p>
                        <p class="text-sm text-gray-400">Start tracking your exercises to see progress</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Active Diet Plans -->
        @if ($active_diet_plans && $active_diet_plans->count() > 0)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-apple-alt text-teal-500 mr-2"></i>
                        Active Diet Plans
                    </h2>
                    <a href="{{ route('member.dietplan.myplan') }}"
                        class="text-teal-500 hover:text-teal-700 text-sm font-medium">
                        View All →
                    </a>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    @foreach ($active_diet_plans as $plan)
                        <div class="p-4 bg-teal-50 border border-teal-200 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-medium text-gray-900">{{ $plan->plan_name ?? 'Diet Plan' }}</h3>
                                <span class="px-2 py-1 bg-teal-100 text-teal-800 text-xs rounded-full">Active</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">
                                By:
                                {{ $plan->dietitian ? $plan->dietitian->first_name . ' ' . $plan->dietitian->last_name : 'Dietitian' }}
                            </p>
                            <p class="text-sm text-gray-600">
                                Started: {{ $plan->created_at->format('M j, Y') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-activity text-purple-500 mr-2"></i>
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
                        <p class="text-sm text-gray-400">Your activities will appear here as you use the system</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Auto-refresh upcoming sessions every 30 seconds to keep time indicators current
        function refreshUpcomingSessions() {
            const sessionsContainer = document.querySelector('.space-y-3');
            if (!sessionsContainer) return;

            // Add a subtle loading indicator
            const loadingIndicator = document.createElement('div');
            loadingIndicator.className = 'text-xs text-gray-400 text-center py-2';
            loadingIndicator.innerHTML = '<i class="fas fa-sync-alt fa-spin mr-1"></i>Updating...';

            // Only refresh if page is visible (performance optimization)
            if (document.visibilityState === 'visible') {
                // Fetch updated session data via AJAX
                fetch(window.location.href, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        // Parse the response and update only the sessions section
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newSessions = doc.querySelector('.space-y-3');

                        if (newSessions && sessionsContainer) {
                            // Smooth transition effect
                            sessionsContainer.style.opacity = '0.7';
                            setTimeout(() => {
                                sessionsContainer.innerHTML = newSessions.innerHTML;
                                sessionsContainer.style.opacity = '1';
                            }, 200);
                        }
                    })
                    .catch(error => {
                        console.log('Session refresh failed:', error);
                    });
            }
        }

        // Refresh every 30 seconds
        setInterval(refreshUpcomingSessions, 30000);

        // Also refresh when page becomes visible (user switches back to tab)
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                setTimeout(refreshUpcomingSessions, 1000);
            }
        });

        // Real-time clock update for current time awareness
        function updateCurrentTime() {
            const now = new Date();
            const timeElements = document.querySelectorAll('[data-live-time]');

            timeElements.forEach(element => {
                const targetTime = new Date(element.dataset.liveTime);
                const diff = targetTime - now;

                if (diff <= 0) {
                    element.className =
                        'px-3 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full animate-pulse';
                    element.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i>SESSION TIME!';
                }
            });
        }

        // Update time indicators every minute
        setInterval(updateCurrentTime, 60000);
    </script>
@endpush
