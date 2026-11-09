<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FilmAdminController;
use App\Http\Controllers\Admin\HallAdminController;
use App\Http\Controllers\Admin\ReferenceAdminController;
use App\Http\Controllers\Admin\SessionAdminController;
use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [FilmController::class, 'index'])->name('home');

Route::get('/locale/{locale}', [LocaleController::class, 'update'])
    ->where('locale', 'ru|en')
    ->name('locale.update');

Route::get('/films', [FilmController::class, 'index'])->name('films.index');
Route::get('/films/{film}', [FilmController::class, 'show'])->name('films.show');

Route::get('/sessions', [SessionController::class, 'index'])->name('sessions.index');
Route::get('/sessions/{session}', [SessionController::class, 'show'])->name('sessions.show');

Route::post('/sessions/{session}/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
Route::post('/bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'permission:films.manage|halls.manage|sessions.manage|bookings.manage-any|users.manage-roles'])
    ->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
    });

Route::middleware(['auth', 'permission:films.manage'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('films', FilmAdminController::class)->except('show');

    Route::get('reference', [ReferenceAdminController::class, 'index'])->name('reference.index');
    Route::post('reference/countries', [ReferenceAdminController::class, 'storeCountry'])->name('reference.countries.store');
    Route::delete('reference/countries/{country:name}', [ReferenceAdminController::class, 'destroyCountry'])->name('reference.countries.destroy');
    Route::post('reference/directors', [ReferenceAdminController::class, 'storeDirector'])->name('reference.directors.store');
    Route::delete('reference/directors/{director:name}', [ReferenceAdminController::class, 'destroyDirector'])->name('reference.directors.destroy');
    Route::post('reference/genres', [ReferenceAdminController::class, 'storeGenre'])->name('reference.genres.store');
    Route::delete('reference/genres/{genre}', [ReferenceAdminController::class, 'destroyGenre'])->name('reference.genres.destroy');
});

Route::middleware(['auth', 'permission:halls.manage'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('halls', HallAdminController::class)->except('show');
});

Route::middleware(['auth', 'permission:sessions.manage'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('sessions', SessionAdminController::class)->except(['show', 'edit', 'update']);
});

Route::middleware(['auth', 'permission:users.manage-roles'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('users', [UserRoleController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserRoleController::class, 'create'])->name('users.create');
    Route::post('users', [UserRoleController::class, 'store'])->name('users.store');
    Route::put('users/{user}/role', [UserRoleController::class, 'update'])->name('users.update-role');
});

require __DIR__.'/auth.php';
