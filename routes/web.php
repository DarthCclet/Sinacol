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
Route::resource('conciliadores','ConciliadorController');
Route::resource('solicitudes','SolicitudController');
Route::resource('expedientes','ExpedienteController');
Route::resource('audiencias','AudienciaController');
Route::get('calendario','AudienciaController@calendario');
Route::resource('roles-atencion','RolAtencionController');
Route::resource('objeto-solicitud','ObjetoSolicitudController');
Route::resource('estatus-solicitud','EstatusSolicitudController');
Route::resource('resolucion-audiencia','ResolucionController');
Route::resource('grupo-prioritario','GrupoPrioritarioController');
Route::resource('jornadas','JornadaController');
Route::resource('ocupaciones','OcupacionController');
Route::resource('contadores','ContadorController');
Route::resource('plantilla-documentos','PlantillasDocumentosController');
Route::get('plantilla-documento/imprimirPDF','PlantillasDocumentosController@imprimirPDF');


Auth::routes(['register' => false]);
