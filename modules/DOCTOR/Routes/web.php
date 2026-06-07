<?php

use Illuminate\Support\Facades\Route;
use Modules\DOCTOR\Http\Controllers\DepartmentController;
use Modules\DOCTOR\Http\Controllers\DoctorController;

Route::middleware(['web', 'auth'])->group(function (): void {
    Route::resource('departments', DepartmentController::class)->only(['index', 'store', 'update', 'destroy', 'create']);
    Route::resource('doctors', DoctorController::class)->only(['index', 'create', 'store', 'update', 'destory']);
});
