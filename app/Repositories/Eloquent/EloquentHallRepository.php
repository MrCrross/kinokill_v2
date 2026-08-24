<?php

namespace App\Repositories\Eloquent;

use App\Models\Hall;
use App\Repositories\Contracts\HallRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Date;

class EloquentHallRepository implements HallRepositoryInterface
{
    public function findWithSeats(int $id): Hall
    {
        return Hall::query()->with('seats')->findOrFail($id);
    }

    /**
     * @return Collection<int, Hall>
     */
    public function all(): Collection
    {
        return Hall::query()->orderBy('name')->get();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Hall
    {
        return Hall::create($attributes);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Hall $hall, array $attributes): Hall
    {
        $hall->update($attributes);

        return $hall->fresh();
    }

    public function delete(Hall $hall): void
    {
        $hall->delete();
    }

    public function hasUpcomingSessions(Hall $hall): bool
    {
        return $hall->cinemaSessions()->where('starts_at', '>=', Date::now())->exists();
    }
}
