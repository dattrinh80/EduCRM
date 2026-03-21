<?php

use Illuminate\Support\Facades\Route;
use Modules\Marketing\LeadSource\Presentation\Web\LeadSourceWebController;

Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('lead-sources', LeadSourceWebController::class)->except(['show']);
});
