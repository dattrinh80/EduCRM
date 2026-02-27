<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Role\Presentation\API\RoleApiController;

Route::prefix('api/v1/roles')
    ->middleware(['api', 'auth:sanctum'])
    ->group(function () {
        Route::get('/', [RoleApiController::class, 'index'])->middleware('permission:roles.view');
        Route::get('/{id}', [RoleApiController::class, 'show'])->middleware('permission:roles.view');
        Route::post('/', [RoleApiController::class, 'store'])->middleware('permission:roles.create');
        Route::put('/{id}', [RoleApiController::class, 'update'])->middleware('permission:roles.update');
        Route::delete('/{id}', [RoleApiController::class, 'destroy'])->middleware('permission:roles.delete');
    });
