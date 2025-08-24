<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Health Assessment - {{ $assessment->member->first_name }} {{ $assessment->member->last_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #ef4444;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #ef4444;
            margin-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
        }
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #ef4444;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 30%;
            padding: 5px 0;
            vertical-align: top;
        }
        .info-value {
            display: table-cell;
            padding: 5px 0;
            vertical-align: top;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            margin: 2px;
        }
        .badge-green {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-yellow {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-red {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .badge-blue {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .emergency-contact {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
        }
        .alert {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
        }
        .alert-text {
            color: #991b1b;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .list-item {
            margin: 5px 0;
        }
        .text-block {
            background-color: #f9fafb;
            border-radius: 6px;
            padding: 10px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">PULSEONE</div>
        <div class="title">Member Health Assessment</div>
        <div class="subtitle">{{ $assessment->member->first_name }} {{ $assessment->member->last_name }}</div>
        <div class="subtitle">Completed: {{ $assessment->completed_at->format('F j, Y \a\t g:i A') }}</div>
    </div>

    <!-- Basic Information -->
    <div class="section">
        <div class="section-title">Basic Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Full Name:</div>
                <div class="info-value">{{ $assessment->member->first_name }} {{ $assessment->member->last_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date of Birth:</div>
                <div class="info-value">{{ $assessment->date_of_birth->format('F j, Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Age:</div>
                <div class="info-value">{{ $assessment->age }} years</div>
            </div>
            <div class="info-row">
                <div class="info-label">Gender:</div>
                <div class="info-value">{{ ucfirst(str_replace('_', ' ', $assessment->gender)) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Height:</div>
                <div class="info-value">{{ $assessment->height_cm }} cm</div>
            </div>
            <div class="info-row">
                <div class="info-label">Weight:</div>
                <div class="info-value">{{ $assessment->weight_kg }} kg</div>
            </div>
            <div class="info-row">
                <div class="info-label">BMI:</div>
                <div class="info-value">{{ $assessment->bmi }}@if($assessment->bmi_category) ({{ $assessment->bmi_category }})@endif</div>
            </div>
        </div>
    </div>

    <!-- Fitness Information -->
    <div class="section">
        <div class="section-title">Fitness & Exercise Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Activity Level:</div>
                <div class="info-value">{{ ucfirst(str_replace('_', ' ', $assessment->activity_level)) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Exercise Experience:</div>
                <div class="info-value">{{ ucfirst($assessment->exercise_experience) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Preferred Workout Time:</div>
                <div class="info-value">{{ ucfirst(str_replace('_', ' ', $assessment->preferred_workout_time)) }}</div>
            </div>
        </div>

        @if($assessment->fitness_goals && count($assessment->fitness_goals) > 0)
            <div style="margin-top: 15px;">
                <strong>Fitness Goals:</strong><br>
                @foreach($assessment->fitness_goals as $goal)
                    <span class="badge badge-blue">{{ ucfirst(str_replace('_', ' ', $goal)) }}</span>
                @endforeach
            </div>
        @endif

        @if($assessment->workout_limitations)
            <div style="margin-top: 15px;">
                <strong>Workout Limitations:</strong>
                <div class="text-block">{{ $assessment->workout_limitations }}</div>
            </div>
        @endif
    </div>

    <!-- Emergency Contacts -->
    <div class="section">
        <div class="section-title">Emergency Contact{{ $assessment->emergency_contacts && count($assessment->emergency_contacts) > 1 ? 's' : '' }}</div>
        
        @if($assessment->emergency_contacts && count($assessment->emergency_contacts) > 0)
            @foreach($assessment->emergency_contacts as $index => $contact)
                <div class="emergency-contact">
                    @if(count($assessment->emergency_contacts) > 1)
                        <strong>Contact {{ $index + 1 }}</strong><br>
                    @endif
                    <div class="info-grid">
                        <div class="info-row">
                            <div class="info-label">Name:</div>
                            <div class="info-value">{{ $contact['name'] ?? 'N/A' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Phone:</div>
                            <div class="info-value">{{ $contact['phone'] ?? 'N/A' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Relationship:</div>
                            <div class="info-value">{{ $contact['relation'] ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <!-- Fallback to old format -->
            <div class="emergency-contact">
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Name:</div>
                        <div class="info-value">{{ $assessment->emergency_contact_name ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Phone:</div>
                        <div class="info-value">{{ $assessment->emergency_contact_phone ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Relationship:</div>
                        <div class="info-value">{{ $assessment->emergency_contact_relation ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Medical Information -->
    <div class="section">
        <div class="section-title">Medical Information</div>
        
        <!-- Medical Conditions -->
        <div style="margin-bottom: 15px;">
            <strong>Medical Conditions:</strong><br>
            @if($assessment->medical_conditions && count($assessment->medical_conditions) > 0)
                @foreach($assessment->medical_conditions as $condition)
                    <div class="list-item">• {{ $condition }}</div>
                @endforeach
            @else
                <div class="list-item" style="color: #666; font-style: italic;">• None reported</div>
            @endif
        </div>

        <!-- Current Medications -->
        <div style="margin-bottom: 15px;">
            <strong>Current Medications:</strong><br>
            @if($assessment->medications && count($assessment->medications) > 0)
                @foreach($assessment->medications as $medication)
                    <div class="list-item">• {{ $medication }}</div>
                @endforeach
            @else
                <div class="list-item" style="color: #666; font-style: italic;">• None reported</div>
            @endif
        </div>

        <!-- Previous Injuries/Surgeries -->
        <div style="margin-bottom: 15px;">
            <strong>Previous Injuries/Surgeries:</strong><br>
            @if($assessment->injuries_surgeries && count($assessment->injuries_surgeries) > 0)
                @foreach($assessment->injuries_surgeries as $injury)
                    <div class="list-item">• {{ $injury }}</div>
                @endforeach
            @else
                <div class="list-item" style="color: #666; font-style: italic;">• None reported</div>
            @endif
        </div>

        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Doctor Clearance:</div>
                <div class="info-value">
                    @if($assessment->doctor_clearance)
                        <span class="badge badge-green">✓ Yes</span>
                    @else
                        <span class="badge badge-red">✗ No - Exercise with caution</span>
                    @endif
                </div>
            </div>
        </div>

        @if($assessment->has_medical_concerns)
            <div class="alert">
                <div class="alert-text">⚠️ Medical concerns detected - Please review carefully before proceeding with exercise or diet plans.</div>
            </div>
        @endif
    </div>

    <!-- Dietary Information -->
    <div class="section">
        <div class="section-title">Dietary Information</div>
        
        @if($assessment->dietary_restrictions && count($assessment->dietary_restrictions) > 0)
            <div style="margin-bottom: 15px;">
                <strong>Dietary Restrictions:</strong><br>
                @foreach($assessment->dietary_restrictions as $restriction)
                    <span class="badge badge-yellow">{{ ucfirst(str_replace('_', ' ', $restriction)) }}</span>
                @endforeach
            </div>
        @else
            <div style="margin-bottom: 15px;">
                <strong>Dietary Restrictions:</strong> None reported
            </div>
        @endif

        @if($assessment->allergies && count($assessment->allergies) > 0)
            <div style="margin-bottom: 15px;">
                <strong>Allergies:</strong><br>
                @foreach($assessment->allergies as $allergy)
                    <span class="badge badge-red">{{ ucfirst(str_replace('_', ' ', $allergy)) }}</span>
                @endforeach
            </div>
        @else
            <div style="margin-bottom: 15px;">
                <strong>Allergies:</strong> None reported
            </div>
        @endif
    </div>

    <!-- Lifestyle Information -->
    <div class="section">
        <div class="section-title">Lifestyle Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Sleep Hours:</div>
                <div class="info-value">{{ $assessment->sleep_hours }} hours per night</div>
            </div>
            <div class="info-row">
                <div class="info-label">Stress Level:</div>
                <div class="info-value">{{ $assessment->stress_level }}/10</div>
            </div>
            <div class="info-row">
                <div class="info-label">Smoking Status:</div>
                <div class="info-value">{{ ucfirst(str_replace('_', ' ', $assessment->smoking_status)) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Alcohol Consumption:</div>
                <div class="info-value">{{ ucfirst($assessment->alcohol_consumption) }}</div>
            </div>
        </div>
    </div>

    <!-- Additional Notes -->
    @if($assessment->additional_notes)
        <div class="section">
            <div class="section-title">Additional Notes</div>
            <div class="text-block">{{ $assessment->additional_notes }}</div>
        </div>
    @endif

    <!-- Assessment Status -->
    <div class="section">
        <div class="section-title">Assessment Status</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Completion Date:</div>
                <div class="info-value">{{ $assessment->completed_at->format('F j, Y \a\t g:i A') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    @if($assessment->needs_update)
                        <span class="badge badge-yellow">Needs Update (Over 6 months old)</span>
                    @else
                        <span class="badge badge-green">Current</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div>PULSEONE Health Assessment Report</div>
        <div>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</div>
        <div>This document contains confidential medical information</div>
    </div>
</body>
</html>
