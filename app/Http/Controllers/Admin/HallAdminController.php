<?php

namespace App\Http\Controllers\Admin;

use App\Data\Hall\HallAdminData;
use App\Data\Hall\SeatMapEntryData;
use App\Data\Hall\StoreHallData;
use App\Data\Hall\UpdateHallData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Hall\StoreHallRequest;
use App\Http\Requests\Hall\UpdateHallRequest;
use App\Services\HallService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HallAdminController extends Controller
{
    public function __construct(
        private readonly HallService $halls,
    ) {}

    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->value() ?: null;

        return Inertia::render('Admin/Halls/Index', [
            'halls' => $this->halls->paginate($search),
            'filters' => ['search' => $search],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Halls/Create');
    }

    public function store(StoreHallRequest $request): RedirectResponse
    {
        $this->halls->create(new StoreHallData(
            name: $request->validated('name'),
            rowsCount: (int) $request->validated('rows_count'),
            columnsCount: (int) $request->validated('columns_count'),
            priceStandard: (float) $request->validated('price_standard'),
            priceVip: (float) $request->validated('price_vip'),
            seats: collect($request->validated('seats'))
                ->map(fn (array $seat) => new SeatMapEntryData(
                    rowNumber: (int) $seat['row_number'],
                    columnNumber: (int) $seat['column_number'],
                    seatType: $seat['seat_type'],
                ))
                ->all(),
        ));

        return redirect()->route('admin.halls.index')->with('success', 'Зал добавлен.');
    }

    public function edit(int $hall): Response
    {
        return Inertia::render('Admin/Halls/Edit', [
            'hall' => HallAdminData::fromModel($this->halls->find($hall)),
        ]);
    }

    public function update(UpdateHallRequest $request, int $hall): RedirectResponse
    {
        $this->halls->update($this->halls->find($hall), new UpdateHallData(
            name: $request->validated('name'),
            priceStandard: (float) $request->validated('price_standard'),
            priceVip: (float) $request->validated('price_vip'),
        ));

        return redirect()->route('admin.halls.index')->with('success', 'Зал обновлён.');
    }

    public function destroy(int $hall): RedirectResponse
    {
        $this->halls->delete($this->halls->find($hall));

        return redirect()->route('admin.halls.index')->with('success', 'Зал удалён.');
    }
}
