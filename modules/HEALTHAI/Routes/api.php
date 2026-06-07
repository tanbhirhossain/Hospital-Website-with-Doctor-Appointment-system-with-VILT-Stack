<?php

use Illuminate\Support\Facades\Route;
use Modules\HEALTHAI\Http\Controllers\AiAgentController;

Route::middleware(['api'])->prefix('api')->group(function (): void {

});

    Route::post('api/chat', [AiAgentController::class, 'handleRequest']);
