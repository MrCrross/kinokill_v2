<?php

namespace App\Models;

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
}
