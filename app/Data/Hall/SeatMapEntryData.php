<?php

namespace App\Data\Hall;

use Spatie\LaravelData\Data;

class SeatMapEntryData extends Data
{
    public function __construct(
        public int $rowNumber,
        public int $columnNumber,
        public string $seatType,
    ) {}
}
