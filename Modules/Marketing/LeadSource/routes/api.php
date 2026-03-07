<?php

use Illuminate\Support\Facades\Route;
use Modules\Marketing\LeadSource\Presentation\API\LeadSourceApiController;

Route::middleware(['api', 'auth:sanctum'])->prefix('api/v1')->name('api.')->group(function () {
    Route::apiResource('lead-sources', LeadSourceApiController::class);
});
