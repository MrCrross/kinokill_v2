<?php

namespace Tests\Feature\Admin;

use App\Models\CinemaSession;
use App\Models\Film;
use App\Models\Hall;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_create_a_session(): void
    {
        $manager = User::factory()->manager()->create();
        $hall = Hall::factory()->withSeats()->create();
        $film = Film::factory()->create(['duration_minutes' => 120]);

        $response = $this->actingAs($manager)->post('/admin/sessions', [
            'film_id' => $film->id,
            'hall_id' => $hall->id,
            'starts_at' => now()->addDay()->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect('/admin/sessions');
        $this->assertSame(1, CinemaSession::count());
    }

    public function test_overlapping_session_in_the_same_hall_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $hall = Hall::factory()->withSeats()->create();
        $film = Film::factory()->create(['duration_minutes' => 120]);
        $existingStart = now()->addDay()->setTime(18, 0);
        CinemaSession::factory()->create([
            'film_id' => $film->id,
            'hall_id' => $hall->id,
            'starts_at' => $existingStart,
        ]);

        // Starts 1h into the existing 120min+15min-cleanup session -> overlaps.
        $response = $this->actingAs($admin)->post('/admin/sessions', [
            'film_id' => $film->id,
            'hall_id' => $hall->id,
            'starts_at' => $existingStart->clone()->addHour()->format('Y-m-d H:i:s'),
        ]);

        $response->assertSessionHasErrors(['starts_at']);
        $this->assertSame(1, CinemaSession::count());
    }

    public function test_a_session_starting_after_the_cleanup_buffer_is_allowed(): void
    {
        $admin = User::factory()->admin()->create();
        $hall = Hall::factory()->withSeats()->create();
        $film = Film::factory()->create(['duration_minutes' => 120]);
        $existingStart = now()->addDay()->setTime(18, 0);
        CinemaSession::factory()->create([
            'film_id' => $film->id,
            'hall_id' => $hall->id,
            'starts_at' => $existingStart,
        ]);

        // 120 min film + 15 min cleanup = ends 20:15; starting at 20:15 is fine.
        $response = $this->actingAs($admin)->post('/admin/sessions', [
            'film_id' => $film->id,
            'hall_id' => $hall->id,
            'starts_at' => $existingStart->clone()->addMinutes(135)->format('Y-m-d H:i:s'),
        ]);

        $response->assertSessionDoesntHaveErrors();
        $this->assertSame(2, CinemaSession::count());
    }

    public function test_admin_can_delete_a_session(): void
    {
        $admin = User::factory()->admin()->create();
        $hall = Hall::factory()->withSeats()->create();
        $session = CinemaSession::factory()->create(['hall_id' => $hall->id]);

        $this->actingAs($admin)->delete("/admin/sessions/{$session->id}");

        $this->assertNull(CinemaSession::find($session->id));
    }

    public function test_customer_cannot_manage_sessions(): void
    {
        $customer = User::factory()->customer()->create();

        $this->actingAs($customer)->get('/admin/sessions')->assertForbidden();
    }
}
