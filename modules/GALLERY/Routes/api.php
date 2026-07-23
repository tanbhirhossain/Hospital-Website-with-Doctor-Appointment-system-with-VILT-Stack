<?php

use Illuminate\Support\Facades\Route;
use Modules\GALLERY\Http\Controllers\GalleryApiController;

Route::middleware('api')->prefix('api')->group(function (): void {
    Route::get('/gallery', [GalleryApiController::class, 'index'])->name('api.gallery.index');
    Route::get('/gallery/category/{category}', [GalleryApiController::class, 'index'])->name('api.gallery.category');
});
