<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $primaryKey = 'exercise_id';

    protected $fillable = [
        'name',
        'default_sets',
        'default_reps',
        'muscle_group',
        'description',
        'video_link',
    ];

    public function workoutPlanExercises()
    {
        return $this->hasMany(WorkoutPlanExercise::class, 'exercise_id');
    }

    public function exerciseLogs()
    {
        return $this->hasMany(ExerciseLog::class, 'exercise_id');
    }

}
