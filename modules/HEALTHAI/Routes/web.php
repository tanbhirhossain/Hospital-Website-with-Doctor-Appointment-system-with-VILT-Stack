<?php

use Illuminate\Support\Facades\Route;
use Modules\HEALTHAI\Http\Controllers\AiAgentController;

Route::middleware(['web', 'auth'])->group(function (): void {
    
});

Route::get('/chat', [AiAgentController::class, 'index']);


Route::get('/check-api', function () {
    $medicine = new \Modules\HEALTHAI\Services\MISDataService();
    $data = $medicine->getPharmacy(request());
    return response()->json($data);
});