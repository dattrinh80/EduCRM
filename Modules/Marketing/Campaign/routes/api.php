<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Marketing\Campaign\Presentation\API\CampaignApiController;

Route::middleware(['api', 'auth:sanctum'])->prefix('api/v1')->group(function () {
    Route::get('/campaigns', [CampaignApiController::class, 'index'])->middleware('permission:campaigns.view');
    Route::post('/campaigns', [CampaignApiController::class, 'store'])->middleware('permission:campaigns.create');
    Route::put('/campaigns/{id}', [CampaignApiController::class, 'update'])->middleware('permission:campaigns.update');
    Route::delete('/campaigns/{id}', [CampaignApiController::class, 'destroy'])->middleware('permission:campaigns.delete');
});
