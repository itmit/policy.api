<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', 'Api\AuthController@login')->name('login');
Route::post('register', 'Api\AuthController@register')->name('register');

Route::group(['middleware' => 'auth:api'], function() {
    Route::post('details', 'Api\AuthController@details');

    Route::post('user/index', 'Api\UserController@index');
    Route::post('user/getUserByUid', 'Api\UserController@getUserByUid');
});