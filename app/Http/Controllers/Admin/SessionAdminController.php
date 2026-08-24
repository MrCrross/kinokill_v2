<?php

namespace App\Http\Controllers\Admin;

use App\Data\Film\FilmOptionData;
use App\Data\Hall\HallOptionData;
use App\Data\Session\SessionAdminData;
use App\Data\Session\StoreSessionData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Session\StoreSessionRequest;
use App\Models\CinemaSession;
use App\Models\Film;
use App\Models\Hall;
use App\Services\CinemaSessionService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SessionAdminController extends Controller
{
    public function __construct(
        private readonly CinemaSessionService $sessions,
    ) {}

    public function index(): Response
    {
        $sessions = $this->sessions->list()
            ->map(fn ($session) => SessionAdminData::fromModel($session))
            ->values()
            ->all();

        return Inertia::render('Admin/Sessions/Index', ['sessions' => $sessions]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Sessions/Create', [
            'films' => Film::query()->orderBy('title')->get()->map(fn ($film) => FilmOptionData::fromModel($film))->all(),
            'halls' => Hall::query()->orderBy('name')->get()->map(fn ($hall) => HallOptionData::fromModel($hall))->all(),
        ]);
    }

    public function store(StoreSessionRequest $request): RedirectResponse
    {
        $this->sessions->create(new StoreSessionData(
            filmId: (int) $request->validated('film_id'),
            hallId: (int) $request->validated('hall_id'),
            startsAt: $request->validated('starts_at'),
            priceStandard: $request->validated('price_standard') !== null ? (float) $request->validated('price_standard') : null,
            priceVip: $request->validated('price_vip') !== null ? (float) $request->validated('price_vip') : null,
        ));

        return redirect()->route('admin.sessions.index')->with('success', 'Сеанс добавлен.');
    }

    public function destroy(CinemaSession $session): RedirectResponse
    {
        $this->sessions->delete($session);

        return redirect()->route('admin.sessions.index')->with('success', 'Сеанс удалён.');
    }
}
