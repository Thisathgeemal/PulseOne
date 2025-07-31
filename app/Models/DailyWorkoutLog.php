<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyWorkoutLog extends Model
{
    use HasFactory;

    protected $primaryKey = 'dailylog_id';

    protected $fillable = [
        'member_id',
        'workoutplan_id',
        'log_date',
        'completed_exercises',
        'total_exercises',
        'completion_percentage',
        'workout_duration',
    ];

    protected $casts = [
        'log_date' => 'date',
    ];

    // Relationships
    public function workoutPlan()
    {
        return $this->belongsTo(WorkoutPlan::class, 'workoutplan_id');
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }
}
