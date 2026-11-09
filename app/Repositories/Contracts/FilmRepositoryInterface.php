<?php

namespace App\Repositories\Contracts;

use App\Models\Film;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface FilmRepositoryInterface
{
    /**
     * @return Collection<int, Film>
     */
    public function all(): Collection;

    public function paginate(?string $search, ?int $genreId, int $perPage): LengthAwarePaginator;

    public function findWithGenres(int $id): Film;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Film;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Film $film, array $attributes): Film;

    public function delete(Film $film): void;

    public function hasUpcomingSessions(Film $film): bool;
}
