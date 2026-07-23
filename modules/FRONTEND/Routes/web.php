<?php

use Illuminate\Support\Facades\Route;
use Modules\FRONTEND\Http\Controllers\FrontAppointmentController;
use Modules\FRONTEND\Http\Controllers\FrontBlogController;
use Modules\FRONTEND\Http\Controllers\FrontCOEController;
use Modules\FRONTEND\Http\Controllers\FrontDepartmentController;
use Modules\FRONTEND\Http\Controllers\FrontDoctorController;
use Modules\FRONTEND\Http\Controllers\FrontOtherController;
use Modules\FRONTEND\Http\Controllers\FrontServiceController;
use Modules\FRONTEND\Http\Controllers\HomepageController;
use Modules\WEBSITE\Models\Department;

Route::middleware(['web', 'auth'])->group(function (): void {
    //
});

Route::get('/', [HomepageController::class, 'index'])->name('home');
Route::get('/departments', [FrontDepartmentController::class, 'index'])->name('front.department.index');
Route::get('/departments/{slug}', [FrontDepartmentController::class, 'details'])->name('front.department.details');
Route::get('/doctors', [FrontDoctorController::class, 'index'])->name('front.doctor.index');
Route::get('/doctors/{slug}', [FrontDoctorController::class, 'profile'])->name('front.doctor.profile');


Route::get('/center-of-excellence', [FrontCOEController::class, 'index'])->name('front.coe.index');
Route::get('/center-of-excellence/{slug}', [FrontCOEController::class, 'show'])->name('front.coe.show');

Route::get('/blogs', [FrontBlogController::class, 'index'])->name('front.blog.index');
Route::get('/blogs/{slug}', [FrontBlogController::class, 'details'])->name('front.blog.details');

Route::get('/about', [FrontOtherController::class, 'about'])->name('front.about');

Route::get('/appointment', [FrontAppointmentController::class, 'index'])->name('front.appointment.index');

Route::get('/contact', [FrontOtherController::class, 'contact'])->name('front.contact');
Route::post('/contact', [FrontOtherController::class, 'storeContact'])->name('front.contact.store');

Route::get('/services', [FrontServiceController::class, 'index'])->name('front.service.index');
Route::get('/services/{slug}', [FrontServiceController::class, 'details'])->name('front.service.details');

Route::get('/test-dept', function () {
    dd(Department::orderBy('serial', 'asc')->get(['name', 'slug']));
});