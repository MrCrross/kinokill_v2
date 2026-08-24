<?php

namespace Database\Factories;

use App\Enums\SeatType;
use App\Models\Hall;
use App\Models\Seat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Seat>
 */
class SeatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hall_id' => Hall::factory(),
            'row_number' => fake()->numberBetween(1, 10),
            'column_number' => fake()->numberBetween(1, 12),
            'seat_type' => SeatType::Standard,
        ];
    }
}
