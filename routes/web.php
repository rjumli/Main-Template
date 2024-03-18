<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {return inertia('Auth/Login'); });

Route::middleware(['2fa','auth'])->group(function () {
    Route::get('/dashboard', function () {return inertia('Modules/Dashboard/Index'); })->name('dashboard');
    Route::resource('/profile', App\Http\Controllers\ProfileController::class);
});


require __DIR__.'/auth.php';
