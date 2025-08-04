<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $primaryKey = 'attendance_id';

    protected $fillable = [
        'user_id',
        'check_in_time',
        'check_out_time',
        'status',
        'qr_code',
    ];

    protected $casts = [
        'check_in_time'  => 'datetime',
        'check_out_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
