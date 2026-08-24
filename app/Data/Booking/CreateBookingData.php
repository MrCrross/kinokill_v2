<?php

namespace App\Data\Booking;

use App\Enums\PaymentMethod;
use Spatie\LaravelData\Data;

class CreateBookingData extends Data
{
    /**
     * @param  array<int, int>  $seatIds
     */
    public function __construct(
        public int $sessionId,
        public array $seatIds,
        public PaymentMethod $paymentMethod,
        public ?string $guestName,
        public ?string $guestEmail,
        public ?string $guestPhone,
    ) {}
}
