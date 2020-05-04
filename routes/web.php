<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
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
Auth::routes(['verify' => true]);
Route::get('social/login/redirect/{provider}', [
    'uses' => 'Auth\LoginController@redirectToProvider',
    'as' => 'social.login',
]);
Route::get('social/login/{provider}', 'Auth\LoginController@handleProviderCallback');
Route::get('/', 'HomeController@index')->name('home');

Route::namespace ('Admin')->prefix('admin')->name('admin.')->middleware('can:manage-users')->group(function () {
    Route::resource('/users', 'UsersController', ['except' => ['show', 'create', 'store']]);
});
