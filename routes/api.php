<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\FilmController;
use App\Http\Controllers\Api\SessionController;
use Illuminate\Support\Facades\Route;

/*
 * Read-only JSON mirror of the public catalog/booking flow, built on top of
 * the same Services/Data classes as the Inertia controllers in routes/web.php.
 * Its main purpose is to give dedoc/scramble (see /docs/api) a real JSON
 * surface to document — Inertia responses aren't a meaningful OpenAPI target.
 */
Route::get('/films', [FilmController::class, 'index']);
Route::get('/films/{film}', [FilmController::class, 'show']);

Route::get('/sessions', [SessionController::class, 'index']);
Route::get('/sessions/{session}', [SessionController::class, 'show']);

Route::post('/sessions/{session}/bookings', [BookingController::class, 'store']);
