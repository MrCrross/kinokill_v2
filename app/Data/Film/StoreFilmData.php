<?php

namespace App\Data\Film;

use Spatie\LaravelData\Data;

class StoreFilmData extends Data
{
    /**
     * @param  array<int, int>  $genreIds
     */
    public function __construct(
        public string $title,
        public string $producer,
        public string $country,
        public int $durationMinutes,
        public int $ageLimit,
        public ?float $rating,
        public string $description,
        public array $genreIds,
    ) {}
}
