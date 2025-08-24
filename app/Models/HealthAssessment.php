<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthAssessment extends Model
{
    protected $fillable = [
        'member_id',
        'date_of_birth',
        'gender',
        'height_cm',
        'weight_kg',
        'activity_level',
        'fitness_goals',
        'medical_conditions',
        'medications',
        'injuries_surgeries',
        'allergies',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'emergency_contacts', // New array field for multiple contacts
        'exercise_experience',
        'preferred_workout_time',
        'workout_limitations',
        'dietary_restrictions',
        'smoking_status',
        'alcohol_consumption',
        'sleep_hours',
        'stress_level',
        'doctor_clearance',
        'par_q_responses',
        'additional_notes',
        'is_complete',
        'completed_at',
    ];

    protected $casts = [
        'date_of_birth'        => 'date',
        'fitness_goals'        => 'array',
        'medical_conditions'   => 'array',
        'medications'          => 'array',
        'injuries_surgeries'   => 'array',
        'allergies'            => 'array',
        'dietary_restrictions' => 'array',
        'emergency_contacts'   => 'array', // New field for multiple emergency contacts
        'par_q_responses'      => 'array',
        'is_complete'          => 'boolean',
        'completed_at'         => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    /**
     * Get the member's age based on date of birth
     */
    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    /**
     * Calculate BMI
     */
    public function getBmiAttribute(): ?float
    {
        if ($this->height_cm && $this->weight_kg) {
            $heightMeters = $this->height_cm / 100;
            return round($this->weight_kg / ($heightMeters * $heightMeters), 1);
        }
        return null;
    }

    /**
     * Get BMI category
     */
    public function getBmiCategoryAttribute(): ?string
    {
        $bmi = $this->bmi;
        if (! $bmi) {
            return null;
        }

        return match (true) {
            $bmi < 18.5 => 'Underweight',
            $bmi < 25 => 'Normal weight',
            $bmi < 30 => 'Overweight',
            default => 'Obese'
        };
    }

    /**
     * Check if assessment needs to be updated (older than 6 months)
     */
    public function needsUpdateAttribute(): bool
    {
        return ! $this->completed_at || $this->completed_at->lt(now()->subMonths(6));
    }

    /**
     * Get activity level description
     */
    public function getActivityLevelDescriptionAttribute(): string
    {
        return match ($this->activity_level) {
            'sedentary' => 'Little to no exercise',
            'lightly_active' => 'Light exercise 1-3 days/week',
            'moderately_active' => 'Moderate exercise 3-5 days/week',
            'very_active' => 'Hard exercise 6-7 days/week',
            'extra_active' => 'Very hard exercise, physical job',
            default => 'Not specified'
        };
    }

    /**
     * Check if member has any medical concerns
     */
    public function hasMedicalConcernsAttribute(): bool
    {
        return ! empty($this->medical_conditions) ||
        ! empty($this->medications) ||
        ! empty($this->injuries_surgeries) ||
        $this->doctor_clearance === false;
    }
}
