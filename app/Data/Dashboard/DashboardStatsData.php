<?php

namespace App\Data\Dashboard;

use Spatie\LaravelData\Data;

class DashboardStatsData extends Data
{
    /**
     * @param  array<int, UpcomingSessionData>  $upcomingSessions
     */
    public function __construct(
        public int $filmsTotal,
        public int $hallsTotal,
        public int $usersTotal,
        public int $upcomingSessionsCount,
        public string $revenueLast30Days,
        public float $averageOccupancyNext7Days,
        public array $upcomingSessions,
    ) {}
}
