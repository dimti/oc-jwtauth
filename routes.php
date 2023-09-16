<?php

use Backend\Facades\Backend;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'jwtauth'], function () {
    Route::post('login', ['uses' => '\Vdomah\JWTAuth\Controllers\AuthController@login']);
    Route::group(['middleware' => 'jwt.auth'], function () {
        Route::post('refresh', ['uses' => '\Vdomah\JWTAuth\Controllers\AuthController@refresh']);
        Route::post('invalidate', ['uses' => '\Vdomah\JWTAuth\Controllers\AuthController@invalidate']);
        Route::post('signup', ['uses' => '\Vdomah\JWTAuth\Controllers\AuthController@signup']);
        Route::get('user', ['uses' => '\Vdomah\JWTAuth\Controllers\AuthController@user']);
    });
});

Route::group(['prefix' => Backend::baseUrl() . '/jwtauth'], function () {
    Route::post('login', ['uses' => '\Vdomah\JWTAuth\Controllers\AuthController@login']);
    Route::group(['middleware' => 'jwt.auth'], function () {
        Route::post('refresh', ['uses' => '\Vdomah\JWTAuth\Controllers\AuthController@refresh']);
        Route::post('invalidate', ['uses' => '\Vdomah\JWTAuth\Controllers\AuthController@invalidate']);
        Route::get('user', ['uses' => '\Vdomah\JWTAuth\Controllers\AuthController@user']);
    });
});

