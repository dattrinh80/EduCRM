<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\LeadSource\Presentation\Web\LeadSourceWebController;

Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('lead-sources', LeadSourceWebController::class)->except(['create', 'edit', 'show']);
});
