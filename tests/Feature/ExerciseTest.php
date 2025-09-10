<?php
namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExerciseTest extends TestCase
{
    use RefreshDatabase;

    protected $trainer;

    protected function setUp(): void
    {
        parent::setUp();

        $trainerRole   = Role::factory()->create(['role_name' => 'Trainer']);
        $this->trainer = User::factory()->create();
        $this->trainer->roles()->attach($trainerRole->role_id);
    }

    /** @test */
    public function trainer_can_view_exercises()
    {
        Exercise::factory()->count(5)->create();

        $response = $this->actingAs($this->trainer)
            ->get(route('trainer.exercises'));

        $response->assertStatus(200);
        $response->assertViewHas('exercises');
        $response->assertViewHas('muscleColors');
        $response->assertViewHas('muscleIcons');
        $response->assertViewHas('allMuscleGroups');
    }

    /** @test */
    public function trainer_can_filter_exercises_by_muscle_group()
    {
        Exercise::factory()->create(['muscle_group' => 'Chest']);
        Exercise::factory()->create(['muscle_group' => 'Legs']);

        $response = $this->actingAs($this->trainer)
            ->get(route('trainer.exercises', ['muscle_group' => 'Chest']));

        $response->assertStatus(200);
        $response->assertViewHas('exercises', function ($exercises) {
            return $exercises->count() === 1 && $exercises->first()->muscle_group === 'Chest';
        });
    }

    /** @test */
    public function trainer_can_create_exercise()
    {
        $exerciseData = [
            'name'         => 'Push Up',
            'description'  => 'Basic chest exercise',
            'default_sets' => 3,
            'default_reps' => 12,
            'muscle_group' => 'Chest',
            'video_link'   => 'https://example.com/video',
        ];

        $response = $this->actingAs($this->trainer)
            ->post(route('trainer.exercises.store'), $exerciseData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Exercise added successfully.');

        $this->assertDatabaseHas('exercises', [
            'name' => 'Push Up',
        ]);
    }

    /** @test */
    public function trainer_can_delete_exercise()
    {
        $exercise = Exercise::factory()->create();

        $response = $this->actingAs($this->trainer)
            ->delete(route('trainer.exercises.destroy', ['id' => $exercise->exercise_id]));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Exercise deleted successfully.');

        $this->assertDatabaseMissing('exercises', ['exercise_id' => $exercise->exercise_id]);
    }
}
