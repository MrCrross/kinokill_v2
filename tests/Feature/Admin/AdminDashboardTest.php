<?php

namespace Tests\Feature\Admin;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\CinemaSession;
use App\Models\Customer;
use App\Models\Film;
use App\Models\Hall;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_cannot_view_the_admin_dashboard(): void
    {
        $customer = User::factory()->customer()->create();

        $this->actingAs($customer)->get('/admin')->assertForbidden();
    }

    public function test_manager_can_view_the_admin_dashboard(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)->get('/admin')->assertOk();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_admin_sees_accurate_stats(): void
    {
        $admin = User::factory()->admin()->create();

        $hall = Hall::factory()->create(['rows_count' => 2, 'columns_count' => 5]);
        $hall->generateSeats();
        $film = Film::factory()->create();
        $session = CinemaSession::factory()->create([
            'film_id' => $film->id,
            'hall_id' => $hall->id,
            'starts_at' => now()->addDay(),
        ]);

        // Bypass BookingFactory/BookingSeatFactory here: both eagerly create
        // their own nested Booking/CinemaSession/Film/Hall as part of
        // resolving defaults, even for fields the caller then overrides —
        // fine for tests that don't count rows, but it would silently
        // inflate the aggregate counts this test asserts on.
        $booking = Booking::query()->create([
            'customer_id' => Customer::factory()->create()->id,
            'cinema_session_id' => $session->id,
            'status' => BookingStatus::Confirmed,
            'total_price' => 900,
            'payment_method' => PaymentMethod::CardStub,
            'paid_at' => now(),
        ]);
        foreach ($hall->seats()->take(3)->get() as $seat) {
            BookingSeat::query()->create([
                'booking_id' => $booking->id,
                'seat_id' => $seat->id,
                'cinema_session_id' => $session->id,
                'price' => 300,
            ]);
        }

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Admin/Dashboard')
                ->where('stats.upcomingSessionsCount', 1)
                ->where('stats.revenueLast30Days', '900.00')
                // PHP's json_encode drops the trailing .0 for whole-number
                // floats, so this round-trips through Inertia as an int.
                ->where('stats.averageOccupancyNext7Days', 30)
                ->has('stats.upcomingSessions', 1)
                ->where('stats.upcomingSessions.0.bookedSeats', 3)
                ->where('stats.upcomingSessions.0.capacity', 10)
        );
    }
}
