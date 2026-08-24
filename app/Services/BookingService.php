<?php

namespace App\Services;

use App\Data\Booking\CreateBookingData;
use App\Enums\BookingStatus;
use App\Enums\SeatType;
use App\Models\Booking;
use App\Models\CinemaSession;
use App\Models\User;
use App\Repositories\Contracts\BookingRepositoryInterface;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(
        private readonly BookingRepositoryInterface $bookings,
        private readonly SeatAvailabilityService $seatAvailability,
        private readonly CustomerService $customers,
    ) {}

    public function createBooking(CreateBookingData $data, ?User $user): Booking
    {
        return DB::transaction(function () use ($data, $user) {
            $session = CinemaSession::with('hall')->findOrFail($data->sessionId);

            $customer = $user
                ? $this->customers->forAuthenticatedUser($user)
                : $this->customers->resolveForGuestCheckout(
                    $data->guestName ?? '',
                    $data->guestEmail ?? '',
                    $data->guestPhone,
                );

            $seats = $this->seatAvailability->lockAndAssertAvailable($session->id, $data->seatIds);

            $seatPrices = $seats->mapWithKeys(fn ($seat) => [
                $seat->id => $seat->seat_type === SeatType::Vip ? $session->vipPrice() : $session->standardPrice(),
            ]);

            $booking = $this->bookings->create([
                'customer_id' => $customer->id,
                'cinema_session_id' => $session->id,
                'status' => BookingStatus::Pending,
                'total_price' => $seatPrices->sum(fn ($price) => (float) $price),
                'payment_method' => $data->paymentMethod,
                'expires_at' => now()->addMinutes(15),
            ]);

            foreach ($seats as $seat) {
                $this->bookings->createSeat([
                    'booking_id' => $booking->id,
                    'seat_id' => $seat->id,
                    'cinema_session_id' => $session->id,
                    'price' => $seatPrices[$seat->id],
                ]);
            }

            return $booking;
        });
    }

    public function find(int $id): Booking
    {
        return $this->bookings->findWithDetails($id);
    }

    public function confirm(Booking $booking): Booking
    {
        $booking->update([
            'status' => BookingStatus::Confirmed,
            'paid_at' => now(),
            'expires_at' => null,
        ]);

        return $booking->fresh();
    }

    public function cancel(Booking $booking): Booking
    {
        $booking->update([
            'status' => BookingStatus::Cancelled,
        ]);

        return $booking->fresh();
    }

    /**
     * Flip timed-out pending bookings to "expired" for reporting/admin
     * purposes. Not required for correctness: Booking::scopeActive()
     * already ignores expired pending bookings by time alone, so their
     * seats become bookable again immediately, before this ever runs.
     */
    public function releaseExpired(): int
    {
        return Booking::query()
            ->where('status', BookingStatus::Pending)
            ->where('expires_at', '<', now())
            ->update(['status' => BookingStatus::Expired]);
    }
}
