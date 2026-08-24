<?php

namespace App\Repositories\Eloquent;

use App\Models\CinemaSession;
use App\Repositories\Contracts\CinemaSessionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Date;

class EloquentCinemaSessionRepository implements CinemaSessionRepositoryInterface
{
    /**
     * @return Collection<int, CinemaSession>
     */
    public function upcoming(): Collection
    {
        return CinemaSession::query()
            ->with(['film', 'hall'])
            ->where('starts_at', '>=', Date::now())
            ->orderBy('starts_at')
            ->get();
    }

    /**
     * @return Collection<int, CinemaSession>
     */
    public function upcomingForFilm(int $filmId): Collection
    {
        return CinemaSession::query()
            ->with('hall')
            ->where('film_id', $filmId)
            ->where('starts_at', '>=', Date::now())
            ->orderBy('starts_at')
            ->get();
    }

    public function findWithHallAndSeats(int $id): CinemaSession
    {
        return CinemaSession::query()
            ->with(['film', 'hall.seats'])
            ->with(['bookingSeats' => fn ($query) => $query->whereHas('booking', fn ($query) => $query->active())])
            ->findOrFail($id);
    }
}
