<?php

namespace App\Models;

use Database\Factories\CinemaSessionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['film_id', 'hall_id', 'starts_at', 'price_standard', 'price_vip'])]
class CinemaSession extends Model
{
    /** @use HasFactory<CinemaSessionFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'price_standard' => 'decimal:2',
            'price_vip' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<Film, $this>
     */
    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }

    /**
     * @return BelongsTo<Hall, $this>
     */
    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    /**
     * @return HasMany<BookingSeat, $this>
     */
    public function bookingSeats(): HasMany
    {
        return $this->hasMany(BookingSeat::class);
    }

    /**
     * The effective standard-seat price for this session, falling back to
     * the hall's default when no session-specific override is set.
     */
    public function standardPrice(): string
    {
        return (string) ($this->price_standard ?? $this->hall->price_standard);
    }

    /**
     * The effective VIP-seat price for this session, falling back to the
     * hall's default when no session-specific override is set.
     */
    public function vipPrice(): string
    {
        return (string) ($this->price_vip ?? $this->hall->price_vip);
    }
}
