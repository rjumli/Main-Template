<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {return inertia('Auth/Login'); });

Route::middleware(['2fa','auth','verified'])->group(function () {
    Route::get('/dashboard', function () {return inertia('Modules/Dashboard/Index'); })->name('dashboard');
    Route::resource('/profile', App\Http\Controllers\User\ProfileController::class);
});


require __DIR__.'/auth.php';
require __DIR__.'/utility.php';
