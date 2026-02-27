<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Permission\Presentation\Web\PermissionWebController;

Route::prefix('admin/permissions')
    ->middleware(['web', 'auth'])
    ->name('admin.permissions.')
    ->group(function () {
        Route::get('/', [PermissionWebController::class, 'index'])->name('index');
    });
