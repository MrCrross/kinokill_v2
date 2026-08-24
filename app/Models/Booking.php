<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['customer_id', 'cinema_session_id', 'status', 'total_price', 'payment_method', 'paid_at', 'expires_at'])]
class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => BookingStatus::class,
            'payment_method' => PaymentMethod::class,
            'total_price' => 'decimal:2',
            'paid_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return BelongsTo<CinemaSession, $this>
     */
    public function cinemaSession(): BelongsTo
    {
        return $this->belongsTo(CinemaSession::class);
    }

    /**
     * @return HasMany<BookingSeat, $this>
     */
    public function bookingSeats(): HasMany
    {
        return $this->hasMany(BookingSeat::class);
    }

    /**
     * Bookings that currently hold their seats: confirmed, or pending and
     * not yet expired.
     *
     * @param  Builder<Booking>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where(function (Builder $query) {
            $query->where('status', BookingStatus::Confirmed)
                ->orWhere(function (Builder $query) {
                    $query->where('status', BookingStatus::Pending)
                        ->where(function (Builder $query) {
                            $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
                        });
                });
        });
    }
}
