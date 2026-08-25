<?php

namespace Tests\Feature\Api;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\CinemaSession;
use App\Models\Customer;
use App\Models\Hall;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_book_seats_by_email(): void
    {
        $hall = Hall::factory()->withSeats()->create([
            'price_standard' => 300,
            'price_vip' => 500,
        ]);
        $session = CinemaSession::factory()->create(['hall_id' => $hall->id]);
        $seats = $hall->seats()->where('row_number', 1)->orderBy('column_number')->take(2)->get();

        $response = $this->postJson("/api/sessions/{$session->id}/bookings", [
            'seat_ids' => $seats->pluck('id')->all(),
            'payment_method' => 'cash',
            'guest_name' => 'Ivan Guest',
            'guest_email' => 'ivan@example.com',
        ]);

        $response->assertCreated();

        $booking = Booking::firstOrFail();
        $response->assertJsonPath('data.id', $booking->id);

        $this->assertSame(BookingStatus::Pending, $booking->status);
        $this->assertSame('600.00', (string) $booking->total_price);

        $customer = Customer::where('email', 'ivan@example.com')->firstOrFail();
        $this->assertSame($customer->id, $booking->customer_id);
    }

    public function test_booking_an_already_taken_seat_returns_a_409(): void
    {
        $hall = Hall::factory()->withSeats()->create();
        $session = CinemaSession::factory()->create(['hall_id' => $hall->id]);
        $seat = $hall->seats()->first();

        $this->postJson("/api/sessions/{$session->id}/bookings", [
            'seat_ids' => [$seat->id],
            'payment_method' => 'cash',
            'guest_name' => 'First Guest',
            'guest_email' => 'first@example.com',
        ])->assertCreated();

        $this->postJson("/api/sessions/{$session->id}/bookings", [
            'seat_ids' => [$seat->id],
            'payment_method' => 'cash',
            'guest_name' => 'Second Guest',
            'guest_email' => 'second@example.com',
        ])->assertStatus(409);
    }

    public function test_booking_requires_guest_email_when_not_authenticated(): void
    {
        $hall = Hall::factory()->withSeats()->create();
        $session = CinemaSession::factory()->create(['hall_id' => $hall->id]);
        $seat = $hall->seats()->first();

        $this->postJson("/api/sessions/{$session->id}/bookings", [
            'seat_ids' => [$seat->id],
            'payment_method' => 'cash',
        ])->assertJsonValidationErrors(['guest_name', 'guest_email']);
    }
}
