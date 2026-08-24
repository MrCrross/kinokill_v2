<?php

namespace App\Data\Hall;

use App\Models\Hall;
use Spatie\LaravelData\Data;

class HallAdminData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public int $rowsCount,
        public int $columnsCount,
        public string $priceStandard,
        public string $priceVip,
    ) {}

    public static function fromModel(Hall $hall): self
    {
        return new self(
            id: $hall->id,
            name: $hall->name,
            rowsCount: $hall->rows_count,
            columnsCount: $hall->columns_count,
            priceStandard: (string) $hall->price_standard,
            priceVip: (string) $hall->price_vip,
        );
    }
}
