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
// Home
Route::get('/', 'Auth\LoginController@home');

// User specific
Route::get('feed', 'FeedController@home');
Route::get('profile', 'ProfileController@home');
Route::get('settings', 'ProfileSettingsController@show');

// Questions
Route::get('question/{id}', 'QuestionController@home')->name('question');
Route::put('api/answer/{id}', 'QuestionController@answer');

// Search
Route::get('browse', 'SearchController@home');
Route::get('api/browse', 'SearchController@browse');

// Authentication
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');
