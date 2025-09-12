<?php
namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserRoleFactory extends Factory
{
    protected $model = UserRole::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id'    => User::factory(), // creates a new user automatically
            'role_id'    => Role::factory(), // creates a new role automatically
            'is_active'  => true,            // default active
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
