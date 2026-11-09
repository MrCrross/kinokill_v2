<?php

namespace App\Services;

use App\Data\Pagination\PaginatedData;
use App\Data\Session\SessionAdminData;
use App\Data\Session\StoreSessionData;
use App\Exceptions\SessionOverlapException;
use App\Models\CinemaSession;
use App\Models\Film;
use App\Repositories\Contracts\CinemaSessionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class CinemaSessionService
{
    public function __construct(
        private readonly CinemaSessionRepositoryInterface $sessions,
    ) {}

    public function paginate(?string $search, ?int $hallId): PaginatedData
    {
        return PaginatedData::fromPaginator(
            $this->sessions->paginate($search, $hallId, perPage: 15),
            fn (CinemaSession $session) => SessionAdminData::fromModel($session),
        );
    }

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
    public function forDate(Carbon $date): Collection
    {
        return $this->sessions->forDate($date);
    }

    /**
     * @return Collection<int, CinemaSession>
     */
    public function upcomingForFilm(int $filmId): Collection
    {
        return $this->sessions->upcomingForFilm($filmId);
    }

    /**
     * @return Collection<int, CinemaSession>
     */
    public function list(): Collection
    {
        return $this->sessions->all();
    }

    public function find(int $id): CinemaSession
    {
        return $this->sessions->findWithHallAndSeats($id);
    }

    /**
     * @throws SessionOverlapException
     */
    public function create(StoreSessionData $data): CinemaSession
    {
        $startsAt = Carbon::parse($data->startsAt);
        $duration = Film::query()->findOrFail($data->filmId)->duration_minutes;
        $endsAt = $startsAt->clone()->addMinutes($duration + 15);

        if ($this->sessions->overlapsExisting($data->hallId, $startsAt, $endsAt)) {
            throw SessionOverlapException::create();
        }

        return $this->sessions->create([
            'film_id' => $data->filmId,
            'hall_id' => $data->hallId,
            'starts_at' => $startsAt,
            'price_standard' => $data->priceStandard,
            'price_vip' => $data->priceVip,
        ]);
    }

    public function delete(CinemaSession $session): void
    {
        $this->sessions->delete($session);
    }
}
