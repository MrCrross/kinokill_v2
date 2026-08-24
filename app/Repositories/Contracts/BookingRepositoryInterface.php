<?php

namespace App\Repositories\Contracts;

use App\Models\Booking;
use App\Models\BookingSeat;

interface BookingRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Booking;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createSeat(array $attributes): BookingSeat;

    public function findWithDetails(int $id): Booking;
}
