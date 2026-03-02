<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\InterestType\Presentation\API\InterestTypeApiController;

Route::middleware(['api', 'auth:sanctum'])->prefix('api/v1')->name('api.')->group(function () {
    Route::apiResource('interest-types', InterestTypeApiController::class)->parameters([
        'interest-types' => 'interest_type'
    ]);
});
