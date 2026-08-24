<?php

namespace Tests\Feature\Admin;

use App\Models\CinemaSession;
use App\Models\Hall;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HallCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_hall_and_seats_are_generated(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/halls', [
            'name' => 'Red Hall',
            'rows_count' => 5,
            'columns_count' => 6,
            'price_standard' => 300,
            'price_vip' => 500,
        ]);

        $response->assertRedirect('/admin/halls');
        $hall = Hall::where('name', 'Red Hall')->firstOrFail();
        $this->assertSame(30, $hall->seats()->count());
        $this->assertSame(12, $hall->seats()->where('seat_type', 'vip')->count());
    }

    public function test_admin_can_update_hall_pricing_without_touching_the_seat_grid(): void
    {
        $admin = User::factory()->admin()->create();
        $hall = Hall::factory()->withSeats()->create(['rows_count' => 5, 'columns_count' => 6]);
        $seatCountBefore = $hall->seats()->count();

        $this->actingAs($admin)->put("/admin/halls/{$hall->id}", [
            'name' => 'Renamed Hall',
            'price_standard' => 350,
            'price_vip' => 600,
        ]);

        $hall->refresh();
        $this->assertSame('Renamed Hall', $hall->name);
        $this->assertSame('350.00', (string) $hall->price_standard);
        $this->assertSame($seatCountBefore, $hall->seats()->count());
    }

    public function test_admin_cannot_delete_a_hall_with_upcoming_sessions(): void
    {
        $admin = User::factory()->admin()->create();
        $hall = Hall::factory()->withSeats()->create();
        CinemaSession::factory()->create(['hall_id' => $hall->id, 'starts_at' => now()->addDay()]);

        $this->actingAs($admin)->delete("/admin/halls/{$hall->id}");

        $this->assertNotNull($hall->fresh());
    }

    public function test_manager_without_halls_permission_is_forbidden(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)->get('/admin/halls')->assertForbidden();
    }
}
