<?php
use Illuminate\Support\Facades\Route;
use Modules\WEBSITE_EXTRA\Http\Controllers\ClientReviewController;
use Modules\WEBSITE_EXTRA\Http\Controllers\ContactMessageController;
use Modules\WEBSITE_EXTRA\Http\Controllers\HealthPackageController;
use Modules\WEBSITE_EXTRA\Http\Controllers\HeroSliderController;
use Modules\WEBSITE_EXTRA\Http\Controllers\PatientReviewController;
use Modules\WEBSITE_EXTRA\Http\Controllers\ServiceController;

Route::middleware(['web','auth'])->name('admin.')->prefix('admin')->group(function(): void {
    Route::resource('hero-sliders', HeroSliderController::class)->only(['index','store','update','destroy']);
    Route::resource('services', ServiceController::class)->only(['index','create','store','edit','update','destroy']);
    Route::resource('client-reviews', ClientReviewController::class)->only(['index','store','update','destroy']);
    Route::resource('patient-reviews', PatientReviewController::class)->only(['index','store','update','destroy']);
    Route::post('patient-reviews/{id}/approve', [PatientReviewController::class,'approve'])->name('patient-reviews.approve');
    Route::post('patient-reviews/{id}/reject', [PatientReviewController::class,'reject'])->name('patient-reviews.reject');
    Route::resource('health-packages', HealthPackageController::class)->only(['index','create','store','edit','update','destroy']);
    Route::get('contact-messages', [ContactMessageController::class, 'index'])->name('contact-messages.index');
    Route::post('contact-messages/{id}/read', [ContactMessageController::class, 'markRead'])->name('contact-messages.read');
    Route::post('contact-messages/{id}/resolve', [ContactMessageController::class, 'markResolved'])->name('contact-messages.resolve');
    Route::post('contact-messages/{id}/archive', [ContactMessageController::class, 'archive'])->name('contact-messages.archive');
    Route::delete('contact-messages/{id}', [ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');
});
