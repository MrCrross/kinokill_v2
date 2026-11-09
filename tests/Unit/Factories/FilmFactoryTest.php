<?php

namespace Tests\Unit\Factories;

use App\Data\Film\FilmListItemData;
use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FilmFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_films_have_no_poster(): void
    {
        $film = Film::factory()->create();

        $this->assertNull($film->poster_path);
    }

    public function test_with_poster_state_generates_and_stores_a_real_image(): void
    {
        Storage::fake('public');

        $film = Film::factory()->withPoster()->create(['title' => 'Дюна']);

        $this->assertNotNull($film->poster_path);
        Storage::disk('public')->assertExists($film->poster_path);
        $this->assertStringStartsWith('posters/', $film->poster_path);

        $posterUrl = FilmListItemData::fromModel($film)->posterUrl;
        $this->assertNotNull($posterUrl);
        $this->assertStringContainsString('/storage/'.$film->poster_path, $posterUrl);
    }
}
