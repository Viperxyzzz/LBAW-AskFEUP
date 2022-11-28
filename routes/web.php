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
Route::get('settings/{id}', 'ProfileController@settings');

// Users
Route::get('users', 'UserController@home');
Route::get('users/{id}', 'ProfileController@home')->name('users');
Route::get('api/users/', 'UserController@search');

// Questions
Route::get('question/create', 'QuestionController@create_view')->name('question_create');
Route::get('question/{id}', 'QuestionController@home')->name('question');
Route::put('api/answer/{id}', 'QuestionController@answer');
Route::get('question/{id}/edit', 'QuestionController@edit_view')->name('edit_question');
Route::match(['put', 'patch'], 'api/question/update/{id}','QuestionController@update')->name('update_question');
//Route::post('api/question/update', 'QuestionController@update')->name('update_question');

// Search
Route::get('browse', 'SearchController@home')->name('browse');
Route::get('api/browse', 'SearchController@browse');

// Tags
Route::get('tags', 'TagController@index');

// API
Route::post('api/question', 'QuestionController@create')->name('question_create_api');
Route::post('api/settings/{id}', 'ProfileController@updateUser')->name('update_user_api');
Route::delete('api/question/{id}', 'QuestionController@delete')->name('question_delete_api');

// Answers
Route::put('api/answer/{id}', 'AnswerController@create');
Route::delete('api/answer/delete/{id}', 'AnswerController@delete')->name('answer_delete_api');
Route::get('api/answer/edit/{id}', 'AnswerController@edit_view')->name('edit_answer_form');
Route::post('api/answer/update/{id}','AnswerController@update')->name('update_answer');

// Authentication
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');
