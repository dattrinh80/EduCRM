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
        Route::get('/{id}/edit', [LeadWebController::class, 'edit'])->name('edit')->middleware('permission:leads.update');
        Route::put('/{id}', [LeadWebController::class, 'update'])->name('update')->middleware('permission:leads.update');
        Route::delete('/{id}', [LeadWebController::class, 'destroy'])->name('destroy')->middleware('permission:leads.delete');
    });
