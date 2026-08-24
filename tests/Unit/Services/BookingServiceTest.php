<?php

namespace Tests\Unit\Services;

use App\Data\Booking\CreateBookingData;
use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Models\CinemaSession;
use App\Models\Customer;
use App\Models\Hall;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_booking_computes_total_price_from_seat_types(): void
    {
        $hall = Hall::factory()->withSeats()->create([
            'rows_count' => 8,
            'columns_count' => 10,
            'price_standard' => 300,
            'price_vip' => 500,
        ]);
        $session = CinemaSession::factory()->create(['hall_id' => $hall->id]);
        $standardSeat = $hall->seats()->where('row_number', 1)->first();
        $vipSeat = $hall->seats()->where('row_number', 8)->first();

        $data = new CreateBookingData(
            sessionId: $session->id,
            seatIds: [$standardSeat->id, $vipSeat->id],
            paymentMethod: PaymentMethod::Cash,
            guestName: 'Test',
            guestEmail: 'test@example.com',
            guestPhone: null,
        );

        $booking = app(BookingService::class)->createBooking($data, null);

        $this->assertSame('800.00', (string) $booking->total_price);
        $this->assertSame(BookingStatus::Pending, $booking->status);
        $this->assertNotNull($booking->expires_at);
    }

    public function test_confirm_marks_the_booking_paid(): void
    {
        $hall = Hall::factory()->withSeats()->create();
        $session = CinemaSession::factory()->create(['hall_id' => $hall->id]);
        $seat = $hall->seats()->first();

        $booking = app(BookingService::class)->createBooking(new CreateBookingData(
            sessionId: $session->id,
            seatIds: [$seat->id],
            paymentMethod: PaymentMethod::CardStub,
            guestName: 'Test',
            guestEmail: 'confirm@example.com',
            guestPhone: null,
        ), null);

        $confirmed = app(BookingService::class)->confirm($booking);

        $this->assertSame(BookingStatus::Confirmed, $confirmed->status);
        $this->assertNotNull($confirmed->paid_at);
        $this->assertNull($confirmed->expires_at);
    }

    public function test_cancel_marks_the_booking_cancelled(): void
    {
        $hall = Hall::factory()->withSeats()->create();
        $session = CinemaSession::factory()->create(['hall_id' => $hall->id]);
        $seat = $hall->seats()->first();

        $booking = app(BookingService::class)->createBooking(new CreateBookingData(
            sessionId: $session->id,
            seatIds: [$seat->id],
            paymentMethod: PaymentMethod::Cash,
            guestName: 'Test',
            guestEmail: 'cancel@example.com',
            guestPhone: null,
        ), null);

        $cancelled = app(BookingService::class)->cancel($booking);

        $this->assertSame(BookingStatus::Cancelled, $cancelled->status);
    }

    public function test_authenticated_customer_reuses_their_linked_customer_profile(): void
    {
        $user = User::factory()->customer()->create();
        Customer::factory()->create(['user_id' => $user->id, 'email' => $user->email]);

        $hall = Hall::factory()->withSeats()->create();
        $session = CinemaSession::factory()->create(['hall_id' => $hall->id]);
        $seat = $hall->seats()->first();

        $booking = app(BookingService::class)->createBooking(new CreateBookingData(
            sessionId: $session->id,
            seatIds: [$seat->id],
            paymentMethod: PaymentMethod::CardStub,
            guestName: null,
            guestEmail: null,
            guestPhone: null,
        ), $user);

        $this->assertSame($user->id, $booking->customer->user_id);
    }
}
