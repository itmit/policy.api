<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/register', function () {
//     return view('auth/register');
// });

// Route::get('/login', function () {
//     return view('auth/login');
// });

Route::group(['as' => 'auth.', 'middleware' => 'auth'], function () {
    Route::get('/', ['as' => 'home', 'uses' => 'HomeController@index']);

    Route::resource('polls', 'Web\PollWebController');
    Route::resource('susliks', 'Web\SuslikWebController');
    Route::delete('susliks/delete', 'Web\SuslikWebController@destroy');
    Route::get('createCategory', 'Web\SuslikWebController@createCategory')->name('createCategory');
    Route::post('storeCategory', 'Web\SuslikWebController@storeCategory')->name('storeCategory');
    Route::post('uploadSusliks', 'Web\SuslikWebController@uploadSusliks')->name('uploadSusliks');

    Route::get('createPollCategory', 'Web\PollWebController@createCategory')->name('createPollCategory');
    Route::post('storePollCategory', 'Web\PollWebController@storeCategory')->name('storePollCategory');
    Route::delete('pollCategory/delete', 'Web\PollWebController@destroyCategory'); // удалить категорию опросов
    Route::delete('pollCategory/showDeleted', 'Web\PollWebController@showDeleted'); // показать удаленные категории опросов
    // Route::delete('poll/showDeleted', 'Web\PollWebController@showDeleted'); // показать удаленные категории опросов
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
