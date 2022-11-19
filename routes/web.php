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
Route::get('settings', 'ProfileController@settings');
Route::get('my_questions', 'ProfileController@myQuestions');
Route::get('my_answers', 'ProfileController@myAnswers');

// Users
Route::get('users', 'UserController@home');
Route::get('api/users/', 'UserController@search');

// Questions
Route::get('question/create', 'QuestionController@create_view')->name('question_create');
Route::get('question/{id}', 'QuestionController@home')->name('question');
Route::put('api/answer/{id}', 'QuestionController@answer');

// Search
Route::get('browse', 'SearchController@home');
Route::get('api/browse', 'SearchController@browse');

// API
Route::post('api/question', 'QuestionController@create')->name('question_create_api');
Route::post('api/settings', 'ProfileController@updateUser')->name('update_user_api');
Route::delete('api/question/{id}', 'QuestionController@delete')->name('question_delete_api');

// Answers
Route::put('api/answer/{id}', 'AnswerController@create');
Route::delete('api/answer/delete/{id}', 'AnswerController@delete');

// Authentication
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');
