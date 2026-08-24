<?php

namespace App\Services;

use App\Models\Film;
use App\Repositories\Contracts\FilmRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class FilmService
{
    public function __construct(
        private readonly FilmRepositoryInterface $films,
    ) {}

    /**
     * @return Collection<int, Film>
     */
    public function list(): Collection
    {
        return $this->films->all();
    }

    public function find(int $id): Film
    {
        return $this->films->findWithGenres($id);
    }
}
