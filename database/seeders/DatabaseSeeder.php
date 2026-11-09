<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * WithoutModelEvents is intentionally NOT used here: Model::withoutEvents()
     * suppresses events for every model for the whole run() call, including
     * nested seeders — which breaks spatie/laravel-permission's cache
     * invalidation (it listens on Permission/Role model events).
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            UserSeeder::class,
            CountriesSeeder::class,
            DirectorsSeeder::class,
            GenreSeeder::class,
            FilmSeeder::class,
            HallSeeder::class,
            CinemaSessionSeeder::class,
        ]);
    }
}
