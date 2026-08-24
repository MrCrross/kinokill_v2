<?php

namespace App\Repositories\Eloquent;

use App\Models\Booking;
use App\Models\BookingSeat;
use App\Repositories\Contracts\BookingRepositoryInterface;

class EloquentBookingRepository implements BookingRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Booking
    {
        return Booking::create($attributes);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createSeat(array $attributes): BookingSeat
    {
        return BookingSeat::create($attributes);
    }

    public function findWithDetails(int $id): Booking
    {
        return Booking::query()
            ->with(['customer', 'cinemaSession.film', 'cinemaSession.hall', 'bookingSeats.seat'])
            ->findOrFail($id);
    }
}
