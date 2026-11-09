<?php

namespace Tests\Feature\Admin;

use App\Models\CinemaSession;
use App\Models\Country;
use App\Models\Director;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Hall;
use App\Models\User;
use Database\Seeders\CountriesSeeder;
use Database\Seeders\DirectorsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FilmCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([CountriesSeeder::class, DirectorsSeeder::class]);
    }

    public function test_admin_sees_a_paginated_film_list(): void
    {
        $admin = User::factory()->admin()->create();
        Film::factory()->count(20)->create();

        $response = $this->actingAs($admin)->get('/admin/films');

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Admin/Films/Index')
                ->has('films.data', 15)
                ->where('films.currentPage', 1)
                ->where('films.lastPage', 2)
                ->where('films.total', 20)
        );
    }

    public function test_admin_can_search_films_by_title(): void
    {
        $admin = User::factory()->admin()->create();
        Film::factory()->create(['title' => 'Дюна']);
        Film::factory()->create(['title' => 'Барби']);

        $response = $this->actingAs($admin)->get('/admin/films?search=Дюна');

        $response->assertInertia(
            fn (Assert $page) => $page
                ->has('films.data', 1)
                ->where('films.data.0.title', 'Дюна')
        );
    }

    public function test_admin_can_filter_films_by_genre(): void
    {
        $admin = User::factory()->admin()->create();
        [$comedy, $drama] = Genre::factory()->count(2)->create();
        $comedyFilm = Film::factory()->create();
        $comedyFilm->genres()->sync([$comedy->id]);
        $dramaFilm = Film::factory()->create();
        $dramaFilm->genres()->sync([$drama->id]);

        $response = $this->actingAs($admin)->get("/admin/films?genre_id={$comedy->id}");

        $response->assertInertia(
            fn (Assert $page) => $page
                ->has('films.data', 1)
                ->where('films.data.0.id', $comedyFilm->id)
        );
    }

    public function test_admin_can_create_a_film_with_a_poster_and_genres(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $genres = Genre::factory()->count(2)->create();

        $response = $this->actingAs($admin)->post('/admin/films', [
            'title' => 'Dune',
            'producer' => 'Denis Villeneuve',
            'country' => 'США',
            'duration_minutes' => 155,
            'age_limit' => 12,
            'rating' => 8.5,
            'description' => 'Desert planet.',
            'genre_ids' => $genres->pluck('id')->all(),
            'poster' => UploadedFile::fake()->image('poster.jpg'),
        ]);

        $response->assertRedirect('/admin/films');
        $film = Film::where('title', 'Dune')->firstOrFail();
        $this->assertCount(2, $film->genres);
        $this->assertNotNull($film->poster_path);
        Storage::disk('public')->assertExists($film->poster_path);
    }

    public function test_saving_a_film_with_a_new_director_registers_him_in_the_reference_list(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/films', [
            'title' => 'Леон',
            'producer' => 'Люк Бессон',
            'country' => 'США',
            'duration_minutes' => 110,
            'age_limit' => 18,
            'rating' => 8.5,
            'description' => 'Профессионал.',
        ])->assertRedirect('/admin/films');

        $this->assertDatabaseHas('directors', ['name' => 'Люк Бессон']);
    }

    public function test_admin_can_update_a_film(): void
    {
        $admin = User::factory()->admin()->create();
        $film = Film::factory()->create(['title' => 'Old Title']);

        $response = $this->actingAs($admin)->put("/admin/films/{$film->id}", [
            'title' => 'New Title',
            'producer' => $film->producer,
            'country' => $film->country,
            'duration_minutes' => $film->duration_minutes,
            'age_limit' => $film->age_limit,
            'rating' => null,
            'description' => $film->description,
            'genre_ids' => [],
        ]);

        $response->assertRedirect('/admin/films');
        $this->assertSame('New Title', $film->fresh()->title);
    }

    public function test_film_forms_provide_country_producer_and_age_options(): void
    {
        $admin = User::factory()->admin()->create();
        Director::query()->firstOrCreate(['name' => 'Кристофер Нолан']);

        $this->actingAs($admin)->get('/admin/films/create')->assertInertia(
            fn (Assert $page) => $page
                ->component('Admin/Films/Create')
                ->where(
                    'countries',
                    Country::query()->orderBy('name')->pluck('name')->all(),
                )
                ->where('ageRatings', config('films.age_ratings'))
                ->where(
                    'producers',
                    Director::query()->orderBy('name')->pluck('name')->all(),
                )
        );

        $film = Film::factory()->create();

        $this->actingAs($admin)->get("/admin/films/{$film->id}/edit")->assertInertia(
            fn (Assert $page) => $page
                ->component('Admin/Films/Edit')
                ->has('countries')
                ->has('ageRatings')
                ->has('producers')
        );
    }

    public function test_admin_cannot_store_a_film_with_unknown_country_or_age_limit(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/films', [
            'title' => 'Dune',
            'producer' => 'Denis Villeneuve',
            'country' => 'Галактика Андромеды',
            'duration_minutes' => 155,
            'age_limit' => 13,
            'description' => 'Desert planet.',
        ])->assertSessionHasErrors(['country', 'age_limit']);

        $this->assertDatabaseMissing('films', ['title' => 'Dune']);
    }

    public function test_admin_can_update_a_film_keeps_legacy_country_outside_the_list(): void
    {
        $admin = User::factory()->admin()->create();
        $film = Film::factory()->create(['country' => 'США/Великобритания']);

        $this->actingAs($admin)->put("/admin/films/{$film->id}", [
            'title' => 'Начало',
            'producer' => $film->producer,
            'country' => 'США/Великобритания',
            'duration_minutes' => $film->duration_minutes,
            'age_limit' => $film->age_limit,
            'rating' => null,
            'description' => $film->description,
            'genre_ids' => [],
        ])->assertRedirect('/admin/films');

        $this->assertSame('Начало', $film->fresh()->title);
    }

    public function test_admin_cannot_update_a_film_to_unknown_country(): void
    {
        $admin = User::factory()->admin()->create();
        $film = Film::factory()->create(['title' => 'Old Title']);

        $this->actingAs($admin)->put("/admin/films/{$film->id}", [
            'title' => $film->title,
            'producer' => $film->producer,
            'country' => 'Галактика Андромеды',
            'duration_minutes' => $film->duration_minutes,
            'age_limit' => $film->age_limit,
            'rating' => null,
            'description' => $film->description,
            'genre_ids' => [],
        ])->assertSessionHasErrors('country');

        $this->assertSame('Old Title', $film->fresh()->title);
    }

    public function test_admin_cannot_delete_a_film_with_upcoming_sessions(): void
    {
        $admin = User::factory()->admin()->create();
        $hall = Hall::factory()->withSeats()->create();
        $film = Film::factory()->create();
        CinemaSession::factory()->create([
            'film_id' => $film->id,
            'hall_id' => $hall->id,
            'starts_at' => now()->addDay(),
        ]);

        $this->actingAs($admin)->delete("/admin/films/{$film->id}");

        $this->assertNotNull($film->fresh());
    }

    public function test_admin_can_delete_a_film_without_upcoming_sessions(): void
    {
        $admin = User::factory()->admin()->create();
        $film = Film::factory()->create();

        $this->actingAs($admin)->delete("/admin/films/{$film->id}");

        $this->assertNull(Film::find($film->id));
    }

    public function test_customer_cannot_access_film_admin_routes(): void
    {
        $customer = User::factory()->customer()->create();

        $this->actingAs($customer)->get('/admin/films')->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/films')->assertRedirect('/login');
    }
}
