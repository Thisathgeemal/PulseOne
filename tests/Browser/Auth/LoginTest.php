<?php
namespace Tests\Browser\Auth;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clean up test users
        User::whereIn('email', [
            'trainer@test.com',
            'member@test.com',
            'admin@test.com',
            'dietitian@test.com',
            'inactive@test.com',
            'mfa@test.com',
            'multirole@test.com',
        ])->delete();

        // Create roles if not exists
        $roles = ['Trainer', 'Member', 'Admin', 'Dietitian'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['role_name' => $roleName]);
        }

        // Role mapping by email
        $roleMap = [
            'trainer@test.com'   => 'Trainer',
            'member@test.com'    => 'Member',
            'admin@test.com'     => 'Admin',
            'dietitian@test.com' => 'Dietitian',
            'inactive@test.com'  => 'Member',
            'mfa@test.com'       => 'Member',
            'multirole@test.com' => ['Trainer', 'Member'], // Multi-role user
        ];

        // Create test users
        $users = [
            [
                'first_name'    => 'Trainer',
                'last_name'     => 'Test',
                'email'         => 'trainer@test.com',
                'password'      => Hash::make('password123'),
                'is_active'     => true,
                'mobile_number' => '0771234567',
            ],
            [
                'first_name'    => 'Member',
                'last_name'     => 'Test',
                'email'         => 'member@test.com',
                'password'      => Hash::make('password123'),
                'is_active'     => true,
                'mobile_number' => '0771234568',
            ],
            [
                'first_name'    => 'Admin',
                'last_name'     => 'Test',
                'email'         => 'admin@test.com',
                'password'      => Hash::make('password123'),
                'is_active'     => true,
                'mobile_number' => '0771234569',
            ],
            [
                'first_name'    => 'Dietitian',
                'last_name'     => 'Test',
                'email'         => 'dietitian@test.com',
                'password'      => Hash::make('password123'),
                'is_active'     => true,
                'mobile_number' => '0771234570',
            ],
            [
                'first_name'    => 'Inactive',
                'last_name'     => 'User',
                'email'         => 'inactive@test.com',
                'password'      => Hash::make('password123'),
                'is_active'     => false,
                'mobile_number' => '0771234571',
            ],
            [
                'first_name'    => 'MFA',
                'last_name'     => 'User',
                'email'         => 'mfa@test.com',
                'password'      => Hash::make('password123'),
                'is_active'     => true,
                'mobile_number' => '0771234572',
                'mfa_enabled'   => true,
            ],
            [
                'first_name'    => 'Multi',
                'last_name'     => 'Role',
                'email'         => 'multirole@test.com',
                'password'      => Hash::make('password123'),
                'is_active'     => true,
                'mobile_number' => '0771234573',
            ],
        ];

        // Create users and attach roles
        foreach ($users as $userData) {
            $user = User::create($userData);

            if ($user->email === 'multirole@test.com') {
                foreach ($roleMap[$user->email] as $roleName) {
                    $role = Role::where('role_name', $roleName)->first();
                    UserRole::create([
                        'user_id'   => $user->id,
                        'role_id'   => $role->role_id,
                        'is_active' => true,
                    ]);
                }
            } else {
                $roleName = $roleMap[$user->email];
                $role     = Role::where('role_name', $roleName)->first();
                UserRole::create([
                    'user_id'   => $user->id,
                    'role_id'   => $role->role_id,
                    'is_active' => true,
                ]);
            }
        }
    }

    /**
     * Test trainer login and dashboard redirect
     */
    public function test_trainer_can_login_and_redirect_to_dashboard()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'trainer@test.com')
                ->type('password', 'password123')
                ->press('Sign in')
                ->waitForLocation('/trainer/dashboard')
                ->assertPathIs('/trainer/dashboard');
        });
    }

    /**
     * Test member login and dashboard redirect
     */
    public function test_member_can_login_and_redirect_to_dashboard()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'member@test.com')
                ->type('password', 'password123')
                ->press('Sign in')
                ->waitForLocation('/member/dashboard')
                ->assertPathIs('/member/dashboard');
        });
    }

    /**
     * Test admin login and dashboard redirect
     */
    public function test_admin_can_login_and_redirect_to_dashboard()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'admin@test.com')
                ->type('password', 'password123')
                ->press('Sign in')
                ->waitForLocation('/admin/dashboard')
                ->assertPathIs('/admin/dashboard');
        });
    }

    /**
     * Test dietitian login and dashboard redirect
     */
    public function test_dietitian_can_login_and_redirect_to_dashboard()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'dietitian@test.com')
                ->type('password', 'password123')
                ->press('Sign in')
                ->waitForLocation('/dietitian/dashboard')
                ->assertPathIs('/dietitian/dashboard');
        });
    }

    /**
     * Test login fails with invalid credentials
     */
    public function test_login_fails_with_invalid_credentials()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'nonexistent@test.com')
                ->type('password', 'wrongpassword')
                ->press('Sign in')
                ->assertSee('Invalid credentials')
                ->assertPathIs('/login');
        });
    }

    /**
     * Test inactive user cannot login
     */
    public function test_inactive_user_cannot_login()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'inactive@test.com')
                ->type('password', 'password123')
                ->press('Sign in')
                ->assertSee('Your account is inactive.')
                ->assertPathIs('/login');
        });
    }

    /**
     * Test mfa user redirects to 2fa verification
     */
    public function test_mfa_user_redirects_to_2fa_verification()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'mfa@test.com')
                ->type('password', 'password123')
                ->press('Sign in')
                ->waitForLocation('/2fa')
                ->assertPathIs('/2fa');
        });
    }

    /**
     * Test user with multiple roles redirects to select role
     */
    public function test_user_with_multiple_roles_redirects_to_select_role()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'multirole@test.com')
                ->type('password', 'password123')
                ->press('Sign in')
                ->waitForLocation('/selectRole')
                ->assertPathIs('/selectRole');
        });
    }

    /**
     * Test that a logged-in user can logout successfully
     */
    public function test_logged_in_user_can_logout()
    {
        $this->browse(function (Browser $browser) {
            // Login first
            $browser->visit('/login')
                ->type('email', 'member@test.com')
                ->type('password', 'password123')
                ->press('Sign in')
                ->waitForLocation('/member/dashboard')
                ->assertPathIs('/member/dashboard');

            // Logout
            $browser->visit('/member/dashboard')
                ->press('Log out')
                ->waitForLocation('/')
                ->assertPathIs('/')
                ->assertGuest();
        });
    }

}
