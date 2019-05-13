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

Route::get('/', 'StaticPagesController@home')->name('home');

Route::get('/help', 'StaticPagesController@help')->name('help');

Route::get('/about', 'StaticPagesController@about')->name('about');

#用户注册页面
Route::get('/signup', 'UsersController@create')->name('signup');

Route::get('/signup/confirm/{token}', 'UsersController@confirmEmail')->name('confirm_email');



#用户路由资源
Route::resource('users', 'UsersController');

#session 相关
Route::get('login','SessionsController@create')->name('login');
Route::post('login','SessionsController@store')->name('login');
Route::delete('logout','SessionsController@destroy')->name('logout');

#密码重置
Route::get('password/reset','Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email','Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');

Route::get('password/reset/{token}','Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset','Auth\ResetPasswordController@reset')->name('password.update');


#微博
Route::resource('statuses', 'StatusesController',['only'=>['store','destroy']]);

#用户关注列表
Route::get('/users/{user}/followings','UsersController@followings')->name('users.followings');
#用户粉丝列表
Route::get('/users/{user}/followers','UsersController@follwers')->name('users.followers');