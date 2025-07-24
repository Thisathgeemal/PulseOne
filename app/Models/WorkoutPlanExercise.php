<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutPlanExercise extends Model
{
    use HasFactory;

    protected $primaryKey = 'planexercise_id';

    protected $fillable = [
        'workoutplan_id',
        'exercise_id',
        'sets',
        'reps',
        'day_number',
        'notes',
    ];

    public function workoutPlan()
    {
        return $this->belongsTo(WorkoutPlan::class, 'workoutplan_id');
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }
}
