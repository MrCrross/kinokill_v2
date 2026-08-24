<?php

namespace App\Repositories\Eloquent;

use App\Models\Hall;
use App\Repositories\Contracts\HallRepositoryInterface;

class EloquentHallRepository implements HallRepositoryInterface
{
    public function findWithSeats(int $id): Hall
    {
        return Hall::query()->with('seats')->findOrFail($id);
    }
}
