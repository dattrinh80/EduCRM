<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\Source\Presentation\Web\SourceWebController;

Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('sources', SourceWebController::class)->except(['create', 'edit', 'show']);
});
