<?php

namespace Tests\Feature\Admin;

use App\Models\CinemaSession;
use App\Models\Hall;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class HallCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_a_paginated_hall_list(): void
    {
        $admin = User::factory()->admin()->create();
        Hall::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get('/admin/halls');

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Admin/Halls/Index')
                ->has('halls.data', 3)
                ->where('halls.total', 3)
        );
    }

    public function test_admin_can_search_halls_by_name(): void
    {
        $admin = User::factory()->admin()->create();
        Hall::factory()->create(['name' => 'Красный зал']);
        Hall::factory()->create(['name' => 'Синий зал']);

        $response = $this->actingAs($admin)->get('/admin/halls?search=Красный');

        $response->assertInertia(
            fn (Assert $page) => $page
                ->has('halls.data', 1)
                ->where('halls.data.0.name', 'Красный зал')
        );
    }

    /**
     * @return array<int, array{row_number: int, column_number: int, seat_type: string}>
     */
    private function rectangularSeatMap(int $rows, int $columns, int $vipRows = 2): array
    {
        $seats = [];

        for ($row = 1; $row <= $rows; $row++) {
            $type = $row > $rows - $vipRows ? 'vip' : 'standard';
            for ($column = 1; $column <= $columns; $column++) {
                $seats[] = ['row_number' => $row, 'column_number' => $column, 'seat_type' => $type];
            }
        }

        return $seats;
    }

    public function test_admin_can_create_a_hall_from_an_explicit_seat_map(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/halls', [
            'name' => 'Red Hall',
            'rows_count' => 5,
            'columns_count' => 6,
            'price_standard' => 300,
            'price_vip' => 500,
            'seats' => $this->rectangularSeatMap(5, 6),
        ]);

        $response->assertRedirect('/admin/halls');
        $hall = Hall::where('name', 'Red Hall')->firstOrFail();
        $this->assertSame(30, $hall->seats()->count());
        $this->assertSame(12, $hall->seats()->where('seat_type', 'vip')->count());
    }

    public function test_admin_can_create_a_hall_with_gaps_for_aisles(): void
    {
        $admin = User::factory()->admin()->create();

        $seats = $this->rectangularSeatMap(3, 3, vipRows: 1);
        // Drop the middle seat of the front row to leave an aisle.
        $seats = array_values(array_filter(
            $seats,
            fn (array $seat) => ! ($seat['row_number'] === 1 && $seat['column_number'] === 2)
        ));

        $response = $this->actingAs($admin)->post('/admin/halls', [
            'name' => 'Aisle Hall',
            'rows_count' => 3,
            'columns_count' => 3,
            'price_standard' => 300,
            'price_vip' => 500,
            'seats' => $seats,
        ]);

        $response->assertRedirect('/admin/halls');
        $hall = Hall::where('name', 'Aisle Hall')->firstOrFail();
        $this->assertSame(8, $hall->seats()->count());
        $this->assertFalse(
            $hall->seats()->where('row_number', 1)->where('column_number', 2)->exists()
        );
    }

    public function test_hall_creation_rejects_duplicate_seat_positions(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/halls', [
            'name' => 'Broken Hall',
            'rows_count' => 2,
            'columns_count' => 2,
            'price_standard' => 300,
            'price_vip' => 500,
            'seats' => [
                ['row_number' => 1, 'column_number' => 1, 'seat_type' => 'standard'],
                ['row_number' => 1, 'column_number' => 1, 'seat_type' => 'vip'],
            ],
        ]);

        $response->assertSessionHasErrors('seats');
        $this->assertDatabaseMissing('halls', ['name' => 'Broken Hall']);
    }

    public function test_hall_creation_rejects_seats_outside_the_grid_bounds(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/halls', [
            'name' => 'Broken Hall',
            'rows_count' => 2,
            'columns_count' => 2,
            'price_standard' => 300,
            'price_vip' => 500,
            'seats' => [
                ['row_number' => 5, 'column_number' => 1, 'seat_type' => 'standard'],
            ],
        ]);

        $response->assertSessionHasErrors('seats.0.row_number');
        $this->assertDatabaseMissing('halls', ['name' => 'Broken Hall']);
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
