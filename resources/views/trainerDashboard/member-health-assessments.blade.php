@extends('trainerDashboard.layout')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Member Health Assessments</h1>
                    <p class="text-gray-600 mt-1">Review member health data for creating personalized workout plans and training guidance.</p>
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
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Search by name..." 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500">
                </div>
                <div class="min-w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Experience Level</label>
                    <select name="experience" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500">
                        <option value="">All Levels</option>
                        <option value="beginner" {{ request('experience') === 'beginner' ? 'selected' : '' }}>Beginner</option>
                        <option value="intermediate" {{ request('experience') === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="advanced" {{ request('experience') === 'advanced' ? 'selected' : '' }}>Advanced</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                    <a href="{{ route('trainer.member.health-assessments') }}" 
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-times mr-2"></i>Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Assessment Cards -->
        <div class="grid gap-6">
            @forelse($assessments as $assessment)
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 {{ $assessment->doctor_clearance ? 'border-red-400' : ($assessment->needs_update ? 'border-yellow-400' : 'border-green-400') }}">
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
                                    @if($assessment->doctor_clearance)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Medical Concerns
                                        </span>
                                    @endif
                                    
                                    @if($assessment->needs_update)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            Needs Update
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
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
                                        <p class="font-medium">{{ $assessment->bmi }} ({{ $assessment->bmi_category }})</p>
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
                                
                                <!-- Fitness Goals -->
                                @if($assessment->fitness_goals)
                                    <div class="mt-3">
                                        <p class="text-gray-600 text-sm mb-2">Fitness Goals:</p>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach(array_slice($assessment->fitness_goals, 0, 3) as $goal)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ ucwords(str_replace('_', ' ', $goal)) }}
                                                </span>
                                            @endforeach
                                            @if(count($assessment->fitness_goals) > 3)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                    +{{ count($assessment->fitness_goals) - 3 }} more
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Medical Alerts -->
                                @if($assessment->doctor_clearance)
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
                            <a href="{{ route('trainer.member.health-assessment', $assessment->member_id) }}" 
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
                        @if(request()->filled('search') || request()->filled('experience'))
                            No assessments match your current filters. Try adjusting your search criteria.
                        @else
                            Members haven't completed their health assessments yet.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($assessments->hasPages())
            <div class="bg-white rounded-lg shadow-sm p-6">
                {{ $assessments->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

@endsection
