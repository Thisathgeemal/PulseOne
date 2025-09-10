<?php
namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MfaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_cannot_verify_with_invalid_2fa_code()
    {
        $user = User::factory()->create([
            'first_name'    => 'John',
            'last_name'     => 'Doe',
            'email'         => 'john@example.com',
            'password'      => bcrypt('password'),
            'mobile_number' => '0771234567',
            'mfa_enabled'   => true,
        ]);

        $this->actingAs($user);

        session([
            '2fa_code'       => '123456',
            '2fa_expires_at' => now()->addMinutes(3),
        ]);

        $response = $this->post('/2fa', ['code' => '654321']);

        $response->assertSessionHasErrors('code');
    }

    /** @test */
    public function user_cannot_verify_with_expired_2fa_code()
    {
        $user = User::factory()->create([
            'first_name'    => 'Jane',
            'last_name'     => 'Doe',
            'email'         => 'jane@example.com',
            'password'      => bcrypt('password'),
            'mobile_number' => '0779876543',
            'mfa_enabled'   => true,
        ]);

        $this->actingAs($user);

        session([
            '2fa_code'       => '123456',
            '2fa_expires_at' => now()->subMinutes(1),
        ]);

        $response = $this->post('/2fa', ['code' => '123456']);

        $response->assertSessionHasErrors('code');
    }

    /** @test */
    public function user_can_verify_with_valid_2fa_code()
    {
        $user = User::factory()->create([
            'first_name'    => 'Alice',
            'last_name'     => 'Smith',
            'email'         => 'alice@example.com',
            'password'      => bcrypt('password'),
            'mobile_number' => '0771122334',
            'mfa_enabled'   => true,
        ]);

        $this->actingAs($user);

        session([
            '2fa_code'       => '123456',
            '2fa_expires_at' => now()->addMinutes(3),
            'user_roles'     => ['Member'],
        ]);

        $response = $this->post('/2fa', ['code' => '123456']);

        $response->assertRedirect(route('Member.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function resend_2fa_code_sends_new_email()
    {
        Mail::fake();

        $user = User::factory()->create([
            'first_name'    => 'Bob',
            'last_name'     => 'Brown',
            'email'         => 'bob@example.com',
            'password'      => bcrypt('password'),
            'mobile_number' => '0775566778',
            'mfa_enabled'   => true,
        ]);

        $this->actingAs($user);

        $response = $this->post('/2fa/resend');

        $response->assertStatus(302); // redirect back
        $response->assertSessionHas('status', 'A new verification code has been sent to your email.');
        $this->assertTrue(session()->has('2fa_code'));
        $this->assertTrue(session()->has('2fa_expires_at'));
    }
}
