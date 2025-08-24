<?php
namespace App\Http\Controllers;

use App\Models\HealthAssessment;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class HealthAssessmentController extends Controller
{
    //  Display the health assessment form
    public function create()
    {
        $member     = Auth::user();
        $assessment = HealthAssessment::where('member_id', $member->id)->first();

        // Check if assessment exists but needs update
        $needsUpdate = $assessment && $assessment->needs_update;

        return view('memberDashboard.health-assessment', compact('assessment', 'needsUpdate'));
    }

    // Store or update the health assessment
    public function store(Request $request)
    {
        $member = Auth::user();

        $validated = $request->validate([
            // Basic Information
            'date_of_birth'                 => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'gender'                        => ['required', Rule::in(['male', 'female', 'other', 'prefer_not_to_say'])],
            'height_cm'                     => ['required', 'numeric', 'min:100', 'max:250'],
            'weight_kg'                     => ['required', 'numeric', 'min:30', 'max:300'],

            // Activity & Goals
            'activity_level'                => ['required', Rule::in(['sedentary', 'lightly_active', 'moderately_active', 'very_active', 'extra_active'])],
            'fitness_goals'                 => ['required', 'array', 'min:1'],
            'fitness_goals.*'               => ['string'],

            // Medical Information (Optional)
            'medical_conditions'            => ['nullable', 'array'],
            'medical_conditions.*'          => ['nullable', 'string'],
            'medications'                   => ['nullable', 'array'],
            'medications.*'                 => ['nullable', 'string'],
            'injuries_surgeries'            => ['nullable', 'array'],
            'injuries_surgeries.*'          => ['nullable', 'string'],
            'allergies'                     => ['nullable', 'array'],
            'allergies.*'                   => ['nullable', 'string'],
            'workout_limitations'           => ['nullable', 'string', 'max:1000'],
            'dietary_restrictions'          => ['nullable', 'array'],
            'dietary_restrictions.*'        => ['nullable', 'string'],

            // Emergency Contacts - Support multiple contacts
            'emergency_contacts'            => ['required', 'array', 'min:1'],
            'emergency_contacts.*.name'     => ['required', 'string', 'max:255'],
            'emergency_contacts.*.phone'    => ['required', 'string', 'max:20'],
            'emergency_contacts.*.relation' => ['required', 'string', 'max:100'],

            // Backward compatibility fields
            'emergency_contact_name'        => ['required', 'string', 'max:255'],
            'emergency_contact_phone'       => ['required', 'string', 'max:20'],
            'emergency_contact_relation'    => ['required', 'string', 'max:100'],

            // Exercise Information
            'exercise_experience'           => ['required', Rule::in(['beginner', 'intermediate', 'advanced'])],
            'preferred_workout_time'        => ['required', Rule::in(['morning', 'afternoon', 'evening', 'flexible'])],

            // Health & Lifestyle
            'smoking_status'                => ['required', Rule::in(['never', 'former', 'current'])],
            'alcohol_consumption'           => ['required', Rule::in(['never', 'rarely', 'occasionally', 'regularly'])],
            'sleep_hours'                   => ['required', 'integer', 'min:1', 'max:24'],
            'stress_level'                  => ['required', 'integer', 'min:1', 'max:10'],

            // Medical Clearance
            'doctor_clearance'              => ['required', 'boolean'],
            'par_q_responses'               => ['required', 'array', 'size:7'], // 7 PAR-Q+ questions
            'par_q_responses.*'             => ['required', 'boolean'],

            // Additional (Optional)
            'additional_notes'              => ['nullable', 'string', 'max:2000'],
        ], [
            // Custom error messages
            'date_of_birth.required'                 => 'Date of birth is required.',
            'date_of_birth.before'                   => 'Date of birth must be before today.',
            'gender.required'                        => 'Please select your gender.',
            'height_cm.required'                     => 'Height is required.',
            'height_cm.min'                          => 'Height must be at least 100 cm.',
            'height_cm.max'                          => 'Height cannot exceed 250 cm.',
            'weight_kg.required'                     => 'Weight is required.',
            'weight_kg.min'                          => 'Weight must be at least 30 kg.',
            'weight_kg.max'                          => 'Weight cannot exceed 300 kg.',
            'activity_level.required'                => 'Please select your activity level.',
            'fitness_goals.required'                 => 'Please select at least one fitness goal.',
            'fitness_goals.min'                      => 'Please select at least one fitness goal.',
            'emergency_contacts.required'            => 'At least one emergency contact is required.',
            'emergency_contacts.*.name.required'     => 'Emergency contact name is required.',
            'emergency_contacts.*.phone.required'    => 'Emergency contact phone is required.',
            'emergency_contacts.*.relation.required' => 'Emergency contact relationship is required.',
            'emergency_contact_name.required'        => 'Emergency contact name is required.',
            'emergency_contact_phone.required'       => 'Emergency contact phone is required.',
            'emergency_contact_relation.required'    => 'Emergency contact relationship is required.',
            'exercise_experience.required'           => 'Please select your exercise experience level.',
            'preferred_workout_time.required'        => 'Please select your preferred workout time.',
            'smoking_status.required'                => 'Please select your smoking status.',
            'alcohol_consumption.required'           => 'Please select your alcohol consumption frequency.',
            'sleep_hours.required'                   => 'Please enter your average sleep hours.',
            'sleep_hours.min'                        => 'Sleep hours must be at least 1.',
            'sleep_hours.max'                        => 'Sleep hours cannot exceed 24.',
            'stress_level.required'                  => 'Please rate your stress level.',
            'stress_level.min'                       => 'Stress level must be at least 1.',
            'stress_level.max'                       => 'Stress level cannot exceed 10.',
            'doctor_clearance.required'              => 'Please indicate if you have medical clearance.',
            'par_q_responses.required'               => 'Please answer all health questionnaire questions.',
            'par_q_responses.size'                   => 'Please answer all 7 health questionnaire questions.',
        ]);

        // Check PAR-Q+ responses for safety concerns
        $hasParQConcerns = collect($validated['par_q_responses'])->contains(true);

        // If user answered yes to any PAR-Q+ question, require doctor clearance
        if ($hasParQConcerns && ! $validated['doctor_clearance']) {
            return back()
                ->withInput()
                ->withErrors(['doctor_clearance' => 'Medical clearance is required based on your health questionnaire responses.']);
        }

        // Clean empty arrays
        $validated = $this->cleanArrayFields($validated);

        // Mark as complete
        $validated['is_complete']  = true;
        $validated['completed_at'] = now();

        // Update or create assessment
        $existingAssessment = HealthAssessment::where('member_id', $member->id)->first();
        $isUpdate           = $existingAssessment !== null;

        $assessment = HealthAssessment::updateOrCreate(
            ['member_id' => $member->id],
            $validated
        );

        $message = $isUpdate
        ? 'Health assessment updated successfully! Your information has been saved.'
        : 'Health assessment submitted successfully! You can now access all features.';

        return redirect()
            ->route('member.health-assessment')
            ->with('success', $message);
    }

    // Check if member has completed health assessment
    public function checkStatus()
    {
        $member     = Auth::user();
        $assessment = HealthAssessment::where('member_id', $member->id)->first();

        $isComplete  = $assessment && $assessment->is_complete && ! $assessment->needs_update;
        $needsUpdate = $assessment && $assessment->needs_update;

        return response()->json([
            'is_complete'  => $isComplete,
            'needs_update' => $needsUpdate,
            'message'      => $this->getStatusMessage($assessment),
        ]);
    }

    // Get assessment for trainer view
    public function viewMemberAssessmentTrainer($memberId)
    {
        if (! $this->RoleIdentifying(['Trainer'])) {
            abort(403, 'Unauthorized');
        }

        $assessment = HealthAssessment::where('member_id', $memberId)
            ->where('is_complete', true)
            ->with('member')
            ->first();

        if (! $assessment) {
            abort(404, 'Member health assessment not found');
        }

        return view('trainerDashboard.member-health-assessment', compact('assessment'));
    }

    // Get assessment for dietitian view
    public function viewMemberAssessmentDietitian($memberId)
    {
        if (! $this->RoleIdentifying(['Dietitian'])) {
            abort(403, 'Unauthorized');
        }

        $assessment = HealthAssessment::where('member_id', $memberId)
            ->where('is_complete', true)
            ->with('member')
            ->first();

        if (! $assessment) {
            abort(404, 'Member health assessment not found');
        }

        return view('dietitianDashboard.member-health-assessment', compact('assessment'));
    }

    // Display all member health assessments for trainers
    public function trainerHealthAssessments(Request $request)
    {
        if (! $this->RoleIdentifying(['Trainer'])) {
            abort(403, 'Unauthorized');
        }

        $assessments = $this->filterAssessments($request, 'trainer');
        return view('trainerDashboard.member-health-assessments', compact('assessments'));
    }

    // Display all member health assessments for dietitians
    public function dietitianHealthAssessments(Request $request)
    {
        if (! $this->RoleIdentifying(['Dietitian'])) {
            abort(403, 'Unauthorized');
        }

        $assessments = $this->filterAssessments($request, 'dietitian');
        return view('dietitianDashboard.member-health-assessments', compact('assessments'));
    }

    //  Filter health assessments based on request and user role
    private function filterAssessments(Request $request, string $type)
    {
        $query = HealthAssessment::where('is_complete', true)
            ->with('member')
            ->latest('completed_at');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('member', function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%");
            });
        }

        // Trainer-specific filter
        if ($type === 'trainer' && $request->filled('experience')) {
            $query->where('exercise_experience', $request->get('experience'));
        }

        // Dietitian-specific filter
        if ($type === 'dietitian' && $request->filled('dietary_filter')) {
            $dietaryFilter = $request->get('dietary_filter');
            switch ($dietaryFilter) {
                case 'restrictions':
                    $query->whereJsonLength('dietary_restrictions', '>', 0);
                    break;
                case 'allergies':
                    $query->whereJsonLength('allergies', '>', 0);
                    break;
                case 'medical':
                    $query->whereJsonLength('medical_conditions', '>', 0);
                    break;
            }
        }

        return $query->paginate(10);
    }

    // Get dashboard data for trainer
    public function getTrainerDashboardData()
    {
        if (! $this->RoleIdentifying(['Trainer'])) {
            abort(403, 'Unauthorized');
        }

        $memberRole = Role::where('role_name', 'Member')->first();

        $stats = [
            'active_members'     => $memberRole ? $memberRole->users()->wherePivot('is_active', 1)->count() : 0,
            'todays_sessions'    => 0,
            'workout_plans'      => 0,
            'health_assessments' => HealthAssessment::where('is_complete', true)->count(),
        ];

        $recent_assessments = HealthAssessment::where('is_complete', true)
            ->with('member')
            ->latest('completed_at')
            ->take(5)
            ->get();

        $todays_bookings   = [];
        $recent_activities = [];

        return compact('stats', 'recent_assessments', 'todays_bookings', 'recent_activities');
    }

    /**
     * Check if the logged-in user has any of the given roles
     *
     * @param array $rolesToCheck
     * @return bool
     */
    private function RoleIdentifying(array $rolesToCheck): bool
    {
        $userId = Auth::id();

        // Correct column names based on your schema
        $userRoles = DB::table('roles')
            ->join('user_roles', 'roles.role_id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', $userId)
            ->pluck('roles.role_name') // <- use role_name instead of name
            ->toArray();

        foreach ($rolesToCheck as $role) {
            if (in_array($role, $userRoles)) {
                return true;
            }
        }

        return false;
    }

    // Clean empty array fields
    private function cleanArrayFields(array $data): array
    {
        $arrayFields = ['medical_conditions', 'medications', 'injuries_surgeries', 'allergies', 'dietary_restrictions'];

        foreach ($arrayFields as $field) {
            if (isset($data[$field]) && is_array($data[$field])) {
                $data[$field] = array_values(array_filter($data[$field], function ($value) {
                    return ! empty(trim($value));
                }));
            } elseif (! isset($data[$field])) {
                $data[$field] = [];
            }
        }

        return $data;
    }

    //  Get status message for member
    private function getStatusMessage(?HealthAssessment $assessment): string
    {
        if (! $assessment) {
            return 'Please complete your health assessment to access workout plans, diet plans, and bookings.';
        }

        if (! $assessment->is_complete) {
            return 'Please complete your health assessment to access all features.';
        }

        if ($assessment->needs_update) {
            return 'Your health assessment is outdated. Please update it to continue accessing all features.';
        }

        return 'Health assessment completed.';
    }
}
