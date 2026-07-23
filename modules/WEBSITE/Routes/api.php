<?php

use Illuminate\Support\Facades\Route;
use Modules\WEBSITE\Http\Controllers\ApiController;

Route::middleware(['api'])->prefix('api')->group(function (): void {
    //
});

Route::get('/api/departments', [ApiController::class, 'departments']);