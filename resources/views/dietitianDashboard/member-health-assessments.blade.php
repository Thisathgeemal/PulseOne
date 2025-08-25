@extends('dietitianDashboard.layout')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mt-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Member Health Assessments</h1>
                    <p class="text-gray-600 mt-1">Review member health data for nutritional planning and dietary guidance.
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Total Assessments</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $assessments->total() }}</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-64">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Member</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500">
                </div>
                <div class="min-w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dietary Needs</label>
                    <select name="dietary_filter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500">
                        <option value="">All Members</option>
                        <option value="restrictions" {{ request('dietary_filter') === 'restrictions' ? 'selected' : '' }}>
                            Has Dietary Restrictions</option>
                        <option value="allergies" {{ request('dietary_filter') === 'allergies' ? 'selected' : '' }}>Has
                            Allergies</option>
                        <option value="medical" {{ request('dietary_filter') === 'medical' ? 'selected' : '' }}>Medical
                            Conditions</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                    <a href="{{ route('dietitian.member.health-assessments') }}"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-times mr-2"></i>Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Assessment Cards -->
        <div class="grid gap-6">
            @forelse($assessments as $assessment)
                <div
                    class="bg-white rounded-lg shadow-sm p-6 border-l-4 {{ $assessment->doctor_clearance ? 'border-red-400' : ($assessment->needs_update ? 'border-yellow-400' : 'border-green-400') }}">
                    <div class="flex items-start justify-between">
                        <!-- Member Info -->
                        <div class="flex items-start space-x-4">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-gray-600 text-xl"></i>
                            </div>

                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h3 class="text-xl font-semibold text-gray-900">
                                        {{ $assessment->member->first_name }} {{ $assessment->member->last_name }}
                                    </h3>

                                    <!-- Status Badges -->
                                    @if ($assessment->doctor_clearance)
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Medical Concerns
                                        </span>
                                    @endif

                                    @if ($assessment->needs_update)
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            Needs Update
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Current
                                        </span>
                                    @endif
                                </div>

                                <!-- Quick Stats -->
                                <div class="grid md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-600">Age</p>
                                        <p class="font-medium">{{ $assessment->age }} years</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">BMI</p>
                                        <p class="font-medium">{{ $assessment->bmi }} ({{ $assessment->bmi_category }})
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">Experience</p>
                                        <p class="font-medium capitalize">{{ $assessment->exercise_experience }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">Completed</p>
                                        <p class="font-medium">{{ $assessment->completed_at->format('M j, Y') }}</p>
                                    </div>
                                </div>

                                <!-- Dietary Information -->
                                <div class="mt-4">
                                    <div class="flex flex-wrap gap-1">
                                        @if ($assessment->dietary_restrictions && count($assessment->dietary_restrictions) > 0)
                                            @foreach (array_slice($assessment->dietary_restrictions, 0, 2) as $restriction)
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    {{ ucfirst(str_replace('_', ' ', $restriction)) }}
                                                </span>
                                            @endforeach
                                            @if (count($assessment->dietary_restrictions) > 2)
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                    +{{ count($assessment->dietary_restrictions) - 2 }} more restrictions
                                                </span>
                                            @endif
                                        @endif

                                        @if ($assessment->allergies && count($assessment->allergies) > 0)
                                            @foreach (array_slice($assessment->allergies, 0, 2) as $allergy)
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    {{ ucfirst(str_replace('_', ' ', $allergy)) }}
                                                </span>
                                            @endforeach
                                            @if (count($assessment->allergies) > 2)
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                    +{{ count($assessment->allergies) - 2 }} more allergies
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                <!-- Medical Alerts -->
                                @if ($assessment->doctor_clearance)
                                    <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                                        <p class="text-red-800 font-medium text-sm">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Medical concerns detected - Review full assessment before training
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-col space-y-2">
                            <a href="{{ route('dietitian.member.health-assessment', $assessment->member_id) }}"
                                class="px-4 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 text-center">
                                <i class="fas fa-eye mr-2"></i>View Full Assessment
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Health Assessments Found</h3>
                    <p class="text-gray-600">
                        @if (request()->filled('search') || request()->filled('experience'))
                            No assessments match your current filters. Try adjusting your search criteria.
                        @else
                            Members haven't completed their health assessments yet.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($assessments->hasPages())
            <div class="bg-white rounded-lg shadow-sm p-6">
                {{ $assessments->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection
