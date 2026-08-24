<?php

namespace App\Data\Hall;

use Spatie\LaravelData\Data;

class StoreHallData extends Data
{
    public function __construct(
        public string $name,
        public int $rowsCount,
        public int $columnsCount,
        public float $priceStandard,
        public float $priceVip,
    ) {}
}
