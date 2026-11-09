<?php

namespace App\Repositories\Eloquent;

use App\Models\CinemaSession;
use App\Repositories\Contracts\CinemaSessionRepositoryInterface;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Date;

class EloquentCinemaSessionRepository implements CinemaSessionRepositoryInterface
{
    public function paginate(?string $search, ?int $hallId, int $perPage): LengthAwarePaginator
    {
        return CinemaSession::query()
            ->with(['film', 'hall'])
            ->when($search, fn ($query, $search) => $query->whereHas(
                'film',
                fn ($query) => $query->where('title', 'like', "%{$search}%")
            ))
            ->when($hallId, fn ($query, $hallId) => $query->where('hall_id', $hallId))
            ->orderByDesc('starts_at')
            ->paginate($perPage);
    }

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
    public function forDate(CarbonInterface $date): Collection
    {
        $rangeStart = $date->isSameDay(Date::today()) ? Date::now() : $date->clone()->startOfDay();

        return CinemaSession::query()
            ->with(['film', 'hall'])
            ->where('starts_at', '>=', $rangeStart)
            ->where('starts_at', '<=', $date->clone()->endOfDay())
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

    /**
     * @return Collection<int, CinemaSession>
     */
    public function all(): Collection
    {
        return CinemaSession::query()
            ->with(['film', 'hall'])
            ->orderByDesc('starts_at')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): CinemaSession
    {
        return CinemaSession::create($attributes);
    }

    public function delete(CinemaSession $session): void
    {
        $session->delete();
    }

    /**
     * A film's duration varies per session, so overlap can't be expressed
     * as a single SQL range condition against a fixed slot length: fetch
     * hall sessions starting before the candidate's end and compare each
     * one's actual [start, start + duration + cleanup) window in PHP.
     */
    public function overlapsExisting(int $hallId, CarbonInterface $start, CarbonInterface $end): bool
    {
        return CinemaSession::query()
            ->with('film')
            ->where('hall_id', $hallId)
            ->where('starts_at', '<', $end)
            ->get()
            ->contains(function (CinemaSession $session) use ($start) {
                $sessionEnd = $session->starts_at->clone()->addMinutes($session->film->duration_minutes + 15);

                return $sessionEnd->gt($start);
            });
    }
}
