<?php

namespace App\Services;

use App\Models\CinemaSession;
use App\Repositories\Contracts\CinemaSessionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CinemaSessionService
{
    public function __construct(
        private readonly CinemaSessionRepositoryInterface $sessions,
    ) {}

    /**
     * @return Collection<int, CinemaSession>
     */
    public function upcoming(): Collection
    {
        return $this->sessions->upcoming();
    }

    /**
     * @return Collection<int, CinemaSession>
     */
    public function upcomingForFilm(int $filmId): Collection
    {
        return $this->sessions->upcomingForFilm($filmId);
    }

    public function find(int $id): CinemaSession
    {
        return $this->sessions->findWithHallAndSeats($id);
    }
}
