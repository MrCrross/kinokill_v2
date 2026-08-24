<?php

namespace App\Data\Booking;

use App\Models\BookingSeat;
use Spatie\LaravelData\Data;

class BookingSeatData extends Data
{
    public function __construct(
        public int $row,
        public int $column,
        public string $price,
    ) {}

    public static function fromModel(BookingSeat $bookingSeat): self
    {
        return new self(
            row: $bookingSeat->seat->row_number,
            column: $bookingSeat->seat->column_number,
            price: (string) $bookingSeat->price,
        );
    }
}
