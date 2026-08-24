<?php

namespace App\Data\Hall;

use App\Models\Hall;
use Spatie\LaravelData\Data;

class HallOptionData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}

    public static function fromModel(Hall $hall): self
    {
        return new self($hall->id, $hall->name);
    }
}
