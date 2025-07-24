<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $primaryKey = 'dietplan_id';
    public $timestamps    = false;

    protected $fillable = [
        'dietitian_id',
        'member_id',
        'plan_name',
        'start_date',
        'end_date',
        'created_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'created_at' => 'datetime',
    ];
    
    public function dietplanMeals()
    {
        return $this->hasMany(DietPlanMeal::class, 'dietplan_id');
    }
}
