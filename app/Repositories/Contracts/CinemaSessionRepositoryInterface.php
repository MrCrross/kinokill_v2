<?php

namespace App\Repositories\Contracts;

use App\Models\CinemaSession;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CinemaSessionRepositoryInterface
{
    public function paginate(?string $search, ?int $hallId, int $perPage): LengthAwarePaginator;

    /**
     * @return Collection<int, CinemaSession>
     */
    public function upcoming(): Collection;

    /**
     * Sessions starting on the given day, excluding those already started.
     *
     * @return Collection<int, CinemaSession>
     */
    public function forDate(CarbonInterface $date): Collection;

    /**
     * @return Collection<int, CinemaSession>
     */
    public function upcomingForFilm(int $filmId): Collection;

    /**
     * @return Collection<int, CinemaSession>
     */
    public function all(): Collection;

    public function findWithHallAndSeats(int $id): CinemaSession;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): CinemaSession;

    public function delete(CinemaSession $session): void;

    public function overlapsExisting(int $hallId, CarbonInterface $start, CarbonInterface $end): bool;
}
