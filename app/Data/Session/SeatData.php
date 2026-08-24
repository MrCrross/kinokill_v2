<?php

namespace App\Data\Session;

use App\Enums\SeatType;
use App\Models\CinemaSession;
use App\Models\Seat;
use Spatie\LaravelData\Data;

class SeatData extends Data
{
    public function __construct(
        public int $id,
        public int $row,
        public int $column,
        public string $type,
        public string $price,
    ) {}

    public static function fromModel(Seat $seat, CinemaSession $session): self
    {
        $price = $seat->seat_type === SeatType::Vip
            ? $session->vipPrice()
            : $session->standardPrice();

        return new self(
            id: $seat->id,
            row: $seat->row_number,
            column: $seat->column_number,
            type: $seat->seat_type->value,
            price: $price,
        );
    }
}
