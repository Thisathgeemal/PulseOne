<?php
namespace Database\Factories;

use App\Models\Exercise;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExerciseFactory extends Factory
{
    protected $model = Exercise::class;

    public function definition(): array
    {
        $muscleGroups = ['Chest', 'Back', 'Legs', 'Arms', 'Shoulders', 'Core'];

        return [
            'name'         => $this->faker->unique()->word() . ' Exercise',
            'default_sets' => $this->faker->numberBetween(2, 5),
            'default_reps' => $this->faker->numberBetween(8, 15),
            'muscle_group' => $this->faker->randomElement($muscleGroups),
            'description'  => $this->faker->sentence(),
            'video_link'   => $this->faker->optional()->url(),
        ];
    }
}
