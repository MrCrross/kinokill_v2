<?php

namespace Tests\Feature\Api;

use App\Models\CinemaSession;
use App\Models\Hall;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_groups_upcoming_sessions_by_film(): void
    {
        $hall = Hall::factory()->withSeats()->create();
        CinemaSession::factory()->create(['hall_id' => $hall->id]);

        $this->getJson('/api/sessions')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure(['data' => [['film', 'sessions']]]);
    }

    public function test_show_returns_the_seat_map(): void
    {
        $hall = Hall::factory()->withSeats()->create([
            'rows_count' => 2,
            'columns_count' => 3,
        ]);
        $session = CinemaSession::factory()->create(['hall_id' => $hall->id]);

        $this->getJson("/api/sessions/{$session->id}")
            ->assertOk()
            ->assertJsonCount(6, 'data.seats');
    }
}
