<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DietProgressPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'photo_path',
        'photo_date',
        'note',
    ];

    protected $casts = [
        'photo_date' => 'date',
    ];

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }
}
