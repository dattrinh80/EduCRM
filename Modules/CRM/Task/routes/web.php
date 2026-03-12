<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\Task\Presentation\Web\TaskWebController;

Route::prefix('admin/tasks')
    ->middleware(['web', 'auth'])
    ->name('admin.tasks.')
    ->group(function () {
        Route::get('/', [TaskWebController::class, 'index'])->name('index')->middleware('permission:leads.view');
        Route::post('/', [TaskWebController::class, 'store'])->name('store')->middleware('permission:leads.update');
        Route::put('/{id}', [TaskWebController::class, 'update'])->name('update')->middleware('permission:leads.update');
        Route::get('/staff-by-center/{center_id}', [TaskWebController::class, 'getStaffByCenter'])->name('staff-by-center')->middleware('permission:leads.view');
        Route::get('/search-relations', [TaskWebController::class, 'searchRelations'])->name('search-relations')->middleware('permission:leads.view');
        Route::get('/{id}', [TaskWebController::class, 'show'])->name('show')->middleware('permission:leads.view');
        Route::patch('/{id}/toggle-status', [TaskWebController::class, 'toggleStatus'])->name('toggle-status')->middleware('permission:leads.update');
        Route::delete('/{id}', [TaskWebController::class, 'destroy'])->name('destroy')->middleware('permission:leads.update');
    });
