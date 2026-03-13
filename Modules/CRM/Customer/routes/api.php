<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\Customer\Presentation\API\CustomerApiController;

Route::prefix('api/v1/customers')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::get('/', [CustomerApiController::class, 'index']);
    Route::post('/', [CustomerApiController::class, 'store']);
    Route::get('/{id}', [CustomerApiController::class, 'show'])->whereUuid('id');
    Route::put('/{id}', [CustomerApiController::class, 'update'])->whereUuid('id');
    Route::delete('/{id}', [CustomerApiController::class, 'destroy'])->whereUuid('id');
});
