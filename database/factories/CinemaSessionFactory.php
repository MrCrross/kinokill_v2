<?php

namespace Database\Factories;

use App\Models\CinemaSession;
use App\Models\Film;
use App\Models\Hall;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CinemaSession>
 */
class CinemaSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'film_id' => Film::factory(),
            'hall_id' => Hall::factory()->withSeats(),
            'starts_at' => fake()->dateTimeBetween('now', '+7 days'),
            'price_standard' => null,
            'price_vip' => null,
        ];
    }
}
