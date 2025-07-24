<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DietPlanMeal extends Model
{
    use HasFactory;

    protected $table      = 'dietplan_meal';
    protected $primaryKey = 'dietplanmeal_id';

    protected $fillable = [
        'dietplan_id',
        'meal_id',
        'day',
        'time',
        'quantity',
        'calories',
        'carbs',
        'protein',
        'fat',
        'notes',
    ];

    protected $casts = [
        'time'     => 'datetime:H:i',
        'quantity' => 'float',
        'calories' => 'integer',
        'carbs'    => 'integer',
        'protein'  => 'integer',
        'fat'      => 'integer',
    ];

    public function dietPlan()
    {
        return $this->belongsTo(DietPlan::class, 'dietplan_id');
    }

    public function meal()
    {
        return $this->belongsTo(Meal::class, 'meal_id');
    }
}
