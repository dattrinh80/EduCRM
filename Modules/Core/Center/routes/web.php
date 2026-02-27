<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Center\Presentation\Web\CenterWebController;

Route::prefix('admin/centers')
    ->middleware(['web', 'auth'])
    ->name('admin.centers.')
    ->group(function () {
        Route::get('/', [CenterWebController::class, 'index'])->name('index')->middleware('permission:centers.view');
        Route::get('/create', [CenterWebController::class, 'create'])->name('create')->middleware('permission:centers.create');
        Route::post('/', [CenterWebController::class, 'store'])->name('store')->middleware('permission:centers.create');
        Route::get('/{id}/edit', [CenterWebController::class, 'edit'])->name('edit')->middleware('permission:centers.update');
        Route::put('/{id}', [CenterWebController::class, 'update'])->name('update')->middleware('permission:centers.update');
        Route::delete('/{id}', [CenterWebController::class, 'destroy'])->name('destroy')->middleware('permission:centers.delete');
    });
