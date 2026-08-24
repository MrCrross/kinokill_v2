<?php

namespace Tests\Feature\Schedule;

use App\Models\CinemaSession;
use App\Models\Film;
use App\Models\Hall;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SessionListTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_the_schedule_grouped_by_film(): void
    {
        $hall = Hall::factory()->withSeats()->create();
        $film = Film::factory()->create(['title' => 'Dune']);
        CinemaSession::factory()->count(2)->create([
            'film_id' => $film->id,
            'hall_id' => $hall->id,
            'starts_at' => now()->addDay(),
        ]);

        $response = $this->get('/sessions');

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Sessions/Index')
                ->has('schedule', 1)
                ->where('schedule.0.film.title', 'Dune')
                ->has('schedule.0.sessions', 2)
        );
    }

    public function test_past_sessions_are_not_listed(): void
    {
        $hall = Hall::factory()->withSeats()->create();
        CinemaSession::factory()->create([
            'hall_id' => $hall->id,
            'starts_at' => now()->subDay(),
        ]);

        $response = $this->get('/sessions');

        $response->assertInertia(fn (Assert $page) => $page->has('schedule', 0));
    }

    public function test_film_show_page_lists_its_upcoming_sessions(): void
    {
        $hall = Hall::factory()->withSeats()->create(['name' => 'Red Hall']);
        $film = Film::factory()->create();
        CinemaSession::factory()->create([
            'film_id' => $film->id,
            'hall_id' => $hall->id,
            'starts_at' => now()->addHours(3),
        ]);

        $response = $this->get("/films/{$film->id}");

        $response->assertInertia(
            fn (Assert $page) => $page
                ->has('sessions', 1)
                ->where('sessions.0.hallName', 'Red Hall')
        );
    }
}
