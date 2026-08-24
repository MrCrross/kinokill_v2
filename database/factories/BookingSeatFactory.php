<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\Seat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookingSeat>
 */
class BookingSeatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $booking = Booking::factory()->create();

        return [
            'booking_id' => $booking->id,
            'seat_id' => Seat::factory(),
            'cinema_session_id' => $booking->cinema_session_id,
            'price' => fake()->randomFloat(2, 300, 500),
        ];
    }
}
