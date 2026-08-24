<?php

namespace App\Data\Film;

use App\Models\Film;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelData\Data;

class FilmData extends Data
{
    /**
     * @param  array<int, GenreData>  $genres
     */
    public function __construct(
        public int $id,
        public string $title,
        public string $producer,
        public string $country,
        public int $durationMinutes,
        public int $ageLimit,
        public ?string $rating,
        public string $description,
        public ?string $posterUrl,
        public array $genres,
    ) {}

    public static function fromModel(Film $film): self
    {
        return new self(
            id: $film->id,
            title: $film->title,
            producer: $film->producer,
            country: $film->country,
            durationMinutes: $film->duration_minutes,
            ageLimit: $film->age_limit,
            rating: $film->rating !== null ? (string) $film->rating : null,
            description: $film->description,
            posterUrl: $film->poster_path ? Storage::url($film->poster_path) : null,
            genres: $film->genres->map(fn ($genre) => GenreData::fromModel($genre))->all(),
        );
    }
}
