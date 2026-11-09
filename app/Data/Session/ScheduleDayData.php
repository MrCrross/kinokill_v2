<?php

namespace App\Data\Session;

use Spatie\LaravelData\Data;

class ScheduleDayData extends Data
{
    public function __construct(
        public string $date,
        public bool $isSelected,
    ) {}
}
