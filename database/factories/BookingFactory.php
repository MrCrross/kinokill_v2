<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Models\Booking;
use App\Models\CinemaSession;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'cinema_session_id' => CinemaSession::factory(),
            'status' => BookingStatus::Pending,
            'total_price' => fake()->randomFloat(2, 300, 2000),
            'payment_method' => PaymentMethod::CardStub,
            'paid_at' => null,
            'expires_at' => now()->addMinutes(15),
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::Confirmed,
            'paid_at' => now(),
            'expires_at' => null,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::Expired,
            'expires_at' => now()->subMinute(),
        ]);
    }
}
