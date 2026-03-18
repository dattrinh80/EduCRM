<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Education\Student\Presentation\API\StudentApiController;

Route::middleware(['api', 'auth:sanctum'])->prefix('v1/students')->group(function () {
    Route::get('/', [StudentApiController::class, 'index'])->middleware('permission:students.view');
    Route::get('/{id}', [StudentApiController::class, 'show'])->middleware('permission:students.view')->whereUuid('id');
    Route::post('/', [StudentApiController::class, 'store'])->middleware('permission:students.create');
    Route::put('/{id}', [StudentApiController::class, 'update'])->middleware('permission:students.update')->whereUuid('id');
    Route::delete('/{id}', [StudentApiController::class, 'destroy'])->middleware('permission:students.delete')->whereUuid('id');
});
