<?php

namespace App\Http\Controllers;

use App\Data\Film\FilmData;
use App\Data\Film\FilmListItemData;
use App\Services\FilmService;
use Inertia\Inertia;
use Inertia\Response;

class FilmController extends Controller
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

        return Inertia::render('Films/Index', [
            'films' => $films,
        ]);
    }

    public function show(int $film): Response
    {
        return Inertia::render('Films/Show', [
            'film' => FilmData::fromModel($this->films->find($film)),
        ]);
    }
}
