<?php

namespace Tests\Feature\Booking;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CancelExpiredBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_release_expired_command_flips_timed_out_pending_bookings(): void
    {
        $expired = Booking::factory()->create([
            'status' => BookingStatus::Pending,
            'expires_at' => now()->subMinute(),
        ]);
        $stillPending = Booking::factory()->create([
            'status' => BookingStatus::Pending,
            'expires_at' => now()->addMinutes(10),
        ]);
        $confirmed = Booking::factory()->confirmed()->create();

        $this->artisan('bookings:release-expired')->assertExitCode(0);

        $this->assertSame(BookingStatus::Expired, $expired->fresh()->status);
        $this->assertSame(BookingStatus::Pending, $stillPending->fresh()->status);
        $this->assertSame(BookingStatus::Confirmed, $confirmed->fresh()->status);
    }
}
