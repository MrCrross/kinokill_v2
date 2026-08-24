<?php

namespace App\Http\Controllers;

use App\Data\Film\FilmListItemData;
use App\Data\Session\FilmScheduleData;
use App\Data\Session\SessionDetailData;
use App\Data\Session\SessionListItemData;
use App\Services\CinemaSessionService;
use Inertia\Inertia;
use Inertia\Response;

class SessionController extends Controller
{
    public function __construct(
        private readonly CinemaSessionService $sessions,
    ) {}

    public function index(): Response
    {
        $schedule = $this->sessions->upcoming()
            ->groupBy('film_id')
            ->map(fn ($sessions) => new FilmScheduleData(
                film: FilmListItemData::fromModel($sessions->first()->film),
                sessions: $sessions->map(fn ($session) => SessionListItemData::fromModel($session))->all(),
            ))
            ->values()
            ->all();

        return Inertia::render('Sessions/Index', [
            'schedule' => $schedule,
        ]);
    }

    public function show(int $session): Response
    {
        return Inertia::render('Booking/Show', [
            'session' => SessionDetailData::fromModel($this->sessions->find($session)),
        ]);
    }
}
