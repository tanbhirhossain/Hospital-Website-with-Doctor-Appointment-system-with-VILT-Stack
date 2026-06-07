<?php

use Illuminate\Support\Facades\Route;
use Modules\HEALTHAI\Http\Controllers\AiAgentController;

Route::middleware(['web', 'auth'])->group(function (): void {
    
});

Route::get('/chat', [AiAgentController::class, 'index']);