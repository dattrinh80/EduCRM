<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\User\Presentation\Web\AuthController;
use Modules\Core\User\Presentation\Web\UserWebController;

// Auth Routes (Guest only)
Route::middleware('web')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Protected Admin Routes
Route::prefix('admin/users')
    ->middleware(['web', 'auth'])
    ->name('admin.users.')
    ->group(function () {
        Route::get('/', [UserWebController::class, 'index'])->name('index');
        Route::get('/create', [UserWebController::class, 'create'])->name('create');
        Route::post('/', [UserWebController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [UserWebController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserWebController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserWebController::class, 'destroy'])->name('destroy');
    });
