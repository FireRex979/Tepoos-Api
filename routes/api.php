<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->group(function () {
    Route::get('logout', 'AuthController@logout');
    Route::get('user', 'AuthController@user');
    Route::post('fcm-token', 'FCMController@store');
    Route::get('get-notif', 'NotificationController@getNotif');
    Route::get('read-notif/{id}', 'NotificationController@readNotif');
    Route::post('user/update/foto-profile', 'AuthController@uploadFotoProfile');
    Route::get('postingan/user', 'PostinganController@postinganUser');
});

Route::post('update/profile/{id}', 'AuthController@updateProfile');
Route::get('user/get-foto-profile/{id}', 'AuthController@getFotoProfile');

Route::get('postingan', 'PostinganController@index');
Route::post('postingan/store', 'PostinganController@store');
Route::get('postingan/user/{id}', 'PostinganController@postinganUser');
Route::get('postingan/show', 'PostinganController@show');
Route::post('postingan/update', 'PostinganController@update');
Route::get('postingan/destroy/{id}', 'PostinganController@destroy');
Route::get('postingan/get-foto/{id}', 'PostinganController@getFotoPostingan');

Route::post('komentar/store', 'KomentarController@store');

Route::middleware('guest')->group(function(){
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
});
