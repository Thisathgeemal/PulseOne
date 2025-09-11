@extends('memberDashboard.layout')

@section('content')
    <div x-data="healthAssessmentForm()" class="min-h-screen bg-gray-50">
        <div class="max-w-4xl mx-auto py-8 px-4">
            <!-- Assessment Update Warning -->
            @if($assessment && $assessment->needs_update)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Assessment Update Required:</strong> Your health assessment was completed more than 6 months ago 
                                ({{ $assessment->completed_at->format('M j, Y') }}). Please update your information to ensure accurate health monitoring and workout planning.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            {{ $assessment && $assessment->is_complete ? 'Update Health Assessment' : 'Health Assessment' }}
                        </h1>
                        <p class="text-gray-600 mt-2">
                            @if($assessment && $assessment->is_complete)
                                Update your health profile to keep your training plan current and effective
                            @else
                                Complete your health profile to ensure safe and effective training
                            @endif
                        </p>
                    </div>
                    <div class="hidden md:block">
                        <i class="fas fa-heartbeat text-4xl accent-text"></i>
                    </div>
                </div>
                
                @if($needsUpdate)
                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                            <p class="text-yellow-800">
                                Your health assessment is over 6 months old. Please update it to continue accessing all features.
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Progress Bar -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                    <span>Progress</span>
                    <span x-text="`${Math.round(progress)}% Complete`"></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="accent-bg h-2 rounded-full transition-all duration-500" 
                        :style="`width: ${progress}%`"></div>
                </div>
            </div>

            <form method="POST" action="{{ route('member.health-assessment.store') }}" 
                @submit="validateAndSubmit($event)" id="healthAssessmentForm" novalidate>
                @csrf

                <!-- Section 1: Basic Information -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-user mr-2 accent-text"></i>
                        Basic Information
                    </h2>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth *</label>
                            <input type="date" name="date_of_birth"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 accent-border"
                                value="{{ old('date_of_birth', $assessment?->date_of_birth?->format('Y-m-d')) }}"
                                max="{{ now()->subYears(13)->format('Y-m-d') }}">
                            @error('date_of_birth')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gender *</label>
                            <select name="gender"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 accent-border">
                                <option value="">Select Gender</option>
                                @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other', 'prefer_not_to_say' => 'Prefer not to say'] as $key => $label)
                                    <option value="{{ $key }}" @selected(old('gender', $assessment?->gender) == $key)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('gender')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Height (cm) *</label>
                            <input type="number" name="height_cm" min="100" max="250" step="0.1"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500"
                                value="{{ old('height_cm', $assessment?->height_cm) }}"
                                placeholder="e.g., 175">
                            @error('height_cm')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Weight (kg) *</label>
                            <input type="number" name="weight_kg" min="30" max="300" step="0.1"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500"
                                value="{{ old('weight_kg', $assessment?->weight_kg) }}"
                                placeholder="e.g., 70">
                            @error('weight_kg')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Activity Level & Goals -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-bullseye mr-2 accent-text"></i>
                        Activity Level & Fitness Goals
                    </h2>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Activity Level *</label>
                            <div class="space-y-2">
                                @foreach([
                                    'sedentary' => 'Sedentary - Little to no exercise',
                                    'lightly_active' => 'Lightly Active - Light exercise 1-3 days/week',
                                    'moderately_active' => 'Moderately Active - Moderate exercise 3-5 days/week',
                                    'very_active' => 'Very Active - Hard exercise 6-7 days/week',
                                    'extra_active' => 'Extra Active - Very hard exercise, physical job'
                                ] as $key => $label)
                                    <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <input type="radio" name="activity_level" value="{{ $key }}" required
                                            @checked(old('activity_level', $assessment?->activity_level) == $key)
                                            class="text-blue-500 focus:ring-blue-500">
                                        <span class="ml-3 text-sm">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('activity_level')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fitness Goals * (Select all that apply)</label>
                            <div class="grid md:grid-cols-2 gap-2">
                                @foreach([
                                    'weight_loss' => 'Weight Loss',
                                    'muscle_gain' => 'Muscle Gain',
                                    'strength_training' => 'Strength Training',
                                    'endurance' => 'Endurance/Cardio',
                                    'flexibility' => 'Flexibility',
                                    'general_fitness' => 'General Fitness',
                                    'sport_specific' => 'Sport-Specific Training',
                                    'rehabilitation' => 'Rehabilitation'
                                ] as $key => $label)
                                    <label class="flex items-center p-2 border border-gray-300 rounded hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox" name="fitness_goals[]" value="{{ $key }}"
                                            @checked(in_array($key, old('fitness_goals', $assessment?->fitness_goals ?? [])))
                                            class="text-blue-500 focus:ring-blue-500">
                                        <span class="ml-2 text-sm">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('fitness_goals')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Exercise Experience *</label>
                                <select name="exercise_experience" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500">
                                    <option value="">Select Experience Level</option>
                                    @foreach(['beginner' => 'Beginner (0-1 years)', 'intermediate' => 'Intermediate (1-3 years)', 'advanced' => 'Advanced (3+ years)'] as $key => $label)
                                        <option value="{{ $key }}" @selected(old('exercise_experience', $assessment?->exercise_experience) == $key)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('exercise_experience')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Workout Time *</label>
                                <select name="preferred_workout_time" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500">
                                    <option value="">Select Preferred Time</option>
                                    @foreach(['morning' => 'Morning (6AM-12PM)', 'afternoon' => 'Afternoon (12PM-6PM)', 'evening' => 'Evening (6PM-10PM)', 'flexible' => 'Flexible'] as $key => $label)
                                        <option value="{{ $key }}" @selected(old('preferred_workout_time', $assessment?->preferred_workout_time) == $key)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('preferred_workout_time')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Medical Information -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-heartbeat mr-2 accent-text"></i>
                        Medical Information
                    </h2>

                    <div class="space-y-6">
                        <!-- Medical Conditions -->
                        <div x-data="{ 
                            items: {{ json_encode(array_values(array_filter(old('medical_conditions', $assessment?->medical_conditions ?? []), function($item) { return !empty(trim($item)); }))) }},
                            addItem() { this.items.push(''); },
                            removeItem(index) { if (this.items.length > 1) this.items.splice(index, 1); }
                        }" x-init="if (items.length === 0) items = ['']">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Medical Conditions (if any)</label>
                            <div class="space-y-2">
                                <template x-for="(item, index) in items" :key="index">
                                    <div class="flex gap-2">
                                        <input type="text" x-model="items[index]" 
                                            :name="`medical_conditions[${index}]`"
                                            placeholder="e.g., Diabetes, High Blood Pressure"
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500">
                                        <button type="button" @click="removeItem(index)"
                                                x-show="items.length > 1"
                                                class="px-3 py-2 btn-primary rounded-lg">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </template>
                                <button type="button" @click="addItem()"
                                        class="flex items-center accent-text-color accent-text-hover text-sm">
                                    <i class="fas fa-plus mr-1"></i> Add Medical Condition
                                </button>
                            </div>
                        </div>

                        <!-- Medications -->
                        <div x-data="{ 
                            items: {{ json_encode(array_values(array_filter(old('medications', $assessment?->medications ?? []), function($item) { return !empty(trim($item)); }))) }},
                            addItem() { this.items.push(''); },
                            removeItem(index) { if (this.items.length > 1) this.items.splice(index, 1); }
                        }" x-init="if (items.length === 0) items = ['']">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Medications (if any)</label>
                            <div class="space-y-2">
                                <template x-for="(item, index) in items" :key="index">
                                    <div class="flex gap-2">
                                        <input type="text" x-model="items[index]" 
                                            :name="`medications[${index}]`"
                                            placeholder="e.g., Medication name and dosage"
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500">
                                        <button type="button" @click="removeItem(index)"
                                                x-show="items.length > 1"
                                                class="px-3 py-2 btn-primary rounded-lg">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </template>
                                <button type="button" @click="addItem()"
                                        class="flex items-center accent-text-color accent-text-hover text-sm">
                                    <i class="fas fa-plus mr-1"></i> Add Medication
                                </button>
                            </div>
                        </div>

                        <!-- Injuries & Surgeries -->
                        <div x-data="{ 
                            items: {{ json_encode(array_values(array_filter(old('injuries_surgeries', $assessment?->injuries_surgeries ?? []), function($item) { return !empty(trim($item)); }))) }},
                            addItem() { this.items.push(''); },
                            removeItem(index) { if (this.items.length > 1) this.items.splice(index, 1); }
                        }" x-init="if (items.length === 0) items = ['']">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Previous Injuries & Surgeries (if any)</label>
                            <div class="space-y-2">
                                <template x-for="(item, index) in items" :key="index">
                                    <div class="flex gap-2">
                                        <input type="text" x-model="items[index]" 
                                            :name="`injuries_surgeries[${index}]`"
                                            placeholder="e.g., Knee surgery 2020, Lower back injury"
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500">
                                        <button type="button" @click="removeItem(index)"
                                                x-show="items.length > 1"
                                                class="px-3 py-2 btn-primary rounded-lg">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </template>
                                <button type="button" @click="addItem()"
                                        class="flex items-center accent-text-color accent-text-hover text-sm">
                                    <i class="fas fa-plus mr-1"></i> Add Injury/Surgery
                                </button>
                            </div>
                        </div>

                        <!-- Allergies -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Allergies (if any)</label>
                            
                            <!-- Common allergies checkboxes -->
                            <div class="grid md:grid-cols-3 gap-2 mb-4">
                                @foreach([
                                    'peanuts' => 'Peanuts',
                                    'tree_nuts' => 'Tree Nuts',
                                    'shellfish' => 'Shellfish',
                                    'fish' => 'Fish',
                                    'milk' => 'Milk/Dairy',
                                    'eggs' => 'Eggs',
                                    'soy' => 'Soy',
                                    'wheat' => 'Wheat/Gluten',
                                    'latex' => 'Latex',
                                    'pollen' => 'Pollen',
                                    'dust_mites' => 'Dust Mites',
                                    'pet_dander' => 'Pet Dander'
                                ] as $key => $label)
                                    <label class="flex items-center p-2 border border-gray-300 rounded hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox" name="allergies[]" value="{{ $key }}"
                                            @checked(in_array($key, old('allergies', $assessment?->allergies ?? [])))
                                            class="text-blue-500 focus:ring-blue-500">
                                        <span class="ml-2 text-sm">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <!-- Custom allergies input -->
                            @php
                                $commonAllergies = ['peanuts', 'tree_nuts', 'shellfish', 'fish', 'milk', 'eggs', 'soy', 'wheat', 'latex', 'pollen', 'dust_mites', 'pet_dander'];
                                $userAllergies = old('allergies', $assessment?->allergies ?? []);
                                // Ensure $userAllergies is an array
                                if (!is_array($userAllergies)) {
                                    $userAllergies = [];
                                }
                                // Find custom allergies that are not in the common list
                                $customAllergies = array_values(array_filter($userAllergies, function($allergy) use ($commonAllergies) {
                                    return !in_array($allergy, $commonAllergies) && !empty(trim($allergy));
                                }));
                            @endphp
                            <div x-data="{ 
                                items: {{ json_encode($customAllergies) }},
                                addItem() { this.items.push(''); },
                                removeItem(index) { if (this.items.length > 1) this.items.splice(index, 1); }
                            }" x-init="if (items.length === 0) items = ['']">
                                <label class="block text-sm font-medium text-gray-600 mb-2">Other Allergies</label>
                                <div class="space-y-2">
                                    <template x-for="(item, index) in items" :key="index">
                                        <div class="flex gap-2">
                                            <input type="text" x-model="items[index]" 
                                                :name="`allergies[${index + 12}]`"
                                                placeholder="Specify other allergies..."
                                                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500">
                                            <button type="button" @click="removeItem(index)"
                                                    x-show="items.length > 1"
                                                    class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </template>
                                    <button type="button" @click="addItem()"
                                            class="flex items-center accent-text-color accent-text-hover text-sm">
                                        <i class="fas fa-plus mr-1"></i> Add Other Allergy
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Workout Limitations -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Workout Limitations or Restrictions (Optional)
                            </label>
                            <textarea name="workout_limitations" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500"
                                    placeholder="Any exercises you cannot do or areas to avoid...">{{ old('workout_limitations', $assessment?->workout_limitations) }}</textarea>
                            @error('workout_limitations')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dietary Restrictions -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Dietary Restrictions (if any)</label>
                            
                            <!-- Common dietary restrictions checkboxes -->
                            <div class="grid md:grid-cols-3 gap-2 mb-4">
                                @foreach([
                                    'vegetarian' => 'Vegetarian',
                                    'vegan' => 'Vegan',
                                    'gluten_free' => 'Gluten-Free',
                                    'lactose_intolerant' => 'Lactose Intolerant',
                                    'keto' => 'Ketogenic',
                                    'paleo' => 'Paleo',
                                    'low_carb' => 'Low Carb',
                                    'low_fat' => 'Low Fat',
                                    'low_sodium' => 'Low Sodium',
                                    'diabetic' => 'Diabetic Diet',
                                    'heart_healthy' => 'Heart Healthy',
                                    'halal' => 'Halal'
                                ] as $key => $label)
                                    <label class="flex items-center p-2 border border-gray-300 rounded hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox" name="dietary_restrictions[]" value="{{ $key }}"
                                            @checked(in_array($key, old('dietary_restrictions', $assessment?->dietary_restrictions ?? [])))
                                            class="text-blue-500 focus:ring-blue-500">
                                        <span class="ml-2 text-sm">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <!-- Custom dietary restrictions input -->
                            @php
                                $commonDietaryRestrictions = ['vegetarian', 'vegan', 'gluten_free', 'lactose_intolerant', 'keto', 'paleo', 'low_carb', 'low_fat', 'low_sodium', 'diabetic', 'heart_healthy', 'halal'];
                                $userDietaryRestrictions = old('dietary_restrictions', $assessment?->dietary_restrictions ?? []);
                                // Ensure $userDietaryRestrictions is an array
                                if (!is_array($userDietaryRestrictions)) {
                                    $userDietaryRestrictions = [];
                                }
                                // Find custom dietary restrictions that are not in the common list
                                $customDietaryRestrictions = array_values(array_filter($userDietaryRestrictions, function($restriction) use ($commonDietaryRestrictions) {
                                    return !in_array($restriction, $commonDietaryRestrictions) && !empty(trim($restriction));
                                }));
                            @endphp
                            <div x-data="{ 
                                items: {{ json_encode($customDietaryRestrictions) }},
                                addItem() { this.items.push(''); },
                                removeItem(index) { if (this.items.length > 1) this.items.splice(index, 1); }
                            }" x-init="if (items.length === 0) items = ['']">
                                <label class="block text-sm font-medium text-gray-600 mb-2">Other Dietary Restrictions</label>
                                <div class="space-y-2">
                                    <template x-for="(item, index) in items" :key="index">
                                        <div class="flex gap-2">
                                            <input type="text" x-model="items[index]" 
                                                :name="`dietary_restrictions[${index + 12}]`"
                                                placeholder="Specify other dietary restrictions..."
                                                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500">
                                            <button type="button" @click="removeItem(index)"
                                                    x-show="items.length > 1"
                                                    class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </template>
                                    <button type="button" @click="addItem()"
                                            class="flex items-center accent-text-color accent-text-hover text-sm">
                                        <i class="fas fa-plus mr-1"></i> Add Other Restriction
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-phone-alt mr-2 accent-text"></i>
                        Emergency Contacts
                    </h2>

                    @php
                        $defaultContact = [
                            ['name' => $assessment?->emergency_contact_name ?? '', 'phone' => $assessment?->emergency_contact_phone ?? '', 'relation' => $assessment?->emergency_contact_relation ?? '']
                        ];
                        $emergencyContacts = old('emergency_contacts', $assessment?->emergency_contacts ?? $defaultContact);
                        // Ensure we always have at least one contact
                        if (empty($emergencyContacts) || (count($emergencyContacts) == 1 && empty($emergencyContacts[0]['name']) && empty($emergencyContacts[0]['phone']) && empty($emergencyContacts[0]['relation']))) {
                            $emergencyContacts = [['name' => '', 'phone' => '', 'relation' => '']];
                        }
                    @endphp
                    <div x-data="{
                        contacts: @js($emergencyContacts),
                        addContact() {
                            this.contacts.push({ name: '', phone: '', relation: '' });
                        },
                        removeContact(index) {
                            if (this.contacts.length > 1) {
                                this.contacts.splice(index, 1);
                            }
                        }
                    }">
                        <div class="space-y-4">
                            <template x-for="(contact, index) in contacts" :key="index">
                                <div class="grid md:grid-cols-12 gap-4 p-4 border border-gray-200 rounded-lg">
                                    <div class="md:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Full Name *
                                        </label>
                                        <input type="text" x-model="contacts[index].name" 
                                            :name="`emergency_contacts[${index}][name]`"
                                            required
                                            placeholder="Emergency contact name"
                                            class="w-full h-11 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500">
                                    </div>

                                    <div class="md:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Phone Number *
                                        </label>
                                        <input type="tel" x-model="contacts[index].phone" 
                                            :name="`emergency_contacts[${index}][phone]`"
                                            required
                                            placeholder="Phone number"
                                            class="w-full h-11 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500">
                                    </div>

                                    <div class="md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Relationship *
                                        </label>
                                        <input type="text" x-model="contacts[index].relation" 
                                            :name="`emergency_contacts[${index}][relation]`"
                                            required
                                            placeholder="e.g., Spouse, Parent"
                                            class="w-full h-11 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500">
                                    </div>

                                    <div class="md:col-span-1 flex items-end">
                                        <button type="button" @click="removeContact(index)"
                                                x-show="contacts.length > 1"
                                                class="w-10 h-11 bg-red-500 text-white rounded-lg hover:bg-red-600 flex justify-center items-center">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                            
                            <button type="button" @click="addContact()"
                                    class="flex items-center accent-text-color accent-text-hover text-sm font-medium">
                                <i class="fas fa-plus mr-2"></i> Add Another Emergency Contact
                            </button>
                        </div>

                        <!-- Hidden fields for backward compatibility -->
                        <input type="hidden" x-model="contacts[0]?.name" name="emergency_contact_name">
                        <input type="hidden" x-model="contacts[0]?.phone" name="emergency_contact_phone">
                        <input type="hidden" x-model="contacts[0]?.relation" name="emergency_contact_relation">
                    </div>

                    @error('emergency_contacts')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @error('emergency_contact_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @error('emergency_contact_phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @error('emergency_contact_relation')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PAR-Q+ Questionnaire -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-clipboard-check mr-2 accent-text"></i>
                        Physical Activity Readiness Questionnaire (PAR-Q+)
                    </h2>

                    <div class="space-y-4">
                        @php
                            $parqQuestions = [
                                'Has your doctor ever said that you have a heart condition?',
                                'Do you feel pain in your chest when you do physical activity?',
                                'In the past month, have you had chest pain when you were not doing physical activity?',
                                'Do you lose your balance because of dizziness or do you ever lose consciousness?',
                                'Do you have a bone or joint problem that could be made worse by physical activity?',
                                'Is your doctor currently prescribing drugs for your blood pressure or heart condition?',
                                'Do you know of any other reason why you should not do physical activity?'
                            ];
                        @endphp

                        @foreach($parqQuestions as $index => $question)
                            <div class="p-4 border border-gray-200 rounded-lg">
                                <p class="text-sm font-medium text-gray-900 mb-3">{{ $index + 1 }}. {{ $question }}</p>
                                <div class="flex gap-6">
                                    <label class="flex items-center">
                                        <input type="radio" name="par_q_responses[{{ $index }}]" value="0" required
                                            @checked(old("par_q_responses.{$index}", ($assessment?->par_q_responses[$index] ?? null)) === '0')
                                            class="text-green-500 focus:ring-green-500">
                                        <span class="ml-2 text-sm text-green-700">No</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="par_q_responses[{{ $index }}]" value="1" required
                                            @checked(old("par_q_responses.{$index}", ($assessment?->par_q_responses[$index] ?? null)) === '1')
                                            class="text-blue-500 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-red-700">Yes</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach

                        @error('par_q_responses')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Lifestyle Information -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-leaf mr-2 accent-text"></i>
                        Lifestyle Information
                    </h2>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Smoking Status *</label>
                            <select name="smoking_status" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500">
                                <option value="">Select Status</option>
                                @foreach(['never' => 'Never', 'former' => 'Former Smoker', 'current' => 'Current Smoker'] as $key => $label)
                                    <option value="{{ $key }}" @selected(old('smoking_status', $assessment?->smoking_status) == $key)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('smoking_status')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alcohol Consumption *</label>
                            <select name="alcohol_consumption" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500">
                                <option value="">Select Frequency</option>
                                @foreach(['never' => 'Never', 'rarely' => 'Rarely', 'occasionally' => 'Occasionally (1-2 times/week)', 'regularly' => 'Regularly (3+ times/week)'] as $key => $label)
                                    <option value="{{ $key }}" @selected(old('alcohol_consumption', $assessment?->alcohol_consumption) == $key)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('alcohol_consumption')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Average Sleep Hours *</label>
                            <input type="number" name="sleep_hours" required min="1" max="24"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500"
                                value="{{ old('sleep_hours', $assessment?->sleep_hours) }}">
                            @error('sleep_hours')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stress Level (1-10) *</label>
                            <input type="number" name="stress_level" required min="1" max="10"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500"
                                value="{{ old('stress_level', $assessment?->stress_level) }}">
                            <p class="text-xs text-gray-500 mt-1">1 = Very Low, 10 = Very High</p>
                            @error('stress_level')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Medical Clearance -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-stethoscope mr-2 accent-text"></i>
                        Medical Clearance
                    </h2>

                    <div class="space-y-4">
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                If you answered "Yes" to any PAR-Q+ questions above, medical clearance from your doctor is required.
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Do you have medical clearance to exercise? *
                            </label>
                            <div class="space-y-2">
                                <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="doctor_clearance" value="1" required
                                        @checked(old('doctor_clearance', $assessment?->doctor_clearance) === '1' || old('doctor_clearance', $assessment?->doctor_clearance) === true)
                                        class="text-green-500 focus:ring-green-500">
                                    <span class="ml-3 text-sm">Yes, I have medical clearance to exercise</span>
                                </label>
                                <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="doctor_clearance" value="0" required
                                        @checked(old('doctor_clearance', $assessment?->doctor_clearance) === '0' || old('doctor_clearance', $assessment?->doctor_clearance) === false)
                                        class="text-red-500 focus:ring-red-500">
                                    <span class="ml-3 text-sm">No, I do not have medical clearance</span>
                                </label>
                            </div>
                            @error('doctor_clearance')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Notes -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-sticky-note mr-2 accent-text"></i>
                        Additional Information
                    </h2>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Additional Notes (Optional)
                        </label>
                        <textarea name="additional_notes" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500"
                                placeholder="Any other information you'd like your trainer to know...">{{ old('additional_notes', $assessment?->additional_notes) }}</textarea>
                        @error('additional_notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">
                                By submitting this form, I confirm that the information provided is accurate and complete.
                            </p>
                        </div>
                        <button type="submit"
                                class="px-8 py-3 btn-primary font-semibold rounded-lg focus:ring-2 focus:ring-offset-2 transition-colors duration-200">
                            <i class="fas fa-save mr-2"></i>
                            {{ $assessment && $assessment->is_complete ? 'Update Assessment' : 'Submit Assessment' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function healthAssessmentForm() {
                return {
                    progress: 0,
                    
                    init() {
                        this.calculateProgress();
                        // Listen for changes
                        this.$nextTick(() => {
                            this.setupChangeListeners();
                        });
                    },
                    
                    validateAndSubmit(event) {
                        console.log('Custom validation triggered'); // Debug log
                        event.preventDefault();
                        
                        // Clear previous error messages
                        document.querySelectorAll('.validation-error').forEach(el => el.remove());
                        document.querySelectorAll('.border-red-500').forEach(el => {
                            el.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                        });
                        
                        const errors = this.validateForm();
                        console.log('Found errors:', errors); // Debug log
                        
                        if (errors.length > 0) {
                            // Show first error and scroll to it
                            this.showValidationErrors(errors);
                            this.scrollToFirstError();
                            return false;
                        }
                        
                        // If validation passes, submit the form
                        console.log('Validation passed, submitting form'); // Debug log
                        event.target.submit();
                    },
                    
                    validateForm() {
                        const errors = [];
                        const form = document.getElementById('healthAssessmentForm');
                        
                        // Basic Information validation
                        if (!this.getFieldValue('date_of_birth')) {
                            errors.push({field: 'date_of_birth', message: 'Date of birth is required'});
                        }
                        if (!this.getFieldValue('gender')) {
                            errors.push({field: 'gender', message: 'Please select your gender'});
                        }
                        
                        const height = this.getFieldValue('height_cm');
                        if (!height || isNaN(height) || height < 100 || height > 250) {
                            errors.push({field: 'height_cm', message: 'Please enter a valid height (100-250 cm)'});
                        }
                        
                        const weight = this.getFieldValue('weight_kg');
                        if (!weight || isNaN(weight) || weight < 30 || weight > 300) {
                            errors.push({field: 'weight_kg', message: 'Please enter a valid weight (30-300 kg)'});
                        }
                        
                        // Activity & Fitness validation
                        if (!this.getFieldValue('activity_level')) {
                            errors.push({field: 'activity_level', message: 'Please select your activity level'});
                        }
                        if (!this.getFieldValue('exercise_experience')) {
                            errors.push({field: 'exercise_experience', message: 'Please select your exercise experience level'});
                        }
                        if (!this.getFieldValue('preferred_workout_time')) {
                            errors.push({field: 'preferred_workout_time', message: 'Please select your preferred workout time'});
                        }
                        
                        // Fitness goals validation (at least one must be selected)
                        const fitnessGoals = form.querySelectorAll('input[name="fitness_goals[]"]:checked');
                        if (fitnessGoals.length === 0) {
                            errors.push({field: 'fitness_goals', message: 'Please select at least one fitness goal'});
                        }
                        
                        // Emergency contacts validation
                        const emergencyName = this.getFieldValue('emergency_contacts[0][name]');
                        const emergencyPhone = this.getFieldValue('emergency_contacts[0][phone]');
                        const emergencyRelation = this.getFieldValue('emergency_contacts[0][relation]');
                        
                        if (!emergencyName) {
                            errors.push({field: 'emergency_contacts[0][name]', message: 'Emergency contact name is required'});
                        }
                        if (!emergencyPhone) {
                            errors.push({field: 'emergency_contacts[0][phone]', message: 'Emergency contact phone is required'});
                        }
                        if (!emergencyRelation) {
                            errors.push({field: 'emergency_contacts[0][relation]', message: 'Emergency contact relationship is required'});
                        }
                        
                        // Lifestyle validation
                        if (!this.getFieldValue('smoking_status')) {
                            errors.push({field: 'smoking_status', message: 'Please select your smoking status'});
                        }
                        if (!this.getFieldValue('alcohol_consumption')) {
                            errors.push({field: 'alcohol_consumption', message: 'Please select your alcohol consumption level'});
                        }
                        
                        const sleepHours = this.getFieldValue('sleep_hours');
                        if (!sleepHours || isNaN(sleepHours) || sleepHours < 1 || sleepHours > 24) {
                            errors.push({field: 'sleep_hours', message: 'Please enter valid sleep hours (1-24)'});
                        }
                        
                        if (!this.getFieldValue('stress_level')) {
                            errors.push({field: 'stress_level', message: 'Please select your stress level'});
                        }
                        
                        // PAR-Q+ validation - check if doctor clearance is needed
                        const yesResponses = form.querySelectorAll('input[name^="par_q_responses"]:checked[value="1"]');
                        console.log('PAR-Q+ Yes responses found:', yesResponses.length);
                        const doctorClearance = this.getFieldValue('doctor_clearance');
                        console.log('Doctor clearance value:', doctorClearance);
                        
                        if (yesResponses.length > 0 && !doctorClearance) {
                            console.log('Adding doctor clearance error');
                            errors.push({field: 'doctor_clearance', message: 'Doctor clearance is required due to your health questionnaire responses'});
                        }
                        
                        return errors;
                    },
                    
                    getFieldValue(fieldName) {
                        const form = document.getElementById('healthAssessmentForm');
                        const field = form.querySelector(`input[name="${fieldName}"], select[name="${fieldName}"], textarea[name="${fieldName}"]`);
                        const radioField = form.querySelector(`input[name="${fieldName}"]:checked`);
                        
                        if (radioField) return radioField.value;
                        if (field) return field.value.trim();
                        return '';
                    },
                    
                    showValidationErrors(errors) {
                        errors.forEach(error => {
                            const field = document.querySelector(`[name="${error.field}"]`);
                            if (field) {
                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'validation-error text-red-500 text-sm mt-1 flex items-center';
                                errorDiv.innerHTML = `<i class="fas fa-exclamation-circle mr-1"></i>${error.message}`;
                                
                                // Add red border to field
                                field.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                                
                                // Insert error message after field
                                if (error.field === 'fitness_goals') {
                                    // For checkbox groups, insert after the container
                                    const container = field.closest('.grid');
                                    if (container && !container.nextElementSibling?.classList.contains('validation-error')) {
                                        container.insertAdjacentElement('afterend', errorDiv);
                                    }
                                } else {
                                    // For regular fields, insert after the field or its parent container
                                    const parent = field.parentElement;
                                    if (!parent.querySelector('.validation-error')) {
                                        parent.appendChild(errorDiv);
                                    }
                                }
                            }
                        });
                        
                        // Scroll to first error without notification
                        this.scrollToFirstError();
                    },
                    
                    scrollToFirstError() {
                        const firstError = document.querySelector('.validation-error');
                        if (firstError) {
                            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    },
                    
                    showNotification(message, type = 'error') {
                        // Create notification
                        const notification = document.createElement('div');
                        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
                            type === 'error' ? 'bg-red-500 text-white' : 'bg-green-500 text-white'
                        } transform transition-all duration-300 translate-x-full`;
                        notification.innerHTML = `
                            <div class="flex items-center">
                                <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : 'check-circle'} mr-2"></i>
                                <span>${message}</span>
                                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
                        
                        document.body.appendChild(notification);
                        
                        // Slide in
                        setTimeout(() => {
                            notification.classList.remove('translate-x-full');
                        }, 100);
                        
                        // Auto remove after 5 seconds
                        setTimeout(() => {
                            if (notification.parentElement) {
                                notification.classList.add('translate-x-full');
                                setTimeout(() => notification.remove(), 300);
                            }
                        }, 5000);
                    },
                    
                    setupChangeListeners() {
                        const form = this.$el;
                        form.addEventListener('input', () => {
                            setTimeout(() => this.calculateProgress(), 100);
                        });
                        form.addEventListener('change', () => {
                            setTimeout(() => this.calculateProgress(), 100);
                        });
                    },
                    
                    calculateProgress() {
                        const requiredFields = [
                            // Basic Information (4 fields)
                            'date_of_birth',
                            'gender', 
                            'height_cm',
                            'weight_kg',
                            
                            // Activity & Goals (3 field groups)
                            'activity_level',
                            'fitness_goals', // checkbox group
                            'exercise_experience',
                            'preferred_workout_time',
                            
                            // Emergency Contact (1 group - at least first contact)
                            'emergency_contacts',
                            
                            // Lifestyle (4 fields)
                            'smoking_status',
                            'alcohol_consumption', 
                            'sleep_hours',
                            'stress_level',
                            
                            // Medical Clearance (2 fields)
                            'doctor_clearance',
                            'par_q_responses' // radio group
                        ];
                        
                        let completedGroups = 0;
                        const totalGroups = requiredFields.length;
                        
                        // Check each required field/group
                        requiredFields.forEach(fieldName => {
                            if (this.isFieldGroupComplete(fieldName)) {
                                completedGroups++;
                            }
                        });
                        
                        this.progress = Math.round((completedGroups / totalGroups) * 100);
                        
                        // Ensure progress doesn't exceed 100%
                        if (this.progress > 100) this.progress = 100;
                    },
                    
                    isFieldGroupComplete(fieldName) {
                        const form = this.$el;
                        
                        switch(fieldName) {
                            case 'fitness_goals':
                                const checkedGoals = form.querySelectorAll('input[name="fitness_goals[]"]:checked');
                                return checkedGoals.length > 0;
                                
                            case 'par_q_responses':
                                // Check if all 7 PAR-Q questions are answered
                                for (let i = 0; i < 7; i++) {
                                    const answered = form.querySelector(`input[name="par_q_responses[${i}]"]:checked`);
                                    if (!answered) return false;
                                }
                                return true;
                                
                            case 'emergency_contacts':
                                // Check if at least first emergency contact is complete
                                const name = form.querySelector('input[name="emergency_contacts[0][name]"]');
                                const phone = form.querySelector('input[name="emergency_contacts[0][phone]"]');
                                const relation = form.querySelector('input[name="emergency_contacts[0][relation]"]');
                                return name?.value.trim() && phone?.value.trim() && relation?.value.trim();
                                
                            case 'gender':
                            case 'activity_level':
                            case 'exercise_experience':
                            case 'preferred_workout_time':
                            case 'smoking_status':
                            case 'alcohol_consumption':
                            case 'doctor_clearance':
                                const selectField = form.querySelector(`select[name="${fieldName}"], input[name="${fieldName}"]:checked`);
                                return selectField && selectField.value !== '';
                                
                            default:
                                const field = form.querySelector(`input[name="${fieldName}"], select[name="${fieldName}"], textarea[name="${fieldName}"]`);
                                return field && field.value.trim() !== '';
                        }
                    }
                };
            }
            
            function multipleInput(fieldName, initialItems = []) {
                console.log('MultipleInput called for:', fieldName);
                console.log('Initial items received:', initialItems);
                console.log('Type of initialItems:', typeof initialItems);
                console.log('Is array:', Array.isArray(initialItems));
                
                // Ensure we have an array and filter out empty items
                let filteredItems = [];
                if (Array.isArray(initialItems)) {
                    filteredItems = initialItems.filter(item => item && typeof item === 'string' && item.trim() !== '');
                    console.log('Filtered items:', filteredItems);
                }
                
                // If no existing items, start with one empty field
                if (filteredItems.length === 0) {
                    filteredItems = [''];
                    console.log('No items found, adding empty field');
                }
                
                console.log('Final items for', fieldName, ':', filteredItems);
                
                return {
                    items: filteredItems,
                    
                    addItem() {
                        console.log('Adding new item to', fieldName);
                        this.items.push('');
                    },
                    
                    removeItem(index) {
                        console.log('Removing item at index', index, 'from', fieldName);
                        if (this.items.length > 1) {
                            this.items.splice(index, 1);
                        }
                    }
                }
            }
        </script>
    @endpush
@endsection
