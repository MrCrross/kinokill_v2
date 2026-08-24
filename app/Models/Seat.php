<?php

namespace App\Models;

use App\Enums\SeatType;
use Database\Factories\SeatFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['hall_id', 'row_number', 'column_number', 'seat_type'])]
class Seat extends Model
{
    /** @use HasFactory<SeatFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'seat_type' => SeatType::class,
        ];
    }

    /**
     * @return BelongsTo<Hall, $this>
     */
    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }
}
