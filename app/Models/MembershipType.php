<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class MembershipType extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'membership_types';

    protected $primaryKey = 'type_id';

    protected $fillable = [
        'type_name',
        'duration',
        'price',
    ];

    public function memberships()
    {
        return $this->hasMany(Membership::class, 'type_id', 'type_id');
    }

}
