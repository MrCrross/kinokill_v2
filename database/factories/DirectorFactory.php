<?php

namespace Database\Factories;

use App\Models\Director;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Director>
 */
class DirectorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->name(),
        ];
    }
}
