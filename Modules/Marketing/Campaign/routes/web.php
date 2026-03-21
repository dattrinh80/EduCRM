<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Marketing\Campaign\Presentation\Web\CampaignWebController;

Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/campaigns', [CampaignWebController::class, 'index'])->name('campaigns.index')->middleware('permission:campaigns.view');
    Route::get('/campaigns/create', [CampaignWebController::class, 'create'])->name('campaigns.create')->middleware('permission:campaigns.create');
    Route::post('/campaigns', [CampaignWebController::class, 'store'])->name('campaigns.store')->middleware('permission:campaigns.create');
    Route::get('/campaigns/{id}/edit', [CampaignWebController::class, 'edit'])->name('campaigns.edit')->middleware('permission:campaigns.update');
    Route::put('/campaigns/{id}', [CampaignWebController::class, 'update'])->name('campaigns.update')->middleware('permission:campaigns.update');
    Route::delete('/campaigns/{id}', [CampaignWebController::class, 'destroy'])->name('campaigns.destroy')->middleware('permission:campaigns.delete');
});
