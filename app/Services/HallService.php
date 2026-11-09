<?php

namespace App\Services;

use App\Data\Hall\HallAdminData;
use App\Data\Hall\SeatMapEntryData;
use App\Data\Hall\StoreHallData;
use App\Data\Hall\UpdateHallData;
use App\Data\Pagination\PaginatedData;
use App\Exceptions\HasUpcomingSessionsException;
use App\Models\Hall;
use App\Repositories\Contracts\HallRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class HallService
{
    public function __construct(
        private readonly HallRepositoryInterface $halls,
    ) {}

    public function find(int $id): Hall
    {
        return $this->halls->findWithSeats($id);
    }

    /**
     * @return Collection<int, Hall>
     */
    public function list(): Collection
    {
        return $this->halls->all();
    }

    public function paginate(?string $search): PaginatedData
    {
        return PaginatedData::fromPaginator(
            $this->halls->paginate($search, perPage: 15),
            fn (Hall $hall) => HallAdminData::fromModel($hall),
        );
    }

    public function create(StoreHallData $data): Hall
    {
        $hall = $this->halls->create([
            'name' => $data->name,
            'rows_count' => $data->rowsCount,
            'columns_count' => $data->columnsCount,
            'price_standard' => $data->priceStandard,
            'price_vip' => $data->priceVip,
        ]);

        $hall->createSeats(array_map(fn (SeatMapEntryData $seat) => [
            'row_number' => $seat->rowNumber,
            'column_number' => $seat->columnNumber,
            'seat_type' => $seat->seatType,
        ], $data->seats));

        return $hall;
    }

    /**
     * The seat grid is fixed at creation time: resizing it after sessions
     * may already reference specific seats would be destructive, so only
     * name/pricing can be changed here.
     */
    public function update(Hall $hall, UpdateHallData $data): Hall
    {
        return $this->halls->update($hall, [
            'name' => $data->name,
            'price_standard' => $data->priceStandard,
            'price_vip' => $data->priceVip,
        ]);
    }

    /**
     * @throws HasUpcomingSessionsException
     */
    public function delete(Hall $hall): void
    {
        if ($this->halls->hasUpcomingSessions($hall)) {
            throw HasUpcomingSessionsException::forHall();
        }

        $this->halls->delete($hall);
    }
}
