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
        'description',
        'default_sets',
        'default_reps',
    ];

    public function workoutPlanExercises()
    {
        return $this->hasMany(WorkoutPlanExercise::class, 'exercise_id');
    }
}
