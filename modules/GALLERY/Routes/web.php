<?php

use Illuminate\Support\Facades\Route;
use Modules\GALLERY\Http\Controllers\GalleryCategoryController;
use Modules\GALLERY\Http\Controllers\GalleryController;
use Modules\GALLERY\Http\Controllers\GalleryItemController;
use Modules\GALLERY\Http\Controllers\PublicGalleryController;

Route::middleware('web')->get('/photo-gallery', PublicGalleryController::class)->name('gallery.public');

Route::middleware(['web', 'auth'])->group(function (): void {
    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
    Route::resource('gallery-items', GalleryItemController::class)
        ->only(['store', 'update', 'destroy'])
        ->parameters(['gallery-items' => 'gallery_item']);
    Route::resource('gallery-categories', GalleryCategoryController::class)
        ->only(['store', 'update', 'destroy'])
        ->parameters(['gallery-categories' => 'gallery_category']);
});
