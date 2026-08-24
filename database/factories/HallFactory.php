<?php

namespace Database\Factories;

use App\Enums\SeatType;
use App\Models\Hall;
use App\Models\Seat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Hall>
 */
class HallFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Hall '.fake()->unique()->numberBetween(1, 1000),
            'rows_count' => 8,
            'columns_count' => 10,
            'price_standard' => 300,
            'price_vip' => 500,
        ];
    }

    /**
     * Generate a rectangular grid of seats for this hall. The last two rows
     * are VIP (matches real-cinema back-row premium seating).
     */
    public function withSeats(): static
    {
        return $this->afterCreating(function (Hall $hall) {
            for ($row = 1; $row <= $hall->rows_count; $row++) {
                $seatType = $row > $hall->rows_count - 2 ? SeatType::Vip : SeatType::Standard;

                for ($column = 1; $column <= $hall->columns_count; $column++) {
                    Seat::create([
                        'hall_id' => $hall->id,
                        'row_number' => $row,
                        'column_number' => $column,
                        'seat_type' => $seatType,
                    ]);
                }
            }
        });
    }
}
