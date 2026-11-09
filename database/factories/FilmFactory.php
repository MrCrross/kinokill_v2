<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\Film;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            'country' => Country::query()->inRandomOrder()->value('name')
                ?? fake()->country(),
            'duration_minutes' => fake()->numberBetween(80, 180),
            'age_limit' => fake()->randomElement(config('films.age_ratings')),
            'rating' => fake()->randomFloat(1, 1, 10),
            'description' => fake()->paragraph(),
            'poster_path' => null,
        ];
    }

    /**
     * Attach a randomly generated placeholder poster, so demo/seeded films
     * don't sit in the catalog with the "no poster" fallback. Generated
     * with GD (no network calls) so it stays fast and offline-safe; opt-in
     * because most tests don't care about posters and shouldn't pay for
     * generating and storing an image file.
     */
    public function withPoster(): static
    {
        return $this->afterCreating(fn (Film $film) => $film->update([
            'poster_path' => $this->generatePosterImage($film->title),
        ]));
    }

    private function generatePosterImage(string $title): string
    {
        $width = 400;
        $height = 600;

        $image = imagecreatetruecolor($width, $height);
        imagefilledrectangle($image, 0, 0, $width, $height, imagecolorallocate(
            $image,
            fake()->numberBetween(20, 90),
            fake()->numberBetween(20, 90),
            fake()->numberBetween(20, 90),
        ));

        $textColor = imagecolorallocate($image, 235, 235, 235);
        $font = 5;
        $lineHeight = imagefontheight($font) + 4;
        $lines = explode("\n", wordwrap($title, 18, "\n"));

        foreach ($lines as $index => $line) {
            $x = (int) (($width - imagefontwidth($font) * strlen($line)) / 2);
            $y = (int) ($height / 2 - (count($lines) * $lineHeight) / 2) + $index * $lineHeight;
            imagestring($image, $font, max($x, 8), $y, $line, $textColor);
        }

        ob_start();
        imagejpeg($image, quality: 85);
        $contents = ob_get_clean();
        imagedestroy($image);

        $path = 'posters/'.Str::uuid().'.jpg';
        Storage::disk('public')->put($path, $contents);

        return $path;
    }
}
