<?php

namespace App\Services;

use App\Data\Dashboard\DashboardStatsData;
use App\Data\Dashboard\UpcomingSessionData;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\CinemaSession;
use App\Models\Film;
use App\Models\Hall;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class DashboardService
{
    /**
     * How many upcoming days count toward the average hall occupancy figure.
     */
    private const OCCUPANCY_WINDOW_DAYS = 7;

    private const UPCOMING_SESSIONS_LIMIT = 5;

    public function stats(): DashboardStatsData
    {
        $now = Carbon::now();

        $revenueLast30Days = Booking::query()
            ->where('status', BookingStatus::Confirmed)
            ->where('paid_at', '>=', $now->clone()->subDays(30))
            ->sum('total_price');

        $upcomingSessions = $this->sessionsWithOccupancy(
            fn ($query) => $query->where('starts_at', '>=', $now)
                ->orderBy('starts_at')
                ->limit(self::UPCOMING_SESSIONS_LIMIT)
        );

        $occupancySample = $this->sessionsWithOccupancy(
            fn ($query) => $query->whereBetween('starts_at', [$now, $now->clone()->addDays(self::OCCUPANCY_WINDOW_DAYS)])
        );

        return new DashboardStatsData(
            filmsTotal: Film::query()->count(),
            hallsTotal: Hall::query()->count(),
            usersTotal: User::query()->count(),
            upcomingSessionsCount: CinemaSession::query()->where('starts_at', '>=', $now)->count(),
            revenueLast30Days: number_format((float) $revenueLast30Days, 2, '.', ''),
            averageOccupancyNext7Days: $this->averageOccupancy($occupancySample),
            upcomingSessions: $upcomingSessions->map(fn (CinemaSession $session) => UpcomingSessionData::fromModel($session))->all(),
        );
    }

    /**
     * @return Collection<int, CinemaSession>
     */
    private function sessionsWithOccupancy(\Closure $scope): Collection
    {
        $query = CinemaSession::query()
            ->with(['film', 'hall' => fn ($query) => $query->withCount('seats')])
            ->withCount(['bookingSeats as booked_seats_count' => fn ($query) => $query->whereHas(
                'booking',
                fn ($query) => $query->active()
            )]);

        return $scope($query)->get();
    }

    /**
     * @param  Collection<int, CinemaSession>  $sessions
     */
    private function averageOccupancy(Collection $sessions): float
    {
        if ($sessions->isEmpty()) {
            return 0.0;
        }

        $rates = $sessions->map(function (CinemaSession $session) {
            $capacity = $session->hall->seats_count ?? 0;

            return $capacity > 0 ? $session->booked_seats_count / $capacity : 0.0;
        });

        return round($rates->avg() * 100, 1);
    }
}
