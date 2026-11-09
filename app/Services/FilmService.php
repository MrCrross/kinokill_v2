<?php

namespace App\Services;

use App\Data\Film\FilmListItemData;
use App\Data\Film\StoreFilmData;
use App\Data\Pagination\PaginatedData;
use App\Exceptions\HasUpcomingSessionsException;
use App\Models\Director;
use App\Models\Film;
use App\Repositories\Contracts\FilmRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

    public function paginate(?string $search, ?int $genreId): PaginatedData
    {
        return PaginatedData::fromPaginator(
            $this->films->paginate($search, $genreId, perPage: 15),
            fn (Film $film) => FilmListItemData::fromModel($film),
        );
    }

    public function find(int $id): Film
    {
        return $this->films->findWithGenres($id);
    }

    public function create(StoreFilmData $data, ?UploadedFile $poster): Film
    {
        $film = $this->films->create([
            ...$this->attributesFromData($data),
            'poster_path' => $poster ? $poster->store('posters', 'public') : null,
        ]);

        $film->genres()->sync($data->genreIds);
        $this->registerDirector($data->producer);

        return $film->fresh('genres');
    }

    public function update(Film $film, StoreFilmData $data, ?UploadedFile $poster): Film
    {
        $attributes = $this->attributesFromData($data);

        if ($poster) {
            if ($film->poster_path) {
                Storage::disk('public')->delete($film->poster_path);
            }

            $attributes['poster_path'] = $poster->store('posters', 'public');
        }

        $film = $this->films->update($film, $attributes);
        $film->genres()->sync($data->genreIds);
        $this->registerDirector($data->producer);

        return $film->fresh('genres');
    }

    /**
     * @throws HasUpcomingSessionsException
     */
    public function delete(Film $film): void
    {
        if ($this->films->hasUpcomingSessions($film)) {
            throw HasUpcomingSessionsException::forFilm();
        }

        $this->films->delete($film);
    }

    /**
     * @return array<string, mixed>
     */
    private function attributesFromData(StoreFilmData $data): array
    {
        return [
            'title' => $data->title,
            'producer' => $data->producer,
            'country' => $data->country,
            'duration_minutes' => $data->durationMinutes,
            'age_limit' => $data->ageLimit,
            'rating' => $data->rating,
            'description' => $data->description,
        ];
    }

    /**
     * Saving a film with a new director name (the "Новый режиссёр" option in
     * the form) registers it in the reference list automatically.
     */
    private function registerDirector(string $name): void
    {
        Director::query()->firstOrCreate(['name' => $name]);
    }
}
