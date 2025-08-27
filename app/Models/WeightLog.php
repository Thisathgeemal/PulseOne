<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeightLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'dietplan_id',
        'member_id',
        'weight',
        'log_date',
        'notes',
    ];

    protected $casts = [
        'log_date' => 'date',
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
