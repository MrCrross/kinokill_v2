<?php

namespace App\Data\Session;

use App\Models\Hall;
use Spatie\LaravelData\Data;

class HallData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public int $rowsCount,
        public int $columnsCount,
    ) {}

    public static function fromModel(Hall $hall): self
    {
        return new self(
            id: $hall->id,
            name: $hall->name,
            rowsCount: $hall->rows_count,
            columnsCount: $hall->columns_count,
        );
    }
}
