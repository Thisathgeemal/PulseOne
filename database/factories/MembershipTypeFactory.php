<?php
namespace Database\Factories;

use App\Models\MembershipType;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipTypeFactory extends Factory
{
    protected $model = MembershipType::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $typeNames = ['Day', 'Month', 'Three Months', 'Six Months', 'Annual'];

        $typeName = $this->faker->unique()->randomElement($typeNames);

        // Define duration and price based on type name for realism
        $durations = [
            'Day'          => 1,
            'Month'        => 30,
            'Three Months' => 90,
            'Six Months'   => 180,
            'Annual'       => 365,
        ];

        $amounts = [
            'Day'          => 500,
            'Month'        => 3000,
            'Three Months' => 9000,
            'Six Months'   => 18000,
            'Annual'       => 30000,
        ];

        $discounts = [
            'Day'          => 0,
            'Month'        => 0,
            'Three Months' => 8.33,
            'Six Months'   => 16.67,
            'Annual'       => 20,
        ];

        $prices = [
            'Day'          => 500,
            'Month'        => 3000,
            'Three Months' => 8250,
            'Six Months'   => 15000,
            'Annual'       => 24000,
        ];

        return [
            'type_name'  => $typeName,
            'duration'   => $durations[$typeName],
            'amount'     => $amounts[$typeName],
            'discount'   => $discounts[$typeName],
            'price'      => $prices[$typeName],
            'created_at' => now(),
        ];
    }
}
