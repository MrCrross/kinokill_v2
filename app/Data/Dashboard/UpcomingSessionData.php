<?php

namespace App\Data\Dashboard;

use App\Models\CinemaSession;
use Spatie\LaravelData\Data;

class UpcomingSessionData extends Data
{
    public function __construct(
        public int $id,
        public string $filmTitle,
        public string $hallName,
        public string $startsAt,
        public int $bookedSeats,
        public int $capacity,
    ) {}

    public static function fromModel(CinemaSession $session): self
    {
        return new self(
            id: $session->id,
            filmTitle: $session->film->title,
            hallName: $session->hall->name,
            startsAt: $session->starts_at->toIso8601String(),
            bookedSeats: $session->booked_seats_count ?? 0,
            capacity: $session->hall->seats_count ?? 0,
        );
    }
}
