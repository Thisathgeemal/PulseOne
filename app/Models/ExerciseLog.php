<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseLog extends Model
{
    use HasFactory;

    protected $primaryKey = 'log_id';

    protected $fillable = [
        'member_id',
        'workoutplan_id',
        'exercise_id',
        'log_date',
        'sets_completed',
        'reps_completed',
        'weight',
    ];

    protected $casts = [
        'log_date' => 'date',
    ];

    // Relationships
    public function workoutPlan()
    {
        return $this->belongsTo(WorkoutPlan::class, 'workoutplan_id');
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

}
