<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Education\Student\Presentation\Web\StudentWebController;

Route::middleware(['web', 'auth'])->prefix('admin/students')->name('admin.students.')->group(function () {
    Route::get('/', [StudentWebController::class, 'index'])->name('index');
});
