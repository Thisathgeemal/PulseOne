<?php
namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ForgotResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function forgot_password_form_is_accessible()
    {
        $response = $this->get(route('password.request'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.forgotPassword');
    }

    /** @test */
    public function reset_link_can_be_sent_to_valid_email()
    {
        $user = User::factory()->create();

        $response = $this->post(route('password.email'), ['email' => $user->email]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
    }

    /** @test */
    public function reset_link_sent_to_invalid_email_returns_error()
    {
        $response = $this->post(route('password.email'), ['email' => 'nonexistent@example.com']);
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function reset_password_form_is_accessible()
    {
        $token    = 'fake-token';
        $response = $this->get(route('password.reset', $token));
        $response->assertStatus(200);
        $response->assertViewIs('auth.resetPassword');
    }

    /** @test */
    public function user_can_reset_password_with_valid_token()
    {
        $user  = User::factory()->create(['password' => bcrypt('oldpass123')]);
        $token = Password::broker()->createToken($user);

        $response = $this->post(route('password.update'), [
            'email'                 => $user->email,
            'token'                 => $token,
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
    }

    /** @test */
    public function reset_password_with_invalid_token_returns_error()
    {
        $user     = User::factory()->create();
        $response = $this->post(route('password.update'), [
            'email'                 => $user->email,
            'token'                 => 'invalid-token',
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHas('reset_error');
    }
}
