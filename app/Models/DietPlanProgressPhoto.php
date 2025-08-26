<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DietPlanProgressPhoto extends Model
{
    use HasFactory;

    protected $table = 'diet_plan_progress_photos';

    protected $primaryKey = 'id';

    protected $fillable = [
        'dietplan_id',
        'member_id',
        'photo_date',
        'photo_path',
        'note',
    ];

    protected $casts = [
        'photo_date' => 'date',
    ];

    public function dietPlan()
    {
        return $this->belongsTo(DietPlan::class, 'dietplan_id', 'dietplan_id');
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }
}
