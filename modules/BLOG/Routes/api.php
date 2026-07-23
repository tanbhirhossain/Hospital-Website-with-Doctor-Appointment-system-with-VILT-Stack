<?php

use Illuminate\Support\Facades\Route;
use Modules\BLOG\Http\Controllers\BlogApiController;

Route::middleware('api')->prefix('api')->group(function (): void {
    Route::get('/blogs', [BlogApiController::class, 'index'])->name('api.blogs.index');
    Route::get('/blogs/{slug}', [BlogApiController::class, 'show'])->name('api.blogs.show');
});
