<?php

namespace Tests\Feature\Booking;

use App\Models\Booking;
use App\Models\CinemaSession;
use App\Models\Customer;
use App\Models\Hall;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestCustomerLinkingTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_booking_becomes_visible_after_registering_with_the_same_email(): void
    {
        $hall = Hall::factory()->withSeats()->create();
        $session = CinemaSession::factory()->create(['hall_id' => $hall->id]);
        $seat = $hall->seats()->first();

        $this->post("/sessions/{$session->id}/bookings", [
            'seat_ids' => [$seat->id],
            'payment_method' => 'cash',
            'guest_name' => 'Future Member',
            'guest_email' => 'future-member@example.com',
        ]);
        $booking = Booking::firstOrFail();
        $this->assertNull($booking->customer->user_id);

        $this->post('/register', [
            'name' => 'Future Member',
            'email' => 'future-member@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'future-member@example.com')->firstOrFail();

        $this->assertSame(1, Customer::where('email', 'future-member@example.com')->count());
        $this->assertSame($user->id, $booking->customer->fresh()->user_id);
        $this->assertTrue($user->customer->bookings->contains($booking));
    }
}
