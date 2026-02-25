<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\Lead\Presentation\Web\LeadWebController;

Route::prefix('admin/leads')
    ->middleware('web')
    ->name('admin.leads.')
    ->group(function () {
        Route::get('/', [LeadWebController::class, 'index'])->name('index');
        Route::get('/create', [LeadWebController::class, 'create'])->name('create');
        Route::post('/', [LeadWebController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [LeadWebController::class, 'edit'])->name('edit');
        Route::put('/{id}', [LeadWebController::class, 'update'])->name('update');
        Route::delete('/{id}', [LeadWebController::class, 'destroy'])->name('destroy');
    });
