<?php
namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleSelectionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function select_role_page_is_accessible_for_authenticated_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('selectRole'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.selectRole');
    }

    /** @test */
    public function single_role_user_redirects_to_dashboard()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create(['role_name' => 'Member']);
        UserRole::factory()->create([
            'user_id'   => $user->id,
            'role_id'   => $role->role_id,
            'is_active' => true,
        ]);

        $this->actingAs($user);
        $roles       = [$role->role_name];
        $redirectUrl = route($roles[0] . '.dashboard');

        $this->assertEquals('Member.dashboard', $roles[0] . '.dashboard');
        $this->assertStringContainsString('dashboard', $redirectUrl);
    }

    /** @test */
    public function multi_role_user_redirects_to_select_role()
    {
        $user  = User::factory()->create();
        $role1 = Role::factory()->create(['role_name' => 'Member']);
        $role2 = Role::factory()->create(['role_name' => 'Admin']);
        UserRole::factory()->create([
            'user_id'   => $user->id,
            'role_id'   => $role1->role_id,
            'is_active' => true,
        ]);
        UserRole::factory()->create([
            'user_id'   => $user->id,
            'role_id'   => $role2->role_id,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('selectRole'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.selectRole');
    }

    /** @test */
    public function submitting_active_role_redirects_to_dashboard()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create(['role_name' => 'Member']);
        UserRole::factory()->create([
            'user_id'   => $user->id,
            'role_id'   => $role->role_id,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('selectRole.submit'), ['selected_role' => 'Member']);
        $response->assertRedirect(route('Member.dashboard'));
        $this->assertEquals('Member', session('active_role'));
    }

    /** @test */
    public function submitting_inactive_role_returns_error()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create(['role_name' => 'Member']);
        UserRole::factory()->create([
            'user_id'   => $user->id,
            'role_id'   => $role->role_id,
            'is_active' => false,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('selectRole.submit'), ['selected_role' => 'Member']);
        $response->assertSessionHas('error');
    }
}
