<?php

namespace App\Repositories\Contracts;

use App\Models\Hall;

interface HallRepositoryInterface
{
    public function findWithSeats(int $id): Hall;
}
