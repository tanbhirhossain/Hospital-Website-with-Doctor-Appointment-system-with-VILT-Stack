<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['api'])->prefix('api')->group(function (): void {
    Route::prefix('doctors/{doctorId}')->group(function () {
    Route::get('/available-slots', [AppointmentApiController::class, 'getAvailableSlots']);
});
});

