<?php

namespace App\Http\Controllers;

use App\Data\Booking\BookingData;
use App\Data\Booking\CreateBookingData;
use App\Enums\PaymentMethod;
use App\Http\Requests\Booking\CreateBookingRequest;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookings,
    ) {}

    public function store(CreateBookingRequest $request, int $session): RedirectResponse
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

        session(['last_booking_id' => $booking->id]);

        return redirect()->route('bookings.show', $booking)->with('success', 'Место забронировано.');
    }

    public function show(Booking $booking): Response
    {
        Gate::authorize('view', $booking);

        return Inertia::render('Booking/Confirmation', [
            'booking' => BookingData::fromModel(
                $this->bookings->find($booking->id),
                Gate::allows('confirm', $booking),
                Gate::allows('cancel', $booking),
            ),
        ]);
    }

    public function confirm(Booking $booking): RedirectResponse
    {
        Gate::authorize('confirm', $booking);

        $this->bookings->confirm($booking);

        return back()->with('success', 'Бронирование подтверждено.');
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        Gate::authorize('cancel', $booking);

        $this->bookings->cancel($booking);

        return back()->with('success', 'Бронирование отменено.');
    }
}
