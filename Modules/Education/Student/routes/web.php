<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Education\Student\Presentation\Web\StudentWebController;

Route::middleware(['web', 'auth'])->prefix('admin/students')->name('admin.students.')->group(function () {
    Route::get('/', [StudentWebController::class, 'index'])->name('index')->middleware('permission:students.view');
    Route::get('/create', [StudentWebController::class, 'create'])->name('create')->middleware('permission:students.create');
    Route::post('/', [StudentWebController::class, 'store'])->name('store')->middleware('permission:students.create');
    Route::get('/export', [StudentWebController::class, 'export'])->name('export')->middleware('permission:students.export');
    Route::post('/import', [StudentWebController::class, 'import'])->name('import')->middleware('permission:students.create');
    Route::post('/import/process', [StudentWebController::class, 'importProcess'])->name('import.process')->middleware('permission:students.create');
    Route::get('/{id}', [StudentWebController::class, 'show'])->name('show')->middleware('permission:students.view')->whereUuid('id');
    Route::get('/{id}/edit', [StudentWebController::class, 'edit'])->name('edit')->middleware('permission:students.update')->whereUuid('id');
    Route::put('/{id}', [StudentWebController::class, 'update'])->name('update')->middleware('permission:students.update')->whereUuid('id');
    Route::delete('/{id}', [StudentWebController::class, 'destroy'])->name('destroy')->middleware('permission:students.delete')->whereUuid('id');
});
