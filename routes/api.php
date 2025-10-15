<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\BookingController;


// Маршруты для услуг
Route::prefix('services')->group(function () {
    Route::get('/', [ServiceController::class, 'index'])->name('api.services.index');
    Route::get('/{id}', [ServiceController::class, 'show'])->name('api.services.show');
    Route::get('/{id}/available-slots', [ServiceController::class, 'getAvailableSlots'])->name('api.services.available-slots');
});

// Маршруты для бронирований
Route::prefix('bookings')->group(function () {
    Route::post('/', [BookingController::class, 'store'])->name('api.bookings.store');
    Route::get('/{id}', [BookingController::class, 'show'])->name('api.bookings.show');
    Route::get('/week/bookings', [BookingController::class, 'getWeekBookings'])->name('api.bookings.week');
    Route::get('/stats/overview', [BookingController::class, 'getStats'])->name('api.bookings.stats');
    Route::patch('/{id}/cancel', [BookingController::class, 'cancel'])->name('api.bookings.cancel');
});

// Общие маршруты
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0',
    ]);
})->name('api.health');
