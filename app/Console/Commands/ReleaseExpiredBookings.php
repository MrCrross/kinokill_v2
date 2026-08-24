<?php

namespace App\Console\Commands;

use App\Services\BookingService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('bookings:release-expired')]
#[Description('Flip timed-out pending bookings to expired so their seats are reported as free')]
class ReleaseExpiredBookings extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(BookingService $bookings): int
    {
        $count = $bookings->releaseExpired();

        $this->info("Released {$count} expired booking(s).");

        return self::SUCCESS;
    }
}
