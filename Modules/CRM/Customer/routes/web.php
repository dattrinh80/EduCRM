<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\CRM\Customer\Presentation\Web\CustomerWebController;

Route::middleware(['web', 'auth'])->prefix('admin/customers')->name('admin.customers.')->group(function () {
    Route::get('/', [CustomerWebController::class, 'index'])->name('index');
    Route::post('/', [CustomerWebController::class, 'store'])->name('store');
    Route::get('/export', [CustomerWebController::class, 'export'])->name('export');
    Route::post('/import', [CustomerWebController::class, 'import'])->name('import');
    Route::post('/import-process', [CustomerWebController::class, 'importProcess'])->name('import_process');
    Route::get('/import-template', [CustomerWebController::class, 'downloadTemplate'])->name('download_template');
    
    // Action routes (Moved after static routes and added UUID constraints)
    Route::get('/{id}', [CustomerWebController::class, 'show'])->name('show')->whereUuid('id');
    Route::put('/{id}', [CustomerWebController::class, 'update'])->name('update')->whereUuid('id');
    Route::delete('/{id}', [CustomerWebController::class, 'destroy'])->name('destroy')->whereUuid('id');
    
    // Child items
    Route::post('/{id}/notes', [CustomerWebController::class, 'storeNote'])->name('notes.store')->whereUuid('id');
    Route::post('/{id}/activities', [CustomerWebController::class, 'storeActivity'])->name('activities.store')->whereUuid('id');
});
