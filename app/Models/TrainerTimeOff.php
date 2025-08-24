<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class TrainerTimeOff extends Model
{
    protected $fillable = [
        'trainer_id',
        'date',
        'start_time',
        'end_time',
        'reason',
    ];

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
}
