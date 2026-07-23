<?php

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Modules\APPOINTMENT\Http\Controllers\AppointmentController;
use Modules\APPOINTMENT\Http\Controllers\BookingController;
use Modules\APPOINTMENT\Services\AppointmentService;
use Modules\WEBSITE\Interfaces\DoctorTimetableRepositoryInterface;

Route::middleware(['web', 'auth'])->prefix('admin/appointments')->name('appointments.')->group(function () {
    Route::get('/', [AppointmentController::class, 'index'])->name('index');
    Route::get('/doctor-availability', [AppointmentController::class, 'getDoctorAvailability'])->name('doctor-availability');
    Route::get('/available-slots', [AppointmentController::class, 'getAvailableSlots'])->name('available-slots');
    Route::get('/create', [AppointmentController::class, 'create'])->name('create');
    Route::post('/', [AppointmentController::class, 'store'])->name('store');
    Route::get('/{id}', [AppointmentController::class, 'show'])->name('show')->where('id', '[0-9]+');
    Route::get('/{id}/edit', [AppointmentController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
    Route::put('/{id}', [AppointmentController::class, 'update'])->name('update')->where('id', '[0-9]+');
    Route::delete('/{id}', [AppointmentController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');

    // Status actions
    Route::post('/{id}/confirm', [AppointmentController::class, 'confirm'])->name('confirm')->where('id', '[0-9]+');
    Route::post('/{id}/cancel', [AppointmentController::class, 'cancel'])->name('cancel')->where('id', '[0-9]+');
    Route::post('/{id}/complete', [AppointmentController::class, 'complete'])->name('complete')->where('id', '[0-9]+');
    Route::post('/{id}/no-show', [AppointmentController::class, 'noShow'])->name('no-show')->where('id', '[0-9]+');
});

Route::prefix('booking')->name('booking.')->group(function () {
    Route::get('/', [BookingController::class, 'index'])->name('index');
    Route::get('/doctor-availability', [AppointmentController::class, 'getDoctorAvailability'])->name('doctor-availability');
    Route::get('/available-slots', [BookingController::class, 'getAvailableSlots'])->name('available-slots');
    Route::post('/', [BookingController::class, 'store'])->name('store');
});

Route::get('test-slot', function(\Modules\APPOINTMENT\Services\TimeSlotService $slot, DoctorTimetableRepositoryInterface $timetableRepo) {
    // $data = $slot->getTimeSlotDateWise(39, '2026-07-01', '2026-07-31');
    // return response()->json($data);

    $data = $timetableRepo->getDoctorAvilabilityDateByRange(39, '2026-07-01', '2026-07-31');
    return response()->json($data);
});



Route::any('/test-appointment', function (Request $request, AppointmentService $service) {
    try {
        // ডামি ডেটা যা আপনার ডাটাবেস টেবিলের কলাম অনুযায়ী হওয়া উচিত
        $data = [
            'doctor_id' => 1,
            'appointment_date' => '2026-07-02',
            'start_time' => '11:00:00',
            'end_time' => '11:15:00',
            'patient_name' => 'Test Patient', // উদাহরণস্বরূপ
            'patient_phone' => '1234567890', // উদাহরণস্বরূপ
            'patient_email' => 'test patient@example.com' // উদাহরণস্বরূপ
            
        ];

        $appointment = $service->createAppointment($data);
        
        return response()->json([
            'message' => 'Appointment created successfully!',
            'data' => $appointment
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Error occurred!',
            'details' => $e->getMessage()
        ], 400);
    }
});

