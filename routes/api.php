<?php

use Illuminate\Http\Request;

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


Route::resource('user','UserController')->middleware('auth');
Route::resource('solicitud','SolicitudController');
Route::resource('resoluciones','ResolucionController');
Route::resource('salas','SalaController');
Route::resource('persona','PersonaController');
Route::resource('conciliador','ConciliadorController');
Route::resource('parte','ParteController');
Route::resource('abogado','AbogadoController');

Route::post('login', 'ApiAuthController@login');
