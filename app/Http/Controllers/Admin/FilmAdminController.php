<?php

namespace App\Http\Controllers\Admin;

use App\Data\Film\FilmData;
use App\Data\Film\GenreData;
use App\Data\Film\StoreFilmData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Film\StoreFilmRequest;
use App\Http\Requests\Film\UpdateFilmRequest;
use App\Models\Country;
use App\Models\Director;
use App\Models\Film;
use App\Models\Genre;
use App\Services\FilmService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FilmAdminController extends Controller
{
    public function __construct(
        private readonly FilmService $films,
    ) {}

    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->value() ?: null;
        $genreId = $request->integer('genre_id') ?: null;

        return Inertia::render('Admin/Films/Index', [
            'films' => $this->films->paginate($search, $genreId),
            'genres' => $this->genreOptions(),
            'filters' => ['search' => $search, 'genre_id' => $genreId],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Films/Create', $this->formOptions());
    }

    public function store(StoreFilmRequest $request): RedirectResponse
    {
        $this->films->create($this->dataFromRequest($request), $request->file('poster'));

        return redirect()->route('admin.films.index')->with('success', 'Фильм добавлен.');
    }

    public function edit(int $film): Response
    {
        $film = $this->films->find($film);

        return Inertia::render('Admin/Films/Edit', [
            'film' => FilmData::fromModel($film),
            ...$this->formOptions(extraCountries: [$film->country]),
        ]);
    }

    public function update(UpdateFilmRequest $request, int $film): RedirectResponse
    {
        $this->films->update($this->films->find($film), $this->dataFromRequest($request), $request->file('poster'));

        return redirect()->route('admin.films.index')->with('success', 'Фильм обновлён.');
    }

    public function destroy(int $film): RedirectResponse
    {
        $this->films->delete($this->films->find($film));

        return redirect()->route('admin.films.index')->with('success', 'Фильм удалён.');
    }

    private function dataFromRequest(StoreFilmRequest $request): StoreFilmData
    {
        return new StoreFilmData(
            title: $request->validated('title'),
            producer: $request->validated('producer'),
            country: $request->validated('country'),
            durationMinutes: (int) $request->validated('duration_minutes'),
            ageLimit: (int) $request->validated('age_limit'),
            rating: $request->validated('rating') !== null ? (float) $request->validated('rating') : null,
            description: $request->validated('description'),
            genreIds: $request->validated('genre_ids') ?? [],
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(array $extraCountries = []): array
    {
        return [
            'genres' => $this->genreOptions(),
            'countries' => array_values(array_unique([
                ...Country::query()->orderBy('name')->pluck('name')->all(),
                ...$extraCountries,
            ])),
            'producers' => Director::query()->orderBy('name')->pluck('name')->all(),
            'ageRatings' => config('films.age_ratings'),
        ];
    }

    /**
     * @return array<int, GenreData>
     */
    private function genreOptions(): array
    {
        return Genre::query()->orderBy('name')->get()
            ->map(fn ($genre) => GenreData::fromModel($genre))
            ->all();
    }
}
