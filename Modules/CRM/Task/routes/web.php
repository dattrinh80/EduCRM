<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\CRM\Task\Presentation\Web\TaskWebController;

Route::prefix('admin/tasks')
    ->middleware(['web', 'auth'])
    ->name('admin.tasks.')
    ->group(function () {
        Route::get('/', [TaskWebController::class, 'index'])->name('index')->middleware('permission:tasks.view');
        Route::post('/', [TaskWebController::class, 'store'])->name('store')->middleware('permission:tasks.create');
        Route::put('/{id}', [TaskWebController::class, 'update'])->name('update')->middleware('permission:tasks.update');
        Route::get('/staff-by-center/{center_id}', [TaskWebController::class, 'getStaffByCenter'])->name('staff-by-center')->middleware('permission:tasks.view');
        Route::get('/search-relations', [TaskWebController::class, 'searchRelations'])->name('search-relations')->middleware('permission:tasks.view');
        Route::get('/{id}', [TaskWebController::class, 'show'])->name('show')->middleware('permission:tasks.view');
        Route::patch('/{id}/toggle-status', [TaskWebController::class, 'toggleStatus'])->name('toggle-status')->middleware('permission:tasks.update');
        Route::delete('/{id}', [TaskWebController::class, 'destroy'])->name('destroy')->middleware('permission:tasks.delete');
    });
