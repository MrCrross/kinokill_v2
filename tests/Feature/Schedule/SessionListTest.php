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

    public function test_guest_can_view_todays_schedule_grouped_by_film(): void
    {
        $hall = Hall::factory()->withSeats()->create();
        $film = Film::factory()->create(['title' => 'Dune']);
        CinemaSession::factory()->count(2)->create([
            'film_id' => $film->id,
            'hall_id' => $hall->id,
            'starts_at' => now()->addHours(2),
        ]);

        $response = $this->get('/sessions');

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Sessions/Index')
                ->has('schedule', 1)
                ->where('schedule.0.film.title', 'Dune')
                ->has('schedule.0.sessions', 2)
                ->has('days', 7)
                ->where('days.0.isSelected', true)
        );
    }

    public function test_guest_can_view_a_future_days_schedule_via_the_date_query_param(): void
    {
        $hall = Hall::factory()->withSeats()->create();
        $film = Film::factory()->create(['title' => 'Dune']);
        CinemaSession::factory()->create([
            'film_id' => $film->id,
            'hall_id' => $hall->id,
            'starts_at' => now()->addDays(3)->setTime(18, 0),
        ]);

        $response = $this->get('/sessions?date='.now()->addDays(3)->toDateString());

        $response->assertInertia(
            fn (Assert $page) => $page
                ->has('schedule', 1)
                ->where('schedule.0.film.title', 'Dune')
                ->where('days.3.isSelected', true)
        );
    }

    public function test_a_date_outside_the_next_week_falls_back_to_today(): void
    {
        $response = $this->get('/sessions?date='.now()->addDays(30)->toDateString());

        $response->assertInertia(fn (Assert $page) => $page->where('days.0.isSelected', true));
    }

    public function test_todays_past_sessions_are_not_listed(): void
    {
        $hall = Hall::factory()->withSeats()->create();
        CinemaSession::factory()->create([
            'hall_id' => $hall->id,
            'starts_at' => now()->subHour(),
        ]);

        $response = $this->get('/sessions');

        $response->assertInertia(fn (Assert $page) => $page->has('schedule', 0));
    }

    public function test_other_days_sessions_are_not_listed_on_todays_schedule(): void
    {
        $hall = Hall::factory()->withSeats()->create();
        CinemaSession::factory()->create([
            'hall_id' => $hall->id,
            'starts_at' => now()->addDays(2),
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
