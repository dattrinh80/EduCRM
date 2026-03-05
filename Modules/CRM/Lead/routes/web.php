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
        Route::post('/merge', [LeadWebController::class, 'merge'])->name('merge')->middleware('permission:leads.update');
        Route::get('/{id}/edit', [LeadWebController::class, 'edit'])->name('edit')->middleware('permission:leads.update');
        Route::put('/{id}', [LeadWebController::class, 'update'])->name('update')->middleware('permission:leads.update');
        Route::delete('/{id}', [LeadWebController::class, 'destroy'])->name('destroy')->middleware('permission:leads.delete');
        Route::get('/export/excel', [LeadWebController::class, 'export'])->name('export')->middleware('permission:leads.export');
    });
