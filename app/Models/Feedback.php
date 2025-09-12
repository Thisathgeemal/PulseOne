<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $primaryKey = 'feedback_id';
    public $timestamps    = false;

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'type',
        'content',
        'rate',
        'is_visible',
        'created_at',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
