<?php

namespace App\Policies;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * A registered customer viewing their own booking, or a guest viewing
     * the booking they just created in this browser session (tracked via
     * a session flash set right after checkout — see BookingController).
     */
    public function view(?User $user, Booking $booking): bool
    {
        return $this->owns($user, $booking);
    }

    /**
     * card_stub bookings are "confirmed" by the purchaser themselves
     * (simulated online payment); cash bookings are confirmed by staff
     * at the box office.
     */
    public function confirm(?User $user, Booking $booking): bool
    {
        if ($booking->status !== BookingStatus::Pending) {
            return false;
        }

        if ($booking->payment_method === PaymentMethod::Cash) {
            return (bool) $user?->can('bookings.manage-any');
        }

        return $this->owns($user, $booking);
    }

    public function cancel(?User $user, Booking $booking): bool
    {
        return $booking->status === BookingStatus::Pending && $this->owns($user, $booking);
    }

    private function owns(?User $user, Booking $booking): bool
    {
        if ($user && $booking->customer?->user_id === $user->id) {
            return true;
        }

        return session('last_booking_id') === $booking->id;
    }
}
