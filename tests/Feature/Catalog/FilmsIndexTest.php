<?php

namespace Tests\Feature\Catalog;

use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FilmsIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_the_films_catalog(): void
    {
        $films = Film::factory()->count(3)->create();

        $response = $this->get('/films');

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Films/Index')
                ->has('films', 3)
                ->where('films.0.title', $films->sortBy('title')->first()->title)
        );
    }

    public function test_films_are_listed_alphabetically_by_title(): void
    {
        Film::factory()->create(['title' => 'Zebra']);
        Film::factory()->create(['title' => 'Alpha']);

        $response = $this->get('/films');

        $response->assertInertia(
            fn (Assert $page) => $page
                ->where('films.0.title', 'Alpha')
                ->where('films.1.title', 'Zebra')
        );
    }
}
