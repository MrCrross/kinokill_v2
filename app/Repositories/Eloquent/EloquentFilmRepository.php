<?php

namespace App\Repositories\Eloquent;

use App\Models\Film;
use App\Repositories\Contracts\FilmRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Date;

class EloquentFilmRepository implements FilmRepositoryInterface
{
    /**
     * @return Collection<int, Film>
     */
    public function all(): Collection
    {
        return Film::query()->orderBy('title')->get();
    }

    public function paginate(?string $search, ?int $genreId, int $perPage): LengthAwarePaginator
    {
        return Film::query()
            ->when($search, fn ($query, $search) => $query->where('title', 'like', "%{$search}%"))
            ->when($genreId, fn ($query, $genreId) => $query->whereHas(
                'genres',
                fn ($query) => $query->where('genres.id', $genreId)
            ))
            ->orderBy('title')
            ->paginate($perPage);
    }

    public function findWithGenres(int $id): Film
    {
        return Film::query()->with('genres')->findOrFail($id);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Film
    {
        return Film::create($attributes);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Film $film, array $attributes): Film
    {
        $film->update($attributes);

        return $film->fresh();
    }

    public function delete(Film $film): void
    {
        $film->delete();
    }

    public function hasUpcomingSessions(Film $film): bool
    {
        return $film->cinemaSessions()->where('starts_at', '>=', Date::now())->exists();
    }
}
