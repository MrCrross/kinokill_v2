<?php

namespace App\Data\Booking;

use App\Models\Booking;
use Spatie\LaravelData\Data;

class BookingData extends Data
{
    /**
     * @param  array<int, BookingSeatData>  $seats
     */
    public function __construct(
        public int $id,
        public string $status,
        public string $totalPrice,
        public string $paymentMethod,
        public string $filmTitle,
        public string $hallName,
        public string $startsAt,
        public array $seats,
        public bool $canConfirm,
        public bool $canCancel,
    ) {}

    public static function fromModel(Booking $booking, bool $canConfirm, bool $canCancel): self
    {
        return new self(
            id: $booking->id,
            status: $booking->status->value,
            totalPrice: (string) $booking->total_price,
            paymentMethod: $booking->payment_method->value,
            filmTitle: $booking->cinemaSession->film->title,
            hallName: $booking->cinemaSession->hall->name,
            startsAt: $booking->cinemaSession->starts_at->toIso8601String(),
            seats: $booking->bookingSeats->map(fn ($bookingSeat) => BookingSeatData::fromModel($bookingSeat))->all(),
            canConfirm: $canConfirm,
            canCancel: $canCancel,
        );
    }
}
