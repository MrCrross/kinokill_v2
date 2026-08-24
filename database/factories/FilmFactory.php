<?php

namespace Database\Factories;

use App\Models\Film;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Film>
 */
class FilmFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'producer' => fake()->name(),
            'country' => fake()->country(),
            'duration_minutes' => fake()->numberBetween(80, 180),
            'age_limit' => fake()->randomElement([0, 6, 12, 16, 18]),
            'rating' => fake()->randomFloat(1, 1, 10),
            'description' => fake()->paragraph(),
            'poster_path' => null,
        ];
    }
}
