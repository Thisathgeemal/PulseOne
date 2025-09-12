<?php
namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Role;
use App\Models\TrainerAvailability;
use App\Models\User;
use App\Services\SlotService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    protected $member;
    protected $trainer;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles first
        $this->memberRole  = Role::factory()->create(['role_id' => 3, 'role_name' => 'Member']);
        $this->trainerRole = Role::factory()->create(['role_id' => 2, 'role_name' => 'Trainer']);

        // Create users
        $this->member  = User::factory()->create();
        $this->trainer = User::factory()->create();

        // Assign roles to users
        $this->member->roles()->attach($this->memberRole->role_id, ['is_active' => 1]);
        $this->trainer->roles()->attach($this->trainerRole->role_id, ['is_active' => 1]);
    }

    // MEMBER TEST CASES

    /** @test */
    public function member_can_view_booking_page()
    {
        $response = $this->actingAs($this->member)
            ->get(route('member.bookings.index'));

        $response->assertStatus(200);
        $response->assertViewHas(['bookings', 'trainers']);
    }

    /** @test */
    public function member_can_create_booking()
    {
        $date = now()->addDay()->toDateString();

        // Mock SlotService so the time slot is available
        $this->instance(SlotService::class, Mockery::mock(SlotService::class, function ($mock) {
            $mock->shouldReceive('normalizeTime')->with('10:00:00')->andReturn('10:00:00');
            $mock->shouldReceive('getAvailableSlots')->andReturn(['10:00:00']);
        }));

        $response = $this->actingAs($this->member)->post(route('member.bookings.store'), [
            'trainer_id'     => $this->trainer->id,
            'preferred_date' => $date,
            'preferred_time' => '10:00:00',
            'description'    => 'Test booking',
        ]);

        $response->assertRedirect(route('member.bookings.index'));

        $this->assertDatabaseHas('bookings', [
            'member_id'   => $this->member->id,
            'trainer_id'  => $this->trainer->id,
            'description' => 'Test booking',
            'status'      => 'pending',
        ]);
    }

    /** @test */
    public function slots_route_returns_available_times()
    {
        $date = now()->addDay()->toDateString();

        // Mock SlotService to return dummy slots
        $this->instance(SlotService::class, Mockery::mock(SlotService::class, function ($mock) {
            $mock->shouldReceive('getAvailableSlots')->andReturn(['09:00:00', '10:00:00']);
        }));

        $response = $this->actingAs($this->member)->getJson(route('member.bookings.slots', [
            'trainer_id' => $this->trainer->id,
            'date'       => $date,
        ]));

        $response->assertStatus(200);
        $response->assertJson(['slots' => ['09:00:00', '10:00:00']]);
    }

    /** @test */
    public function member_can_cancel_pending_booking()
    {
        $booking = Booking::factory()->create([
            'member_id'  => $this->member->id,
            'trainer_id' => $this->trainer->id,
            'status'     => 'pending',
        ]);

        $response = $this->actingAs($this->member)
            ->post(route('member.bookings.cancel', $booking));

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'booking_id' => $booking->booking_id,
            'status'     => 'cancelled',
        ]);
    }

    /** @test */
    public function member_can_cancel_approved_booking()
    {
        $booking = Booking::factory()->create([
            'member_id'  => $this->member->id,
            'trainer_id' => $this->trainer->id,
            'status'     => 'approved',
        ]);

        $response = $this->actingAs($this->member)
            ->post(route('member.bookings.cancel', $booking));

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'booking_id' => $booking->booking_id,
            'status'     => 'cancelled',
        ]);
    }

    // TRAINER TEST CASES

    /** @test */
    public function trainer_can_view_pending_requests()
    {
        Booking::factory()->create([
            'trainer_id' => $this->trainer->id,
            'member_id'  => $this->member->id,
            'status'     => 'pending',
        ]);

        $response = $this->actingAs($this->trainer)
            ->get(route('trainer.bookings.requests'));

        $response->assertStatus(200);
        $response->assertViewHas('requests');
    }

    /** @test */
    public function trainer_can_approve_pending_booking()
    {
        // Set a date for tomorrow
        $date = now()->addDay()->toDateString();

        $weekdayIso  = Carbon::parse($date)->isoWeekday();
        $weekdayZero = Carbon::parse($date)->dayOfWeek;

        TrainerAvailability::factory()->create([
            'trainer_id'     => $this->trainer->id,
            'weekday'        => $weekdayIso,
            'start_time'     => '09:00:00',
            'end_time'       => '17:00:00',
            'slot_minutes'   => 60,
            'buffer_minutes' => 10,
        ]);

        // Create a pending booking
        $booking = Booking::factory()->create([
            'trainer_id' => $this->trainer->id,
            'member_id'  => $this->member->id,
            'status'     => 'pending',
            'date'       => $date,
            'time'       => null,
        ]);

        // Mock SlotService to allow '11:00:00'
        $slotServiceMock = Mockery::mock(SlotService::class);
        $slotServiceMock->shouldReceive('normalizeTime')->with('11:00:00')->andReturn('11:00:00');
        $slotServiceMock->shouldReceive('getAvailableSlots')->andReturn(['11:00:00']);
        $this->instance(SlotService::class, $slotServiceMock);

        // Approve booking
        $response = $this->actingAs($this->trainer)
            ->post(route('trainer.bookings.approve', $booking->booking_id), [
                'time' => '11:00:00',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'booking_id' => $booking->booking_id,
            'status'     => 'approved',
        ]);
    }

    /** @test */
    public function trainer_can_decline_pending_booking()
    {
        $booking = Booking::factory()->create([
            'trainer_id' => $this->trainer->id,
            'member_id'  => $this->member->id,
            'status'     => 'pending',
        ]);

        $response = $this->actingAs($this->trainer)
            ->post(route('trainer.bookings.decline', $booking->booking_id), [
                'reason' => 'Not available',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'booking_id'     => $booking->booking_id,
            'status'         => 'declined',
            'decline_reason' => 'Not available',
        ]);
    }

    /** @test */
    public function trainer_can_cancel_approved_booking()
    {
        $booking = Booking::factory()->create([
            'member_id'  => $this->member->id,
            'trainer_id' => $this->trainer->id,
            'status'     => 'approved',
        ]);

        $response = $this->actingAs($this->trainer)
            ->post(route('trainer.bookings.cancel', $booking));

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'booking_id' => $booking->booking_id,
            'status'     => 'cancelled',
        ]);
    }

    /** @test */
    public function trainer_can_change_the_member_choice_time_slot()
    {
        $booking = Booking::factory()->create([
            'trainer_id' => $this->trainer->id,
            'member_id'  => $this->member->id,
            'status'     => 'pending',
            'date'       => now()->addDay()->toDateString(),
            'time'       => '10:00:00',
        ]);

        TrainerAvailability::factory()->create([
            'trainer_id'   => $this->trainer->id,
            'weekday'      => now()->addDay()->isoWeekday(),
            'start_time'   => '09:00:00',
            'end_time'     => '18:00:00',
            'slot_minutes' => 60,
        ]);

        $response = $this->actingAs($this->trainer)
            ->post(route('trainer.bookings.approve', $booking->booking_id), [
                'time' => '11:00:00',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'booking_id' => $booking->booking_id,
            'time'       => '11:00:00',
            'status'     => 'approved',
        ]);
    }
}
