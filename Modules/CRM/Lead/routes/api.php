<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\Lead\Presentation\API\LeadApiController;

Route::prefix('api/v1/leads')
    ->middleware('api')
    ->group(function () {
        Route::get('/', [LeadApiController::class, 'index']);
        Route::get('/{id}', [LeadApiController::class, 'show']);
        Route::post('/', [LeadApiController::class, 'store']);
        Route::put('/{id}', [LeadApiController::class, 'update']);
        Route::delete('/{id}', [LeadApiController::class, 'destroy']);
    });
