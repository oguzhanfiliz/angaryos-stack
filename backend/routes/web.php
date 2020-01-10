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

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
Route::any('/', '\App\Http\Controllers\GeneralController@serviceOk');

//Route::get('/auth/login', ['as'=>'login','uses'=>'Auth\AuthController@getLogin']);