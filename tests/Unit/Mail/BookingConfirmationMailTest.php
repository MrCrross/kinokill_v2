<?php

namespace Tests\Unit\Mail;

use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Models\CinemaSession;
use App\Models\Customer;
use App\Models\Hall;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingConfirmationMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_renders_the_film_hall_and_seat_prices(): void
    {
        $hall = Hall::factory()->withSeats()->create(['name' => 'Зал 1']);
        $session = CinemaSession::factory()->create(['hall_id' => $hall->id]);
        $customer = Customer::factory()->create(['email' => 'ivan@example.com']);
        $seat = $hall->seats()->first();

        $booking = Booking::factory()
            ->for($customer)
            ->for($session, 'cinemaSession')
            ->create();
        $booking->bookingSeats()->create([
            'seat_id' => $seat->id,
            'cinema_session_id' => $session->id,
            'price' => 300,
        ]);

        $mail = new BookingConfirmationMail($booking->fresh(['customer', 'cinemaSession.film', 'cinemaSession.hall', 'bookingSeats.seat']));

        $mail->assertSeeInHtml($session->film->title);
        $mail->assertSeeInHtml('Зал 1');
        $mail->assertSeeInHtml('300');
        $mail->assertHasSubject('Подтверждение брони — '.$session->film->title);
    }
}
