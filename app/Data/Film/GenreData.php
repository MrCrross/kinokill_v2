<?php

namespace App\Data\Film;

use App\Models\Genre;
use Spatie\LaravelData\Data;

class GenreData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}

    public static function fromModel(Genre $genre): self
    {
        return new self($genre->id, $genre->name);
    }
}
