<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Sent once a booking is created. Queued (see BookingService::createBooking)
 * so checkout doesn't wait on an SMTP round-trip; the transport is whatever
 * QUEUE_CONNECTION points at — RabbitMQ in this project (see compose.yaml's
 * `queue` service and config/queue.php's `rabbitmq` connection).
 */
class BookingConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Booking $booking,
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Подтверждение брони — '.$this->booking->cinemaSession->film->title)
            ->view('emails.booking-confirmation');
    }
}
