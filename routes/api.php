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
    Route::post('user/edit', 'Api\UserController@edit');
    Route::post('user/changePhoto', 'Api\UserController@changePhoto')->name('user/changePhoto');
    Route::post('user/sendFeedback', 'Api\UserController@sendFeedback');

    Route::post('suslik/getCategoryList', 'Api\SuslikApiController@getCategoryList');
    Route::post('suslik/getSusliksByCategory', 'Api\SuslikApiController@getSusliksByCategory');
    Route::post('suslik/getSuslikByID', 'Api\SuslikApiController@getSuslikByID');
    Route::post('suslik/rateSuslik', 'Api\SuslikApiController@rateSuslik');
    Route::post('suslik/getSuslikRatingHistory', 'Api\SuslikApiController@getSuslikRatingHistory');
    
    Route::post('suslik/getFavsList', 'Api\SuslikApiController@getFavsList');
    Route::post('suslik/addToFav', 'Api\SuslikApiController@addToFav');
    Route::post('suslik/removeFromFav', 'Api\SuslikApiController@removeFromFav');

    Route::get('statistic/{uuid}', 'Api\SuslikApiController@showStatistic')->name('showStatistic');
    Route::get('showPollResults/{uuid}', 'Api\PollApiController@showPollResults')->name('showPollResults');
});

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

Route::post('poll/getPollCategoryList', 'Api\PollApiController@getPollCategoryList');
Route::post('poll/getPollList', 'Api\PollApiController@getPollList');
Route::post('poll/getPollQuestionList', 'Api\PollApiController@getPollQuestionList');
Route::post('poll/passPoll', 'Api\PollApiController@passPoll');
Route::post('statistic/{uuid}', 'Api\SuslikApiController@statistic');

Route::post('suslik/search', 'Api\SuslikApiController@search');

Route::post('user/resetPassword', 'Api\ResetPasswords@ResetPasswordAPI')->name('user/reset');

Route::get('user/changePhoto/form', function () {
    return view('form');
});

Route::get('getRegions', 'Api\AuthController@getRegions')->name('getRegions');

Route::fallback(function () {
    $code = 404;
    $response = [
        'success' => false,
        'message' => 'Page not found',
    ];

    return response()->json($response, $code);
});

Route::any('{url?}/{sub_url?}', function(){
    $code = 404;
    $response = [
        'success' => false,
        'message' => 'Page not found',
    ];

    return response()->json($response, $code);
});