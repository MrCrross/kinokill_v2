<?php

namespace App\Http\Controllers\Api;

use App\Data\Film\FilmData;
use App\Data\Film\FilmListItemData;
use App\Http\Controllers\Controller;
use App\Services\FilmService;
use Illuminate\Http\JsonResponse;

class FilmController extends Controller
{
    public function __construct(
        private readonly FilmService $films,
    ) {}

    /**
     * List every film currently in the catalog.
     */
    public function index(): JsonResponse
    {
        $films = $this->films->list()
            ->map(fn ($film) => FilmListItemData::fromModel($film))
            ->values()
            ->all();

        return response()->json(['data' => $films]);
    }

    /**
     * Show a single film, including its genres.
     */
    public function show(int $film): JsonResponse
    {
        return response()->json(['data' => FilmData::fromModel($this->films->find($film))]);
    }
}
