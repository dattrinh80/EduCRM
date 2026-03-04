<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/admin/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('admin.dashboard');
