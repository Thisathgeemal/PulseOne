<?php
namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'first_name'     => $this->faker->firstName(),
            'last_name'      => $this->faker->lastName(),
            'email'          => $this->faker->unique()->safeEmail(),
            'password'       => Hash::make('password123'), // default password
            'dob'            => $this->faker->date(),
            'mobile_number'  => $this->faker->numerify('077#######'),
            'address'        => $this->faker->address(),
            'is_active'      => true,
            'mfa_enabled'    => false,
            'total_points'   => 0,
            'profile_image'  => null,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the user has MFA enabled.
     */
    public function withMfa(): static
    {
        return $this->state(fn(array $attributes) => [
            'mfa_enabled' => true,
        ]);
    }

    /**
     * Indicate that the user is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }
}
