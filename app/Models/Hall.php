<?php

namespace App\Models;

use App\Enums\SeatType;
use Database\Factories\HallFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'rows_count', 'columns_count', 'price_standard', 'price_vip'])]
class Hall extends Model
{
    /** @use HasFactory<HallFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_standard' => 'decimal:2',
            'price_vip' => 'decimal:2',
        ];
    }

    /**
     * @return HasMany<Seat, $this>
     */
    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }

    /**
     * @return HasMany<CinemaSession, $this>
     */
    public function cinemaSessions(): HasMany
    {
        return $this->hasMany(CinemaSession::class);
    }

    /**
     * Generate a rectangular grid of seats for this hall. The last two rows
     * are VIP (matches real-cinema back-row premium seating).
     */
    public function generateSeats(): void
    {
        for ($row = 1; $row <= $this->rows_count; $row++) {
            $seatType = $row > $this->rows_count - 2 ? SeatType::Vip : SeatType::Standard;

            for ($column = 1; $column <= $this->columns_count; $column++) {
                $this->seats()->create([
                    'row_number' => $row,
                    'column_number' => $column,
                    'seat_type' => $seatType,
                ]);
            }
        }
    }

    /**
     * Create the hall's seat map from an explicit list of positions, as
     * designed in the admin seat-map editor. Unlike generateSeats(), a
     * position not listed here simply has no seat (an aisle/gap).
     *
     * @param  array<int, array{row_number: int, column_number: int, seat_type: string}>  $seats
     */
    public function createSeats(array $seats): void
    {
        $now = now();

        $this->seats()->insert(array_map(
            fn (array $seat) => [
                ...$seat,
                'hall_id' => $this->id,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            $seats,
        ));
    }
}
