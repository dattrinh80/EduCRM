<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Role\Presentation\Web\RoleWebController;

Route::prefix('admin/roles')
    ->middleware(['web', 'auth'])
    ->name('admin.roles.')
    ->group(function () {
        Route::get('/', [RoleWebController::class, 'index'])->name('index')->middleware('permission:roles.view');
        Route::get('/create', [RoleWebController::class, 'create'])->name('create')->middleware('permission:roles.create');
        Route::post('/', [RoleWebController::class, 'store'])->name('store')->middleware('permission:roles.create');
        Route::get('/{id}/edit', [RoleWebController::class, 'edit'])->name('edit')->middleware('permission:roles.update');
        Route::put('/{id}', [RoleWebController::class, 'update'])->name('update')->middleware('permission:roles.update');
        Route::delete('/{id}', [RoleWebController::class, 'destroy'])->name('destroy')->middleware('permission:roles.delete');
    });
