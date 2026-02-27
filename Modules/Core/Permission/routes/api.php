<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Permission\Presentation\API\PermissionApiController;

Route::prefix('api/v1/permissions')
    ->middleware(['api', 'auth:sanctum'])
    ->group(function () {
        Route::get('/', [PermissionApiController::class, 'index'])->middleware('permission:roles.view');
        // We use roles.view permission here since viewing permissions is typically associated with creating/editing roles
    });
