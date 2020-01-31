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

Route::get('/home','HomeController@index');

Route::resource('users','UserController');
Route::resource('salas','SalaController');
Route::resource('centros','CentroController');
Route::resource('solicitudes','SolicitudController');
Route::resource('expedientes','ExpedienteController');
Route::resource('audiencias','AudienciaController');
Route::resource('roles-conciliadores','RolConciliadorController');
Route::resource('motivos-solicitud','MotivoSolicitudController');
Route::resource('jornadas','JornadaController');

Auth::routes(['register' => false]);
