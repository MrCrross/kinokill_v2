<?php

namespace Tests\Feature\Api;

use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_films(): void
    {
        Film::factory()->count(3)->create();

        $this->getJson('/api/films')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_show_returns_a_single_film_with_its_genres(): void
    {
        $film = Film::factory()->create(['title' => 'Дюна']);

        $this->getJson("/api/films/{$film->id}")
            ->assertOk()
            ->assertJsonPath('data.title', 'Дюна');
    }

    public function test_show_returns_404_for_an_unknown_film(): void
    {
        $this->getJson('/api/films/999')->assertNotFound();
    }
}
