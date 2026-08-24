<?php

namespace Tests\Feature\Admin;

use App\Models\CinemaSession;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Hall;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FilmCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_film_with_a_poster_and_genres(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $genres = Genre::factory()->count(2)->create();

        $response = $this->actingAs($admin)->post('/admin/films', [
            'title' => 'Dune',
            'producer' => 'Denis Villeneuve',
            'country' => 'USA',
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
