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

Route::get('header/{id}', 'HeaderFooterTemplatesController@getHeader');
Route::get('footer/{id}', 'HeaderFooterTemplatesController@getFooter');

Route::get('/asesoria/{accion}', 'AsesoriaController@index');
Route::get('/solicitudes/create-public','SolicitudController@create');
Route::post('/solicitudes/store-public','SolicitudController@store');
Route::Get('solicitudes/documentos/{solicitud_id}/acuse','SolicitudController@getAcuseSolicitud');
Route::post('/solicitud/identificacion','SolicitudController@identificacion');
Route::get('/aviso-privacidad','DocumentoController@aviso_privacidad');
Route::get('/version','HomeController@version');
Route::get('centro/getAtiendeVirtual/{estado_id}','CentroController@getAtiendeVirtual');
Route::Get('canal/{canal}','SolicitudController@canal');
Route::middleware(['auth'])->group(function () {


    Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

    Route::resource('users','UserController');
    Route::get('/home','HomeController@index')->name('home');


    Route::resource('users','UserController');
    Route::resource('salas','SalaController');
    Route::post('salas/disponibilidad','SalaController@disponibilidad');
    Route::Post('salas/disponibilidades','SalaController@getDisponibilidades');
    Route::Post('salas/incidencias','SalaController@incidencia');
    Route::resource('centros','CentroController');
    Route::post('centros/disponibilidad','CentroController@disponibilidad');
    Route::Post('centros/disponibilidades','CentroController@getDisponibilidades');
    Route::Post('centros/incidencias','CentroController@incidencia');
    Route::Get('/calendariocolectivas','CentroController@CalendarioColectivas');
    Route::get('/calendariocentro', 'CentroController@getAudienciasCalendario');
    Route::get('/info_audiencia/{audiencia_id}', 'AudienciaController@getFullAudiencia');
    Route::get('/calendariocentro', 'CentroController@getAudienciasCalendario');
    Route::resource('conciliadores','ConciliadorController');
    Route::post('conciliadores/disponibilidad','ConciliadorController@disponibilidad');
    Route::Post('conciliadores/disponibilidades','ConciliadorController@getDisponibilidades');
    Route::Post('conciliadores/incidencias','ConciliadorController@incidencia');
    Route::Post('conciliadores/roles','ConciliadorController@roles');
    Route::Post('conciliadores/ConciliadoresDisponibles','ConciliadorController@conciliadoresDisponibles');
    Route::Get('conciliadores/ConciliadorAudiencias','ConciliadorController@conciliadorAudiencias');
    Route::resource('disponibilidad','DisponibilidadController');
    Route::resource('incidencia','IncidenciaController');
    Route::resource('solicitudes','SolicitudController');
    Route::get('solicitudes/consulta/{id}','SolicitudController@consulta')->name('solicitudes.consulta');
    Route::post('solicitudes/folio','SolicitudController@getSolicitudByFolio');
    Route::POST('solicitud/ratificar','SolicitudController@Ratificar');
    Route::POST('solicitud/ratificarIncompetencia','SolicitudController@ratificarIncompetencia');
    Route::POST('solicitud/excepcion','SolicitudController@ExcepcionConciliacion');
    Route::Get('solicitud/correos/{solicitud_id}','SolicitudController@validarCorreos');
    Route::POST('solicitud/correos','SolicitudController@cargarCorreos');
    Route::Get('solicitudes/documentos/{solicitud_id}','SolicitudController@getDocumentosSolicitud');
    Route::POST('guardarUrlVirtual','SolicitudController@guardarUrlVirtual');
    Route::resource('expedientes','ExpedienteController');
    Route::resource('audiencias','AudienciaController');
    Route::resource('audiencia','AudienciaController');
    Route::post('audiencia/folio','AudienciaController@getAudienciaByFolio');
    Route::Post('audiencia/ConciliadoresDisponibles','AudienciaController@ConciliadoresDisponibles');
    Route::Post('audiencia/ConciliadoresDisponiblesCentral','AudienciaController@ConciliadoresDisponiblesCentral');
    Route::Post('audiencia/SalasDisponiblesCentral','AudienciaController@SalasDisponiblesCentral');
    Route::Post('audiencia/SalasDisponibles','AudienciaController@SalasDisponibles');
    Route::Post('audiencia/calendarizar','AudienciaController@calendarizar');
    Route::Post('audiencia/calendarizarCentral','AudienciaController@calendarizarCentral');
    Route::Post('audiencia/reagendar','AudienciaController@cambiarFecha');
    Route::Post('audiencia/getCalendario','AudienciaController@getCalendario');
    Route::Post('audiencia/getCalendarioCentral','AudienciaController@getCalendarioCentral');
    Route::Post('audiencia/getCalendarioColectivas','AudienciaController@getCalendarioColectivas');
    Route::Post('audiencia/getAgenda','AudienciaController@getAgenda');
    Route::Post('audiencia/resolucion','AudienciaController@Resolucion');
    Route::Post('audiencia/nuevaAudiencia','AudienciaController@NuevaAudiencia');
    Route::Post('audiencia/registrarPagoDiferido','AudienciaController@registrarPagoDiferido');
    Route::Post('audiencia/generarConstanciaNoPago','AudienciaController@generarConstanciaNoPago');

    Route::get('debug','HeaderFooterTemplatesController@debug');
    Route::post('preview','HeaderFooterTemplatesController@preview');

    Route::Get('audiencia/documentos/{audiencia_id}','AudienciaController@getDocumentosAudiencia');
    Route::Get('audiencia/fisicas/{id}','AudienciaController@GetPartesFisicas');
    Route::Get('audiencia/validar_partes/{id}','AudienciaController@validarPartes');
    Route::Post('audiencia/comparecientes','AudienciaController@guardarComparecientes');
    Route::Get('audiencia/comparecientes/{audiencia_id}','AudienciaController@getComparecientes');
    Route::Get('audiencia/negarCancelacion/{audiencia_id}','AudienciaController@negarCancelacion');
    Route::Post('audiencias/cambiar_fecha','AudienciaController@cambiarFecha');
    Route::Post('audiencias/solicitar_nueva','AudienciaController@SolicitarNueva');
    Route::Post('audiencias/nuevaAudienciaNotificacion','AudienciaController@NuevaAudienciaNotificacion');

    Route::get('guiaAudiencia/{id}','AudienciaController@guiaAudiencia')->name('guiaAudiencia');
    Route::get('resolucionColectiva/{id}','AudienciaController@resolucionColectiva')->name('resolucionColectiva');
    Route::Post('audiencia/guardarAudienciaColectiva','AudienciaController@guardarAudienciaColectiva');
    Route::get('calendario','AudienciaController@calendario');
    Route::get('getAudienciaConciliador','AudienciaController@GetAudienciaConciliador');
    Route::get('agendaConciliador','AudienciaController@AgendaConciliador');
    Route::post('audiencia/cambiar_conciliador','AudienciaController@CambiarConciliador');
    Route::resource('parte','ParteController');
    Route::Get('partes/representante/{id}','ParteController@GetRepresentanteLegal');
    Route::Get('partes/datoLaboral/{id}','ParteController@GetDatoLaboral');
    Route::Post('partes/datoLaboral','ParteController@GuardarDatoLaboral');
    Route::Post('partes/representante','ParteController@GuardarRepresentanteLegal');
    Route::Post('partes/getParteCurp','ParteController@getParteCurp');
    Route::Post('partes/representante/contacto','ParteController@AgregarContactoRepresentante');
    Route::Post('partes/representante/contacto/eliminar','ParteController@EliminarContactoRepresentante');
    Route::GET('partes/getComboDocumentos/{solicitud_id}','ParteController@getPartesComboDocumentos');
    Route::Get('partes/getParteSolicitud/{parte_id}','ParteController@getParteSolicitud');
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
    Route::resource('bitacora','BitacoraController');
    Route::resource('oficio-documentos','OficiosDocumentosController');
    Route::Post('oficio-documento/imprimirPDF','OficiosDocumentosController@imprimirPDF')->name('oficio-documento.imprimirPDF');
    Route::get('plantilla-documento/imprimirPDF','PlantillasDocumentosController@imprimirPDF');
    Route::get('plantilla-documento/cargarDefault','PlantillasDocumentosController@cargarDefault');
    Route::get('plantilla-documento/{id}/imprimirPDF','PlantillasDocumentosController@imprimirPDF')->name('plantilla-documento/imprimirPDF');
    Route::Post('plantilla-documento/previewDocumento','PlantillasDocumentosController@previewDocumento');

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
    Route::post('giros_comerciales/filtrarGirosComerciales','GiroComercialController@filtrarGirosComerciales');
    Route::Post('giros_comerciales/cambiar_padre','GiroComercialController@CambiarPadre');
    Route::Post('giros_comerciales/cambiar_ambito','GiroComercialController@CambiarAmbito');
    Route::resource('documento','DocumentoController');
    Route::post('guardar_documento','DocumentoController@storeDocument');
    Route::get('regenerar_documento','DocumentoController@generar_documento');
    Route::post('store_regenerar_documento','DocumentoController@storeRegenerarDocumento');

    Route::get('incidencias_solicitudes','SolicitudController@incidencias_solicitudes');
    Route::get('deshacer_solicitudes','SolicitudController@deshacer_solicitudes');
    Route::post('rollback_proceso','SolicitudController@rollback_proceso');
    Route::post('guardar_incidencia','SolicitudController@guardar_incidencia');
    Route::post('borrar_incidencia','SolicitudController@borrar_incidencia');
    Route::resource('permisos','PermissionController');
    Route::resource('roles','RoleController');
    Route::get('roles/permisos/{id}','RoleController@GetPermisosRol');
    Route::put('roles/permisos/{id}','RoleController@DeletePermisosRol');
    Route::post('usuario/roles','UserController@AddRol');
    Route::get('usuario/roles/{id}','UserController@GetRoles');
    Route::post('usuario/roles/delete','UserController@EliminarRol');
    Route::post('usuario/centros','UserController@AgregarCentro');
    Route::get('usuario/centros/{user_id}','UserController@ObtenerCentros');
    Route::post('usuario/centros/delete','UserController@EliminarCentro');

    Route::get('getMenu/{rol_id}','PermissionController@getMenu');
    Route::get('cambiarRol/{rol_id}','PermissionController@cambiarRol');
    Route::get('cambiarCentro/{centro_id}','PermissionController@cambiarCentro');
    Route::get('impersonate/{user_id}','UserController@impersonate')->name('impersonate');
    Route::get('impersonate_leave','UserController@impersonate_leave')->name('impersonate_leave');


    // Catalogos
    Route::resource('ambitos','AmbitoController');
    Route::resource('clasificacion_archivos','ClasificacionArchivoController');
    Route::resource('conceptos_pagos','ConceptoPagoResolucionesController');
    Route::resource('estados','EstadoController');
    Route::resource('generos','GeneroController');
    Route::resource('lenguas_indigenas','LenguaIndigenaController');
    Route::resource('motivos_archivado','MotivoArchivadoController');
    Route::resource('periodicidades','PeriodicidadController');
    Route::resource('tipos_contactos','TipoContactoController');
    Route::resource('tipos_contadores','TipoContadorController');
    Route::resource('tipos_discapacidades','TipoDiscapacidadController');
    Route::get('reenviar_notificaciones','SolicitudController@ReenviarNotificacion');

    // Visor de los logs via web
    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

    Route::get('get_personas','PersonaController@GetPersonaCentro');

    /**
     * Notificaciones
     */
    Route::get('notificaciones','CentroController@notificaciones');
    Route::post('notificaciones/enviar','CentroController@EnviarNotificacion');
    Route::get('obtenerHistorial/{audiencia_parte_id}','CentroController@obtenerHistorial');

    /**
     * descarga de calendario
     */
    Route::post('descargaCalendario','CentroController@descargarCalendario')->name("descargaCalendario");

    /*
     * Centro
     */
    Route::post('centros/contactos','CentroController@ObtenerContactos');
    Route::post('centros/agregar_contacto','CentroController@AgregarContacto');
    Route::post('centros/contactos/eliminar','CentroController@EliminarContacto');

    /*
     * Suspensión de virtuales
     */
    Route::get('audiencia/suspension/{audiencia_id}','AudienciaController@SuspensionVirtual');


});

Route::post('externo/giros_comerciales/filtrarGirosComerciales','GiroComercialController@filtrarGirosComerciales');


/**
 * Ruta para envío de certificados digitales desde el buzón
 */
Route::post('documentos/firmado','DocumentoController@firmado');
Route::get('documentos/firmado/obtener/{firma_documento_id}','DocumentoController@ObtenerFirmado');

Route::get('solicitud_buzon','BuzonController@SolicitudBuzon')->name('solicitud_buzon');

Route::Get('partes/datoLaboral/{id}','ParteController@GetDatoLaboral');
Route::Get('partes/representante/{id}','ParteController@GetRepresentanteLegal');
Route::post('solicitar_acceso','BuzonController@SolicitarAcceso')->name('solicitar_acceso2');
Route::get('buzon','BuzonController@BuzonElectronico')->name('buzon');
Route::get('validar_token/{token}/{correo}','BuzonController@validar_token');
Route::resource('etapa_resolucion_audiencia','EtapaResolucionAudienciaController');
Route::get('acceso_buzon','BuzonController@AccesoBuzon')->name('acceso_buzon');

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

