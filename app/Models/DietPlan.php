<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DietPlan extends Model
{
    use HasFactory;

    protected $primaryKey = 'dietplan_id';

    protected $fillable = [
        'dietitian_id',
        'member_id',
        'request_id',
        'plan_name',
        'daily_calories_target',
        'daily_protein_target',
        'daily_carbs_target',
        'daily_fats_target',
        'meals_per_day',
        'plan_description',
        'dietitian_instructions',
        'weekly_schedule',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date'      => 'date',
        'end_date'        => 'date',
        'weekly_schedule' => 'array',
    ];

    public function dietitian()
    {
        return $this->belongsTo(User::class, 'dietitian_id');
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function request()
    {
        return $this->belongsTo(Request::class, 'request_id');
    }

    public function progress_photos()
    {
        return $this->hasMany(DietProgressPhoto::class, 'dietplan_id', 'dietplan_id');
    }

    public function dietPlanMeals()
    {
        return $this->hasMany(DietPlanMeal::class, 'dietplan_id', 'dietplan_id');
    }

    public function mealCompliances()
    {
        return $this->hasMany(MealCompliance::class, 'dietplan_id', 'dietplan_id');
    }

    public function weightLogs()
    {
        return $this->hasMany(WeightLog::class, 'dietplan_id', 'dietplan_id');
    }
}
