<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Education\Student\Presentation\API\StudentApiController;

Route::middleware(['api', 'auth:sanctum'])->prefix('v1/students')->group(function () {
    Route::get('/', [StudentApiController::class, 'index']);
    Route::get('/{id}', [StudentApiController::class, 'show'])->whereUuid('id');
    Route::post('/', [StudentApiController::class, 'store']);
    Route::put('/{id}', [StudentApiController::class, 'update'])->whereUuid('id');
    Route::delete('/{id}', [StudentApiController::class, 'destroy'])->whereUuid('id');
});
