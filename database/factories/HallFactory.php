<?php

namespace Database\Factories;

use App\Models\Hall;
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
     * Generate this hall's seat grid (see Hall::generateSeats()).
     */
    public function withSeats(): static
    {
        return $this->afterCreating(fn (Hall $hall) => $hall->generateSeats());
    }
}
