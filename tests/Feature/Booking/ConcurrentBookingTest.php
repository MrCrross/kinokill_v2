<?php

namespace Tests\Feature\Booking;

use App\Models\Booking;
use App\Models\CinemaSession;
use App\Models\Hall;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConcurrentBookingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Exercises the same lock-then-check code path a real race would hit:
     * SeatAvailabilityService locks the Seat row and re-checks active
     * booking_seats before inserting. A second attempt for a seat that is
     * already held (by a confirmed OR still-pending booking) must fail
     * instead of double-booking it.
     */
    public function test_second_attempt_to_book_an_already_taken_seat_fails(): void
    {
        $hall = Hall::factory()->withSeats()->create();
        $session = CinemaSession::factory()->create(['hall_id' => $hall->id]);
        $seat = $hall->seats()->first();

        $first = $this->post("/sessions/{$session->id}/bookings", [
            'seat_ids' => [$seat->id],
            'payment_method' => 'cash',
            'guest_name' => 'First Buyer',
            'guest_email' => 'first@example.com',
        ]);
        $first->assertSessionDoesntHaveErrors();

        $second = $this->post("/sessions/{$session->id}/bookings", [
            'seat_ids' => [$seat->id],
            'payment_method' => 'cash',
            'guest_name' => 'Second Buyer',
            'guest_email' => 'second@example.com',
        ]);

        $second->assertSessionHasErrors(['seat_ids']);
        $this->assertSame(1, Booking::count());
    }

    public function test_cancelling_a_booking_frees_its_seat_for_rebooking(): void
    {
        $hall = Hall::factory()->withSeats()->create();
        $session = CinemaSession::factory()->create(['hall_id' => $hall->id]);
        $seat = $hall->seats()->first();

        $this->post("/sessions/{$session->id}/bookings", [
            'seat_ids' => [$seat->id],
            'payment_method' => 'cash',
            'guest_name' => 'First Buyer',
            'guest_email' => 'first@example.com',
        ]);
        $booking = Booking::firstOrFail();

        $this->post("/bookings/{$booking->id}/cancel")->assertSessionDoesntHaveErrors();

        $retry = $this->post("/sessions/{$session->id}/bookings", [
            'seat_ids' => [$seat->id],
            'payment_method' => 'cash',
            'guest_name' => 'Second Buyer',
            'guest_email' => 'second@example.com',
        ]);

        $retry->assertSessionDoesntHaveErrors();
        $this->assertSame(2, Booking::count());
    }

    public function test_an_expired_pending_booking_no_longer_blocks_the_seat(): void
    {
        $hall = Hall::factory()->withSeats()->create();
        $session = CinemaSession::factory()->create(['hall_id' => $hall->id]);
        $seat = $hall->seats()->first();

        $this->post("/sessions/{$session->id}/bookings", [
            'seat_ids' => [$seat->id],
            'payment_method' => 'cash',
            'guest_name' => 'First Buyer',
            'guest_email' => 'first@example.com',
        ]);
        Booking::firstOrFail()->update(['expires_at' => now()->subMinute()]);

        $retry = $this->post("/sessions/{$session->id}/bookings", [
            'seat_ids' => [$seat->id],
            'payment_method' => 'cash',
            'guest_name' => 'Second Buyer',
            'guest_email' => 'second@example.com',
        ]);

        $retry->assertSessionDoesntHaveErrors();
    }
}
