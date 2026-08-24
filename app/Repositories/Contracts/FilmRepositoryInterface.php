<?php

namespace App\Repositories\Contracts;

use App\Models\Film;
use Illuminate\Database\Eloquent\Collection;

interface FilmRepositoryInterface
{
    /**
     * @return Collection<int, Film>
     */
    public function all(): Collection;

    public function findWithGenres(int $id): Film;
}
