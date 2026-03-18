<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\CRM\Customer\Presentation\Web\CustomerWebController;

Route::middleware(['web', 'auth'])->prefix('admin/customers')->name('admin.customers.')->group(function () {
    Route::get('/', [CustomerWebController::class, 'index'])->name('index')->middleware('permission:customers.view');
    Route::post('/', [CustomerWebController::class, 'store'])->name('store')->middleware('permission:customers.create');
    Route::get('/export', [CustomerWebController::class, 'export'])->name('export')->middleware('permission:customers.export');
    Route::post('/import', [CustomerWebController::class, 'import'])->name('import')->middleware('permission:customers.create');
    Route::post('/import-process', [CustomerWebController::class, 'importProcess'])->name('import_process')->middleware('permission:customers.create');
    Route::get('/import-template', [CustomerWebController::class, 'downloadTemplate'])->name('download_template')->middleware('permission:customers.create');
    
    // Action routes (Moved after static routes and added UUID constraints)
    Route::get('/{id}', [CustomerWebController::class, 'show'])->name('show')->middleware('permission:customers.view')->whereUuid('id');
    Route::put('/{id}', [CustomerWebController::class, 'update'])->name('update')->middleware('permission:customers.update')->whereUuid('id');
    Route::delete('/{id}', [CustomerWebController::class, 'destroy'])->name('destroy')->middleware('permission:customers.delete')->whereUuid('id');
    
    // Child items
    Route::post('/{id}/notes', [CustomerWebController::class, 'storeNote'])->name('notes.store')->middleware('permission:customers.update')->whereUuid('id');
    Route::post('/{id}/activities', [CustomerWebController::class, 'storeActivity'])->name('activities.store')->middleware('permission:customers.update')->whereUuid('id');
});
