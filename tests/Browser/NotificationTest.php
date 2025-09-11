<?php
namespace Tests\Browser;

use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class NotificationTest extends DuskTestCase
{
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        User::where('email', 'admin@test.com')->delete();

        $role = Role::firstOrCreate(['role_name' => 'Admin']);

        $this->admin = User::create([
            'first_name'    => 'Admin',
            'last_name'     => 'Test',
            'email'         => 'admin@test.com',
            'password'      => Hash::make('password123'),
            'is_active'     => true,
            'mobile_number' => '0771234569',
        ]);

        UserRole::firstOrCreate([
            'user_id'   => $this->admin->id,
            'role_id'   => $role->role_id,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_open_notification_panel()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'admin@test.com')
                ->type('password', 'password123')
                ->press('Sign in')
                ->waitForText('Welcome, ' . $this->admin->first_name);

            $browser->loginAs($this->admin)
                ->click('@notifications-button')
                ->pause(1000)
                ->assertSee('Notifications');
        });
    }

    public function test_unread_notifications_badge_is_visible()
    {
        Notification::factory()->create([
            'user_id' => $this->admin->id,
            'is_read' => false,
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'admin@test.com')
                ->type('password', 'password123')
                ->press('Sign in')
                ->waitForText('Welcome, ' . $this->admin->first_name);

            $browser->loginAs($this->admin)
                ->assertVisible('#unread-badge');
        });
    }

    public function test_admin_can_toggle_read_notifications_visibility()
    {
        $readNotif = Notification::factory()->create([
            'user_id' => $this->admin->id,
            'is_read' => true,
        ]);

        $this->browse(function (Browser $browser) use ($readNotif) {
            $browser->visit('/login')
                ->type('email', 'admin@test.com')
                ->type('password', 'password123')
                ->press('Sign in')
                ->waitForText('Welcome, ' . $this->admin->first_name);

            $browser->loginAs($this->admin)
                ->click('@notifications-button')
                ->pause(1000)
                ->press('Show Read Notifications')
                ->pause(500)
                ->assertSee($readNotif->title)
                ->press('Hide Read Notifications')
                ->pause(500)
                ->assertDontSee($readNotif->title);
        });
    }

    public function test_admin_sees_no_notifications_message_if_none_exist()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'admin@test.com')
                ->type('password', 'password123')
                ->press('Sign in')
                ->waitForText('Welcome, ' . $this->admin->first_name);

            $browser->loginAs($this->admin)
                ->click('@notifications-button')
                ->pause(1000)
                ->assertSee('No notifications');
        });
    }

}
