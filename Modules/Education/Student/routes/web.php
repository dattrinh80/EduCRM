<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Education\Student\Presentation\Web\StudentWebController;

Route::middleware(['web', 'auth'])->prefix('admin/students')->name('admin.students.')->group(function () {
    Route::get('/', [StudentWebController::class, 'index'])->name('index');
    Route::get('/create', [StudentWebController::class, 'create'])->name('create');
    Route::post('/', [StudentWebController::class, 'store'])->name('store');
    Route::get('/export', [StudentWebController::class, 'export'])->name('export');
    Route::post('/import', [StudentWebController::class, 'import'])->name('import');
    Route::post('/import/process', [StudentWebController::class, 'importProcess'])->name('import.process');
    Route::get('/{id}', [StudentWebController::class, 'show'])->name('show')->whereUuid('id');
    Route::get('/{id}/edit', [StudentWebController::class, 'edit'])->name('edit')->whereUuid('id');
    Route::put('/{id}', [StudentWebController::class, 'update'])->name('update')->whereUuid('id');
    Route::delete('/{id}', [StudentWebController::class, 'destroy'])->name('destroy')->whereUuid('id');
});
