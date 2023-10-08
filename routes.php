<?php

use Backend\Facades\Backend;
use Illuminate\Support\Facades\Route;
use Vdomah\JWTAuth\Controllers\AuthController;
use Vdomah\JWTAuth\Controllers\RefreshController;

Route::group(['prefix' => 'jwtauth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [RefreshController::class, 'refresh']);
    Route::group(['middleware' => 'jwt.auth'], function () {
        Route::post('invalidate', [AuthController::class, 'invalidate']);
        Route::post('signup', [AuthController::class, 'signup']);
        Route::get('user', [AuthController::class, 'user']);
    });
});

Route::group(['prefix' => Backend::baseUrl() . '/jwtauth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::group(['middleware' => 'jwt.auth'], function () {
        Route::post('refresh', ['uses' => '\Vdomah\JWTAuth\Controllers\AuthController@refresh']);
        Route::post('invalidate', [AuthController::class, 'invalidate']);
        Route::get('user', [AuthController::class, 'user']);
    });
});

