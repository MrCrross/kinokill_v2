<?php

namespace App\Data\Session;

use Spatie\LaravelData\Data;

class StoreSessionData extends Data
{
    public function __construct(
        public int $filmId,
        public int $hallId,
        public string $startsAt,
        public ?float $priceStandard,
        public ?float $priceVip,
    ) {}
}
