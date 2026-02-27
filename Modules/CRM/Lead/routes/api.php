<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\Lead\Presentation\API\LeadApiController;

Route::prefix('api/v1/leads')
    ->middleware(['api', 'auth:sanctum'])
    ->group(function () {
        Route::get('/', [LeadApiController::class, 'index'])->middleware('permission:leads.view');
        Route::get('/{id}', [LeadApiController::class, 'show'])->middleware('permission:leads.view');
        Route::post('/', [LeadApiController::class, 'store'])->middleware('permission:leads.create');
        Route::put('/{id}', [LeadApiController::class, 'update'])->middleware('permission:leads.update');
        Route::delete('/{id}', [LeadApiController::class, 'destroy'])->middleware('permission:leads.delete');
    });
