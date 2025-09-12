<?php
namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    // The model this factory is for
    protected $model = Role::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $roles = ['Admin', 'Trainer', 'Member', 'Dietitian'];

        return [
            'role_name'  => $this->faker->randomElement($roles), // picks a role from the list
            'created_at' => now(),
        ];
    }
}
