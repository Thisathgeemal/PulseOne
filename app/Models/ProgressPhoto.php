<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressPhoto extends Model
{
    use HasFactory;

    protected $table = 'progress_photos';

    protected $fillable = [
        'workoutplan_id',
        'user_id',
        'photo_date',
        'photo_path',
        'note',
    ];

    protected $casts = [
        'photo_date' => 'date',
    ];

    /**
     * Relation to WorkoutPlan (optional, if you have that model)
     */
    public function workoutPlan()
    {
        return $this->belongsTo(WorkoutPlan::class, 'workoutplan_id', 'workoutplan_id');
    }

    /**
     * Relation to User (optional)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
