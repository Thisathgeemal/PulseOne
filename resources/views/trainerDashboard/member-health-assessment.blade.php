@extends('trainerDashboard.layout')

@section('content')
    <div class="p-6 max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Member Health Assessment</h1>
                    <p class="text-gray-600 mt-1">
                        {{ $assessment->member->first_name }} {{ $assessment->member->last_name }}
                    </p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1"></i>
                        Completed {{ $assessment->completed_at->format('M j, Y') }}
                    </span>
                    @if($assessment->needs_update)
                        <p class="text-xs text-yellow-600 mt-1">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Assessment is over 6 months old
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <i class="fas fa-birthday-cake text-blue-500 text-xl mr-3"></i>
                    <div>
                        <p class="text-sm text-gray-600">Age</p>
                        <p class="text-lg font-semibold">{{ $assessment->age ?? 'N/A' }} years</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <i class="fas fa-calculator text-green-500 text-xl mr-3"></i>
                    <div>
                        <p class="text-sm text-gray-600">BMI</p>
                        <p class="text-lg font-semibold">
                            {{ $assessment->bmi ?? 'N/A' }}
                            <span class="text-sm text-gray-500">({{ $assessment->bmi_category ?? 'N/A' }})</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <i class="fas fa-dumbbell text-purple-500 text-xl mr-3"></i>
                    <div>
                        <p class="text-sm text-gray-600">Experience</p>
                        <p class="text-lg font-semibold capitalize">{{ $assessment->exercise_experience ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center">
                    <i class="fas fa-clock text-orange-500 text-xl mr-3"></i>
                    <div>
                        <p class="text-sm text-gray-600">Preferred Time</p>
                        <p class="text-lg font-semibold capitalize">{{ str_replace('_', ' ', $assessment->preferred_workout_time ?? 'N/A') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Basic  Information -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-user mr-2 text-red-500"></i>
                Basic  Information
            </h2>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-700">Height & Weight</p>
                    <p class="text-md pt-1">{{ $assessment->height_cm }}cm, {{ $assessment->weight_kg }}kg</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-700">Gender</p>
                    <p class="text-md capitalize pt-1">{{ str_replace('_', ' ', $assessment->gender) }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-700">Activity Level</p>
                    <p class="text-md pt-1">{{ $assessment->activity_level_description }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-700">Date of Birth</p>
                    <p class="text-md pt-1">{{ $assessment->date_of_birth->format('M j, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Fitness Goals -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-bullseye mr-2 text-blue-500"></i>
                Fitness Goals
            </h2>

            <div class="flex flex-wrap gap-2">
                @foreach($assessment->fitness_goals ?? [] as $goal)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        {{ ucwords(str_replace('_', ' ', $goal)) }}
                    </span>
                @endforeach
            </div>
        </div>

        <!-- Medical Information -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-heartbeat mr-2 text-red-500"></i>
                Medical Information
            </h2>

            <!-- Medical Conditions -->
            <div class="mb-4">
                <p class="text-md font-medium text-gray-700 mb-2">Medical Conditions</p>
                @if($assessment->medical_conditions && count($assessment->medical_conditions) > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($assessment->medical_conditions as $condition)
                            <span class="inline-flex items-center px-2 py-1 rounded text-sm bg-red-100 text-red-800">
                                {{ ucfirst(str_replace('_', ' ', $condition)) }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 italic">No medical conditions reported</p>
                @endif
            </div>

            <!-- Medications -->
            <div class="mb-4">
                <p class="text-sm font-medium text-gray-700 mb-2">Current Medications</p>
                @if($assessment->medications && count($assessment->medications) > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($assessment->medications as $medication)
                            <span class="inline-flex items-center px-2 py-1 rounded text-sm bg-blue-100 text-blue-800">
                                {{ $medication }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 italic">No medications reported</p>
                @endif
            </div>

            <!-- Injuries/Surgeries -->
            <div class="mb-4">
                <p class="text-sm font-medium text-gray-700 mb-2">Previous Injuries/Surgeries</p>
                @if($assessment->injuries_surgeries && count($assessment->injuries_surgeries) > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($assessment->injuries_surgeries as $injury)
                            <span class="inline-flex items-center px-2 py-1 rounded text-sm bg-yellow-100 text-yellow-800">
                                {{ $injury }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 italic">No injuries or surgeries reported</p>
                @endif
            </div>

            @if($assessment->has_medical_concerns && !$assessment->doctor_clearance)
                <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-800 font-medium">
                        <i class="fas fa-times-circle mr-1"></i>
                        No medical clearance - Exercise with extreme caution
                    </p>
                </div>
            @endif
        </div>

        <!-- Workout Limitations -->
        @if($assessment->workout_limitations)
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-exclamation-circle mr-2 text-yellow-500"></i>
                    Workout Limitations
                </h2>
                <p class="text-gray-700">{{ $assessment->workout_limitations }}</p>
            </div>
        @endif
        
        <!-- Emergency Contact Information -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-phone-alt text-red-500 mr-2"></i>
                Emergency Contact Information
            </h3>
            @if($assessment->emergency_contacts && count($assessment->emergency_contacts) > 0)
                <div class="space-y-4">
                    @foreach($assessment->emergency_contacts as $index => $contact)
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">Contact {{ $index + 1 }}</h4>
                            <div class="grid md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                    <p class="text-gray-900">{{ $contact['name'] ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                    <p class="text-gray-900">{{ $contact['phone'] ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                                    <p class="text-gray-900">{{ $contact['relation'] ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Fallback to old format if new format not available -->
                <div class="grid md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <p class="text-gray-900">{{ $assessment->emergency_contact_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <p class="text-gray-900">{{ $assessment->emergency_contact_phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                        <p class="text-gray-900">{{ $assessment->emergency_contact_relation ?? 'N/A' }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Lifestyle Information -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-leaf mr-2 text-blue-500"></i>
                Lifestyle Information
            </h2>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm font-medium text-gray-700">Sleep</p>
                    <p class="text-md pt-1">{{ $assessment->sleep_hours }} hours per night</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-700">Stress Level</p>
                    <p class="text-md pt-1">{{ $assessment->stress_level }}/10</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-700">Smoking Status</p>
                    <p class="text-md capitalize pt-1">{{ str_replace('_', ' ', $assessment->smoking_status) }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-700">Alcohol Consumption</p>
                    <p class="text-md capitalize pt-1">{{ $assessment->alcohol_consumption }}</p>
                </div>
            </div>
        </div>

        <!-- Additional Notes -->
        @if($assessment->additional_notes)
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-sticky-note mr-2 text-orange-500"></i>
                    Additional Notes
                </h2>
                <p class="text-gray-700 whitespace-pre-wrap">{{ $assessment->additional_notes }}</p>
            </div>
        @endif

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">
                        Assessment completed on {{ $assessment->completed_at->format('M j, Y \a\t g:i A') }}
                    </p>
                </div>
                <div class="space-x-2 flex">
                    <a href="{{ route('trainer.member.health-assessment.pdf', $assessment->member_id) }}" 
                    class="flex items-center justify-center w-30 h-10 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                        <i class="fas fa-download mr-2"></i>Download
                    </a>

                    <button onclick="history.back()" 
                            class="flex items-center justify-center w-28 h-10 bg-red-500 text-white rounded-lg hover:bg-red-600">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </button>
                </div>
            </div>
        </div>
    </div>

    
@endsection
