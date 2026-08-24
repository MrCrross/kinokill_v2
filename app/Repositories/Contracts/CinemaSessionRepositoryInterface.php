<?php

namespace App\Repositories\Contracts;

use App\Models\CinemaSession;
use Illuminate\Database\Eloquent\Collection;

interface CinemaSessionRepositoryInterface
{
    /**
     * @return Collection<int, CinemaSession>
     */
    public function upcoming(): Collection;

    /**
     * @return Collection<int, CinemaSession>
     */
    public function upcomingForFilm(int $filmId): Collection;

    public function findWithHallAndSeats(int $id): CinemaSession;
}
