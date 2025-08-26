<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeightLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'weight',
        'log_date',
    ];

    protected $casts = [
        'log_date' => 'date',
    ];

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }
}
