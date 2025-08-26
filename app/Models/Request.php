<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $primaryKey = 'request_id';

    protected $fillable = [
        'member_id',
        'trainer_id',
        'dietitian_id',
        'plan_type',
        'preferred_start_date',
        'available_days',
        'goal',
        'current_weight',
        'target_weight',
        'timeframe',
        'meals_per_day',
        'type',
        'description',
        'status',

    ];

    public function workoutPlan()
    {
        return $this->hasOne(WorkoutPlan::class, 'request_id');
    }

    public function dietPlan()
    {
        return $this->hasOne(dietPlan::class, 'request_id');
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function dietitian()
    {
        return $this->belongsTo(User::class, 'dietitian_id');
    }
}
