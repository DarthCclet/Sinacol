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
Route::resource('expediente','ExpedienteController');
Route::resource('datoLaboral','DatoLaboralController');
Route::resource('solicitud','SolicitudController');
Route::resource('resoluciones','ResolucionController');
Route::resource('sala','SalaController');
Route::post('salas/disponibilidad','SalaController@disponibilidad');
Route::Post('salas/disponibilidades','SalaController@getDisponibilidades');
Route::Post('salas/incidencias','SalaController@incidencia');
Route::resource('persona','PersonaController');
Route::resource('conciliador','ConciliadorController');
Route::post('conciliador/disponibilidad','ConciliadorController@disponibilidad');
Route::Post('conciliador/disponibilidades','ConciliadorController@getDisponibilidades');
Route::Post('conciliador/incidencias','ConciliadorController@incidencia');
Route::Post('conciliador/roles','ConciliadorController@roles');
Route::Post('conciliador/ConciliadoresDisponibles','ConciliadorController@conciliadoresDisponibles');
Route::resource('parte','ParteController');
Route::resource('documento','DocumentoController');
Route::resource('disponibilidad','DisponibilidadController');
Route::resource('incidencia','IncidenciaController');
Route::resource('audiencia','AudienciaController');
Route::Post('audiencia/conciliadoresDisponibles','AudienciaController@incidencia');
Route::Post('audiencia/calendarizar','AudienciaController@calendarizar');
Route::resource('compareciente','ComparecienteController');
Route::resource('centro','CentroController');
Route::post('centros/disponibilidad','CentroController@disponibilidad');
Route::Post('centros/disponibilidades','CentroController@getDisponibilidades');
Route::Post('centros/incidencias','CentroController@incidencia');
Route::resource('objeto-solicitud','ObjetoSolicitudController');
Route::resource('rol-atencion','RolAtencionController');
Route::Post('ocupacion/multiples','OcupacionController@editMultiple');

//Route::resource('rol-conciliador','RolAtencionController');

Route::post('login', 'ApiAuthController@login');
