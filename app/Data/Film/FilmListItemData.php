<?php

namespace App\Data\Film;

use App\Models\Film;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelData\Data;

class FilmListItemData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public string $country,
        public int $ageLimit,
        public ?string $posterUrl,
    ) {}

    public static function fromModel(Film $film): self
    {
        return new self(
            id: $film->id,
            title: $film->title,
            country: $film->country,
            ageLimit: $film->age_limit,
            posterUrl: $film->poster_path ? Storage::url($film->poster_path) : null,
        );
    }
}
