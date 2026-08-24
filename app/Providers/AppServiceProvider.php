<?php

namespace App\Providers;

use App\Repositories\Contracts\CinemaSessionRepositoryInterface;
use App\Repositories\Contracts\FilmRepositoryInterface;
use App\Repositories\Contracts\HallRepositoryInterface;
use App\Repositories\Eloquent\EloquentCinemaSessionRepository;
use App\Repositories\Eloquent\EloquentFilmRepository;
use App\Repositories\Eloquent\EloquentHallRepository;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FilmRepositoryInterface::class, EloquentFilmRepository::class);
        $this->app->bind(HallRepositoryInterface::class, EloquentHallRepository::class);
        $this->app->bind(CinemaSessionRepositoryInterface::class, EloquentCinemaSessionRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
