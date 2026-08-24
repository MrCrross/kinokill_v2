<?php

namespace App\Exceptions;

use RuntimeException;

class SeatAlreadyBookedException extends RuntimeException
{
    /**
     * @param  array<int, int>  $seatIds
     */
    public static function forSeats(array $seatIds): self
    {
        return new self('Одно или несколько выбранных мест уже забронированы: '.implode(', ', $seatIds));
    }

    public static function seatsNotFound(): self
    {
        return new self('Один или несколько выбранных мест не существуют.');
    }
}
