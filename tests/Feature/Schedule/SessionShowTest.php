<?php

namespace Tests\Feature\Schedule;

use App\Data\Session\SessionDetailData;
use App\Models\CinemaSession;
use App\Models\Hall;
use App\Services\CinemaSessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SessionShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_the_seat_map_for_a_session(): void
    {
        $hall = Hall::factory()->withSeats()->create([
            'rows_count' => 8,
            'columns_count' => 10,
            'price_standard' => 300,
            'price_vip' => 500,
        ]);
        $session = CinemaSession::factory()->create(['hall_id' => $hall->id]);

        $response = $this->get("/sessions/{$session->id}");

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Booking/Show')
                ->has('session.seats', 80)
                ->where('session.hall.rowsCount', 8)
                ->where('session.hall.columnsCount', 10)
        );
    }

    public function test_back_rows_are_priced_as_vip(): void
    {
        $hall = Hall::factory()->withSeats()->create([
            'rows_count' => 8,
            'columns_count' => 10,
            'price_standard' => 300,
            'price_vip' => 500,
        ]);
        $session = CinemaSession::factory()->create(['hall_id' => $hall->id]);

        $data = SessionDetailData::fromModel(
            app(CinemaSessionService::class)->find($session->id)
        );

        $seats = collect($data->seats);
        $rowSeven = $seats->firstWhere('row', 7);
        $rowOne = $seats->firstWhere('row', 1);

        $this->assertSame('vip', $rowSeven->type);
        $this->assertSame('500.00', $rowSeven->price);
        $this->assertSame('standard', $rowOne->type);
        $this->assertSame('300.00', $rowOne->price);
    }
}
