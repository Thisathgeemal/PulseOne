<?php
namespace Tests\Feature\Auth;

use App\Mail\MembershipConfirmationMail;
use App\Models\MembershipType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function registration_form_is_accessible()
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    /** @test */
    public function member_can_register_and_session_is_set()
    {
        $membershipType = MembershipType::factory()->create();

        $response = $this->post(route('register.member'), [
            'first_name'      => 'John',
            'last_name'       => 'Doe',
            'email'           => 'john@example.com',
            'password'        => 'password123',
            'contact_number'  => '+1234567890',
            'membership_type' => $membershipType->type_id,
            'price'           => 100,
        ]);

        $response->assertRedirect(route('register'));
        $this->assertTrue(session()->has('member_data'));
        $this->assertEquals('John', session('member_data')['first_name']);
    }

    /** @test */
    public function member_registration_with_existing_email_and_member_role_returns_error()
    {
        $user       = User::factory()->create(['email' => 'john@example.com']);
        $memberRole = Role::factory()->create(['role_name' => 'Member']);
        $user->roles()->attach($memberRole->role_id);

        $membershipType = MembershipType::factory()->create();

        $response = $this->post(route('register.member'), [
            'first_name'      => 'John',
            'last_name'       => 'Doe',
            'email'           => 'john@example.com',
            'password'        => 'password123',
            'contact_number'  => '+1234567890',
            'membership_type' => $membershipType->type_id,
            'price'           => 100,
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function register_payment_redirects_with_session_expired()
    {
        $response = $this->post(route('register.payment'), [
            'card_type'    => 'Visa',
            'card_name'    => 'John Doe',
            'card_number'  => '1234567812345678',
            'expiry_month' => 12,
            'expiry_year'  => 2030,
            'cvv'          => '123',
        ]);

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('session');
    }

    /** @test */
    public function register_payment_successfully_creates_user_membership_and_sends_email()
    {
        Mail::fake();

        $membershipType = MembershipType::factory()->create(['duration' => 30]);
        $memberData     = [
            'first_name'      => 'John',
            'last_name'       => 'Doe',
            'email'           => 'john@example.com',
            'password'        => Hash::make('password123'),
            'contact_number'  => '+1234567890',
            'membership_type' => $membershipType->type_id,
            'price'           => 100,
        ];

        session(['member_data' => $memberData]);

        $memberRole = Role::factory()->create(['role_name' => 'Member']);

        $response = $this->post(route('register.payment'), [
            'card_type'    => 'Visa',
            'card_name'    => 'John Doe',
            'card_number'  => '1234567812345678',
            'expiry_month' => 12,
            'expiry_year'  => 2030,
            'cvv'          => '123',
        ]);

        $response->assertRedirect(route('register'));
        $response->assertSessionHas('showSuccess');

        // Mail sent
        Mail::assertSent(MembershipConfirmationMail::class);

        // User created
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);

        // Membership created
        $this->assertDatabaseHas('memberships', [
            'user_id' => User::where('email', 'john@example.com')->first()->id,
            'type_id' => $membershipType->type_id,
        ]);

        // Payment created
        $this->assertDatabaseHas('payments', [
            'user_id' => User::where('email', 'john@example.com')->first()->id,
            'amount'  => 100,
        ]);
    }
}
