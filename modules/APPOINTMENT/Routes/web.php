<?php

use Illuminate\Support\Facades\Route;
use Modules\APPOINTMENT\Http\Controllers\AppointmentController;
use Modules\APPOINTMENT\Http\Controllers\BookingController;
use Modules\APPOINTMENT\Services\TimeSlotService;
use Modules\WEBSITE\Interfaces\DoctorTimetableRepositoryInterface;

Route::middleware(['web', 'auth'])->prefix('admin/appointments')->name('appointments.')->group(function () {
    Route::get('/', [AppointmentController::class, 'index'])->name('index');
    Route::get('/create', [AppointmentController::class, 'create'])->name('create');
    Route::post('/', [AppointmentController::class, 'store'])->name('store');
    Route::get('/{id}', [AppointmentController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [AppointmentController::class, 'edit'])->name('edit');
    Route::put('/{id}', [AppointmentController::class, 'update'])->name('update');
    Route::delete('/{id}', [AppointmentController::class, 'destroy'])->name('destroy');

    // Status actions
    Route::post('/{id}/confirm', [AppointmentController::class, 'confirm'])->name('confirm');
    Route::post('/{id}/cancel', [AppointmentController::class, 'cancel'])->name('cancel');
    Route::post('/{id}/complete', [AppointmentController::class, 'complete'])->name('complete');
    Route::post('/{id}/no-show', [AppointmentController::class, 'noShow'])->name('no-show');
});

Route::prefix('booking')->name('booking.')->group(function () {
    Route::get('/', [BookingController::class, 'index'])->name('index');
    Route::get('/available-slots', [BookingController::class, 'getAvailableSlots'])->name('available-slots');
    Route::post('/', [BookingController::class, 'store'])->name('store');
});

Route::get('test-slot', function(\Modules\APPOINTMENT\Services\TimeSlotService $slot, DoctorTimetableRepositoryInterface $timetableRepo) {
    // $data = $slot->getTimeSlotDateWise(39, '2026-07-01', '2026-07-31');
    // return response()->json($data);

    $data = $timetableRepo->getDoctorAvilabilityDateByRange(39, '2026-07-01', '2026-07-31');
    return response()->json($data);
});
