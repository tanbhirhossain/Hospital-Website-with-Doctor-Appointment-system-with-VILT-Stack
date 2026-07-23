<?php

use Illuminate\Support\Facades\Route;
use Modules\EMAILMARKETING\Http\Controllers\NewsletterController;

Route::middleware(['api'])->prefix('api')->group(function (): void {
    Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('api.newsletter.subscribe');
});
