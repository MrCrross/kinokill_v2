<?php

namespace Tests\Feature\Admin;

use App\Models\Country;
use App\Models\Director;
use App\Models\Film;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ReferenceCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_reference_lists(): void
    {
        $admin = User::factory()->admin()->create();
        Country::factory()->create(['name' => 'Исландия']);
        Director::factory()->create(['name' => 'Люк Бессон']);
        $genre = Genre::factory()->create(['name' => 'Вестерн']);

        $response = $this->actingAs($admin)->get('/admin/reference');

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Admin/Reference/Index')
                ->where('countries', ['Исландия'])
                ->where('directors', ['Люк Бессон'])
                ->has('genres', 1)
                ->where('genres.0.id', $genre->id)
                ->where('genres.0.name', 'Вестерн')
                ->where('genres.0.filmsCount', 0)
        );
    }

    public function test_admin_can_add_country_director_and_genre(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post('/admin/reference/countries', ['name' => 'Исландия'])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
        $this->actingAs($admin)
            ->post('/admin/reference/directors', ['name' => 'Люк Бессон'])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
        $this->actingAs($admin)
            ->post('/admin/reference/genres', ['name' => 'Вестерн'])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('countries', ['name' => 'Исландия']);
        $this->assertDatabaseHas('directors', ['name' => 'Люк Бессон']);
        $this->assertDatabaseHas('genres', ['name' => 'Вестерн']);
    }

    public function test_duplicate_reference_names_are_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        Country::factory()->create(['name' => 'Исландия']);
        Director::factory()->create(['name' => 'Люк Бессон']);
        Genre::factory()->create(['name' => 'Вестерн']);

        $this->actingAs($admin)
            ->post('/admin/reference/countries', ['name' => 'Исландия'])
            ->assertSessionHasErrors('name');
        $this->actingAs($admin)
            ->post('/admin/reference/directors', ['name' => 'Люк Бессон'])
            ->assertSessionHasErrors('name');
        $this->actingAs($admin)
            ->post('/admin/reference/genres', ['name' => 'Вестерн'])
            ->assertSessionHasErrors('name');
    }

    public function test_admin_can_delete_country_and_director(): void
    {
        $admin = User::factory()->admin()->create();
        Country::factory()->create(['name' => 'Исландия']);
        Director::factory()->create(['name' => 'Люк Бессон']);

        $this->actingAs($admin)
            ->delete('/admin/reference/countries/Исландия')
            ->assertRedirect();
        $this->actingAs($admin)
            ->delete('/admin/reference/directors/Люк Бессон')
            ->assertRedirect();

        $this->assertDatabaseMissing('countries', ['name' => 'Исландия']);
        $this->assertDatabaseMissing('directors', ['name' => 'Люк Бессон']);
    }

    public function test_admin_can_delete_an_unused_genre(): void
    {
        $admin = User::factory()->admin()->create();
        $genre = Genre::factory()->create(['name' => 'Вестерн']);

        $this->actingAs($admin)
            ->delete("/admin/reference/genres/{$genre->id}")
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('genres', ['name' => 'Вестерн']);
    }

    public function test_admin_cannot_delete_a_genre_attached_to_films(): void
    {
        $admin = User::factory()->admin()->create();
        $genre = Genre::factory()->create(['name' => 'Вестерн']);
        $film = Film::factory()->create();
        $film->genres()->sync([$genre->id]);

        $this->actingAs($admin)
            ->delete("/admin/reference/genres/{$genre->id}")
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('genres', ['name' => 'Вестерн']);
    }

    public function test_customer_cannot_access_reference_routes(): void
    {
        $customer = User::factory()->customer()->create();

        $this->actingAs($customer)->get('/admin/reference')->assertForbidden();
        $this->actingAs($customer)
            ->post('/admin/reference/genres', ['name' => 'Вестерн'])
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/reference')->assertRedirect('/login');
    }
}
