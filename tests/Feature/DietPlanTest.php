<?php
namespace Tests\Feature;

use App\Models\DietPlan;
use App\Models\Meal;
use App\Models\Request as DietRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DietPlanTest extends TestCase
{
    use RefreshDatabase;

    protected $member;
    protected $dietitian;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles first
        $this->memberRole    = Role::factory()->create(['role_id' => 3, 'role_name' => 'Member']);
        $this->dietitianRole = Role::factory()->create(['role_id' => 2, 'role_name' => 'Dietitian']);

        // Create users
        $this->member    = User::factory()->create();
        $this->dietitian = User::factory()->create();

        // Assign roles to users
        $this->member->roles()->attach($this->memberRole->role_id, ['is_active' => 1]);
        $this->dietitian->roles()->attach($this->dietitianRole->role_id, ['is_active' => 1]);
    }

    /** @test */
    public function member_can_request_diet_plan()
    {
        $response = $this->actingAs($this->member)->post(route('member.dietplan.request'), [
            'dietitian_id'         => $this->dietitian->id,
            'goal'                 => 'weight_loss',
            'timeframe'            => '1 month',
            'current_weight'       => 80,
            'target_weight'        => 70,
            'special_requirements' => 'I want a balanced diet',
        ]);

        $response->assertRedirect(route('member.dietplan.request'));
        $this->assertDatabaseHas('requests', [
            'member_id' => $this->member->id,
            'type'      => 'Diet',
        ]);
    }

    /** @test */
    public function member_can_view_diet_plans()
    {
        $dietRequest = DietRequest::factory()->create([
            'member_id' => $this->member->id,
            'type'      => 'Diet',
            'status'    => 'Completed',
        ]);

        $dietPlan = DietPlan::factory()->create([
            'member_id'    => $this->member->id,
            'dietitian_id' => $this->dietitian->id,
            'request_id'   => $dietRequest->request_id,
            'plan_name'    => 'My Test Diet Plan',
            'status'       => 'Active',
            'start_date'   => now()->toDateString(),
            'end_date'     => now()->addDays(7)->toDateString(),
        ]);

        $response = $this->actingAs($this->member)->get(route('member.dietplan.myplan'));

        $response->assertStatus(200);
        $response->assertViewHas('plans', function ($plans) use ($dietPlan) {
            return $plans->contains('dietplan_id', $dietPlan->dietplan_id);
        });
    }

    /** @test */
    public function member_can_cancel_diet_plan()
    {
        $dietRequest = DietRequest::factory()->create([
            'member_id' => $this->member->id,
            'type'      => 'Diet',
            'status'    => 'Completed',
        ]);

        $dietPlan = DietPlan::factory()->create([
            'member_id'    => $this->member->id,
            'dietitian_id' => $this->dietitian->id,
            'request_id'   => $dietRequest->request_id,
            'plan_name'    => 'My Test Diet Plan',
            'status'       => 'Active',
            'start_date'   => now()->toDateString(),
            'end_date'     => now()->addDays(7)->toDateString(),
        ]);

        $response = $this->actingAs($this->member)
            ->get(route('member.dietplan.cancel', ['dietPlan' => $dietPlan->dietplan_id]));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Your diet plan has been cancelled successfully.');

        $this->assertDatabaseHas('diet_plans', [
            'dietplan_id' => $dietPlan->dietplan_id,
            'status'      => 'Cancelled',
        ]);
    }

    /** @test */
    public function member_can_download_diet_plan()
    {
        $dietRequest = DietRequest::factory()->create([
            'member_id' => $this->member->id,
            'type'      => 'Diet',
            'status'    => 'Completed',
        ]);

        $dietPlan = DietPlan::factory()->create([
            'member_id'    => $this->member->id,
            'dietitian_id' => $this->dietitian->id,
            'request_id'   => $dietRequest->request_id,
            'plan_name'    => 'My Test Diet Plan',
            'status'       => 'Active',
            'start_date'   => now()->toDateString(),
            'end_date'     => now()->addDays(7)->toDateString(),
        ]);

        $response = $this->actingAs($this->member)
            ->get(route('member.dietplan.download', ['dietPlan' => $dietPlan->dietplan_id]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $response->assertHeader('content-disposition');

        $this->assertStringContainsString(
            'My Test Diet Plan',
            $response->headers->get('content-disposition')
        );
    }

    /** @test */
    public function dietitian_can_approve_or_reject_diet_plan_request()
    {
        $dietRequest = DietRequest::factory()->create([
            'dietitian_id' => $this->dietitian->id,
            'member_id'    => $this->member->id,
            'type'         => 'Diet',
            'status'       => 'Pending',
        ]);

        // Act as dietitian and approve the request
        $responseApprove = $this->actingAs($this->dietitian)
            ->post(route('dietitian.request.update', ['id' => $dietRequest->request_id]), [
                'status' => 'Approved',
            ]);

        $responseApprove->assertRedirect();
        $responseApprove->assertSessionHas('success', 'Request approved successfully.');

        $this->assertDatabaseHas('requests', [
            'request_id' => $dietRequest->request_id,
            'status'     => 'Approved',
        ]);

        // Act as dietitian and reject the request
        $dietRequest->status = 'Pending';
        $dietRequest->save();

        $responseReject = $this->actingAs($this->dietitian)
            ->post(route('dietitian.request.update', ['id' => $dietRequest->request_id]), [
                'status' => 'Rejected',
            ]);

        $responseReject->assertRedirect();
        $responseReject->assertSessionHas('success', 'Request rejected successfully.');

        $this->assertDatabaseHas('requests', [
            'request_id' => $dietRequest->request_id,
            'status'     => 'Rejected',
        ]);

        // Assert that a notification is created for the member
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->member->id,
            'type'    => 'Request',
        ]);
    }

    /** @test */
    public function dietitian_can_view_create_diet_plan()
    {
        $dietRequest = DietRequest::factory()->create([
            'member_id' => $this->member->id,
            'type'      => 'Diet',
            'status'    => 'Approved',
            'timeframe' => '1_week',
        ]);

        $meals = Meal::factory()->count(3)->create([
            'is_active'            => true,
            'calories_per_serving' => 200,
        ]);

        $response = $this->actingAs($this->dietitian)
            ->get(route('dietitian.dietplan.create', ['request_id' => $dietRequest->request_id]));
        $response->assertStatus(200);
        $response->assertViewHasAll([
            'request' => $dietRequest,
            'meals'   => $meals,
            'nutritionTargets',
            'suggestedDates',
        ]);

        $response->assertViewIs('dietitianDashboard.dietplanCreate');
    }

    /** @test */
    public function dietitian_can_download_diet_plan()
    {
        $dietRequest = DietRequest::factory()->create([
            'member_id' => $this->member->id,
            'type'      => 'Diet',
        ]);

        $dietPlan = DietPlan::factory()->create([
            'request_id'   => $dietRequest->request_id,
            'member_id'    => $this->member->id,
            'dietitian_id' => $this->dietitian->id,
            'status'       => 'Active',
        ]);

        $response = $this->actingAs($this->dietitian)
            ->get(route('dietitian.dietplan.download', ['dietPlan' => $dietPlan->dietplan_id]));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition');
    }
}
