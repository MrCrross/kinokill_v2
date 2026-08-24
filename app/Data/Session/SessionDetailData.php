<?php

namespace App\Data\Session;

use App\Models\CinemaSession;
use Spatie\LaravelData\Data;

class SessionDetailData extends Data
{
    /**
     * @param  array<int, SeatData>  $seats
     */
    public function __construct(
        public int $id,
        public string $filmTitle,
        public string $startsAt,
        public HallData $hall,
        public array $seats,
    ) {}

    public static function fromModel(CinemaSession $session): self
    {
        return new self(
            id: $session->id,
            filmTitle: $session->film->title,
            startsAt: $session->starts_at->toIso8601String(),
            hall: HallData::fromModel($session->hall),
            seats: $session->hall->seats->map(fn ($seat) => SeatData::fromModel($seat, $session))->all(),
        );
    }
}
