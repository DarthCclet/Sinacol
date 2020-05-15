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
Route::resource('tipo-documento','TipoDocumentoController');
Route::get('plantilla-documento/imprimirPDF','PlantillasDocumentosController@imprimirPDF');
Route::get('plantilla-documento/cargarDefault','PlantillasDocumentosController@cargarDefault');
Route::get('plantilla-documento/{id}/imprimirPDF','PlantillasDocumentosController@imprimirPDF')->name('plantilla-documento/imprimirPDF');

// Catalogos csv para el CJF
Route::get('catalogos/tipo-personas','TipoPersonaController@index')->name('catalogos.tipo_persona');
Route::get('catalogos/objeto-solicitud','ObjetoSolicitudController@index')->name('catalogos.objeto_solicitud');
Route::get('catalogos/ocupacion','OcupacionController@index')->name('catalogos.ocupacion');
Route::get('catalogos/lengua-indigena','LenguaIndigenaController@index')->name('catalogos.lengua_indigena');
Route::get('catalogos/tipo-discapacidad','TipoDiscapacidadController@index')->name('catalogos.tipo_discapacidad');
Route::get('catalogos/tipo-vialidad','TipoVialidadController@index')->name('catalogos.tipo_vialidad');
Route::get('catalogos/tipo-asentamiento','TipoAsentamientoController@index')->name('catalogos.tipo_asentamiento');
Route::get('catalogos/tipo-partes','TipoParteController@index')->name('catalogos.tipo_controller');
Route::get('catalogos/tipo-partes','TipoParteController@index')->name('catalogos.tipo_controller');



Route::resource('giros','GiroComercialController');

Auth::routes(['register' => false]);

