<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\User\Presentation\API\AuthApiController;
use Modules\Core\User\Presentation\API\UserApiController;

Route::prefix('api/v1')
    ->middleware('api')
    ->group(function () {
        
        // Public Auth Routes
        Route::post('/auth/login', [AuthApiController::class, 'login']);

        // Protected Auth Routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/auth/logout', [AuthApiController::class, 'logout']);
            Route::get('/auth/me', [AuthApiController::class, 'me']);
        });

        // Protected User Routes
        Route::middleware('auth:sanctum')->prefix('users')->group(function () {
            Route::get('/', [UserApiController::class, 'index'])->middleware('permission:users.view');
            Route::get('/{id}', [UserApiController::class, 'show'])->middleware('permission:users.view');
            Route::post('/', [UserApiController::class, 'store'])->middleware('permission:users.create');
            Route::put('/{id}', [UserApiController::class, 'update'])->middleware('permission:users.update');
            Route::delete('/{id}', [UserApiController::class, 'destroy'])->middleware('permission:users.delete');
        });
    });
