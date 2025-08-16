<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'is_read',
    ];

    /**
     * Relationship: Notification belongs to a User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Get only unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
