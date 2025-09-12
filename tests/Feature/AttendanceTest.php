<?php
namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected $trainer;
    protected $member;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Roles
        $trainerRole = Role::factory()->create(['role_name' => 'Trainer']);
        $memberRole  = Role::factory()->create(['role_name' => 'Member']);
        $adminRole   = Role::factory()->create(['role_name' => 'Admin']);

        // Create Users
        $this->trainer = User::factory()->create();
        $this->trainer->roles()->attach($trainerRole->role_id);

        $this->member = User::factory()->create();
        $this->member->roles()->attach($memberRole->role_id);

        $this->admin = User::factory()->create();
        $this->admin->roles()->attach($adminRole->role_id);
    }

    /** @test */
    public function trainer_can_access_qr_scanner_page()
    {
        $this->actingAs($this->trainer)
            ->get(route('trainer.qr'))
            ->assertStatus(200)
            ->assertViewIs('trainerDashboard.qr');
    }

    /** @test */
    public function member_can_access_qr_scanner_page()
    {
        $this->actingAs($this->member)
            ->get(route('member.qr'))
            ->assertStatus(200)
            ->assertViewIs('memberDashboard.qr');
    }

    /** @test */
    public function admin_can_generate_qr_code()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.qr.display'))
            ->assertStatus(200)
            ->assertViewIs('adminDashboard.qr_display')
            ->assertViewHasAll(['qrCode', 'token', 'url']);
    }

    /** @test */
    public function admin_can_store_manual_attendance()
    {
        $date = Carbon::now()->toDateString();
        $time = Carbon::now()->format('H:i');

        $this->actingAs($this->admin)
            ->post(route('admin.attendance.manual'), [
                'user_id' => $this->member->id,
                'date'    => $date,
                'time'    => $time,
            ])
            ->assertSessionHas('success', 'Manual attendance recorded.');

        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->member->id,
        ]);
    }

    /** @test */
    public function user_can_checkout_attendance()
    {
        $attendance = Attendance::create([
            'user_id'       => $this->member->id,
            'check_in_time' => Carbon::now(),
        ]);

        $this->actingAs($this->member)
            ->post(route('attendance.checkout', $attendance->attendance_id))
            ->assertRedirect()
            ->assertSessionHas('success', 'Checked out successfully!');

        $this->assertNotNull($attendance->fresh()->check_out_time);
    }

    /** @test */
    public function member_can_view_own_attendance()
    {
        Attendance::factory()->count(3)->create(['user_id' => $this->member->id]);

        $this->actingAs($this->member)
            ->get(route('member.attendance'))
            ->assertStatus(200)
            ->assertViewIs('memberDashboard.attendance');
    }

    /** @test */
    public function trainer_can_view_own_attendance()
    {
        Attendance::factory()->count(2)->create(['user_id' => $this->trainer->id]);

        $this->actingAs($this->trainer)
            ->get(route('trainer.attendance'))
            ->assertStatus(200)
            ->assertViewIs('trainerDashboard.attendance');
    }

    /** @test */
    public function admin_can_view_all_attendance_with_filters()
    {
        $memberAttendance  = Attendance::factory()->create(['user_id' => $this->member->id]);
        $trainerAttendance = Attendance::factory()->create(['user_id' => $this->trainer->id]);

        $this->actingAs($this->admin)
            ->get(route('admin.attendance', [
                'member_id' => $this->member->id,
                'role'      => 'Member',
                'date'      => Carbon::now()->toDateString(),
            ]))
            ->assertStatus(200)
            ->assertViewIs('adminDashboard.attendance')
            ->assertViewHas('attendances');
    }
}
