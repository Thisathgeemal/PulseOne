<?php
namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function login_fails_with_invalid_credentials()
    {
        $response = $this->post('/login', [
            'email'    => 'wrong@example.com',
            'password' => 'invalid',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function inactive_user_cannot_login()
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'password'   => Hash::make('password123'),
            'is_active'  => false,
        ]);

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function active_user_can_login_and_redirects_to_role_dashboard()
    {
        $user = User::factory()->create([
            'first_name' => 'Jane',
            'last_name'  => 'Doe',
            'password'   => Hash::make('password123'),
            'is_active'  => true,
        ]);

        $role = Role::factory()->create(['role_name' => 'Admin']);
        UserRole::factory()->create([
            'user_id'   => $user->id,
            'role_id'   => $role->role_id,
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password123',
        ]);

        // Match your existing route exactly
        $response->assertRedirect(route('Admin.dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertEquals(['Admin'], session('user_roles'));
    }

    /** @test */
    public function user_with_mfa_enabled_gets_redirected_to_2fa()
    {
        Mail::fake();

        $user = User::factory()->create([
            'first_name'  => 'Alice',
            'last_name'   => 'Smith',
            'email'       => 'alice@example.com',
            'password'    => Hash::make('password123'),
            'is_active'   => true,
            'mfa_enabled' => true,
        ]);

        $role = Role::factory()->create(['role_name' => 'Member']);
        UserRole::factory()->create([
            'user_id'   => $user->id,
            'role_id'   => $role->role_id,
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('2fa.verify'));
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::factory()->create([
            'first_name' => 'Bob',
            'last_name'  => 'Brown',
        ]);

        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }
}
