<?php

namespace App\Repositories\Eloquent;

use App\Models\Film;
use App\Repositories\Contracts\FilmRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentFilmRepository implements FilmRepositoryInterface
{
    /**
     * @return Collection<int, Film>
     */
    public function all(): Collection
    {
        return Film::query()->orderBy('title')->get();
    }

    public function findWithGenres(int $id): Film
    {
        return Film::query()->with('genres')->findOrFail($id);
    }
}
