<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\CustomResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'dob',
        'mobile_number',
        'address',
        'is_active',
        'mfa_enabled',
        'total_points',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'dob'         => 'date',
        'is_active'   => 'boolean',
        'mfa_enabled' => 'boolean',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id')
            ->withPivot('is_active');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token, $this->email));
    }

    public function requests()
    {
        return $this->hasMany(Request::class, 'member_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function workoutPlansAsTrainer()
    {
        return $this->hasMany(WorkoutPlan::class, 'trainer_id');
    }

    public function workoutPlansAsMember()
    {
        return $this->hasMany(WorkoutPlan::class, 'member_id');
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function bookingsAsTrainer()
    {
        return $this->hasMany(Booking::class, 'trainer_id');
    }

    public function bookingsAsMember()
    {
        return $this->hasMany(Booking::class, 'member_id');
    }

    public function feedbackGiven()
    {
        return $this->hasMany(Feedback::class, 'from_user_id');
    }

    public function feedbackReceived()
    {
        return $this->hasMany(Feedback::class, 'to_user_id');
    }

    public function dietPlansAsDietitian()
    {
        return $this->hasMany(DietPlan::class, 'dietitian_id');
    }

    public function dietPlansAsMember()
    {
        return $this->hasMany(DietPlan::class, 'member_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }

    public function exerciseLogs()
    {
        return $this->hasMany(ExerciseLog::class, 'member_id');
    }

    public function dailyWorkoutLogs()
    {
        return $this->hasMany(DailyWorkoutLog::class, 'member_id');
    }

    public function chatMessagesSent()
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    public function chatMessagesReceived()
    {
        return $this->hasMany(ChatMessage::class, 'receiver_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
