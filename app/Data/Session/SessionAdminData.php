<?php

namespace App\Data\Session;

use App\Models\CinemaSession;
use Spatie\LaravelData\Data;

class SessionAdminData extends Data
{
    public function __construct(
        public int $id,
        public string $filmTitle,
        public string $hallName,
        public string $startsAt,
    ) {}

    public static function fromModel(CinemaSession $session): self
    {
        return new self(
            id: $session->id,
            filmTitle: $session->film->title,
            hallName: $session->hall->name,
            startsAt: $session->starts_at->toIso8601String(),
        );
    }
}
