<?php

use Illuminate\Support\Facades\Route;
use Modules\APPOINTMENT\Http\Controllers\AppointmentApiController;

Route::middleware(['api'])->prefix('api')->group(function (): void {

    Route::prefix('doctors')->group(function (): void {

        Route::get('/availability', [AppointmentApiController::class, 'getDoctorAvailability'])
            ->name('api.doctors.availability');   // renamed — no more collision

        Route::get('/{doctorId}/available-slots', [AppointmentApiController::class, 'getAvailableSlots'])
            ->name('api.doctors.available-slots'); // renamed — no more collision

        Route::post('/{doctorId}/book-appointment', [AppointmentApiController::class, 'bookAppointment'])
            ->name('api.doctors.book-appointment'); // renamed — no more collision
            
    });

    Route::get('/departments', [AppointmentApiController::class, 'getDepartments'])
        ->name('api.departments'); // renamed — no more collision

    Route::get('/doctors', [AppointmentApiController::class, 'getDoctors'])
        ->name('api.doctors'); // renamed — no more collision
});