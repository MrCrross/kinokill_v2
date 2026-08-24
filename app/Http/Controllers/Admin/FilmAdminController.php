<?php

namespace App\Http\Controllers\Admin;

use App\Data\Film\FilmData;
use App\Data\Film\FilmListItemData;
use App\Data\Film\GenreData;
use App\Data\Film\StoreFilmData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Film\StoreFilmRequest;
use App\Http\Requests\Film\UpdateFilmRequest;
use App\Models\Genre;
use App\Services\FilmService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FilmAdminController extends Controller
{
    public function __construct(
        private readonly FilmService $films,
    ) {}

    public function index(): Response
    {
        $films = $this->films->list()
            ->map(fn ($film) => FilmListItemData::fromModel($film))
            ->values()
            ->all();

        return Inertia::render('Admin/Films/Index', ['films' => $films]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Films/Create', ['genres' => $this->genreOptions()]);
    }

    public function store(StoreFilmRequest $request): RedirectResponse
    {
        $this->films->create($this->dataFromRequest($request), $request->file('poster'));

        return redirect()->route('admin.films.index')->with('success', 'Фильм добавлен.');
    }

    public function edit(int $film): Response
    {
        return Inertia::render('Admin/Films/Edit', [
            'film' => FilmData::fromModel($this->films->find($film)),
            'genres' => $this->genreOptions(),
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
     * @return array<int, GenreData>
     */
    private function genreOptions(): array
    {
        return Genre::query()->orderBy('name')->get()
            ->map(fn ($genre) => GenreData::fromModel($genre))
            ->all();
    }
}
