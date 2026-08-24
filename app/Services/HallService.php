<?php

namespace App\Services;

use App\Models\Hall;
use App\Repositories\Contracts\HallRepositoryInterface;

class HallService
{
    public function __construct(
        private readonly HallRepositoryInterface $halls,
    ) {}

    public function find(int $id): Hall
    {
        return $this->halls->findWithSeats($id);
    }
}
