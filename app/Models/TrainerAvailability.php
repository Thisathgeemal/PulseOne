<?php
namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class TrainerAvailability extends Model
{
    protected $fillable = [
        'trainer_id',
        'weekday',
        'start_time',
        'end_time',
        'slot_minutes',
        'buffer_minutes',
    ];

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
}
