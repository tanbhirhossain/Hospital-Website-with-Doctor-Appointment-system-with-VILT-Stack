<?php

use Illuminate\Support\Facades\Route;
use Modules\ACL\Http\Controllers\RoleController;
use Modules\ACL\Http\Controllers\UserController;

Route::middleware(['web', 'auth'])->group(function (): void {
    Route::resource('roles', RoleController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);
});
