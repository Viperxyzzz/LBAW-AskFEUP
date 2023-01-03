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
Route::get('/about', function () {
    return view('pages.about');})->name('about');
Route::get('/sitemap', function () {
    return view('pages.sitemap');})->name('sitemap');

// User specific
Route::get('feed', 'FeedController@home')->name('feed');
Route::get('settings/{id}', 'ProfileController@settings');

// Users
Route::get('users', 'UserController@home')->name('users');
Route::get('users/{id}', 'ProfileController@home')->name('users');
Route::get('api/users/', 'UserController@search');
Route::put('api/user/delete/{id}', 'ProfileController@delete')->name('user_delete');
Route::post('api/disable/create/{id}', 'DisableController@store');

// Questions
Route::get('question/create', 'QuestionController@create_view')->name('question_create');
Route::get('question/{id}', 'QuestionController@home')->name('question');
Route::get('question/{id}/edit', 'QuestionController@edit_view')->name('edit_question');
Route::put('api/question/update/{id}','QuestionController@update')->name('update_question');
Route::post('api/question/{id}/vote', 'QuestionController@vote')->name('vote_question');
Route::post('api/question/follow/{id}', 'QuestionController@follow');
Route::delete('api/question/unFollow/{id}', 'QuestionController@unFollow');

// Search
Route::get('browse', 'SearchController@home')->name('browse');
Route::get('api/browse', 'SearchController@browse');

// Tags
Route::get('tags', 'TagController@index');
Route::get('api/tags/', 'TagController@search');
Route::post('api/tag/follow/{id}', 'TagController@follow');
Route::delete('api/tag/unFollow/{id}', 'TagController@unFollow');
Route::post('api/tag/create', 'TagController@store')->name('tag_create_api');
Route::delete('api/tag/delete/{id}', 'TagController@destroy')->name('tag_delete_api');
Route::put('api/tag/edit/{id}', 'TagController@update')->name('tag_update_api');

// Admin / Reports
Route::get('dashboard', 'ModController@index')->name('dashboard');
Route::delete('api/report/delete/{id}', 'ModController@delete_report');
Route::post('api/blocks/add/{id}', 'BlockController@store');
Route::delete('api/blocks/delete/{id}', 'BlockController@destroy');
Route::post('api/report/create', 'ModController@create_report');


// API
Route::post('api/question', 'QuestionController@create')->name('question_create_api');
Route::put('api/settings/{id}','ProfileController@updateUser')->name('update_user_api');
Route::delete('api/question/{id}', 'QuestionController@delete')->name('question_delete_api');

// Answers
Route::post('api/answer/{id}', 'AnswerController@create');
Route::delete('api/answer/delete/{id}', 'AnswerController@delete')->name('answer_delete_api');
Route::get('api/answer/edit/{id}', 'AnswerController@edit_view')->name('edit_answer_form');
Route::put('api/answer/update/{id}','AnswerController@update')->name('update_answer');
Route::post('api/answer/valid/{id}', 'AnswerController@make_valid')->name('valid_answer');
Route::post('api/answer/invalid/{id}', 'AnswerController@make_invalid')->name('invalid_answer');
Route::post('api/answer/{id}/vote', 'AnswerController@vote')->name('vote_answer');

// Notifications
Route::post('api/notification/update/{id}', 'NotificationController@update')->name('update_notification');
Route::get('/notification/{id}', 'NotificationController@redirectNotification')->name('redirect_notification');

// Authentication
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');
Route::get('forgot-password', 'Auth\ResetPasswordController@show_forgot')->name('password.request');
Route::post('forgot-password', 'Auth\ResetPasswordController@send')->name('password.email');
Route::get('reset-password/{token}', 'Auth\ResetPasswordController@show_reset')->name('password.reset');
Route::post('reset-password', 'Auth\ResetPasswordController@reset')->name('password.update');

// OAuth authentication
Route::get('/auth/redirect', 'Auth\OAuthController@redirect');
Route::get('/auth/callback', 'Auth\OAuthController@callback');

// Comments
Route::post('api/comment/{id}', 'CommentController@create')->name('create_comment');
Route::delete('api/comment/delete/{id}', 'CommentController@delete')->name('comment_delete_api');
Route::get('api/comment/edit/{id}', 'CommentController@edit_view')->name('edit_comment_form');
Route::put('api/comment/update/{id}', 'CommentController@update')->name('update_comment');
Route::post('api/comment/{id}/vote', 'CommentController@vote')->name('vote_comment');

// Badges 
Route::post('api/badge/support', 'BadgeController@support');
Route::post('api/badge/unsupport', 'BadgeController@unsupport');
