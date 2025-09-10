<?php
namespace Tests\Feature;

use App\Models\Meal;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MealTest extends TestCase
{
    use RefreshDatabase;

    protected $dietitian;

    protected function setUp(): void
    {
        parent::setUp();

        $dietitianRole   = Role::factory()->create(['role_name' => 'Dietitian']);
        $this->dietitian = User::factory()->create();
        $this->dietitian->roles()->attach($dietitianRole->role_id);
    }

    /** @test */
    public function dietitian_can_view_meal_index()
    {
        $this->actingAs($this->dietitian)
            ->get(route('dietitian.meals'))
            ->assertStatus(200)
            ->assertViewIs('dietitianDashboard.meals');
    }

    /** @test */
    public function dietitian_can_create_a_meal()
    {
        $mealData = [
            'meal_name'          => 'Test Meal',
            'description'        => 'This is a test meal.',
            'category'           => 'lunch',
            'serving_size'       => '1 cup',
            'difficulty_level'   => 'easy',
            'ingredients'        => ['ingredient1', 'ingredient2'],
            'preparation_method' => 'Mix ingredients and cook.',
            'prep_time_minutes'  => 10,
            'cook_time_minutes'  => 15,
            'total_time_minutes' => 25,
            'calories'           => 250,
            'protein'            => 20,
            'carbs'              => 30,
            'fats'               => 10,
            'fiber'              => 5,
            'sugar'              => 2,
            'sodium'             => 100,
            'meal_times'         => ['lunch'],
            'is_public'          => true,
        ];

        $response = $this->actingAs($this->dietitian)
            ->post(route('dietitian.meals.store'), $mealData);

        $this->assertDatabaseHas('meals', [
            'meal_name'               => $mealData['meal_name'],
            'created_by_dietitian_id' => $this->dietitian->id,
        ]);
    }

    /** @test */
    public function dietitian_can_view_a_meal()
    {
        $meal = Meal::factory()->create([
            'created_by_dietitian_id' => $this->dietitian->id,
        ]);

        $this->actingAs($this->dietitian)
            ->get(route('dietitian.meals.show', $meal))
            ->assertStatus(200)
            ->assertViewHas('meal');
    }

    /** @test */
    public function dietitian_can_update_a_meal()
    {
        $meal = Meal::factory()->create([
            'created_by_dietitian_id' => $this->dietitian->id,
        ]);

        $updatedData = [
            'meal_name'            => 'Updated Meal Name',
            'description'          => 'Updated description',
            'category'             => 'dinner',
            'difficulty_level'     => 'medium',
            'serving_size'         => 2,
            'preparation_time'     => 10,
            'cooking_time'         => 15,
            'ingredients'          => ['updated ingredient1', 'updated ingredient2'],
            'instructions'         => ['Step 1', 'Step 2'],
            'calories_per_serving' => 300,
            'protein_per_serving'  => 25,
            'carbs_per_serving'    => 35,
            'fat_per_serving'      => 12,
            'fiber_per_serving'    => 4,
            'sugar_per_serving'    => 3,
            'sodium_per_serving'   => 120,
            'dietary_tags'         => [
                'meal_times' => ['dinner'],
            ],
            'is_public'            => true,
        ];

        $this->actingAs($this->dietitian)
            ->put(route('dietitian.meals.update', $meal), $updatedData);

        $this->assertDatabaseHas('meals', [
            'meal_id'   => $meal->meal_id,
            'meal_name' => 'Updated Meal Name',
        ]);
    }

    /** @test */
    public function dietitian_can_delete_a_meal()
    {
        $meal = Meal::factory()->create([
            'created_by_dietitian_id' => $this->dietitian->id,
        ]);

        $this->actingAs($this->dietitian)
            ->delete(route('dietitian.meals.destroy', $meal))
            ->assertRedirect(route('dietitian.meals'));

        $this->assertDatabaseMissing('meals', [
            'meal_id' => $meal->meal_id,
        ]);
    }

    /** @test */
    public function calculate_nutrition_requires_ingredients()
    {
        $this->actingAs($this->dietitian)
            ->postJson(route('meals.calculate-nutrition'), [])
            ->assertStatus(422);
    }
}
