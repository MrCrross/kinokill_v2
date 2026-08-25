<?php

namespace Tests\Feature\Booking;

use App\Enums\BookingStatus;
use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Models\CinemaSession;
use App\Models\Customer;
use App\Models\Hall;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CreateBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_book_seats_by_email(): void
    {
        Mail::fake();

        $hall = Hall::factory()->withSeats()->create([
            'price_standard' => 300,
            'price_vip' => 500,
        ]);
        $session = CinemaSession::factory()->create(['hall_id' => $hall->id]);
        $seats = $hall->seats()->where('row_number', 1)->orderBy('column_number')->take(2)->get();

        $response = $this->post("/sessions/{$session->id}/bookings", [
            'seat_ids' => $seats->pluck('id')->all(),
            'payment_method' => 'cash',
            'guest_name' => 'Ivan Guest',
            'guest_email' => 'ivan@example.com',
        ]);

        $booking = Booking::firstOrFail();
        $response->assertRedirect("/bookings/{$booking->id}");

        $this->assertSame(BookingStatus::Pending, $booking->status);
        $this->assertSame('600.00', (string) $booking->total_price);
        $this->assertSame(2, $booking->bookingSeats()->count());

        $customer = Customer::where('email', 'ivan@example.com')->firstOrFail();
        $this->assertNull($customer->user_id);
        $this->assertSame($customer->id, $booking->customer_id);

        Mail::assertQueued(
            BookingConfirmationMail::class,
            fn (BookingConfirmationMail $mail) => $mail->hasTo('ivan@example.com')
                && $mail->booking->id === $booking->id
        );
    }

    public function test_authenticated_customer_does_not_need_guest_details(): void
    {
        $user = User::factory()->customer()->create();
        Customer::factory()->create(['user_id' => $user->id, 'email' => $user->email]);

        $hall = Hall::factory()->withSeats()->create();
        $session = CinemaSession::factory()->create(['hall_id' => $hall->id]);
        $seat = $hall->seats()->first();

        $response = $this->actingAs($user)->post("/sessions/{$session->id}/bookings", [
            'seat_ids' => [$seat->id],
            'payment_method' => 'card_stub',
        ]);

        $response->assertSessionDoesntHaveErrors();

        $booking = Booking::firstOrFail();
        $this->assertSame($user->id, $booking->customer->user_id);
    }

    public function test_booking_requires_guest_email_when_not_authenticated(): void
    {
        $hall = Hall::factory()->withSeats()->create();
        $session = CinemaSession::factory()->create(['hall_id' => $hall->id]);
        $seat = $hall->seats()->first();

        $response = $this->post("/sessions/{$session->id}/bookings", [
            'seat_ids' => [$seat->id],
            'payment_method' => 'cash',
        ]);

        $response->assertSessionHasErrors(['guest_name', 'guest_email']);
    }

    public function test_client_supplied_price_is_ignored_and_recomputed_server_side(): void
    {
        $hall = Hall::factory()->withSeats()->create(['price_standard' => 300, 'price_vip' => 500]);
        $session = CinemaSession::factory()->create(['hall_id' => $hall->id]);
        $seat = $hall->seats()->where('row_number', 1)->first();

        $this->post("/sessions/{$session->id}/bookings", [
            'seat_ids' => [$seat->id],
            'payment_method' => 'cash',
            'guest_name' => 'Test',
            'guest_email' => 'test-price@example.com',
            // A real client can't send total_price/price at all — the form
            // doesn't have such a field — but even if it did, the request
            // only whitelists seat_ids/payment_method/guest_*.
            'total_price' => 1,
            'price' => 1,
        ]);

        $booking = Booking::firstOrFail();
        $this->assertSame('300.00', (string) $booking->total_price);
    }
}
