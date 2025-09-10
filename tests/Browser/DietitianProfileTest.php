<?php
namespace Tests\Browser;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DietitianProfileTest extends DuskTestCase
{
    protected $dietitian;

    protected function setUp(): void
    {
        parent::setUp();

        User::where('email', 'dietitian@test.com')->delete();

        $role = Role::firstOrCreate(['role_name' => 'Dietitian']);

        $this->dietitian = User::create([
            'first_name'    => 'Dietitian',
            'last_name'     => 'Test',
            'email'         => 'dietitian@test.com',
            'password'      => Hash::make('password123'),
            'is_active'     => true,
            'mobile_number' => '0771234569',
        ]);

        UserRole::firstOrCreate([
            'user_id'   => $this->dietitian->id,
            'role_id'   => $role->role_id,
            'is_active' => true,
        ]);
    }

    public function test_dietitian_can_update_profile()
    {
        $this->browse(function (Browser $browser) {
            // Login first
            $browser->visit('/login')
                ->type('email', 'dietitian@test.com')
                ->type('password', 'password123')
                ->press('Sign in')
                ->waitForText('Welcome, ' . $this->dietitian->first_name);

            // Open profile sidebar
            $browser->click('@profile-avatar')
                ->waitForText('Edit Profile')
                ->type('first_name', 'UpdatedFirst')
                ->type('last_name', 'UpdatedLast')
                ->type('mobile_number', '0779999999')
                ->type('address', 'No 123, Colombo')
                ->type('dob', '1990-01-01')
                ->press('Save')
                ->pause(500)
                ->script('Swal.clickConfirm();');

            // Assert DB updated
            $this->assertDatabaseHas('users', [
                'id'            => $this->dietitian->id,
                'first_name'    => 'UpdatedFirst',
                'last_name'     => 'UpdatedLast',
                'mobile_number' => '0779999999',
            ]);
        });
    }

    public function test_dietitian_can_change_password()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'dietitian@test.com')
                ->type('password', 'password123')
                ->press('Sign in')
                ->waitForText('Welcome, ' . $this->dietitian->first_name);

            // Open profile sidebar
            $browser->click('@profile-avatar')
                ->waitForText('Edit Profile')
                ->type('current_password', 'password123')
                ->click('#verifyPasswordBtn')
                ->pause(500)
                ->type('password', 'newpassword123')
                ->type('password_confirmation', 'newpassword123')
                ->press('Save')
                ->pause(500)
                ->script('Swal.clickConfirm();');

            // Assert new password works
            $user = User::find($this->dietitian->id);
            $this->assertTrue(Hash::check('newpassword123', $user->password));
        });
    }

    public function test_dietitian_can_update_profile_image()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'dietitian@test.com')
                ->type('password', 'password123')
                ->press('Sign in')
                ->waitForText('Welcome, ' . $this->dietitian->first_name);

            // Open profile sidebar
            $browser->click('@profile-avatar')
                ->waitForText('Edit Profile');

            // Attach new image
            $browser->attach('profile_image', __DIR__ . '/fixtures/profile_test_image.jpg')
                ->press('Save')
                ->pause(500)
                ->script('Swal.clickConfirm();');

            $browser->pause(1000);

            $user = User::find($this->dietitian->id)->fresh();

            $this->assertNotNull($user->profile_image, 'Profile image was not updated in DB');
            $this->assertStringContainsString('profile_', $user->profile_image);
        });
    }

    public function test_dietitian_can_remove_profile_image()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'dietitian@test.com')
                ->type('password', 'password123')
                ->press('Sign in')
                ->waitForText('Welcome, ' . $this->dietitian->first_name);

            // Open profile sidebar
            $browser->click('@profile-avatar')
                ->waitForText('Edit Profile');

            // Click remove image button
            $browser->press('Remove Image')
                ->pause(500)
                ->script('Swal.clickConfirm();');

            // Assert DB no longer has profile image
            $user = User::find($this->dietitian->id);
            $this->assertNull($user->profile_image);
        });
    }

    public function test_dietitian_can_toggle_mfa()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'dietitian@test.com')
                ->type('password', 'password123')
                ->press('Sign in')
                ->waitForText('Welcome, ' . $this->dietitian->first_name);

            // Open settings panel
            $browser->click('@settings-button')
                ->waitForText('MFA Security');

            // Toggle MFA
            $browser->press('Enable Two-Factor Authentication')
                ->pause(1000);

            $browser->assertSee('Two-factor authentication has been enabled.');

            // Refresh DB and assert
            $this->dietitian->refresh();
            $this->assertTrue($this->dietitian->mfa_enabled);
        });
    }

    public function test_dietitian_can_logout_specific_device()
    {
        $this->browse(function (Browser $browser) {

            DB::table('sessions')->insert([
                'id'            => Str::random(40),
                'user_id'       => $this->dietitian->id,
                'ip_address'    => '127.0.0.1',
                'user_agent'    => 'Laravel Dusk Test',
                'payload'       => base64_encode(serialize(['login_time' => now()])),
                'last_activity' => now()->timestamp,
            ]);

            $browser->visit('/login')
                ->type('email', 'dietitian@test.com')
                ->type('password', 'password123')
                ->press('Sign in')
                ->waitForText('Welcome, ' . $this->dietitian->first_name);

            $browser->loginAs($this->dietitian)
                ->click('@settings-button')
                ->waitForText('Browser Sessions');

            // Simulate clicking log out button for another session
            $browser->with('form[action*="/logout/device"]', function ($form) {
                $form->click('@logout-device');
            })

                ->pause(1000)
                ->script('Swal.clickConfirm();');
            $browser->assertSee('Device logged out successfully.');
        });
    }

    public function test_dietitian_can_logout_all_other_devices()
    {
        $this->browse(function (Browser $browser) {

            DB::table('sessions')->insert([
                'id'            => Str::random(40),
                'user_id'       => $this->dietitian->id,
                'ip_address'    => '127.0.0.1',
                'user_agent'    => 'Laravel Dusk Test',
                'payload'       => base64_encode(serialize(['login_time' => now()])),
                'last_activity' => now()->timestamp,
            ]);

            $browser->visit('/login')
                ->type('email', 'dietitian@test.com')
                ->type('password', 'password123')
                ->press('Sign in')
                ->waitForText('Welcome, ' . $this->dietitian->first_name);

            $browser->loginAs($this->dietitian)
                ->click('@settings-button')
                ->waitForText('Browser Sessions');

            $browser->with('form[action*="/logout/all/devices"]', function ($form) {
                $form->click('@logout-all-devices');
            })
                ->pause(1000)
                ->script('Swal.clickConfirm();');
            $browser->assertSee('All other devices have been logged out.');
        });
    }

}
