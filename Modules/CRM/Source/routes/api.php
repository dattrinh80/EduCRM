<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\Source\Presentation\API\SourceApiController;

Route::middleware(['api', 'auth:sanctum'])->prefix('api/v1')->name('api.')->group(function () {
    Route::apiResource('sources', SourceApiController::class);
});
