<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'dob',
        'mobile_number',
        'address',
        'is_active',
        'mfa_enabled',
        'total_points',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'dob'         => 'date',
        'is_active'   => 'boolean',
        'mfa_enabled' => 'boolean',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

}
