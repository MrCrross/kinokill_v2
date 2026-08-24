<?php

namespace App\Repositories\Contracts;

use App\Models\Hall;
use Illuminate\Database\Eloquent\Collection;

interface HallRepositoryInterface
{
    public function findWithSeats(int $id): Hall;

    /**
     * @return Collection<int, Hall>
     */
    public function all(): Collection;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Hall;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Hall $hall, array $attributes): Hall;

    public function delete(Hall $hall): void;

    public function hasUpcomingSessions(Hall $hall): bool;
}
