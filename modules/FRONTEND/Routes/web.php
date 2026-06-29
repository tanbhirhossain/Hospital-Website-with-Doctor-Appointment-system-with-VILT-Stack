<?php

use Illuminate\Support\Facades\Route;
use Modules\FRONTEND\Http\Controllers\FrontDepartmentController;
use Modules\FRONTEND\Http\Controllers\FrontDoctorController;
use Modules\FRONTEND\Http\Controllers\HomepageController;

Route::middleware(['web', 'auth'])->group(function (): void {
    //
});

Route::get('/', [HomepageController::class, 'index'])->name('home');
Route::get('/departments', [FrontDepartmentController::class, 'index'])->name('front.department.index');
Route::get('/departments/{slug}', [FrontDepartmentController::class, 'details'])->name('front.department.details');
Route::get('/doctors', [FrontDoctorController::class, 'index'])->name('front.doctor.index');
Route::get('/doctors/{slug}', [FrontDoctorController::class, 'profile'])->name('front.doctor.profile');

