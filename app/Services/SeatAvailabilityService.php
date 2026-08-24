<?php

namespace App\Services;

use App\Exceptions\SeatAlreadyBookedException;
use App\Models\BookingSeat;
use App\Models\Seat;
use Illuminate\Database\Eloquent\Collection;

class SeatAvailabilityService
{
    /**
     * Lock the given seats and assert none of them is held by an active
     * booking for this session. Must be called inside a DB transaction:
     * locking the Seat rows is what serializes concurrent booking attempts
     * for the same seat, so the check performed right after is race-free.
     *
     * @param  array<int, int>  $seatIds
     * @return Collection<int, Seat>
     *
     * @throws SeatAlreadyBookedException
     */
    public function lockAndAssertAvailable(int $sessionId, array $seatIds): Collection
    {
        $seatIds = array_values(array_unique($seatIds));

        $seats = Seat::query()->whereIn('id', $seatIds)->lockForUpdate()->get();

        if ($seats->count() !== count($seatIds)) {
            throw SeatAlreadyBookedException::seatsNotFound();
        }

        $takenSeatIds = BookingSeat::query()
            ->where('cinema_session_id', $sessionId)
            ->whereIn('seat_id', $seatIds)
            ->whereHas('booking', fn ($query) => $query->active())
            ->pluck('seat_id');

        if ($takenSeatIds->isNotEmpty()) {
            throw SeatAlreadyBookedException::forSeats($takenSeatIds->all());
        }

        return $seats;
    }
}
