<?php

namespace App\Data\Hall;

use Spatie\LaravelData\Data;

class UpdateHallData extends Data
{
    public function __construct(
        public string $name,
        public float $priceStandard,
        public float $priceVip,
    ) {}
}
