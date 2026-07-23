<?php

use Illuminate\Support\Facades\Route;
use Modules\BLOG\Http\Controllers\BlogController;
use Modules\BLOG\Http\Controllers\PublicBlogController;

Route::middleware('web')->group(function (): void {
    Route::get('/blog', [PublicBlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/{slug}', [PublicBlogController::class, 'show'])->name('blog.show');
});

Route::middleware(['web', 'auth'])->group(function (): void {
    Route::resource('blogs', BlogController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::patch('/blogs/{blog}/restore', [BlogController::class, 'restore'])->name('blogs.restore');
    Route::delete('/blogs/{blog}/force', [BlogController::class, 'forceDelete'])->name('blogs.force-delete');
});
