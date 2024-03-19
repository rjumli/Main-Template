<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['2fa','auth'])->group(function () {
    Route::get('/utilities/{type}', [App\Http\Controllers\User\UtilityController::class, 'index']);
    Route::prefix('utility')->group(function(){

        Route::prefix('menus')->group(function(){
            Route::controller(App\Http\Controllers\User\Utility\MenuController::class)->group(function () {
                Route::get('/','index');
                Route::post('/','store');
            });
        });

        Route::prefix('roles')->group(function(){
            Route::controller(App\Http\Controllers\User\Utility\RoleController::class)->group(function () {
                Route::get('/','index');
                Route::post('/','store');
                Route::put('/','update');
            });
        });
        
        Route::resource('/users', App\Http\Controllers\User\Utility\UserController::class);
    });
});