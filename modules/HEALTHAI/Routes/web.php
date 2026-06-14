<?php

use Illuminate\Support\Facades\Route;
use Modules\HEALTHAI\Http\Controllers\AiAgentController;

Route::middleware(['web', 'auth'])->group(function (): void {
    Route::get('/chat', [AiAgentController::class, 'index'])->name('healthai.chat');
});

// Debug-only route — remove in production or guard behind a gate
if (app()->isLocal()) {
    Route::get('/check-api', function () {
        $service = new \Modules\HEALTHAI\Services\MISDataService();
        return response()->json($service->searchPharmacy(request()->query('search', '')));
    });
}
