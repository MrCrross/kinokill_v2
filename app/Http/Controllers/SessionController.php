<?php

namespace App\Http\Controllers;

use App\Data\Film\FilmListItemData;
use App\Data\Session\FilmScheduleData;
use App\Data\Session\ScheduleDayData;
use App\Data\Session\SessionDetailData;
use App\Data\Session\SessionListItemData;
use App\Services\CinemaSessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class SessionController extends Controller
{
    /**
     * Sessions further out than this are not shown on the schedule page.
     */
    private const SCHEDULE_DAYS_AHEAD = 7;

    public function __construct(
        private readonly CinemaSessionService $sessions,
    ) {}

    public function index(Request $request): Response
    {
        $today = Carbon::today();
        $lastAvailableDay = $today->clone()->addDays(self::SCHEDULE_DAYS_AHEAD - 1);
        $selectedDate = $this->resolveSelectedDate($request->query('date'), $today, $lastAvailableDay);

        $schedule = $this->sessions->forDate($selectedDate)
            ->groupBy('film_id')
            ->map(fn ($sessions) => new FilmScheduleData(
                film: FilmListItemData::fromModel($sessions->first()->film),
                sessions: $sessions->map(fn ($session) => SessionListItemData::fromModel($session))->all(),
            ))
            ->values()
            ->all();

        $days = [];
        for ($i = 0; $i < self::SCHEDULE_DAYS_AHEAD; $i++) {
            $date = $today->clone()->addDays($i);
            $days[] = new ScheduleDayData(
                date: $date->toDateString(),
                isSelected: $date->isSameDay($selectedDate),
            );
        }

        return Inertia::render('Sessions/Index', [
            'schedule' => $schedule,
            'days' => $days,
        ]);
    }

    private function resolveSelectedDate(mixed $requestedDate, Carbon $today, Carbon $lastAvailableDay): Carbon
    {
        if (! is_string($requestedDate)) {
            return $today;
        }

        try {
            $parsed = Carbon::createFromFormat('Y-m-d', $requestedDate)->startOfDay();
        } catch (\Exception) {
            return $today;
        }

        return $parsed->between($today, $lastAvailableDay) ? $parsed : $today;
    }

    public function show(int $session): Response
    {
        return Inertia::render('Booking/Show', [
            'session' => SessionDetailData::fromModel($this->sessions->find($session)),
        ]);
    }
}
