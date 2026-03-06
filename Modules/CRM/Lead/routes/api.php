<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\Lead\Presentation\API\LeadApiController;
use Modules\CRM\Lead\Presentation\API\LeadWebhookController;

Route::prefix('api/v1/leads')
    ->middleware(['api'])
    ->group(function () {
        Route::post('/webhook', [LeadWebhookController::class, 'receive']);
    });

Route::prefix('api/v1/leads')
    ->middleware(['api', 'auth:sanctum'])
    ->group(function () {
        Route::get('/', [LeadApiController::class, 'index'])->middleware('permission:leads.view');
        Route::get('/{id}', [LeadApiController::class, 'show'])->middleware('permission:leads.view');
        Route::post('/', [LeadApiController::class, 'store'])->middleware('permission:leads.create');
        Route::put('/{id}', [LeadApiController::class, 'update'])->middleware('permission:leads.update');
        Route::delete('/{id}', [LeadApiController::class, 'destroy'])->middleware('permission:leads.delete');

        // Lead Notes
        Route::get('/{id}/notes', [LeadApiController::class, 'getNotes'])->middleware('permission:leads.view');
        Route::post('/{id}/notes', [LeadApiController::class, 'storeNote'])->middleware('permission:leads.update');

        // Lead Activities
        Route::get('/{id}/activities', [LeadApiController::class, 'getActivities'])->middleware('permission:leads.view');
        Route::post('/{id}/activities', [LeadApiController::class, 'storeActivity'])->middleware('permission:leads.update');
    });
