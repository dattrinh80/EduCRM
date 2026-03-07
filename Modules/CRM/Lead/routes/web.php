<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\Lead\Presentation\Web\LeadWebController;

Route::prefix('admin/leads')
    ->middleware(['web', 'auth'])
    ->name('admin.leads.')
    ->group(function () {
        Route::get('/', [LeadWebController::class, 'index'])->name('index')->middleware('permission:leads.view');
        Route::get('/create', [LeadWebController::class, 'create'])->name('create')->middleware('permission:leads.create');
        Route::post('/', [LeadWebController::class, 'store'])->name('store')->middleware('permission:leads.create');
        Route::post('/import', [LeadWebController::class, 'import'])->name('import')->middleware('permission:leads.create');
        Route::post('/import/process', [LeadWebController::class, 'importProcess'])->name('import.process')->middleware('permission:leads.create');
        Route::get('/template', [LeadWebController::class, 'downloadTemplate'])->name('template')->middleware('permission:leads.create');
        Route::post('/assign', [LeadWebController::class, 'assign'])->name('assign')->middleware('permission:leads.update');
        Route::post('/bulk-update', [LeadWebController::class, 'bulkUpdate'])->name('bulk-update')->middleware('permission:leads.update');
        Route::post('/merge', [LeadWebController::class, 'merge'])->name('merge')->middleware('permission:leads.update');
        Route::get('/export', [LeadWebController::class, 'export'])->name('export')->middleware('permission:leads.export');

        // Lead Detail + sub-resources
        Route::get('/{id}', [LeadWebController::class, 'show'])->name('show')->middleware('permission:leads.view');
        Route::post('/{id}/notes', [LeadWebController::class, 'storeNote'])->name('notes.store')->middleware('permission:leads.update');
        Route::post('/{id}/activities', [LeadWebController::class, 'storeActivity'])->name('activities.store')->middleware('permission:leads.update');

        Route::get('/{id}/edit', [LeadWebController::class, 'edit'])->name('edit')->middleware('permission:leads.update');
        Route::put('/{id}', [LeadWebController::class, 'update'])->name('update')->middleware('permission:leads.update');
        Route::delete('/{id}', [LeadWebController::class, 'destroy'])->name('destroy')->middleware('permission:leads.delete');
    });

// Lead Config routes (Sub-modules)
Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('lead-statuses', \Modules\CRM\LeadStatus\Presentation\Web\LeadStatusWebController::class)->except(['create', 'edit', 'show'])->middleware('permission:leads.update');
    Route::resource('lead-tags', \Modules\CRM\LeadTag\Presentation\Web\LeadTagWebController::class)->except(['create', 'edit', 'show'])->middleware('permission:leads.update');

    // Conversion
    Route::get('leads/{id}/convert', [\Modules\CRM\Lead\Presentation\Web\LeadConversionWebController::class, 'show'])->name('leads.convert');
    Route::post('leads/{id}/convert', [\Modules\CRM\Lead\Presentation\Web\LeadConversionWebController::class, 'convert'])->name('leads.convert.submit');
});
