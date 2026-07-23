<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Modules\WEBSITE\Http\Controllers\COEController;
use Modules\WEBSITE\Http\Controllers\DepartmentController;
use Modules\WEBSITE\Http\Controllers\DoctorController;



Route::middleware(['web', 'auth'])->name('admin.')->prefix('admin')->group(function (): void {
    Route::resource('departments', DepartmentController::class)->only(['index', 'store', 'update', 'destroy', 'create', 'edit']);
    Route::resource('doctors', DoctorController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('coe', COEController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

});

