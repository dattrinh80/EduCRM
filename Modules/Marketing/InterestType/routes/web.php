<?php

use Illuminate\Support\Facades\Route;
use Modules\Marketing\InterestType\Presentation\Web\InterestTypeWebController;

Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('interest-types', InterestTypeWebController::class)->except(['create', 'edit', 'show'])->parameters([
        'interest-types' => 'interest_type'
    ]);
});
