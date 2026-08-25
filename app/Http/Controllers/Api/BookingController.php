<?php

namespace App\Http\Controllers\Api;

use App\Data\Booking\BookingData;
use App\Data\Booking\CreateBookingData;
use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\CreateBookingRequest;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookings,
    ) {}

    /**
     * Book one or more seats for a session. Guest checkout: pass
     * guest_name/guest_email when the request is unauthenticated, the
     * price is always computed server-side from the session/seat data.
     */
    public function store(CreateBookingRequest $request, int $session): JsonResponse
    {
        $data = new CreateBookingData(
            sessionId: $session,
            seatIds: $request->validated('seat_ids'),
            paymentMethod: PaymentMethod::from($request->validated('payment_method')),
            guestName: $request->validated('guest_name'),
            guestEmail: $request->validated('guest_email'),
            guestPhone: $request->validated('guest_phone'),
        );

        $booking = $this->bookings->createBooking($data, $request->user());

        return response()->json([
            'data' => BookingData::fromModel($booking, canConfirm: false, canCancel: true),
        ], 201);
    }
}
