<?php

use App\Http\Controllers\Admin\FilmAdminController;
use App\Http\Controllers\Admin\HallAdminController;
use App\Http\Controllers\Admin\SessionAdminController;
use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [FilmController::class, 'index'])->name('home');

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

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('dashboard');
});

Route::middleware(['auth', 'permission:films.manage'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('films', FilmAdminController::class)->except('show');
});

Route::middleware(['auth', 'permission:halls.manage'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('halls', HallAdminController::class)->except('show');
});

Route::middleware(['auth', 'permission:sessions.manage'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('sessions', SessionAdminController::class)->except(['show', 'edit', 'update']);
});

Route::middleware(['auth', 'permission:users.manage-roles'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('users', [UserRoleController::class, 'index'])->name('users.index');
    Route::put('users/{user}/role', [UserRoleController::class, 'update'])->name('users.update-role');
});

require __DIR__.'/auth.php';
