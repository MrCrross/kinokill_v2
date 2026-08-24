<?php

namespace Tests\Feature\Catalog;

use App\Models\Film;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FilmShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_a_film_with_its_genres(): void
    {
        $film = Film::factory()->create(['title' => 'Interstellar']);
        $genres = Genre::factory()->count(2)->create();
        $film->genres()->attach($genres);

        $response = $this->get("/films/{$film->id}");

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Films/Show')
                ->where('film.title', 'Interstellar')
                ->has('film.genres', 2)
        );
    }

    public function test_unknown_film_returns_404(): void
    {
        $response = $this->get('/films/999');

        $response->assertNotFound();
    }
}
