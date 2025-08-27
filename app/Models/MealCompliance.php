<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealCompliance extends Model
{
    use HasFactory;

    protected $table = 'meal_compliances';

    protected $fillable = [
        'dietplan_id',
        'member_id',
        'log_date',
        'meals_completed',
    ];

    protected $casts = [
        'meals_completed' => 'array',
    ];

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function dietPlan()
    {
        return $this->belongsTo(DietPlan::class, 'dietplan_id');
    }
}
