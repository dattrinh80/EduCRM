<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\CRM\Customer\Presentation\Web\CustomerWebController;

Route::middleware(['web', 'auth'])->prefix('admin/customers')->name('admin.customers.')->group(function () {
    Route::get('/', [CustomerWebController::class, 'index'])->name('index');
});
