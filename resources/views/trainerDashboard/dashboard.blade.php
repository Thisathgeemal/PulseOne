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
                    <i class="fas fa-users text-emerald-500 text-2xl mr-4"></i>
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
                        <p class="text-sm text-gray-600">Upcoming Sessions</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['upcoming_sessions'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <i class="fas fa-dumbbell text-teal-500 text-2xl mr-4"></i>
                    <div>
                        <p class="text-sm text-gray-600">Workout Plans</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['workout_plans'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <i class="fas fa-chart-pie text-orange-500 text-2xl mr-4"></i>
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
                        <i class="fas fa-chart-pie text-orange-500 mr-2"></i>
                        Member Health Assessments
                    </h2>
                    <a href="{{ route('trainer.member.health-assessments') ?? '#' }}"
                        class="text-orange-500 hover:text-orange-700 text-sm font-medium">
                        View All →
                    </a>
                </div>

                <p class="text-gray-600 mb-4">Review member health data before creating workout plans or approving bookings.
                </p>

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
                                        @if ($assessment->completed_at)
                                            Completed {{ $assessment->completed_at->format('M j, Y') }}
                                        @else
                                            Updated {{ optional($assessment->updated_at)->format('M j, Y') }}
                                        @endif
                                        @if (isset($assessment->needs_update) && $assessment->completed_at && $assessment->needs_update)
                                            <span class="text-yellow-600">• Needs Update</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('trainer.member.health-assessment', $assessment->member_id) ?? '#' }}"
                                class="px-3 py-1 bg-orange-500 text-white text-sm rounded-lg hover:bg-orange-600">
                                View
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-chart-pie text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500">No health assessments yet</p>
                            <p class="text-sm text-gray-400">Member health assessments will appear here once completed</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Upcoming Sessions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-calendar-alt text-emerald-500 mr-2"></i>
                        Upcoming Sessions
                    </h2>
                    <a href="{{ route('trainer.bookings.sessions') ?? '#' }}"
                        class="text-emerald-500 hover:text-emerald-700 text-sm font-medium">
                        View All →
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($upcoming_bookings ?? [] as $booking)
                        @php
                            // Build session datetime (legacy date/time first)
                            if ($booking->date && $booking->time) {
                                $rawDate =
                                    $booking->date instanceof \Carbon\Carbon
                                        ? $booking->date->format('Y-m-d')
                                        : substr((string) $booking->date, 0, 10);
                                $rawTime = preg_match('/^\d{2}:\d{2}(:\d{2})?$/', (string) $booking->time)
                                    ? (strlen($booking->time) == 5
                                        ? $booking->time . ':00'
                                        : $booking->time)
                                    : '00:00:00';
                                $sessionDateTime = \Carbon\Carbon::createFromFormat(
                                    'Y-m-d H:i:s',
                                    $rawDate . ' ' . $rawTime,
                                    config('app.timezone'),
                                );
                            } elseif ($booking->start_at) {
                                $sessionDateTime = \Carbon\Carbon::createFromFormat(
                                    'Y-m-d H:i:s',
                                    $booking->getRawOriginal('start_at'),
                                    'UTC',
                                )->setTimezone(config('app.timezone'));
                            } else {
                                $sessionDateTime = \Carbon\Carbon::now();
                            }
                            $now = \Carbon\Carbon::now(config('app.timezone'));
                            $diffMinutes = $now->diffInMinutes($sessionDateTime, false);
                            $diffHours = $now->diffInHours($sessionDateTime, false);
                            $isToday = $sessionDateTime->isToday();
                            $isTomorrow = $sessionDateTime->isTomorrow();
                            $badge = null;
                            $badgeClass = 'bg-gray-100 text-gray-700';
                            if ($diffMinutes <= 0) {
                                $badge = ['SESSION', 'bg-red-100 text-red-700 font-bold animate-pulse'];
                            } elseif ($diffMinutes <= 15) {
                                $badge = [$diffMinutes . 'm', 'bg-red-100 text-red-700 animate-pulse'];
                            } elseif ($diffMinutes <= 60) {
                                $badge = [$diffMinutes . 'm', 'bg-red-100 text-red-700'];
                            } elseif ($diffHours <= 3) {
                                $badge = [$diffHours . 'h', 'bg-orange-100 text-orange-700'];
                            } elseif ($isToday) {
                                $badge = ['Today', 'bg-amber-100 text-amber-700'];
                            } elseif ($isTomorrow) {
                                $badge = ['Tomorrow', 'bg-emerald-600 text-emerald-50'];
                            } else {
                                $badge = [$sessionDateTime->format('M j'), 'bg-gray-100 text-gray-700'];
                            }
                        @endphp
                        <div class="p-4 bg-gray-50 rounded-lg flex items-center justify-between border border-emerald-100"
                            data-session-timestamp="{{ $sessionDateTime->timestamp * 1000 }}">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-emerald-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $booking->member->first_name }}
                                        {{ $booking->member->last_name }}</p>
                                    <p class="text-sm text-gray-600 flex items-center space-x-2">
                                        <span><i
                                                class="far fa-calendar-alt mr-1"></i>{{ $sessionDateTime->format('M j, Y') }}</span>
                                        <span class="mx-1">•</span>
                                        <span><i
                                                class="far fa-clock mr-1"></i>{{ $sessionDateTime->format('h:i A') }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-col items-end space-y-2">
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Confirmed</span>
                                @if ($badge)
                                    <span
                                        class="px-2 py-1 text-xs rounded-full {{ $badge[1] }}">{{ $badge[0] }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-calendar text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500">No upcoming sessions</p>
                            <p class="text-sm text-gray-400">Approved future sessions will appear here</p>
                        </div>
                    @endforelse
                </div>
                @push('scripts')
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            function prune() {
                                const now = Date.now();
                                document.querySelectorAll('[data-session-timestamp]').forEach(el => {
                                    const ts = parseInt(el.getAttribute('data-session-timestamp'));
                                    if (ts < now) {
                                        el.classList.add('opacity-0', 'transition');
                                        setTimeout(() => el.remove(), 300);
                                    }
                                });
                            }
                            prune();
                            setInterval(prune, 60000);
                        });
                    </script>
                @endpush
            </div>
        </div>

        <!-- Recent Feedback -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-star text-yellow-500 mr-2"></i>
                    Recent Feedback
                </h2>
                <a href="{{ route('trainer.feedback') ?? '#' }}"
                    class="text-emerald-500 hover:text-emerald-700 text-sm font-medium">
                    View All →
                </a>
            </div>

            <div class="space-y-4">
                @forelse($recent_feedback as $feedback)
                    <div class="flex items-start space-x-3 p-3 border-l-4 border-emerald-200 bg-emerald-50 rounded-r-lg">
                        <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-star text-emerald-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <p class="font-medium text-gray-900">
                                    {{ $feedback->fromUser->first_name }} {{ $feedback->fromUser->last_name }}
                                </p>
                                <div class="flex items-center">
                                    @if ($feedback->rate)
                                        @for ($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $feedback->rate ? 'text-yellow-400' : 'text-gray-300' }}"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                </path>
                                            </svg>
                                        @endfor
                                    @else
                                        @for ($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
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
                        <p class="text-gray-500">No feedback yet</p>
                        <p class="text-sm text-gray-400">Member feedback will appear here once received</p>
                    </div>
                @endforelse
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
