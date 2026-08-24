<?php

namespace Database\Seeders;

use App\Models\CinemaSession;
use App\Models\Film;
use App\Models\Hall;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CinemaSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Slots are spaced 4 hours apart, comfortably longer than any seeded
     * film's duration plus cleanup buffer, so sessions never overlap
     * without needing to run them through the overlap-checking service.
     */
    public function run(): void
    {
        $films = Film::all();
        $halls = Hall::all();
        $slots = ['11:00', '15:00', '19:00'];

        if ($films->isEmpty() || $halls->isEmpty()) {
            return;
        }

        foreach ($halls as $hall) {
            for ($day = 0; $day < 7; $day++) {
                foreach ($slots as $slot) {
                    [$hour, $minute] = explode(':', $slot);

                    CinemaSession::create([
                        'film_id' => $films->random()->id,
                        'hall_id' => $hall->id,
                        'starts_at' => Carbon::today()->addDays($day)->setTime((int) $hour, (int) $minute),
                    ]);
                }
            }
        }
    }
}
