<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\CRM\Customer\Presentation\Web\CustomerWebController;

Route::middleware(['web', 'auth'])->prefix('admin/customers')->name('admin.customers.')->group(function () {
    Route::get('/', [CustomerWebController::class, 'index'])->name('index');
    Route::get('/search-json', [CustomerWebController::class, 'searchJson'])->name('search_json');
    Route::post('/', [CustomerWebController::class, 'store'])->name('store');
    Route::put('/{id}', [CustomerWebController::class, 'update'])->name('update');
});
