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

Route::get('/home','HomeController@index')->name('home');

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
Route::get('catalogos/tipo-persona','TipoPersonaController@index')->name('catalogos.tipo_persona');
Route::get('catalogos/objeto-solicitud','ObjetoSolicitudController@index')->name('catalogos.objeto_solicitud');
Route::get('catalogos/ocupacion','OcupacionController@index')->name('catalogos.ocupacion');
Route::get('catalogos/giro-comercial','GiroComercialController@index')->name('catalogos.giro_comercial');
Route::get('catalogos/lengua-indigena','LenguaIndigenaController@index')->name('catalogos.lengua_indigena');
Route::get('catalogos/tipo-discapacidad','TipoDiscapacidadController@index')->name('catalogos.tipo_discapacidad');
Route::get('catalogos/tipo-vialidad','TipoVialidadController@index')->name('catalogos.tipo_vialidad');
Route::get('catalogos/tipo-asentamiento','TipoAsentamientoController@index')->name('catalogos.tipo_asentamiento');
Route::get('catalogos/tipo-parte','TipoParteController@index')->name('catalogos.tipo_partes');
Route::get('catalogos/estados','EstadoController@index')->name('catalogos.estado');
Route::get('catalogos/nacionalidades','NacionalidadController@index')->name('catalogos.nacionalidad');
Route::get('catalogos/centros','CentroController@index')->name('catalogos.centro');



Route::resource('giros','GiroComercialController');

Route::resource('permisos','PermissionController');
Route::resource('roles','RoleController');
Route::get('roles/permisos/{id}','RoleController@GetPermisosRol');
Route::put('roles/permisos/{id}','RoleController@DeletePermisosRol');
Route::post('usuario/roles/','UserController@AddRol');
Route::get('usuario/roles/{id}','UserController@GetRoles');
Route::post('usuario/roles/delete','UserController@EliminarRol');

Route::get('getMenu/{rol_id}','PermissionController@getMenu');
Route::get('cambiarRol/{rol_id}','PermissionController@cambiarRol');
Route::get('impersonate/{user_id}','UserController@impersonate')->name('impersonate');
Route::get('impersonate_leave','UserController@impersonate_leave')->name('impersonate_leave');

Auth::routes(['register' => false]);

Route::group(['middleware' => ['role:Super Usuario']], function () {
    //
});
Route::group(['middleware' => ['role:Supervisor regional']], function () {
    //
});
Route::group(['middleware' => ['role:Conciliador']], function () {
    //
});
Route::group(['middleware' => ['role:Secretario']], function () {
    //
});
Route::group(['middleware' => ['role:Parte']], function () {
    //
});
Route::group(['middleware' => ['role:público en general']], function () {
    //
});

