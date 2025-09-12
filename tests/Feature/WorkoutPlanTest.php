<?php
namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\Request as WorkoutRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Tests\TestCase;

class WorkoutPlanTest extends TestCase
{
    use RefreshDatabase;

    protected $member;
    protected $trainer;

    protected function setUp(): void
    {
        parent::setUp();

        // Disable notifications during test
        NotificationFacade::fake();

        // Create roles first
        $this->memberRole  = Role::factory()->create(['role_id' => 3, 'role_name' => 'Member']);
        $this->trainerRole = Role::factory()->create(['role_id' => 2, 'role_name' => 'Trainer']);

        // Create users
        $this->member  = User::factory()->create();
        $this->trainer = User::factory()->create();

        // Assign roles to users
        $this->member->roles()->attach($this->memberRole->role_id, ['is_active' => 1]);
        $this->trainer->roles()->attach($this->trainerRole->role_id, ['is_active' => 1]);
    }

    /** @test */
    public function member_can_request_workout_plan()
    {
        $request = WorkoutRequest::factory()->create([
            'member_id'  => $this->member->id,
            'trainer_id' => $this->trainer->id,
            'type'       => 'Workout',
            'status'     => 'Pending',
        ]);

        $response = $this->actingAs($this->member)
            ->post(route('member.workoutplan.request'), [
                'trainer_id'           => $this->trainer->id,
                'plan_type'            => 'Basic',
                'plan_dis'             => 'Test plan description',
                'available_days'       => 'Mon,Wed,Fri',
                'preferred_start_date' => now()->addDay()->toDateString(),
            ]);

        $response->assertRedirect(route('member.workoutplan.request'));
        $this->assertDatabaseHas('requests', ['member_id' => $this->member->id, 'trainer_id' => $this->trainer->id, 'type' => 'Workout']);
        $this->assertDatabaseHas('notifications', ['user_id' => $this->trainer->id]);
    }

    /** @test */
    public function member_can_view_my_plans()
    {
        $plan = WorkoutPlan::factory()->create([
            'member_id'  => $this->member->id,
            'trainer_id' => $this->trainer->id,
            'status'     => 'Pending',
            'start_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->member)->get(route('member.workoutplan.myplan'));

        $response->assertStatus(200);
        $response->assertViewHas('plans');
    }

    /** @test */
    public function member_can_cancel_workout_plan()
    {
        $plan = WorkoutPlan::factory()->create([
            'member_id' => $this->member->id,
            'status'    => 'Active',
        ]);

        $response = $this->actingAs($this->member)
            ->get(route('member.workoutplan.cancel', ['id' => $plan->workoutplan_id]));

        $response->assertRedirect();
        $this->assertDatabaseHas('workout_plans', ['workoutplan_id' => $plan->workoutplan_id, 'status' => 'Cancelled']);
        $this->assertDatabaseHas('notifications', ['user_id' => $this->member->id]);
    }

    /** @test */
    public function member_can_download_workout_plan()
    {
        $plan = WorkoutPlan::factory()->create([
            'trainer_id' => $this->trainer->id,
            'member_id'  => $this->member->id,
            'status'     => 'Active',
        ]);

        // Act as member and request download
        $response = $this->actingAs($this->member)
            ->get(route('member.workoutplan.download', ['id' => $plan->workoutplan_id]));

        // Assert the response is successful (HTTP 200) and is a download
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition');
    }

    /** @test */
    public function trainer_can_approve_or_reject_workout_request()
    {
        // Create a pending workout request for the member assigned to the trainer
        $workoutRequest = WorkoutRequest::factory()->create([
            'trainer_id' => $this->trainer->id,
            'member_id'  => $this->member->id,
            'type'       => 'Workout',
            'status'     => 'Pending',
        ]);

        // Act as trainer and approve the request
        $responseApprove = $this->actingAs($this->trainer)
            ->post(route('trainer.request.update', ['id' => $workoutRequest->request_id]), [
                'status' => 'Approved',
            ]);

        $responseApprove->assertRedirect();
        $responseApprove->assertSessionHas('success', 'Request approved successfully.');

        $this->assertDatabaseHas('requests', [
            'request_id' => $workoutRequest->request_id,
            'status'     => 'Approved',
        ]);

        // Act as trainer and reject the request
        $workoutRequest->status = 'Pending';
        $workoutRequest->save();

        $responseReject = $this->actingAs($this->trainer)
            ->post(route('trainer.request.update', ['id' => $workoutRequest->request_id]), [
                'status' => 'Rejected',
            ]);

        $responseReject->assertRedirect();
        $responseReject->assertSessionHas('success', 'Request rejected successfully.');

        $this->assertDatabaseHas('requests', [
            'request_id' => $workoutRequest->request_id,
            'status'     => 'Rejected',
        ]);

        // Assert that a notification is created for the member
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->member->id,
            'type'    => 'Request',
        ]);
    }

    /** @test */
    public function trainer_can_create_workout_plan()
    {
        $workoutRequest = WorkoutRequest::factory()->create([
            'trainer_id' => $this->trainer->id,
            'member_id'  => $this->member->id,
            'status'     => 'Approved',
        ]);

        $exercise = Exercise::factory()->create();

        $response = $this->actingAs($this->trainer)
            ->post(route('trainer.workoutplan.store'), [
                'request_id' => $workoutRequest->request_id,
                'plan_name'  => 'Test Plan',
                'start_date' => now()->toDateString(),
                'end_date'   => now()->addDays(7)->toDateString(),
                'days'       => [
                    [
                        'day_number'    => 1,
                        'muscle_groups' => ['Chest', 'Triceps'],
                        'exercises'     => [
                            ['exercise_id' => $exercise->exercise_id, 'sets' => 3, 'reps' => 12],
                        ],
                        'notes'         => 'Warm-up first',
                    ],
                ],
            ]);

        $response->assertRedirect(route('trainer.workoutplan'));
        $this->assertDatabaseHas('workout_plans', ['trainer_id' => $this->trainer->id, 'member_id' => $this->member->id]);
        $this->assertDatabaseHas('workout_plan_exercises', ['day_number' => 1]);
        $this->assertDatabaseHas('notifications', ['user_id' => $this->member->id]);
    }

    /** @test */
    public function trainer_can_view_workout_plan()
    {
        // Create a workout plan
        $plan = WorkoutPlan::factory()->create([
            'trainer_id' => $this->trainer->id,
            'member_id'  => $this->member->id,
            'status'     => 'Active',
        ]);

        // Act as trainer and view the workout plan
        $response = $this->actingAs($this->trainer)
            ->get(route('trainer.workoutplan.view', ['id' => $plan->workoutplan_id]));

        // Check status
        $response->assertStatus(200);
    }

    /** @test */
    public function trainer_can_download_workout_plan()
    {
        // Create a workout plan assigned to a trainer and member
        $plan = WorkoutPlan::factory()->create([
            'trainer_id' => $this->trainer->id,
            'member_id'  => $this->member->id,
            'status'     => 'Active',
        ]);

        // Act as trainer and request download
        $response = $this->actingAs($this->trainer)
            ->get(route('workout.report', ['id' => $plan->workoutplan_id]));

        // Assert the response is successful (HTTP 200) and is a download
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition');
    }

}
