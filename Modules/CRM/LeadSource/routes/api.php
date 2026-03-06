<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\LeadSource\Presentation\API\LeadSourceApiController;

Route::middleware(['api', 'auth:sanctum'])->prefix('api/v1')->name('api.')->group(function () {
    Route::apiResource('lead-sources', LeadSourceApiController::class);
});
