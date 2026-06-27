<?php

use Illuminate\Support\Facades\Route;
use Modules\FRONTEND\Http\Controllers\FrontDepartmentController;
use Modules\FRONTEND\Http\Controllers\HomepageController;

Route::middleware(['web', 'auth'])->group(function (): void {
    //
});

Route::get('/', [HomepageController::class, 'index'])->name('home');
Route::get('/departments', [FrontDepartmentController::class, 'index'])->name('front.department.index');
