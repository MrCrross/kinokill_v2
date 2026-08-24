<?php

namespace Tests\Unit\Services;

use App\Enums\BookingStatus;
use App\Exceptions\SeatAlreadyBookedException;
use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\CinemaSession;
use App\Models\Hall;
use App\Services\SeatAvailabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeatAvailabilityServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_available_seats_are_returned_locked(): void
    {
        $hall = Hall::factory()->withSeats()->create();
        $session = CinemaSession::factory()->create(['hall_id' => $hall->id]);
        $seat = $hall->seats()->first();

        $seats = app(SeatAvailabilityService::class)->lockAndAssertAvailable($session->id, [$seat->id]);

        $this->assertCount(1, $seats);
        $this->assertSame($seat->id, $seats->first()->id);
    }

    public function test_a_seat_held_by_a_confirmed_booking_is_not_available(): void
    {
        $hall = Hall::factory()->withSeats()->create();
        $session = CinemaSession::factory()->create(['hall_id' => $hall->id]);
        $seat = $hall->seats()->first();

        $booking = Booking::factory()->confirmed()->create(['cinema_session_id' => $session->id]);
        BookingSeat::factory()->create([
            'booking_id' => $booking->id,
            'seat_id' => $seat->id,
            'cinema_session_id' => $session->id,
        ]);

        $this->expectException(SeatAlreadyBookedException::class);

        app(SeatAvailabilityService::class)->lockAndAssertAvailable($session->id, [$seat->id]);
    }

    public function test_a_seat_held_by_an_expired_pending_booking_is_available_again(): void
    {
        $hall = Hall::factory()->withSeats()->create();
        $session = CinemaSession::factory()->create(['hall_id' => $hall->id]);
        $seat = $hall->seats()->first();

        $booking = Booking::factory()->create([
            'cinema_session_id' => $session->id,
            'status' => BookingStatus::Pending,
            'expires_at' => now()->subMinute(),
        ]);
        BookingSeat::factory()->create([
            'booking_id' => $booking->id,
            'seat_id' => $seat->id,
            'cinema_session_id' => $session->id,
        ]);

        $seats = app(SeatAvailabilityService::class)->lockAndAssertAvailable($session->id, [$seat->id]);

        $this->assertCount(1, $seats);
    }

    public function test_an_unknown_seat_id_is_rejected(): void
    {
        $hall = Hall::factory()->withSeats()->create();
        $session = CinemaSession::factory()->create(['hall_id' => $hall->id]);

        $this->expectException(SeatAlreadyBookedException::class);

        app(SeatAvailabilityService::class)->lockAndAssertAvailable($session->id, [999999]);
    }
}
