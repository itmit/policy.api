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
    Route::get('susliks/createCategory', 'Web\SuslikWebController@createCategory')->name('createCategory');; 
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
