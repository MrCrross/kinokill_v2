<?php

namespace App\Http\Controllers\Api;

use App\Data\Film\FilmListItemData;
use App\Data\Session\FilmScheduleData;
use App\Data\Session\SessionDetailData;
use App\Data\Session\SessionListItemData;
use App\Http\Controllers\Controller;
use App\Services\CinemaSessionService;
use Illuminate\Http\JsonResponse;

class SessionController extends Controller
{
    public function __construct(
        private readonly CinemaSessionService $sessions,
    ) {}

    /**
     * Upcoming sessions, grouped by film.
     */
    public function index(): JsonResponse
    {
        /** @var array<int, FilmScheduleData> $schedule */
        $schedule = [];

        foreach ($this->sessions->upcoming()->groupBy('film_id') as $filmSessions) {
            $schedule[] = new FilmScheduleData(
                film: FilmListItemData::fromModel($filmSessions->first()->film),
                sessions: $filmSessions->map(fn ($session) => SessionListItemData::fromModel($session))->all(),
            );
        }

        return response()->json(['data' => $schedule]);
    }

    /**
     * A single session, including its hall's seat map and each seat's
     * current availability.
     */
    public function show(int $session): JsonResponse
    {
        return response()->json(['data' => SessionDetailData::fromModel($this->sessions->find($session))]);
    }
}
