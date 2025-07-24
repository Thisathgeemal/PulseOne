<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutPlan extends Model
{
    protected $primaryKey = 'workoutplan_id';

    protected $fillable = [
        'trainer_id',
        'member_id',
        'request_id',
        'plan_name',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function request()
    {
        return $this->belongsTo(Request::class, 'request_id');
    }

    public function workoutPlanExercises()
    {
        return $this->hasMany(WorkoutPlanExercise::class, 'workoutplan_id');
    }
}
