<?php

use Illuminate\Support\Facades\Route;
use Modules\DOCTOR\Http\Controllers\DepartmentController;

Route::middleware(['web', 'auth'])->group(function (): void {
    Route::resource('departments', DepartmentController::class)->only(['index', 'store', 'update', 'destroy', 'create']);
});
