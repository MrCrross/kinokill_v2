<?php

namespace App\Data\Session;

use App\Models\CinemaSession;
use Spatie\LaravelData\Data;

class SessionListItemData extends Data
{
    public function __construct(
        public int $id,
        public string $startsAt,
        public string $hallName,
    ) {}

    public static function fromModel(CinemaSession $session): self
    {
        return new self(
            id: $session->id,
            startsAt: $session->starts_at->toIso8601String(),
            hallName: $session->hall->name,
        );
    }
}
