<?php
namespace Tests\Browser\Auth;

use App\Models\MembershipType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RegistrationTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clean up test user
        User::where('email', 'john@example.com')->delete();

        // Ensure Member role exists
        Role::firstOrCreate(['role_name' => 'Member']);
    }

    public function test_registration_form_is_accessible()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->assertSee('Create Account')
                ->assertInputPresent('first_name')
                ->assertInputPresent('last_name')
                ->assertInputPresent('email')
                ->assertInputPresent('password')
                ->assertInputPresent('contact_number')
                ->assertInputPresent('membership_type');
        });
    }

    public function test_member_can_register_and_redirect_to_payment_page()
    {
        $membershipType = MembershipType::factory()->create();

        $this->browse(function (Browser $browser) use ($membershipType) {
            $browser->visit('/register')
                ->type('first_name', 'John')
                ->type('last_name', 'Doe')
                ->type('email', 'john@example.com')
                ->type('password', 'password123')
                ->type('contact_number', '+1234567890')
                ->select('membership_type', $membershipType->type_id)
                ->press('Proceed to Payment')
                ->assertSee('Complete Payment');
        });
    }

    public function test_member_can_complete_payment_and_registration_successfully()
    {
        $membershipType = MembershipType::factory()->create();

        session(['member_data' => [
            'first_name'      => 'John',
            'last_name'       => 'Doe',
            'email'           => 'john@example.com',
            'password'        => Hash::make('password123'),
            'contact_number'  => '+1234567890',
            'membership_type' => $membershipType->type_id,
            'price'           => 100,
        ]]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->waitFor('#card_type')
                ->select('card_type', 'visa')
                ->type('card_name', 'John Doe')
                ->type('card_number', '1234567812345678')
                ->waitFor('#expiry_month')
                ->select('expiry_month', '12')
                ->waitFor('#expiry_year')
                ->select('expiry_year', '2030')
                ->type('cvv', '123')
                ->check('consent')
                ->press('Sign Up')
                ->pause(1000);

            $browser->script('Swal.clickConfirm();');

            $browser->waitForLocation('/login')
                ->assertPathIs('/login');
        });

        // Assert database records exist for the user
        $user = User::where('email', 'john@example.com')->first();
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);

        // Assert a membership exists for the user, regardless of type_id
        $this->assertDatabaseHas('memberships', ['user_id' => $user->id]);

        $membership     = \DB::table('memberships')->where('user_id', $user->id)->first();
        $expectedAmount = $membershipType->price ?? 100; // Use factory value

        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'amount'  => $expectedAmount,
        ]);

    }
}
