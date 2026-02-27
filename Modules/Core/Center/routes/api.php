<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Center\Presentation\API\CenterApiController;

Route::prefix('api/v1/centers')
    ->middleware(['api', 'auth:sanctum'])
    ->group(function () {
        Route::get('/', [CenterApiController::class, 'index'])->middleware('permission:centers.view');
        Route::get('/{id}', [CenterApiController::class, 'show'])->middleware('permission:centers.view');
        Route::post('/', [CenterApiController::class, 'store'])->middleware('permission:centers.create');
        Route::put('/{id}', [CenterApiController::class, 'update'])->middleware('permission:centers.update');
        Route::delete('/{id}', [CenterApiController::class, 'destroy'])->middleware('permission:centers.delete');
    });
