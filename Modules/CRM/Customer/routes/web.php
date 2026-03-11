<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\CRM\Customer\Presentation\Web\CustomerWebController;

Route::middleware(['web', 'auth'])->prefix('admin/customers')->name('admin.customers.')->group(function () {
    Route::get('/', [CustomerWebController::class, 'index'])->name('index');
    Route::get('/search-json', [CustomerWebController::class, 'searchJson'])->name('search_json');
    Route::post('/', [CustomerWebController::class, 'store'])->name('store');
    Route::get('/export', [CustomerWebController::class, 'export'])->name('export');
    Route::post('/import', [CustomerWebController::class, 'import'])->name('import');
    Route::post('/import-process', [CustomerWebController::class, 'importProcess'])->name('import_process');
    Route::get('/import-template', [CustomerWebController::class, 'downloadTemplate'])->name('download_template');
    
    // Action routes
    Route::get('/{id}', [CustomerWebController::class, 'show'])->name('show');
    Route::put('/{id}', [CustomerWebController::class, 'update'])->name('update');
    Route::delete('/{id}', [CustomerWebController::class, 'destroy'])->name('destroy');
    
    // Child items
    Route::post('/{id}/notes', [CustomerWebController::class, 'storeNote'])->name('notes.store');
    Route::post('/{id}/activities', [CustomerWebController::class, 'storeActivity'])->name('activities.store');
});
