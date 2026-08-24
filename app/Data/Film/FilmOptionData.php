<?php

namespace App\Data\Film;

use App\Models\Film;
use Spatie\LaravelData\Data;

class FilmOptionData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
    ) {}

    public static function fromModel(Film $film): self
    {
        return new self($film->id, $film->title);
    }
}
