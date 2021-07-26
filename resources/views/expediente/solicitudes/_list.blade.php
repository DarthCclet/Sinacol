<style>
    .upper{
        text-transform: uppercase;
    }
    .needed:after {
        color:darkred;
        content: " (*)";
    }
    #ui-datepicker-div {z-index:9999 !important}
    .selectedButton{
        color: #fff !important;
        background-color: #9D2449 !important;
        border-color: #9D2449 !important;
    }
</style>

<input type="hidden" id="instancia" value="{{ env('INSTANCIA','federal')}}">
<input type="hidden" id="ruta" value="{!! route("solicitudes.edit",1) !!}">
<input type="hidden" id="rutaConsulta" value="{!! route("solicitudes.consulta",'-rutaConsulta') !!}">
<table id="tabla-detalle" style="width:100%;" class="table display">
    <thead>
        <tr><th>Id</th><th class="all">Estatus</th><th class="all">Folio</th><th >Anio</th><th class="all">Fecha de confirmaci&oacute;n</th><th>Fecha de recepción</th><th class="all">Fecha de conflicto</th><th class="all">Centro</th><th class="all">Partes</th><th class="all">Expediente</th><th class="all">Atendi&oacute;</th><th class="all">Días para expiraci&oacute;n</th><th class="all">Acción</th></tr>
    </thead>

</table>


{{-- Div carga documento --}}
<div id="docs" style="display: none;">

    <div class="text-right">
        <button class="btn btn-primary btn-sm m-l-5" id='btnAgregarArchivo'><i class="fa fa-plus"></i> Agregar documento</button>
    </div>
    <input type="hidden" id="centro_id" value="{{Auth::user()->centro_id}}">
    <input type="hidden" id="atiende_virtual" value="{{Auth::user()->centro->tipo_atencion_centro_id}}">
    <input type="hidden" id="oficina_central" value="{{(auth()->user()->hasRole('Orientador Central')) ? 'true':'false'}}">
    <div class="col-md-12">
        <div id="gallery" class="gallery row"></div>
        <!--<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">-->
    </div>

    <!-- The template to display files available for upload -->
    <script id="template-upload" type="text/x-tmpl">
        {% for (var i=0, file; file=o.files[i]; i++) { %}
        <tr class="template-upload fade show">
        <td>
        <span class="preview"></span>
        </td>
        <td>
        <div class="bg-light rounded p-10 mb-2">
        <dl class="m-b-0">
        <dt class="text-inverse">Nombre del documento:</dt>
        <dd class="name">{%=file.name%}</dd>
        <dt class="text-inverse m-t-10">Tama&ntilde;o del archivo:</dt>
        <dd class="size">Processing...</dd>
        </dl>
        </div>
        <strong class="error text-danger h-auto d-block text-left"></strong>
        </td>
        <td>
        <select class="form-control catSelectFile" name="tipo_documento_id[]">
        <option value="">Seleccione una opci&oacute;n</option>
        @if(isset($clasificacion_archivo))
        @foreach($clasificacion_archivo as $clasificacion)
        @if($clasificacion->tipo_archivo_id == 1)
        <option value="{{$clasificacion->id}}">{{$clasificacion->nombre}}</option>
        @endif
        @endforeach
        @endif
        </select>
        </td>
        <td>
        <select class="form-control catSelectFile parteClass" name="parte[]">
        <option value="">Seleccione una opci&oacute;n</option>
        {{-- @if(isset($solicitud))
        @foreach($solicitud->partes as $parte)
        @if(($parte->tipo_parte_id == 1 || $parte->tipo_parte_id == 3) && $parte->tipo_persona_id == 1 )
        <option value="{{$parte->id}}">{{$parte->nombre_comercial}}{{$parte->nombre}} {{$parte->primer_apellido}} {{$parte->segundo_apellido}}</option>
        @endif
        @endforeach
        @endif --}}
        </select>
        </td>
        <td>
        <dl>
        <dt class="text-inverse m-t-3">Progreso:</dt>
        <dd class="m-t-5">
        <div class="progress progress-sm progress-striped active rounded-corner"><div class="progress-bar progress-bar-primary" style="width:0%; min-width: 0px;">0%</div></div>
        </dd>
        </dl>
        </td>
        <td nowrap>
        {% if (!i && !o.options.autoUpload) { %}
        <button class="btn btn-primary start width-100 p-r-20 m-r-3" disabled>
        <i class="fa fa-upload fa-fw text-inverse"></i>
        <span>Guardar</span>
        </button>
        {% } %}
        </td>
        <td nowrap>
        {% if (!i) { %}
        <button class="btn btn-default cancel width-100 p-r-20">
        <i class="fa fa-trash fa-fw text-muted"></i>
        <span>Cancelar</span>
        </button>
        {% } %}
        </td>
        </tr>
        {% } %}
    </script>
    <!-- The template to display files available for download -->
    <script id="template-download" type="text/x-tmpl">
        {% for (var i=0, file; file=o.files[i]; i++) { %}
        <tr class="template-download fade show">
        <td width="1%">
        <span class="preview">
        {% if (file.thumbnailUrl) { %}
        <div class="bg-light text-center f-s-20" style="width: 80px; height: 80px; line-height: 80px; border-radius: 6px;">
        <img src="{%=file.thumbnailUrl%}" width="80px" height="80px" class="rounded">
        </div>
        {% } else { %}
        <div class="bg-light text-center f-s-20" style="width: 80px; height: 80px; line-height: 80px; border-radius: 6px;">
        <i class="fa fa-file-image fa-lg text-muted"></i>
        </div>
        {% } %}
        </span>
        </td>
        <td>
        <div class="bg-light p-10 mb-2">
        <dl class="m-b-0">
        <dt class="text-inverse">Nombre del archivo:</dt>
        <dd class="name">
        {% if (file.url) { %}
        <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
        {% } else { %}
        <span>{%=file.name%}</span>
        {% } %}
        </dd>
        <dt class="text-inverse m-t-10">Tama&ntilde;o del archivo:</dt>
        <dd class="size">{%=o.formatFileSize(file.size)%}</dd>
        </dl>
        {% if (file.error) { %}
        <div><span class="label label-danger">ERROR</span> {%=file.error%}</div>
        {% } %}
        {% if (file.success) { %}
        <div><span class="label label-success">Correcto</span> {%=file.success%}</div>
        {% } %}
        </div>
        </td>
        <td></td>
        <td></td>
        <td>
        {% if (file.deleteUrl) { %}
        <button class="btn btn-danger delete width-100 m-r-3 p-r-20" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
        <i class="fa fa-trash pull-left fa-fw text-inverse m-t-2"></i>
        <span>Delete</span>
        </button>
        <input type="checkbox" name="delete" value="1" class="toggle">
        {% } else { %}
        <button class="btn btn-default cancel width-100 m-r-3 p-r-20">
        <i class="fa fa-trash pull-left fa-fw text-muted m-t-2"></i>
        <span>Cancelar</span>
        </button>
        {% } %}
        </td>
        </tr>
        {% } %}
    </script>

</div>
{{-- Div carga documento --}}

{{-- Modal ratificacion --}}
<!--inicio modal para representante-->
<input type="hidden" id="solicitud_id" value="">

<div class="modal" id="modalRatificacion" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Confirmaci&oacute;n</h2>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div id="confirmacion_virtual" style="display: none;">
                    <h5>Proceso v&iacute;a remota</h5>
                    <hr class="red">
                    <input type="text" id="url_virtual" class="form-control" placeholder="Url virtual">
                    <p class="help-block needed">Url remota</p>
                    <button class="btn btn-primary" onclick="guardarUrlVirtual()">Guardar</button>
                </div>

                <div style="overflow:scroll"  id="div_confirmacion" class="col-md-12">
                    <div id="divNeedRepresentante" style="display: none;">
                        <h5>Representante legal (solo si hay representante)</h5>
                        <hr class="red">
                        <div class="alert alert-muted" style="display: none;" id="menorAlert" >
                            <strong>Menor de edad:</strong> Detectamos que al menos un solicitante no es mayor de edad, para poder continuar con la solicitud es necesario agregar al representante del menor y la identificación oficial de dicho representante.
                        </div>
                        <input type="hidden" id="parte_id" />
                        <input type="hidden" id="parte_representada_id">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <td>Solicitante</td>
                                    <td>Acci&oacute;n</td>
                                </tr>
                            </thead>
                            <tbody id="tbodyRepresentante">
                            <tbody>
                        </table>
                    </div>
                    <div style="margin: 2%;">
                        <a class="btn btn-primary btn-sm" style="float: right;" data-dismiss="modal" onclick="$('#modal-archivos').modal('show');" ><i class="fa fa-plus"></i> Agregar Documentos</a>
                    </div>
                    <h5>Identificaciones oficiales (solo si no hay representante legal)</h5>
                    <hr class="red">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <td>Solicitante</td>
                                <td>Documento</td>
                                <td>Revisar</td>
                            </tr>
                        </thead>
                        <tbody id="tbodyRatificacion">
                        <tbody>
                    </table>
                    <h5>Notificaciones <small>Notificación de los citados</small></h5>
                    <hr class="red">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <td>Tipo de notificación</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="col-md-12 row">
                                        <div class="col-md-6 ">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="aradioNotificacionA1" value="1" name="aradioNotificacion1" class="custom-control-input">
                                                <label class="custom-control-label" for="aradioNotificacionA1">A) El solicitante entrega citatorio al citado(s)</label>
                                            </div>
                                            <div class="custom-control custom-radio" id="divGeolocalizable">
                                                <input type="radio" id="aradioNotificacionB1" value="2" name="aradioNotificacion1" class="custom-control-input">
                                                <label class="custom-control-label" for="aradioNotificacionB1">B) Un notificador del centro entrega citatorio al citado(s)</label>
                                            </div>
                                            <div class="custom-control custom-radio" id="divNoGeolocalizable">
                                                <input type="radio" id="aradioNotificacionB2" value="3" name="aradioNotificacion1" class="custom-control-input">
                                                <label class="custom-control-label" for="aradioNotificacionB2">B) Agendar cita con el notificador para entrega de citatorio</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 " id="divFechaCita" style="display:none;">
                                            <div class="form-group">
                                                <label for="fecha_cita" class="control-label needed">Fecha de cita</label>
                                                <input type="text" id="fecha_cita" class="form-control dateBirth" placeholder="Fecha para atender cita">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <tbody>
                    </table>
                </div>
                <div class="col-md-4">
                    <div >
                        <span class="text-muted m-l-5 m-r-20" for='switch1'>Acepto notificacion por buzon</span>
                    </div>
                    <div >
                        <input type="checkbox" value="1" data-render="switchery" data-theme="default" id="aceptar_notif_buzon" name='aceptar_notif_buzon'/>
                    </div>
                </div>
                <div class="col-md-12" id="divCalendarioCentral">
                    <h5>Calendario de oficina central</h5>
                    <hr class="red">
                    @include('expediente.audiencias.calendarioRatificacion')
                </div>
                <div class="col-md-12">
                    <p id="notificaAmbito" style="color: darkred; font-weight:bold; display:none; text-align:right;"></p>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right row">
                    <a class="btn btn-white btn-sm" data-dismiss="modal" ><i class="fa fa-times"></i> Cancelar</a>
                    <div id="btnVirtual" >
                        <button class="btn btn-primary btn-sm m-l-5" id='btnGuardarRatificar'><i class="fa fa-save"></i> Confirmar</button>
                        <button class="btn btn-primary btn-sm m-l-5" id='btnRatificarIncompetencia'><i class="fa fa-save"></i> Confirmar con incompetencia</button>
                        <button class="btn btn-primary btn-sm m-l-5" id='btnGuardarConvenio'><i class="fa fa-save"></i> Confirmar con convenio</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="modalRatificacionJustificacion" aria-hidden="true" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Confirmaci&oacute;n con incompetencia</h2>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-muted">
                    - En el siguiente cuadro de texto deberá indicar la justificación de la incompetencia
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Justificación</label>
                        <textarea id="justificacion_incompetencia" class="form-control"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right row">
                    <a class="btn btn-white btn-sm" data-dismiss="modal" ><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5" id='btnRatificarIncompetenciaJustificacion'><i class="fa fa-save"></i> Confirmar la incompetencia</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="modal-representante" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Representante</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div style="overflow:scroll">
                    <h5>Datos del Representante</h5>
                    <input type="hidden" id="id_representante">
                    <div class="col-md-12 row">
                        <div class="col-md-6 ">
                            <div class="form-group">
                                <label for="curpRep" class="control-label needed">CURP</label>
                                <input type="text" id="curpRep" maxlength="18" onblur="getParteCurp(this.value)" class="form-control upper" placeholder="CURP del representante">
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="form-group">
                                <label for="nombreRep" class="control-label needed">Nombre</label>
                                <input type="text" id="nombreRep" class="form-control upper" placeholder="Nombre del representante">
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="form-group">
                                <label for="primer_apellidoRep" class="control-label needed">Primer apellido</label>
                                <input type="text" id="primer_apellidoRep" class="form-control upper" placeholder="Primer apellido del representante">
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="form-group">
                                <label for="segundo_apellidoRep" class="control-label">Segundo apellido</label>
                                <input type="text" id="segundo_apellidoRep" class="form-control upper" placeholder="Segundo apellido representante">
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="form-group">
                                <label for="fecha_nacimientoRep" class="control-label needed">Fecha de nacimiento</label>
                                <input type="text" id="fecha_nacimientoRep" class="form-control fecha" placeholder="Fecha de nacimiento del representante">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="genero_idRep" class="col-sm-6 control-label needed">Género</label>
                            <select id="genero_idRep" class="form-control catSelect select-element">
                                <option value="">-- Selecciona un género</option>
                            </select>
                        </div>
                        <div class="col-md-12 row">
                            <div class="col-md-6">
                                <label id="labelIdentificacion" class=" needed">Documento de identificaci&oacute;n</label>
                                <span class="btn btn-primary fileinput-button m-r-3">
                                    <i class="fa fa-fw fa-plus"></i>
                                    <span>Seleccionar identificaci&oacute;n</span>
                                    <input type="file" id="fileIdentificacion" name="files">
                                </span>
                                <p style="margin-top: 1%;" id="labelIdentifRepresentante"></p>
                            </div>
                            <div class="col-md-6">
                                <label for="clasificacion_archivo_id_representante" class="control-label needed">Tipo de documento</label>
                                <select class="form-control catSelect" required id="tipo_documento_id" name="tipo_documento_id">
                                    <option value="">Seleccione una opci&oacute;n</option>
                                    @if(isset($clasificacion_archivo))
                                    @foreach($clasificacion_archivo as $clasificacion)
                                    <option value="{{$clasificacion->id}}">{{$clasificacion->nombre}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label >Cedula profesional</label>
                                <span class="btn btn-primary fileinput-button m-r-3">
                                    <i class="fa fa-fw fa-plus"></i>
                                    <span>Seleccionar identificaci&oacute;n</span>
                                    <input type="file" id="fileCedula" name="files">
                                </span>
                                <p style="margin-top: 1%;" id="labelCedula"></p>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h5>Datos de comprobante como representante</h5>
                    <div class="col-md-12 row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clasificacion_archivo_id_representante" class="control-label needed">Instrumento</label>
                                <select id="clasificacion_archivo_id_representante" class="form-control select-element">
                                    <option value="">-- Selecciona un instrumento</option>
                                    @foreach($clasificacion_archivos_Representante as $clasificacion)
                                    <option class='{{($clasificacion->tipo_archivo_id == 10) ? "archivo_sindical" : ""}}' value="{{$clasificacion->id}}">{{$clasificacion->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="feha_instrumento" class="control-label needed">Fecha de instrumento</label>
                                <input type="text" id="feha_instrumento" class="form-control fecha" placeholder="Fecha en que se extiende el instrumento">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label id="labelInstrumento" class="needed">Documento de Instrumento</label> 
                            <span class="btn btn-primary fileinput-button m-r-3">
                                <i class="fa fa-fw fa-plus"></i>
                                <span>Seleccionar instrumento</span>
                                <input type="file" id="fileInstrumento" name="files">
                            </span>
                            <p style="margin-top: 1%;" id="labelInstrumentoRepresentante"></p>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="detalle_instrumento" class="control-label">Detalle del instrumento notarial</label>
                                <textarea type="text" id="detalle_instrumento" class="form-control" placeholder=""></textarea>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h5 class=" needed">Datos de contacto</h5>
                    <div class="col-md-12 row">
                        <div class="col-md-5">
                            <label for="tipo_contacto_id" class="col-sm-6 control-label">Tipo de contacto</label>
                            <select id="tipo_contacto_id" class="form-control catSelect select-element">
                                <option value="">-- Selecciona un género</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="contacto" class="control-label">Contacto</label>
                                <input type="text" id="contacto" class="form-control" placeholder="Información de contacto">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary" type="button" id="btnAgregarContacto">
                                <i class="fa fa-plus-circle"></i> Agregar
                            </button>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-bordered" >
                            <thead>
                                <tr>
                                    <th style="width:80%;">Tipo</th>
                                    <th style="width:80%;">Contacto</th>
                                    <th style="width:20%; text-align: center;">Acci&oacute;n</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyContacto">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5" id="btnGuardarRepresentante"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="modal-registro-correos" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Buz&oacute;n Electr&oacute;nico</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-muted">
                    - Para acceder al buz&oacute;n electr&oacute;nico se deber&aacute; registrar 
                    <ol>
                        <li>El CURP o RFC de la persona y </li>
                        <li>Un correo electr&oacute;nico al cual asociarlo.  </li>
                    </ol>
                    <p>En el caso de que usted no haya proporcionado un correo electr&oacute;nico con anterioridad, podr&aacute; capturarlo en este momento, de lo contrario seleccione "Proporcionar accesos" y el sistema le proporcionar&aacute; un usuario y una contrase&ntilde;a para acceder al buz&oacute;n electr&oacute;nico.</p>
                    <p>El correo electrónico que usted asocie a su buzón electrónico servirá para recibir correos con el asunto de avisos, en el caso de que no haya aceptado las notificaciones por este medio; o con el asunto de notificaciones, en el caso de que sí haya aceptado las notificaicones por buzón electrónico. Estos correos electrónicos son una herramienta auxiliar del buzón electrónico, no son notificaciones.</p>
                </div>
                <table class="table table-bordered table-striped table-hover" id="tableSolicitantesCorreo">
                    <thead>
                        <tr>
                            <th>Solicitante</th>
                            <th></th>
                            <th>RFC/CURP</th>
                            <th>Correo electrónico</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5" id="btnGuardarCorreos"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="modal-visor" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div id="bodyArchivo">
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="modal-aviso-resolucion-inmediata" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Confirmaci&oacute;n inmediata</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-muted">
                    <p>
                        Usted est&aacute; a punto de confirmar la solicitud para que se de resolución inmediatamente, las indicaciones para esta resolución son las siguientes.<br><br>
                    <ul>
                        <li>Debido a que no se requiere sala para realizar la audiencia, se asignar&aacute; una sala virtual y el conciliador ser&aacute; asignado de acuerdo a la disponibilidad</li>
                        <li>Debido a que ya hay un convenio entré las partes, la unica labor del conciliador ser&aacute; dar fe de lo acordado</li>
                        <li>Se deber&aacute; acceder a la guia de audiencia donde se llenar&aacute;n los datos requerido para extender la confirmaci&oacute;n y el documento que de esta resulte</li>
                        <li>Si desea continuar con el proceso de confirmaci&oacute;n inmediata presione confirmar</li>
                        <li>Si desea agendar una audiencia para conciliar, presione cancelar</li>
                    </ul>
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5" id="btnRatificarInmediata"><i class="fa fa-arrow-right"></i> Continuar</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal ratificacion --}}

<!-- inicio Modal cargar archivos-->
<div class="modal" id="modal-archivos" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Documentos de identificaci&oacute;n</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form id="fileupload" action="/api/documentos/solicitud" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="solicitud_id[]" id='solicitud_id_modal'/>
                    <div class="row fileupload-buttonbar">
                        <div class="col-xl-12">
                            <span class="btn btn-primary fileinput-button m-r-3">
                                <i class="fa fa-fw fa-plus"></i>
                                <span>Agregar...</span>
                                <input type="file" name="files[]" multiple>
                            </span>
                            {{-- <button type="submit" class="btn btn-primary start m-r-3">
                            <i class="fa fa-fw fa-upload"></i>
                                        <span>Cargar</span>
                                        </button> --}}
                                <button type="reset" class="btn btn-default cancel m-r-3" id="btnCancelFiles">
                                            <i class="fa fa-fw fa-ban"></i>
                            <span>Cancelar</span>
                            </button>
                            <!-- The global file processing state -->
                            <span class="fileupload-process"></span>
                        </div>
                        <!-- The global progress state -->
                        <div class="col-xl-5 fileupload-progress fade d-none d-xl-block">
                            <!-- The global progress bar -->
                            <div class="progress progress-striped active m-b-0">
                                <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                            </div>
                            <!-- The extended global progress state -->
                            <div class="progress-extended">&nbsp;</div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-condensed text-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th width="10%">VISTA PREVIA</th>
                                    <th>INFORMACION</th>
                                    <th>TIPO DE DOCUMENTO</th>
                                    <th>PARTE RELACIONADA</th>
                                    <th>PROGRESO</th>
                                    <th width="1%"></th>
                                    <th width="1%"></th>
                                </tr>
                            </thead>
                            <tbody class="files">
                                <tr data-id="empty">
                                    <td colspan="5" class="text-center text-muted p-t-30 p-b-30">
                                        <div class="m-b-10"><i class="fa fa-file fa-3x"></i></div>
                                        <div>Sin documentos</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-primary btn-sm" data-dismiss="modal" onclick="continuarRatificacion()"><i class="fa fa-sign-out"></i> Continuar a confirmaci&oacute;n</a>
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-sign-out"></i> Cerrar</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Fin Modal de cargar archivos-->
<div class="modal" id="modal-ratificacion-success" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Audiencia generada</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-muted">
                    <p>
                        Se generó la audiencia con la siguiente información
                    </p>
                </div>
                <div class="col-md-12 row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>Folio: </strong><span id="spanFolio"></span><br>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>Fecha de Audiencia: </strong><span id="spanFechaAudiencia"></span><br>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>Hora de inicio: </strong><span id="spanHoraInicio"></span><br>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>Hora de t&eacute;rmino: </strong><span id="spanHoraFin"></span><br>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <table class="table table-striped table-hover" id="tableAudienciaSuccess">
                        <thead>
                            <tr>
                                <th>Tipo de parte</th>
                                <th>Conciliador</th>
                                <th>Sala</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <button class="btn btn-primary btn-sm m-l-5" id="btnFinalizarRatificacion"><i class="fa fa-check"></i> Finalizar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="hddSolicitud_id">
@push('scripts')
<script>
    var estatus_solicitudes = [];
            @foreach($estatus_solicitudes as $key => $node)
    estatus_solicitudes['{{$key}}'] = '{{$node}}';
    @endforeach
            var esSindical = false;
    var filtrado = false;
    $(document).ready(function () {
        var ruta = $("#ruta").val();
        var rutaConsulta = $("#rutaConsulta").val();
        var dt = $('#tabla-detalle').DataTable({
            "deferRender": false,
            "ajax": {
                "url": '/solicitudes',
                "dataSrc": function (json) {
                    var array = new Array();
                    this.recordsTotal = json.recordsTotal;
                    if (mis_solicitudes == true) {
                        $("#spanMisSol").html(json.recordsFiltered);
                    }
                    $.each(json.data, function (key, value) {
                        array.push(Object.values(value));
                    });
                    return array;
                },
                "data": function (d) {
                    d.fechaRatificacion = dateFormat($("#fechaRatificacion").val(), 1),
                            d.fechaRecepcion = dateFormat($("#fechaRecepcion").val(), 1),
                            d.fechaConflicto = dateFormat($("#fechaConflicto").val(), 1),
                            d.folio = $("#folio").val(),
                            d.Expediente = $("#Expediente").val(),
                            d.anio = $("#anio").val(),
                            d.estatus_solicitud_id = $("#estatus_solicitud_id").val(),
                            d.mis_solicitudes = $("#mis_solicitudes").val(),
                            d.curp = $("#curp").val(),
                            d.nombre = $("#nombre").val(),
                            d.nombre_citado = $("#nombre_citado").val(),
                            d.dias_expiracion = $("#dias_expiracion").val(),
                            d.tipo_solicitud_id = $("#tipo_solicitud_id").val(),
                            d.conciliador_id = $("#conciliador_filter_id").val(),
                            d.IsDatatableScroll = true,
                            d.loadPartes = true
                    // d.objeto_solicitud_id = $("#objeto_solicitud_id").val()
                },
            },
            "columnDefs": [
                {"targets": [0], "visible": false},
                {
                    "targets": [1],
                    "render": function (data, type, row) {
                        if (data != null) {
                            return  estatus_solicitudes[data];
                        } else {
                            return "";
                        }
                    }
                },
                {
                    "targets": [2],
                    "render": function (data, type, row) {
                        return row[2] + "/" + row[3];
                    }
                },
                {"targets": [3], "visible": false},
                {
                    "targets": [4],
                    "render": function (data, type, row) {
                        if (data != null) {
                            return  dateFormat(data, 2);
                        } else {
                            return "";
                        }
                    }
                },
                {
                    "targets": [5],
                    "render": function (data, type, row) {
                        if (data != null) {
                            return  dateFormat(data, 2);
                        } else {
                            return "";
                        }
                    }
                },

                {
                    "targets": [6],
                    "render": function (data, type, row) {
                        if (data != null) {
                            return  dateFormat(data);
                        } else {
                            return "";
                        }
                    }
                },
                {"targets": [7], "visible": false},
                {
                    "targets": [8],
                    "render": function (data, type, row) {
                        var html = "";
                        var solicitantes = "";
                        var solicitados = "";
                        var contSol = 0;
                        var contCit = 0;
                        $.each(row[10], function (key, value) {
                            var nombre = "";
                            if (value.tipo_persona_id == 1) {
                                nombre = value.nombre + " " + value.primer_apellido + " " + (value.segundo_apellido || "")
                            } else {
                                nombre = value.nombre_comercial;
                            }
                            var addetc = "";
                            if (nombre.length > 30) {
                                addetc = "...";
                            }
                            if (value.tipo_parte_id == 1) {
                                contSol++;
                                solicitantes = "<p> -" + nombre.substring(0, 30) + addetc + "</p>";
                            } else if (value.tipo_parte_id == 2) {
                                contCit++;
                                solicitados = "<p> - " + nombre.substring(0, 30) + addetc + "</p>";
                            }
                        });
                        html += "<div>";
                        html += "<h5>Solicitantes</h5>";
                        html += solicitantes;
                        if (contSol > 1) {
                            html += "<p> Y OTROS</p>";
                        }
                        html += "<h5>Citados</h5>";
                        html += solicitados;
                        if (contCit > 1) {
                            html += "<p> Y OTROS</p>";
                        }
                        html += "</div>";
                        return  html;
                    }
                },
                {
                    "targets": [9],
                    "render": function (data, type, row) {
                        html = "N/A";
                        if (row[11] != null) {
                            html = "" + row[11].folio;
                        }
                        return  html;
                    }
                },
                {
                    "targets": [10],
                    "render": function (data, type, row) {
                        html = "";
                        if (row[12] != null) {
                            html = " " + row[12].persona.nombre + " " + row[12].persona.primer_apellido + " " + (row[12].persona.segundo_apellido || "");
                        }
                        return  html;
                    }
                },
                {
                    "targets": -2,
                    "render": function (data, type, row) {
                        if (row[1] == "2" && row[5] != null) {
                            var d = new Date();
                            var dateToday = d.getFullYear() + "-" + String(d.getMonth() + 1).padStart(2, '0') + "-" + String(d.getDate()).padStart(2, '0');
                            var date1 = new Date(row[5].split(" ")[0]);
                            var date2 = new Date(dateToday);
                            var dias = date2 - date1;
                            dias = (dias / (1000 * 3600 * 24));
                            diasExpira = 45;
                            resultado = diasExpira - dias;
                            if (resultado > 0) {
                                return resultado + " d&iacute;as";
                            }
                            expiro = resultado * -1;
                            return  "<p style='color:red;'>Expir&oacute; hace: " + expiro + " d&iacute;as </p>";
                        } else {
                            return '';
                        }
                    }
                    // "defaultContent": '<div style="display: inline-block;"><a href="{{route("solicitudes.edit",['+row[0]+'])}}" class="btn btn-xs btn-primary"><i class="fa fa-pencil-alt"></i></a>&nbsp;<button class="btn btn-xs btn-danger btn-borrar"><i class="fa fa-trash btn-borrar"></i></button></div>',
                },
                {
                    "targets": -1,
                    "render": function (data, type, row) {
                        var buttons = '';
                        if ((row[7] == $("#centro_id").val() || $("#oficina_central").val() == "true") && row[1] != 3) {
                            buttons += '<div title="Editar solicitud" data-toggle="tooltip" data-placement="top" style="display: inline-block;" class="m-2"><a href="' + ruta.replace('/1/', "/" + row[0] + "/") + '#step-4" class="btn btn-xs btn-primary"><i class="fa fa-pencil-alt"></i></a></div>';
                        }
                        buttons += '<div title="Ver datos de la solicitud" data-toggle="tooltip" data-placement="top" style="display: inline-block;" class="m-2"><a href="' + rutaConsulta.replace('/-rutaConsulta', "/" + row[0]) + '" class="btn btn-xs btn-primary"><i class="fa fa-search"></i></a></div>';

                        if (row[1] == 1 && (row[7] == $("#centro_id").val() || $("#oficina_central").val() == "true") && ($("#atiende_virtual").val() == "1" && row[9] || $("#atiende_virtual").val() == "2" || $("#atiende_virtual").val() == "3")) {
                            buttons += '<div title="Confirmar solicitud" data-toggle="tooltip" data-placement="top" style="display: inline-block;" class="m-2"><button onclick="continuarRatificacion(' + row[0] + ')" class="btn btn-xs btn-primary"><i class="fa fa-tasks"></i></button></div>';
                        }
                        if (row[1] == 1 && $("#atiende_virtual").val() == "1" && !row[9]) {
                            buttons += '<label>Aplazada (No virtual)</label>';
                        }
                        return buttons;
                    }

                }
            ],
            "serverSide": true,
            "processing": true,
            select: true,
            "ordering": false,
            "searching": false,
            "pageLength": 20,
            "recordsTotal": 20,
            "recordsFiltered": 20,
            "lengthChange": false,
            "scrollX": false,
            "scrollY": $(window).height() - $('#header').height() - 200,
            "scrollColapse": false,
            "scroller": {
                "serverWait": 200,
                "loadingIndicator": true,
            },
            "responsive": true,
            "language": {
                "url": "/assets/plugins/datatables.net/dataTable.es.json"
            },
            "stateSaveParams": function (settings, data) {
                //data.search.search = "";
            },
            "dom": "tiS", // UI layout
        });
        dt.on('draw', function () {
            if (filtrado) {
                //dt.scroller().scrollToRow(0);
                filtrado = false;
            }
        });

        $('.filtros').on('dp.change change clear', function () {
            dt.clear();
            dt.ajax.reload(function () {}, true);
            filtrado = true;
        });
        $(".catSelect").select2({width: '100%'});
        $(".date").datetimepicker({useCurrent: false, locale: "es", format: 'DD/MM/YYYY'});
        $(".date").keypress(function (event) {
            event.preventDefault();
        });
        function dateFormat(fecha, tipo) {
            if (fecha != "") {
                if (tipo == 1) {
                    var vecFecha = fecha.split("/");
                    var formatedDate = vecFecha[2] + "-" + vecFecha[1] + "-" + vecFecha[0];
                    return formatedDate;
                } else if (tipo == 2) {
                    var vecFechaHora = fecha.split(" ");
                    var vecFecha = vecFechaHora[0].split("-");
                    var formatedDate = vecFecha[2] + "/" + vecFecha[1] + "/" + vecFecha[0] + " " + vecFechaHora[1];
                    return formatedDate;
                } else if (tipo == 3) {
                    var vecFechaHora = fecha.split(" ");
                    var vecFecha = vecFechaHora[0].split("/");
                    var formatedDate = vecFecha[2] + "-" + vecFecha[1] + "-" + vecFecha[0] + " " + vecFechaHora[1];
                    return formatedDate;
                } else {
                    var vecFecha = fecha.split("-");
                    var formatedDate = vecFecha[2] + "/" + vecFecha[1] + "/" + vecFecha[0];
                    return formatedDate;
                }
            }
        }
        $("#limpiarFiltros").click(function () {
            $(".filtros").val("");
            $(".catSelect").trigger('change');
            dt.clear();
            dt.ajax.reload(function () {}, true);
            filtrado = true;
        });
        $('[data-toggle="tooltip"]').tooltip();
        cargarGeneros();
        cargarTipoContactos();
        FormMultipleUpload.init();
    });

    function filtrarMisSolicitudes() {
        mis_solicitudes = !mis_solicitudes;
        $('#mis_solicitudes').val(mis_solicitudes).trigger('change');
        if (mis_solicitudes) {
            $("#spanMisSol").addClass('badge-success');
            $("#btnMisSol").addClass('selectedButton');
        } else {
            $("#btnMisSol").removeClass('selectedButton');
            $("#spanMisSol").removeClass('badge-success');
            $("#spanMisSol").html("0");
        }
    }

    //    para confirmacion

    $(document).on('click', '[data-toggle="iframe"]', function (event) {
        event.preventDefault();
        var pdf_link = $(this).attr('href');
        var iframe = "";
        iframe += '    <div id="Iframe-Cicis-Menu-To-Go" class="set-margin-cicis-menu-to-go set-padding-cicis-menu-to-go set-border-cicis-menu-to-go set-box-shadow-cicis-menu-to-go center-block-horiz">';
        iframe += '        <div class="responsive-wrapper responsive-wrapper-padding-bottom-90pct" style="-webkit-overflow-scrolling: touch; overflow: auto;">';
        iframe += '            <iframe src="' + pdf_link + '"></iframe>';
        iframe += '        </div>';
        iframe += '    </div>';

        $("#bodyArchivo").html(iframe);
        $("#modal-visor").modal("show");

        return false;
    });

    // Funcion para ratificar solicitudes
    function continuarRatificacion(solicitud_id = null) {
        if (solicitud_id == null) {
            var solicitud_id = $("#solicitud_id").val();
        } else {
            $("#solicitud_id").val(solicitud_id);
        }

        getSolicitudFromBD(solicitud_id);
        $("#solicitud_id_modal").val(solicitud_id);
        actualizarPartes();
        var instancia = $("#instancia").val();
        if ((solicitudObj.ambito_id != 1 && instancia == "federal") || (solicitudObj.ambito_id != 2 && instancia == "local")) { //No es ambito Federal
            $('#btnGuardarRatificar').hide();
            $('#btnGuardarConvenio').hide();
            $('#btnRatificarIncompetencia').show();
            $('#notificaAmbito').show();
            let ambito = (solicitudObj.ambito_nombre !== undefined) ? 'ATENCIÓN! La actividad principal del patrón registrada es de competencia ' + solicitudObj.ambito_nombre : "";
            $('#notificaAmbito').html(ambito);
        } else {
            $('#btnGuardarRatificar').show();
            $('#btnGuardarConvenio').show();
            $('#btnRatificarIncompetencia').hide();
            $('#notificaAmbito').hide();
        }
        if (solicitudObj.geolocalizable) {
            $("#divNoGeolocalizable").hide();
            $("#divGeolocalizable").show();
        } else {
            $("#divNoGeolocalizable").show();
            $("#divGeolocalizable").hide();
        }
        if (solicitudObj.tipo_solicitud_id == 3 || solicitudObj.tipo_solicitud_id == 4) {
            $(".archivo_sindical").show();
            $("#btnGuardarRatificar").hide();
            $("#divCalendarioCentral").show();
        } else {
            $(".archivo_sindical").hide();
            $("#divCalendarioCentral").hide();
            $("#btnGuardarRatificar").show();
        }
        try {
            cargarDocumentos();
            var solicitanteMenor = arraySolicitantes.filter(x => x.edad <= 16).filter(x => x.edad != null);
            var solicitanteMoral = arraySolicitantes.filter(x => x.tipo_persona_id == "2");
            if (solicitanteMenor.length > 0 || solicitanteMoral.length > 0) {
                $("#divNeedRepresentante").show();
                var html = "";
                $.each(solicitanteMenor, function (key, parte) {
                    html += "<tr>";
                    html += "<td>" + parte.nombre + " " + parte.primer_apellido + " " + (parte.segundo_apellido || "") + "</td>";
                    html += "<td><button class='btn btn-primary' type='button' onclick='AgregarRepresentante(" + parte.id + ",1)' id='btnaddRep" + parte.id + "' > <i class='fa fa-plus-circle'></i> Agregar Representante</button> <span style='color:green; font-size:Large;' id='tieneRepresentante" + parte.id + "'></span></td>";
                    html += "</tr>";
                });
                $.each(solicitanteMoral, function (key, parte) {
                    html += "<tr>";
                    html += "<td>" + parte.nombre_comercial + "</td>";
                    html += "<td><button class='btn btn-primary' type='button' onclick='AgregarRepresentante(" + parte.id + ",0)' id='btnaddRep" + parte.id + "' > <i class='fa fa-plus-circle'></i> Agregar Representante</button> <span style='color:green; font-size:Large;' id='tieneRepresentante" + parte.id + "'></span></td>";
                    html += "</tr>";
                });
                $("#tbodyRepresentante").html(html);
            } else {
                $("#divNeedRepresentante").hide();
            }
            $("#modalRatificacion").modal("show");
//
        } catch (error) {
            console.log(error);
    }
    }
    $("#btnRatificarIncompetencia").on("click",function(){
        $("#justificacion_incompetencia").val("");
        $("#modalRatificacionJustificacion").modal("show");
    });

    $("#btnRatificarIncompetenciaJustificacion").on("click", function () {
        if("{{auth()->user()->hasRole('Administrador del centro')}}"){
            if($("#justificacion_incompetencia").val() != ""){
                if (ratifican) {
                    $.ajax({
                        url: '/solicitud/correos/' + $("#solicitud_id").val(),
                        type: 'GET',
                        dataType: "json",
                        async: true,
                        success: function (data) {
                            if (data == null || data == "") {
                                swal({
                                    title: '¿Estás seguro?',
                                    text: 'Al oprimir aceptar se emitirá la constancia de incompetencia correspondiente, de lo contrario oprime cancelar y actualiza la actividad en la soliciud.',
                                    icon: 'warning',
                                    buttons: {
                                        cancel: {
                                            text: 'Cancelar',
                                            value: null,
                                            visible: true,
                                            className: 'btn btn-default',
                                            closeModal: true,
                                        },
                                        confirm: {
                                            text: 'Aceptar',
                                            value: true,
                                            visible: true,
                                            className: 'btn btn-danger',
                                            closeModal: true
                                        }
                                    }
                                }).then(function (isConfirm) {
                                    if (isConfirm) {
                                        $.ajax({
                                            url: '/solicitud/ratificarIncompetencia',
                                            type: 'POST',
                                            dataType: "json",
                                            async: true,
                                            data: {
                                                id: $("#solicitud_id").val(),
                                                justificacion: $("#justificacion_incompetencia").val(),
                                                _token: "{{ csrf_token() }}"
                                            },
                                            success: function (data) {
                                                if (data != null && data != "") {
                                                    $("#modalRatificacion").modal("hide");
                                                    swal({
                                                        title: 'Correcto',
                                                        text: 'Solicitud confirmada correctamente',
                                                        icon: 'success'
                                                    });
                                                    location.reload();
                                                } else {
                                                    swal({
                                                        title: 'Error',
                                                        text: 'No se pudo confirmar',
                                                        icon: 'error'
                                                    });
                                                }
                                            }, error: function (data) {
                                                swal({
                                                    title: 'Error',
                                                    text: data.responseJSON.message,
                                                    icon: 'error'
                                                });
                                            }
                                        });
                                    }
                                });

                            } else {
                                var tableSolicitantes = '';
                                $.each(data, function (index, element) {
                                    tableSolicitantes += '<tr>';
                                    if (element.tipo_persona_id == 1) {
                                        tableSolicitantes += '<td>' + element.nombre + ' ' + element.primer_apellido + ' ' + (element.segundo_apellido || "") + '</td>';
                                    } else {
                                        tableSolicitantes += '<td>' + element.nombre_comercial + '</td>';
                                    }
                                    tableSolicitantes += '  <td>';
                                    tableSolicitantes += '      <div class="col-md-12">';
                                    tableSolicitantes += '          <span class="text-muted m-l-5 m-r-20" for="checkCorreo' + element.id + '">Proporcionar accesos</span>';
                                    tableSolicitantes += '          <input type="checkbox" class="checkCorreo" data-id="' + element.id + '" checked="checked" id="checkCorreo' + element.id + '" name="checkCorreo' + element.id + '" onclick="checkCorreo(' + element.id + ')"/>';
                                    tableSolicitantes += '      </div>';
                                    tableSolicitantes += '  </td>';
                                    tableSolicitantes += '  <td>';
                                    if(element.tipo_persona_id == 1){
                                        tableSolicitantes += '      <input type="text" class="form-control upper" value="'+(element.curp || '') +'" id="rfcCurpValidar'+element.id+'">';
                                    }else{
                                        tableSolicitantes += '      <input type="text" class="form-control upper" value="'+(element.rfc || '') +'" id="rfcCurpValidar'+element.id+'">';
                                    }
                                    tableSolicitantes += '  </td>';
                                    tableSolicitantes += '  <td>';
                                    tableSolicitantes += '      <input type="text" class="form-control" disabled="disabled" id="correoValidar' + element.id + '">';
                                    tableSolicitantes += '  </td>';
                                    tableSolicitantes += '</tr>';
                                });
                                $("#tableSolicitantesCorreo tbody").html(tableSolicitantes);
                                $("#modal-registro-correos").modal("show");
                            }
                        }
                    });
                } else {
                    swal({
                        title: 'Error',
                        text: 'Al menos un solicitante debe presentar documentos para confirmar',
                        icon: 'warning'
                    });
                }
            }else{
                swal({
                    title: 'Aviso',
                    text: 'Coloca la justificación',
                    icon: 'warning'
                });
            }
        
        }else{
            swal({
                title: 'Aviso',
                text: 'Esta acción solo esta permitida para el director del centro',
                icon: 'warning'
            });
        }
    });

    $("#btnGuardarRatificar").on("click", function () {
        var validarRatificacion = RatificacionValidar();
        if (!validarRatificacion.error) {
            // if(!$("#aceptar_notif_buzon").is(":checked")){
            //         aceptarExpediente(validarRatificacion);
            // }else{
                $.ajax({
                    url: '/solicitud/correos/' + $("#solicitud_id").val(),
                    type: 'GET',
                    dataType: "json",
                    async: true,
                    success: function (data) {
                        try {
                            if (data == null || data == "") {
                                aceptarExpediente(validarRatificacion);
                            } else {
                                var tableSolicitantes = '';
                                $.each(data, function (index, element) {
                                    tableSolicitantes += '<tr>';
                                    if (element.tipo_persona_id == 1) {
                                        tableSolicitantes += '<td>' + element.nombre + ' ' + element.primer_apellido + ' ' + (element.segundo_apellido || "") + '</td>';
                                    } else {
                                        tableSolicitantes += '<td>' + element.nombre_comercial + '</td>';
                                    }
                                    tableSolicitantes += '  <td>';
                                    tableSolicitantes += '      <div class="col-md-12">';
                                    tableSolicitantes += '          <span class="text-muted m-l-5 m-r-20" for="checkCorreo' + element.id + '">Proporcionar accesos</span>';
                                    tableSolicitantes += '          <input type="checkbox" class="checkCorreo" data-id="' + element.id + '" checked="checked" id="checkCorreo' + element.id + '" name="checkCorreo' + element.id + '" onclick="checkCorreo(' + element.id + ')"/>';
                                    tableSolicitantes += '      </div>';
                                    tableSolicitantes += '  </td>';
                                    tableSolicitantes += '  <td>';
                                    if(element.tipo_persona_id == 1){
                                        tableSolicitantes += '      <input type="text" class="form-control upper" value="'+(element.curp || '') +'" id="rfcCurpValidar'+element.id+'">';
                                    }else{
                                        tableSolicitantes += '      <input type="text" class="form-control upper" value="'+(element.rfc || '') +'" id="rfcCurpValidar'+element.id+'">';
                                    }
                                    tableSolicitantes += '  </td>';
                                    tableSolicitantes += '  <td>';
                                    var correo = element.correo_buzon != null ? element.correo_buzon : "";
                                    tableSolicitantes += '      <input type="text" class="form-control" disabled="disabled" id="correoValidar' + element.id + '" value="'+ correo +'">';
                                    tableSolicitantes += '  </td>';
                                    tableSolicitantes += '</tr>';
                                });
                                $("#tableSolicitantesCorreo tbody").html(tableSolicitantes);
                                $("#modal-registro-correos").modal("show");
                            }
                        } catch (error) {
                            console.log(error);
                        }
                    }
                });
            // }
        } else {
            swal({
                title: 'Error',
                text: validarRatificacion.msg,
                icon: 'warning'
            });
        }
    });

    function aceptarExpediente(validarRatificacion){
        swal({
            title: '¿Estas seguro?',
            text: 'Al oprimir aceptar se creará un expediente y se podrán agendar audiencias para conciliación',
            icon: 'warning',
            buttons: {
                cancel: {
                    text: 'Cancelar',
                    value: null,
                    visible: true,
                    className: 'btn btn-default',
                    closeModal: true,
                },
                confirm: {
                    text: 'Aceptar',
                    value: true,
                    visible: true,
                    className: 'btn btn-danger',
                    closeModal: true
                }
            }
        }).then(function (isConfirm) {
            if (isConfirm) {
                elegirSala(validarRatificacion);
            }
        });
    }

    function elegirSala(validarRatificacion){
        swal({
            title: '¿Las partes concilian en la misma sala?',
            text: 'Selecciona el tipo de conciliación que se llevará a cabo',
            icon: 'warning',
            buttons: {
                cancel: {
                    text: 'cancelar',
                    value: null,
                    visible: true,
                    className: 'btn btn-default',
                    closeModal: true,
                },
                roll: {
                    text: "Separados",
                    value: 2,
                    className: 'btn btn-warning',
                    visible: !esSindical,
                    closeModal: true
                },
                confirm: {
                    text: 'Juntos',
                    value: 1,
                    visible: true,
                    className: 'btn btn-warning',
                    closeModal: true
                }
            }
        }).then(function (tipo) {
            if (tipo == 1 || tipo == 2) {
                if (tipo == 1) {
                    var separados = false;
                } else {
                    var separados = true;
                }
                aceptarRatificacion(validarRatificacion,separados);
            }
        });
    }

    function aceptarRatificacion(validarRatificacion,separados){
        $.ajax({
                    url: '/solicitud/ratificar',
                    type: 'POST',
                    dataType: "json",
                    async: true,
                    data: {
                        id: $("#solicitud_id").val(),
                        tipo_notificacion_id: validarRatificacion.tipo_notificacion_id,
                        inmediata: false,
                        fecha_cita: $("#fecha_cita").val(),
                        url_virtual: $("#url_virtual").val(),
                        separados: separados,
                        acepta_buzon: $("#aceptar_notif_buzon").is(":checked"),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (data) {
                        try {
                            if (data != null && data != "") {
                                if (data.encontro_audiencia) {
                                    $("#spanFolio").text(data.folio + "/" + data.anio);
                                    $("#spanFechaAudiencia").text(dateFormat(data.fecha_audiencia, 4));
                                    $("#spanHoraInicio").text(data.hora_inicio);
                                    $("#spanHoraFin").text(data.hora_fin);
                                    var table = "";
                                    if (data.multiple) {
                                        $.each(data.conciliadores_audiencias, function (index, element) {
                                            table += '<tr>';
                                            if (element.solicitante) {
                                                table += '   <td>Solicitante(s)</td>';
                                            } else {
                                                table += '   <td>Citado(s)</td>';
                                            }
                                            table += '   <td>' + element.conciliador.persona.nombre + ' ' + element.conciliador.persona.primer_apellido + ' ' + element.conciliador.persona.segundo_apellido + '</td>';
                                            $.each(data.salas_audiencias, function (index2, element2) {
                                                if (element2.solicitante == element.solicitante) {
                                                    table += '<td>' + element2.sala.sala + '</td>';
                                                }
                                            });
                                            table += '</tr>';
                                        });
                                    } else {
                                        table += '<tr>';
                                        table += '   <td>Solicitante(s) y citado(s)</td>';
                                        table += '   <td>' + data.conciliadores_audiencias[0].conciliador.persona.nombre + ' ' + data.conciliadores_audiencias[0].conciliador.persona.primer_apellido + ' ' + data.conciliadores_audiencias[0].conciliador.persona.segundo_apellido + '</td>';
                                        table += '   <td>' + data.salas_audiencias[0].sala.sala + '</td>';
                                        table += '</tr>';
                                    }
                                    $("#tableAudienciaSuccess tbody").html(table);
                                    $("#modalRatificacion").modal("hide");
                                    $("#modal-ratificacion-success").modal({backdrop: 'static', keyboard: false});
                                    swal({
                                        title: 'Correcto',
                                        text: 'Solicitud confirmada correctamente',
                                        icon: 'success'
                                    });
                                } else {
                                    swal({
                                        title: 'Correcto',
                                        text: 'Se genero la audiencia con el folio: ' + data.folio + '/' + data.anio + ', la cual no encontró espacio en la agenda y deberá ser asignada por el supervisor del centro',
                                        icon: 'success'
                                    });
                                }
                            } else {
                                swal({
                                    title: 'Error',
                                    text: 'No se pudo ratificar',
                                    icon: 'error'
                                });
                            }
                        } catch (error) {
                            console.log(error);
                        }
                    }, error: function (data) {
                        swal({
                            title: 'Error',
                            text: data.responseJSON.message,
                            icon: 'error'
                        });
                    }
                });
    }
    function RatificacionValidar() {
        var error = false;
        var listaNotificaciones = [];
        var msg = "";
        if (!ratifican) {
            error = true;
            msg = "Al menos un solicitante debe presentar documentos para confirmar";
        }
        if($("#aradioNotificacionA1").is(":checked")){
            var tipo_notificacion_id=1;
        }else if($("#aradioNotificacionB1").is(":checked")){
            var tipo_notificacion_id=2;
        }else if($("#aradioNotificacionB2").is(":checked")){
           var tipo_notificacion_id=3;
        }else{
            var tipo_notificacion_id = null;
            msg = "Indica el tipo de notificación para los citados";
            error = true;
        }
        var array = [];
        array.error = error;
        array.msg = msg;
        array.tipo_notificacion_id = tipo_notificacion_id;
        return array;
    }
    $("#btnRatificarInmediata").on("click", function () {
        swal({
            title: '¿Estas seguro?',
            text: 'Al oprimir aceptar se creará un expediente y podra relizar la resolución inmediatamente',
            icon: 'warning',
            buttons: {
                cancel: {
                    text: 'Cancelar',
                    value: null,
                    visible: true,
                    className: 'btn btn-default',
                    closeModal: true,
                },
                confirm: {
                    text: 'Aceptar',
                    value: true,
                    visible: true,
                    className: 'btn btn-danger',
                    closeModal: true
                }
            }
        }).then(function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: '/solicitud/ratificar',
                    type: 'POST',
                    dataType: "json",
                    async: true,
                    data: {
                        id: $("#solicitud_id").val(),
                        inmediata: true,
                        url_virtual: $("#url_virtual").val(),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (data) {
                        try {

                            if (data != null && data != "") {
                                $("#modal-aviso-resolucion-inmediata").modal("hide");
                                $("#modalRatificacion").modal("hide");
                                swal({
                                    title: 'Correcto',
                                    text: 'Solicitud confirmada correctamente',
                                    icon: 'success'
                                });
                                if (data.tipo_solicitud_id == 1) {
                                    window.location.href = "/guiaAudiencia/" + data.id;
                                } else if (data.tipo_solicitud_id == 2) {
                                    window.location.href = "/guiaPatronal/" + data.id;
                                } else {
                                    window.location.href = "/resolucionColectiva/" + data.id;
                                }
                            } else {
                                swal({
                                    title: 'Error',
                                    text: 'No se pudo confirmar',
                                    icon: 'error'
                                });
                            }
                        } catch (error) {
                            console.log(error);
                        }
                    }, error: function (data) {
                        swal({
                            title: 'Error',
                            text: data.responseJSON.message,
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });
    $("#btnGuardarCorreos").on("click", function () {
        var validacion = validarCorreos();
        if (!validacion.error) {
            $.ajax({
                url: '/solicitud/correos',
                type: 'POST',
                dataType: "json",
                async: true,
                data: {
                    _token: "{{ csrf_token() }}",
                    listaCorreos: validacion.listaCorreos
                },
                success: function (data) {
                    $("#modal-registro-correos").modal("hide");
                }, error: function (error) {
                    swal({
                        title: 'Error',
                        text: 'Ocurrio un error al guardar los correos',
                        icon: 'warning'
                    });
                }
            });
        } else {
            swal({
                title: 'Error',
                text: 'Es necesario complementar la información solicitada',
                icon: 'warning'
            });
        }
    });

    function checkCorreo(id) {
        if (!$("#checkCorreo" + id).is(":checked")) {
            $("#correoValidar" + id).prop("disabled", false);
        } else {
            $("#correoValidar" + id).prop("disabled", true);
        }
    }

    function validarCorreos() {
        var listaCorreos = [];
        var error = false;
        $.each($(".checkCorreo"), function (index, element) {
            var id = $(element).data('id');
            $("#correoValidar" + id).css("border-color", "");
            $("#rfcCurpValidar" + id).css("border-color", "");
            if ($(element).is(":checked")) {
                if($("#rfcCurpValidar"+id).val() != ""){
                    listaCorreos.push({
                        crearAcceso: true,
                        correo: "",
                        rfcCurp:$("#rfcCurpValidar"+id).val(),
                        parte_id: id
                    });
                }else{
                    error = true;
                    $("#rfcCurpValidar" + id).css("border-color", "red");
                }
            } else {
                if ($("#correoValidar" + id).val() != "" && $("#rfcCurpValidar"+id).val() != "") {
                    listaCorreos.push({
                        crearAcceso: false,
                        correo: $("#correoValidar" + id).val(),
                        rfcCurp:$("#rfcCurpValidar"+id).val(),
                        parte_id: id
                    });
                } else {
                    error = true;
                    $("#rfcCurpValidar" + id).css("border-color", "red");
                    $("#correoValidar" + id).css("border-color", "red");
                }
            }
        });
        var respuesta = new Array();
        respuesta.error = error;
        respuesta.listaCorreos = listaCorreos;
        return respuesta;
    }
    function cargarDocumentos() {
        $.ajax({
            url: "/solicitudes/documentos/" + $("#solicitud_id").val(),
            type: "GET",
            dataType: "json",
            async: true,
            success: function (data) {
                try {
                    var html = "";
                    $("#tbodyRatificacion").html("");
                    if (data != null && data != "") {
                        //Carga información en la ratificacion
                        $.each(data, function (key, value) {
                            if (value.documentable_type == "App\\Parte") {
                                // var parte = arraySolicitantes.find(x=>x.id == value.documentable_id);
                                // if(parte != undefined){
                                html += "<tr>";
                                html += "<td>" + value.parte + "</td>";
                                html += "<td>" + value.clasificacion_archivo.nombre + "</td>";
                                html += "<td><a class='btn btn-link' href='/api/documentos/getFile/" + value.uuid + "' target='_blank'>Revisar</a></td>";
                                html += "</tr>";
                                ratifican = true;
                                // }
                            }
                        });
                        $("#tbodyRatificacion").html(html);
                        // end carga ratificacion
                        var table = "";
                        var div = "";
                        $.each(data, function (index, element) {
                            div += '<div class="image gallery-group-1">';
                            div += '    <div class="image-inner" style="position: relative;">';
                            if (element.tipo == 'pdf' || element.tipo == 'PDF') {
                                div += '            <a href="/api/documentos/getFile/' + element.uuid + '" data-toggle="iframe" data-gallery="example-gallery-pdf" data-type="url">';
                                div += '                <div class="img" align="center">';
                                div += '                    <i class="fa fa-file-pdf fa-4x" style="color:black;margin: 0;position: absolute;top: 50%;transform: translateX(-50%);"></i>';
                                div += '                </div>';
                                div += '            </a>';
                            } else {
                                div += '            <a href="/api/documentos/getFile/' + element.uuid + '" data-toggle="lightbox" data-gallery="example-gallery" data-type="image">';
                                div += '                <div class="img" style="background-image: url(\'/api/documentos/getFile/' + element.uuid + '\')"></div>';
                                div += '            </a>';
                            }
                            div += '            <p class="image-caption">';
                            div += '                ' + element.longitud + ' kb';
                            div += '            </p>';
                            div += '    </div>';
                            div += '    <div class="image-info">';
                            div += '            <h5 class="title">' + element.nombre_original + '</h5>';
                            div += '            <div class="desc">';
                            div += '                <strong>Documento: </strong>' + element.clasificacionArchivo.nombre;
                            div += element.descripcion + '<br>';
                            div += '            </div>';
                            div += '    </div>';
                            div += '</div>';
                        });
                        $("#gallery").html(div);
                    }
                } catch (error) {
                    console.log(error);
                }
            }
        });
    }
    var handleJqueryFileUpload = function () {
        // Initialize the jQuery File Upload widget:
        $('#fileupload').fileupload({
            autoUpload: false,
            disableImageResize: /Android(?!.*Chrome)|Opera/.test(window.navigator.userAgent),
            maxFileSize: 5000000,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png|pdf)$/i,
            messages: {
                acceptFileTypes: 'Archivo no permitido',
                maxFileSize: 'El archivo es muy pesado'
            },
            stop: function (e, data) {
                cargarDocumentos();
                //   $("#modal-archivos").modal("hide");
            }, uploadTemplate: function (o) {
                var rows = $();
                $.each(o.files, function (index, file) {
                    var row = $('<tr class="template-upload fade show">' +
                            '    <td>' +
                            '        <span class="preview"></span>' +
                            '    </td>' +
                            '    <td>' +
                            '        <div class="bg-light rounded p-10 mb-2">' +
                            '            <dl class="m-b-0">' +
                            '                <dt class="text-inverse">Nombre del documento:</dt>' +
                            '                <dd class="name">' + file.name + '</dd>' +
                            '                <dt class="text-inverse m-t-10">Tama&ntilde;o del archivo:</dt>' +
                            '                <dd class="size">Processing...</dd>' +
                            '            </dl>' +
                            '        </div>' +
                            '        <strong class="error text-danger h-auto d-block text-left"></strong>' +
                            '    </td>' +
                            '    <td>' +
                            '        <select class="form-control catSelectFile" name="tipo_documento_id[]">' +
                            '            <option value="">Seleccione una opci&oacute;n</option>' +
                            '            @if(isset($clasificacion_archivo))' +
                            '                @foreach($clasificacion_archivo as $clasificacion)' +
                            '                    @if($clasificacion->tipo_archivo_id == 1 || $clasificacion->tipo_archivo_id == 9)' +
                            '                    <option value="{{$clasificacion->id}}">{{$clasificacion->nombre}}</option>' +
                            '                    @endif' +
                            '                @endforeach' +
                            '            @endif' +
                            '        </select>' +
                            '    </td>' +
                            '    <td>' +
                            '        <select class="form-control catSelectFile parteClass" name="parte[]">' +
                            '            <option value="">Seleccione una opci&oacute;n</option>' +
                            '        </select>' +
                            '    </td>' +
                            '    <td>' +
                            '        <dl>' +
                            '            <dt class="text-inverse m-t-3">Progreso:</dt>' +
                            '            <dd class="m-t-5">' +
                            '                <div class="progress progress-sm progress-striped active rounded-corner"><div class="progress-bar progress-bar-primary" style="width:0%; min-width: 0px;">0%</div></div>' +
                            '            </dd>' +
                            '        </dl>' +
                            '    </td>' +
                            '    <td nowrap>' +
                            '            <button class="btn btn-primary start width-100 p-r-20 m-r-3" disabled>' +
                            '                <i class="fa fa-upload fa-fw text-inverse"></i>' +
                            '                <span>Guardar</span>' +
                            '            </button>' +
                            '    </td>' +
                            '    <td nowrap>' +
                            '            <button class="btn btn-default cancel width-100 p-r-20">' +
                            '                <i class="fa fa-trash fa-fw text-muted"></i>' +
                            '                <span>Cancelar</span>' +
                            '            </button>' +
                            '    </td>' +
                            '</tr>');
                    if (file.error) {
                        row.find('.error').text(file.error);
                    }
                    rows = rows.add(row);
                });
                return rows;
                // Uncomment the following to send cross-domain cookies:
                //xhrFields: {withCCOLOR_REDentials: true},
            }
        });

        // Enable iframe cross-domain access via COLOR_REDirect option:
        $('#fileupload').fileupload(
                'option',
                'COLOR_REDirect',
                window.location.href.replace(
                        /\/[^\/]*$/,
                        '/cors/result.html?%s'
                        )
                );

        // hide empty row text
        $('#fileupload').on('fileuploadsend', function (e, data) {

            // if(){
            //     e.preventDefault();
            // }
        })
        $('#fileupload').bind('fileuploadadd', function (e, data) {
            $('#fileupload [data-id="empty"]').hide();
            $(".catSelectFile").select2();
        });
        $('#fileupload').bind('fileuploaddone', function (e, data) {
            // console.log("add");
        });

        // show empty row text
        $('#fileupload').bind('fileuploadfail', function (e, data) {
            var rowLeft = (data['originalFiles']) ? data['originalFiles'].length : 0;
            if (rowLeft === 0) {
                $('#fileupload [data-id="empty"]').show();
            } else {
                $('#fileupload [data-id="empty"]').hide();
            }
        });

        // Upload server status check for browsers with CORS support:
        if ($.support.cors) {
            $.ajax({
                type: 'HEAD'
            }).fail(function () {
                $('<div class="alert alert-danger"/>').text('Upload server currently unavailable - ' + new Date()).appendTo('#fileupload');
            });
        }

        // Load & display existing files:
        $('#fileupload').addClass('fileupload-processing');
        $.ajax({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCCOLOR_REDentials: true},
            url: $('#fileupload').fileupload('option', 'url'),
            dataType: 'json',
            context: $('#fileupload')[0]
        }).always(function () {
            $(this).removeClass('fileupload-processing');
        }).done(function (result) {
            $(this).fileupload('option', 'done')
                    .call(this, $.Event('done'), {result: result});
        });
    };
    var handleIsotopesGallery = function () {
        var container = $('#gallery');
        $(window).on('resize', function () {
            var dividerValue = calculateDivider();
            var containerWidth = $(container).width();
            var columnWidth = containerWidth / dividerValue;
            $(container).isotope({
                masonry: {
                    columnWidth: columnWidth
                }
            });
        });
    };
    function calculateDivider() {
        var dividerValue = 4;
        if ($(this).width() <= 576) {
            dividerValue = 1;
        } else if ($(this).width() <= 992) {
            dividerValue = 2;
        } else if ($(this).width() <= 1200) {
            dividerValue = 3;
        }
        return dividerValue;
    }
    var FormMultipleUpload = function () {
        "use strict";
        return {
            //main function
            init: function () {
                handleJqueryFileUpload();
            }
        };
    }();
    var Gallery = function () {
        "use strict";
        return {
            //main function
            init: function () {
                handleIsotopesGallery();
            }
        };
    }();
    $("#excepcionForm").submit(function (e) {
        var falta = false;

        $(".fileGrupoVulnerable").each(function (e) {
            if ($(this).val() == "") {
                falta = true;
            }
        });
        if ($("#conciliador_excepcion_id").val() == "" && falta) {
            e.preventDefault();
        }
    });
    var listaContactos = [];
    function AgregarRepresentante(parte_id, tipoRepresentante) {
        $.ajax({
            url: "/partes/representante/" + parte_id,
            type: "GET",
            dataType: "json",
            success: function (data) {
                try {
                    if (data != null && data != "") {
                        data = data[0];
                        $("#tieneRepresentante" + parte_id).html("<i class='fa fa-check'></i> ");
                        $("#btnaddRep" + parte_id).html("Ver Representante");
                        $("#id_representante").val(data.id);
                        $("#curpRep").val(data.curp);
                        $("#nombreRep").val(data.nombre);
                        $("#primer_apellidoRep").val(data.primer_apellido);
                        $("#segundo_apellidoRep").val((data.segundo_apellido || ""));
                        $("#fecha_nacimientoRep").val(dateFormat(data.fecha_nacimiento, 4));
                        $("#genero_idRep").val(data.genero_id).trigger("change");
                        $("#clasificacion_archivo_id_representante").val(data.clasificacion_archivo_id).trigger('change');
                        $("#feha_instrumento").val(dateFormat(data.feha_instrumento, 4));
                        $("#detalle_instrumento").val(data.detalle_instrumento);
                        $("#parte_id").val(data.id);
                        listaContactos = data.contactos;
                        if (data.documentos && data.documentos.length > 0) {
                            $.each(data.documentos, function (index, doc) {
                                if (doc.tipo_archivo == 1) {
                                    $("#labelIdentifRepresentante").html("<b>Identificado con:</b> " + doc.descripcion);
                                    $("#tipo_documento_id").val(doc.clasificacion_archivo_id).trigger('change');
                                } else {
                                    $("#labelInstrumentoRepresentante").html("<b>Identificado con:</b> " + doc.descripcion);
                                    $("#clasificacion_archivo_id_representante").val(doc.clasificacion_archivo_id).trigger('change');
                                }
                            });

                        } else {
                            $("#tipo_documento_id").val("").trigger("change");
                            $("#labelIdentifRepresentante").html("");
                            $("#clasificacion_archivo_id_representante").val("").trigger('change');
                            $("#labelInstrumentoRepresentante").html("");
                        }
                    } else {
                        $("#id_representante").val("");
                        $("#curpRep").val("");
                        $("#nombreRep").val("");
                        $("#primer_apellidoRep").val("");
                        $("#segundo_apellidoRep").val("");
                        $("#fecha_nacimientoRep").val("");
                        $("#genero_idRep").val("").trigger("change");
                        $("#clasificacion_archivo_id_representante").val("").trigger('change');
                        $("#feha_instrumento").val("");
                        $("#detalle_instrumento").val("");
                        $("#parte_id").val("");
                        $("#tipo_documento_id").val("").trigger("change");
                        $("#labelIdentifRepresentante").html("");
                        listaContactos = [];
                    }
                    $("#tipo_contacto_id").val("").trigger("change");
                    $("#contacto").val("");
                    $("#parte_representada_id").val(parte_id);
                    if (tipoRepresentante == 1) {
                        $("#menorAlert").show();
                        $("#representanteMoral").hide();
                    } else {
                        $("#menorAlert").hide();
                        $("#representanteMoral").show();
                    }
                    cargarContactos();
                    $("#modal-representante").modal("show");
                } catch (error) {
                    console.log(error);
                }
            }
        });
    }
    function cargarGeneros() {
        $.ajax({
            url: "/generos",
            type: "GET",
            dataType: "json",
            success: function (data) {
                try {
                    $("#genero_idRep").html("<option value=''>-- Selecciona un género</option>");
                    if (data.data.length > 0) {
                        $.each(data.data, function (index, element) {
                            $("#genero_idRep").append("<option value='" + element.id + "'>" + element.nombre + "</option>");
                        });
                    }
                    $("#genero_idRep").trigger("change");
                } catch (error) {
                    console.log(error);
                }
            }
        });
    }

    function cargarTipoContactos() {
        $.ajax({
            url: "/tipos_contactos",
            type: "GET",
            global: false,
            dataType: "json",
            success: function (data) {
                if (data.data.total > 0) {
                    $("#tipo_contacto_id").html("<option value=''>-- Selecciona un tipo de contacto</option>");
                    $.each(data.data.data, function (index, element) {
                        $("#tipo_contacto_id").append("<option value='" + element.id + "'>" + element.nombre + "</option>");
                    });
                } else {
                    $("#tipo_contacto_id").html("<option value=''>-- Selecciona un tipo de contacto</option>");
                }
                $("#tipo_contacto_id").trigger("change");
            }
        });
    }
    $("#btnAgregarContacto").on("click", function () {
        if ($("#contacto").val() != "" && $("#tipo_contacto_id").val() != "") {
            var contactoVal = $("#contacto").val();
            if ($("#tipo_contacto_id").val() == 3) {
                if (!validateEmail(contactoVal)) {
                    swal({
                        title: 'Error',
                        text: 'El correo no tiene la estructura correcta',
                        icon: 'error',

                    });
                    return false;
                }

            } else {
                if (!/^[0-9]{10}$/.test(contactoVal)) {
                    swal({
                        title: 'Error',
                        text: 'El contacto debe tener 10 digitos de tipo numero',
                        icon: 'error',

                    });
                    return false;
                }
            }
            if ($("#parte_id").val() != "") {
                $.ajax({
                    url: "/partes/representante/contacto",
                    type: "POST",
                    global: false,
                    dataType: "json",
                    data: {
                        tipo_contacto_id: $("#tipo_contacto_id").val(),
                        contacto: $("#contacto").val(),
                        parte_id: $("#parte_id").val(),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (data) {
                        if (data != null && data != "") {
                            listaContactos = data;
                            cargarContactos();
                        } else {
                            swal({title: 'Error', text: 'Algo salió mal', icon: 'warning'});
                        }
                    }
                });
            } else {
                listaContactos.push({
                    tipo_contacto_id: $("#tipo_contacto_id").val(),
                    contacto: $("#contacto").val(),
                    id: null,
                    tipo_contacto: {
                        nombre: $("#tipo_contacto_id option:selected").text()
                    }
                });
            }
            cargarContactos();
            $("#contacto").val("");
            $("#tipo_contacto_id").val("").trigger("change");
        } else {
            swal({
                title: 'Error',
                text: 'Los campos Tipo de contacto y Contacto son obligatorios',
                icon: 'error',

            });
        }
    });
    function validarRepresentante() {
        var error = false;
        $(".control-label").css("color", "");
        if ($("#curpRep").val() == "") {
            $("#curpRep").prev().css("color", "red");
            error = true;
        }
        if ($("#nombreRep").val() == "") {
            $("#nombreRep").prev().css("color", "red");
            error = true;
        }
        if ($("#primer_apellidoRep").val() == "") {
            $("#primer_apellidoRep").prev().css("color", "red");
            error = true;
        }
        if ($("#fecha_nacimientoRep").val() == "") {
            $("#fecha_nacimientoRep").prev().css("color", "red");
            error = true;
        }
        if ($("#genero_idRep").val() == "") {
            $("#genero_idRep").prev().css("color", "red");
            error = true;
        }
        if ($("#clasificacion_archivo_id_representante").val() == "") {
            $("#clasificacion_archivo_id_representante").prev().css("color", "red");
            error = true;
        }
        if ($("#parte_id").val() == "") {
            if ($("#fileIdentificacion").val() == "") {
                $("#labelIdentificacion").css("color", "red");
                error = true;
            }
            if ($("#fileInstrumento").val() == "") {
                $("#labelInstrumento").css("color", "red");
                error = true;
            }
        }
        if ($("#fileIdentificacion").val() != "") {
            if ($("#tipo_documento_id").val() == "") {
                $("#tipo_documento_id").prev().css("color", "red");
                error = true;
            }
        }
        if ($("#fileInstrumento").val() != "") {
            if ($("#clasificacion_archivo_id_representante").val() == "") {
                $("#clasificacion_archivo_id_representante").prev().css("color", "red");
                error = true;
            }
        }
        if ($("#feha_instrumento").val() == "") {
            $("#feha_instrumento").prev().css("color", "red");
            error = true;
        }
        // console.log(listaContactos.length);
        if (listaContactos.length == 0) {
            $("#contacto").prev().css("color", "red");
            $("#tipo_contacto_id").prev().css("color", "red");
            error = true;
            error = true;
        }
        return error;
    }

    function cargarContactos() {
        var table = "";
        $.each(listaContactos, function (index, element) {
            table += '<tr>';
            table += '   <td>' + element.tipo_contacto.nombre + '</td>';
            table += '   <td>' + element.contacto + '</td>';
            table += '   <td style="text-align: center;">';
            table += '       <a class="btn btn-xs btn-warning" onclick="eliminarContacto(' + index + ')">'
            table += '           <i class="fa fa-trash" style="color:white;"></i>';
            table += '       </a>';
            table += '   </td>';
            table += '<tr>';
        });
        $("#tbodyContacto").html(table);
    }
    function eliminarContacto(indice){
            if(listaContactos[indice].id != null){
                $.ajax({
                    url:"/partes/representante/contacto/eliminar",
                    type:"POST",
                    dataType:"json",
                    data:{
                        contacto_id:listaContactos[indice].id,
                        parte_id:$("#parte_id").val(),
                        _token:"{{ csrf_token() }}"
                    },
                    success:function(response){
                        try{
                            if(response.success){
                                listaContactos = response.data;
                                cargarContactos();
                            }else{
                                swal({title: 'Error',text: 'Algo salió mal',icon: 'warning'});
                            }
                        }catch(error){
                            console.log(error);
                        }
                    }
                });
            }else{
                listaContactos.splice(indice,1);
                cargarContactos();
            }
        }

    $("#btnGuardarRepresentante").on("click", function () {
        if (!validarRepresentante()) {

            var formData = new FormData(); // Currently empty
            if ($("#fileIdentificacion").val() != "") {
                formData.append('fileIdentificacion', $("#fileIdentificacion")[0].files[0]);
            }
            if ($("#fileCedula").val() != "") {
                formData.append('fileCedula', $("#fileCedula")[0].files[0]);
            }
            if ($("#fileInstrumento").val() != "") {
                formData.append('fileInstrumento', $("#fileInstrumento")[0].files[0]);
            }
            formData.append('fuente_solicitud', true);
            formData.append('nombre', $("#nombreRep").val());
            formData.append('curp', $("#curpRep").val());
            formData.append('primer_apellido', $("#primer_apellidoRep").val());
            formData.append('segundo_apellido', $("#segundo_apellidoRep").val());
            formData.append('fecha_nacimiento', dateFormat($("#fecha_nacimientoRep").val()));
            formData.append('genero_id', $("#genero_idRep").val());
            formData.append('clasificacion_archivo_id', $("#clasificacion_archivo_id_representante").val());
            formData.append('feha_instrumento', dateFormat($("#feha_instrumento").val()));
            formData.append('detalle_instrumento', $("#detalle_instrumento").val());
            formData.append('parte_id', $("#parte_id").val());
            formData.append('parte_representada_id', $("#parte_representada_id").val());
            formData.append('solicitud_id', $("#solicitud_id").val());
            formData.append('tipo_documento_id', $("#tipo_documento_id").val());
            formData.append('listaContactos', JSON.stringify(listaContactos));
            formData.append('_token', "{{ csrf_token() }}");
            // {
            //     curp:$("#curp").val(),
            //     nombre:$("#nombre").val(),
            //     primer_apellido:$("#primer_apellido").val(),
            //     segundo_apellido:$("#segundo_apellido").val(),
            //     fecha_nacimiento:dateFormat($("#fecha_nacimiento").val()),
            //     genero_id:$("#genero_id").val(),
            //     clasificacion_archivo_id:$("#clasificacion_archivo_id_representante").val(),
            //     feha_instrumento:dateFormat($("#feha_instrumento").val()),
            //     detalle_instrumento:$("#detalle_instrumento").val(),
            //     parte_id:$("#parte_id").val(),
            //     parte_representada_id:$("#parte_representada_id").val(),
            //     audiencia_id:$("#audiencia_id").val(),
            //     listaContactos:listaContactos,
            //     _token:"{{ csrf_token() }}"
            // }
            $.ajax({
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    var progreso = 0;
                    // Download progress
                    xhr.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            // Do something with download progress
                            $('#progress-bar').show();
                            var percent = parseInt(percentComplete * 100)
                            $("#progressbar-ajax-value").text(percent + "%");
                            ;
                            $('#progressbar-ajax').css({
                                width: percent + '%'
                            });
                            if (percentComplete === 1) {
                                $('#progress-bar').hide();
                                $('#progressbar-ajax').css({
                                    width: '0%'
                                });
                            }
                        }
                    }, false);
                    // Upload progress
                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            //Do something with upload progress
                            $('#progress-bar').show();
                            var percent = parseInt(percentComplete * 100)
                            $("#progressbar-ajax-value").text(percent + "%");
                            ;
                            $('#progressbar-ajax').css({
                                width: percent + '%'
                            });
                            if (percentComplete === 1) {
                                $('#progress-bar').hide();
                                $('#progressbar-ajax').css({
                                    width: '0%'
                                });
                            }
                        }
                    }, false);
                    return xhr;
                },
                url: "/partes/representante",
                type: "POST",
                dataType: "json",
                processData: false,
                contentType: false,
                data: formData,
                success: function (data) {
                    try {
                        if (data != null && data != "") {
                            swal({title: 'ÉXITO', text: 'Se agregó el representante', icon: 'success'});
                            actualizarPartes();
                            cargarDocumentos();
                            limpiarRepresentante();
                            $("#modal-representante").modal("hide");
                        } else {
                            swal({title: 'Error', text: 'Algo salió mal', icon: 'warning'});
                        }
                    } catch (error) {
                        console.log(error);
                    }
                }, error: function (data) {
                    // console.log(data);
                    try {
                        swal({title: 'Error', text: 'Error al guardar representante', icon: 'warning'});
                    } catch (error) {
                        console.log(error);
                    }
                },
                error: function () {
                    swal({title: 'Error', text: 'No se pudo capturar el representante legal, revisa que el tamaño de tus documentos nos sea mayo a 10M ', icon: 'warning'});
                }
            });
        } else {
            swal({title: 'Error', text: 'Llena todos los campos', icon: 'warning'});
        }
    });
    function limpiarRepresentante(){
        $("#id_representante").val("");
        $("#curpRep").val("");
        $("#nombreRep").val("");
        $("#primer_apellidoRep").val("");
        $("#segundo_apellidoRep").val("");
        $("#fecha_nacimientoRep").val("");
        $("#genero_idRep").val("").trigger("change");
        $("#clasificacion_archivo_id_representante").val("").change();
        $("#feha_instrumento").val("");
        $("#detalle_instrumento").val("");
        $("#parte_id").val("");
        $("#tipo_documento_id").val("").trigger("change");
        $("#labelIdentifRepresentante").html("");
        $("#fileCedula").val("");
        $("#fileIdentificacion").val("");
        $("#fileInstrumento").val("");
        $("#labelCedula").html("");
        $("#tipo_contacto_id").val("").trigger("change");
        $("#contacto").val("");
        $("#modal-representante").modal("show");
        listaContactos = [];
        cargarContactos();
    }
    function actualizarPartes() {
        $.ajax({
            url: "/partes/getComboDocumentos/" + $("#solicitud_id").val(),
            type: "GET",
            dataType: "json",
            success: function (data) {
                try {
                    if (data != null && data != "") {
                        var html = "";
                        $('#fileupload').fileupload({
                            uploadTemplate: function (o) {
                                var rows = $();
                                $.each(o.files, function (index, file) {
                                    var html = '<tr class="template-upload fade show">' +
                                            '    <td>' +
                                            '        <span class="preview"></span>' +
                                            '    </td>' +
                                            '    <td>' +
                                            '        <div class="bg-light rounded p-10 mb-2">' +
                                            '            <dl class="m-b-0">' +
                                            '                <dt class="text-inverse">Nombre del documento:</dt>' +
                                            '                <dd class="name">' + file.name + '</dd>' +
                                            '                <dt class="text-inverse m-t-10">Tama&ntilde;o del archivo:</dt>' +
                                            '                <dd class="size">Processing...</dd>' +
                                            '            </dl>' +
                                            '        </div>' +
                                            '        <strong class="error text-danger h-auto d-block text-left"></strong>' +
                                            '    </td>' +
                                            '    <td>' +
                                            '        <select class="form-control catSelectFile" name="tipo_documento_id[]">' +
                                            '            <option value="">Seleccione una opci&oacute;n</option>' +
                                            '            @if(isset($clasificacion_archivo))' +
                                            '                @foreach($clasificacion_archivo as $clasificacion)' +
                                            '                    @if($clasificacion->tipo_archivo_id == 1 || $clasificacion->tipo_archivo_id == 9)' +
                                            '                    <option value="{{$clasificacion->id}}">{{$clasificacion->nombre}}</option>' +
                                            '                    @endif' +
                                            '                @endforeach' +
                                            '            @endif' +
                                            '        </select>' +
                                            '    </td>' +
                                            '    <td>' +
                                            '        <select class="form-control catSelectFile parteClass" name="parte[]">' +
                                            '            <option value="">Seleccione una opci&oacute;n</option>';
                                    $.each(data, function (index, element) {
                                        if (element.tipo_persona_id == 1 && element.tipo_parte_id == 1) {
                                            html += '<option value="' + element.id + '">' + element.nombre + ' ' + element.primer_apellido + ' ' + (element.segundo_apellido || "") + '</option>';
                                        }
                                        // else{
                                        //     html +='<option value="'+element.id+'">'+element.nombre_comercial+'</option>';
                                        //     // html +='<option value="'+element.id+'">'+element.nombre_comercial+'</option>';
                                        // }
                                    });
                                    html += ' </select>' +
                                            '    </td>' +
                                            '    <td>' +
                                            '        <dl>' +
                                            '            <dt class="text-inverse m-t-3">Progreso:</dt>' +
                                            '            <dd class="m-t-5">' +
                                            '                <div class="progress progress-sm progress-striped active rounded-corner"><div class="progress-bar progress-bar-primary" style="width:0%; min-width: 0px;">0%</div></div>' +
                                            '            </dd>' +
                                            '        </dl>' +
                                            '    </td>' +
                                            '    <td nowrap>' +
                                            '            <button class="btn btn-primary start width-100 p-r-20 m-r-3" disabled>' +
                                            '                <i class="fa fa-upload fa-fw text-inverse"></i>' +
                                            '                <span>Guardar</span>' +
                                            '            </button>' +
                                            '    </td>' +
                                            '    <td nowrap>' +
                                            '            <button class="btn btn-default cancel width-100 p-r-20">' +
                                            '                <i class="fa fa-trash fa-fw text-muted"></i>' +
                                            '                <span>Cancelar</span>' +
                                            '            </button>' +
                                            '    </td>' +
                                            '</tr>';
                                    var row = $(html);
                                    if (file.error) {
                                        row.find('.error').text(file.error);
                                    }
                                    rows = rows.add(row);
                                });
                                return rows;
                            }
                        });
                    } else {
                        swal({title: 'Error', text: 'Algo salió mal', icon: 'warning'});
                    }
                } catch (error) {
                    console.log(error);
                }
            }
        });
    }
    $("#btnGuardarConvenio").on("click",function(){
        if("{{auth()->user()->hasRole('Personal conciliador')}}"){
            if(ratifican){
                // if($("#aceptar_notif_buzon").is(":checked")){
                    $.ajax({
                        url: '/solicitud/correos/' + $("#solicitud_id").val(),
                        type: 'GET',
                        dataType: "json",
                        async: true,
                        success: function (data) {
                            try {

                                if (data == null || data == "") {
                                    $("#modal-aviso-resolucion-inmediata").modal("show");
                                } else { //si parte no proporciono correo
                                    var tableSolicitantes = '';
                                    $.each(data, function (index, element) {
                                        tableSolicitantes += '<tr>';
                                        if (element.tipo_persona_id == 1) {
                                            tableSolicitantes += '<td>' + element.nombre + ' ' + element.primer_apellido + ' ' + (element.segundo_apellido || "") + '</td>';
                                        } else {
                                            tableSolicitantes += '<td>' + element.nombre_comercial + '</td>';
                                        }
                                        tableSolicitantes += '  <td>';
                                        tableSolicitantes += '      <div class="col-md-12">';
                                        tableSolicitantes += '          <span class="text-muted m-l-5 m-r-20" for="checkCorreo' + element.id + '">Proporcionar accesos</span>';
                                        tableSolicitantes += '          <input type="checkbox" class="checkCorreo" data-id="' + element.id + '" checked="checked" id="checkCorreo' + element.id + '" name="checkCorreo' + element.id + '" onclick="checkCorreo(' + element.id + ')"/>';
                                        tableSolicitantes += '      </div>';
                                        tableSolicitantes += '  </td>';
                                        tableSolicitantes += '  <td>';
                                    if(element.tipo_persona_id == 1){
                                        tableSolicitantes += '      <input type="text" class="form-control upper" value="'+(element.curp || '') +'" id="rfcCurpValidar'+element.id+'">';
                                    }else{
                                        tableSolicitantes += '      <input type="text" class="form-control upper" value="'+(element.rfc || '') +'" id="rfcCurpValidar'+element.id+'">';
                                    }
                                    tableSolicitantes += '  </td>';
                                        tableSolicitantes += '  <td>';
                                        tableSolicitantes += '      <input type="text" class="form-control" disabled="disabled" id="correoValidar' + element.id + '">';
                                        tableSolicitantes += '  </td>';
                                        tableSolicitantes += '</tr>';
                                    });
                                    $("#tableSolicitantesCorreo tbody").html(tableSolicitantes);
                                    $("#modal-registro-correos").modal("show");
                                }
                            } catch (error) {
                                console.log(error);
                            }
                        }
                    });
                // }else{
                //     $("#modal-aviso-resolucion-inmediata").modal("show");
                // }
            } else {
                swal({
                    title: 'Error',
                    text: 'Al menos un solicitante debe presentar documentos para confirmar',
                    icon: 'warning'
                });
            }
        }else{
            swal({
                title: 'Error',
                text: 'La confirmaci&oacute;n de esta solicitud solo se puede realizar por el conciliador que la llevará acabo',
                icon: 'warning'
            });
        }
    });
    /**
     *  Aqui comienzan las funciones para carga de documentos de la solicitud
     */
    $("#btnAgregarArchivo").on("click", function () {
        $("#btnCancelFiles").click();
        $("#modal-archivos").modal("show");
    });
    var arraySolicitados = []; //Lista de citados
    var arraySolicitantes = []; //Lista de solicitantes
    var arrayDomiciliosSolicitante = []; // Array de domicilios para el solicitante
    var arrayDomiciliosSolicitado = []; // Array de domicilios para el citado
    var arrayObjetoSolicitudes = []; // Array de objeto_solicitude para el citado
    var solicitudObj = {}; // Array de objeto_solicitude para el citado
    var ratifican = false;
    ; // Array de solicitante excepción

    function getSolicitudFromBD(solicitud) {
        arraySolicitados = []; //Lista de citados
        arraySolicitantes = []; //Lista de solicitantes
        arrayDomiciliosSolicitante = []; // Array de domicilios para el solicitante
        arrayDomiciliosSolicitado = []; // Array de domicilios para el citado
        arrayObjetoSolicitudes = []; // Array de objeto_solicitude para el citado
        solicitudObj = {}; // Array de objeto_solicitude para el citado
        ratifican = false;
        ; // Array de solicitante excepción
        esSindical = false;
        $.ajax({
            url: '/solicitudes/' + solicitud,
            type: "GET",
            dataType: "json",
            async: false,
            data: {},
            success: function (data) {
                try {
                    $("#datosIdentificacionSolicitado").show();
                    arraySolicitados = Object.values(data.solicitados);
                    // formarTablaSolicitado();
                    arraySolicitantes = Object.values(data.solicitantes);
                    $.each(arraySolicitantes, function (key, value) {
                        if ($.isArray(arraySolicitantes[key].dato_laboral)) {
                            arraySolicitantes[key].dato_laboral = arraySolicitantes[key].dato_laboral[0];
                        }
                    })
                    solicitudObj.geolocalizable = true;
                    $.each(arraySolicitados, function (key, value) {
                        if ($.isArray(value.domicilios)) {
                            if (value.domicilios[0].latitud == "" || value.domicilios[0].longitud == "" || value.domicilios[0].latitud == null || value.domicilios[0].latitud == null) {
                                solicitudObj.geolocalizable = false;
                            }
                        } else {
                            solicitudObj.geolocalizable = false;
                        }
                    })
                    // formarTablaSolicitante();
                    $.each(data.objeto_solicitudes, function (key, value) {
                        var objeto_solicitud = {};
                        objeto_solicitud.id = value.id;
                        objeto_solicitud.objeto_solicitud_id = value.pivot.objeto_solicitud_id.toString();
                        objeto_solicitud.nombre = value.nombre;
                        objeto_solicitud.activo = 1;
                        arrayObjetoSolicitudes.push(objeto_solicitud);
                    });
                    // arrayObjetoSolicitudes = data.objeto_solicitudes;
                    // formarTablaObjetoSol();
                    solicitudObj.ratificada = data.ratificada;
                    solicitudObj.fecha_ratificacion = dateFormat(data.fecha_ratificacion, 2);
                    solicitudObj.fecha_recepcion = dateFormat(data.fecha_recepcion, 2);
                    solicitudObj.fecha_conflicto = dateFormat(data.fecha_conflicto, 4);
                    solicitudObj.giro_comercial_id = data.giro_comercial_id;
                    solicitudObj.giro_comercial = data.giro_comercial.nombre;
                    solicitudObj.ambito_id = data.giro_comercial.ambito_id;
                    solicitudObj.ambito_nombre = data.giro_comercial.ambito.nombre;
                    solicitudObj.tipo_solicitud_id = data.tipo_solicitud_id;
                    $("#url_virtual").val("");
                    if (data.virtual) {
                        $("#confirmacion_virtual").show();
                        $("#div_confirmacion").hide();
                        $("#btnVirtual").hide();
                        if (data.url_virtual != "" && data.url_virtual != null) {
                            $("#url_virtual").val(data.url_virtual);
                            $("#div_confirmacion").show();
                            $("#btnVirtual").show();

                        }
                    } else {
                        $("#div_confirmacion").show();
                        $("#confirmacion_virtual").hide();
                        $("#btnVirtual").show();
                    }
                    if (solicitudObj.tipo_solicitud_id == 3 || solicitudObj.tipo_solicitud_id == 4) {
                        esSindical = true;
                    }

                    cargarGeneros();
                    cargarTipoContactos();
                } catch (error) {
                    console.log(error);
                }
            }
        });
    }
    function consultarSolicitud(solicitud_id) {
        $("#divSolicitudMod").html("");
        $("#divSolicitantesMod").html("");
        $("#divCitadosMod").html("");
        getSolicitudFromBD(solicitud_id);
        $("#solicitud_id").val(solicitud_id);
        $("#solicitud_id_modal").val(solicitud_id);
        $("#modalSolicitud").modal('show');

        var htmlSolicitud = formatoSolicitud();
        var htmlSolicitantes = formarSolicitantes();
        var htmlCitados = formarCitados();
        $("#divSolicitudMod").html(htmlSolicitud);
        $("#divSolicitantesMod").html(htmlSolicitantes);
        $("#divCitadosMod").html(htmlCitados);
    }

    function getParteCurp(curp) {
        if (validaCURP(curp) && $("#id_representante").val() == "") {
            $.ajax({
                url: "/partes/getParteCurp",
                type: "POST",
                dataType: "json",
                data: {
                    curp: curp,
                    _token: "{{ csrf_token() }}"
                },
                async: true,
                success: function (data) {
                    try {

                        if (data != null && data != "") {
                            $("#nombreRep").val(data.nombre);
                            $("#primer_apellidoRep").val(data.primer_apellido);
                            $("#segundo_apellidoRep").val(data.segundo_apellido);
                            $("#fecha_nacimientoRep").val(dateFormat(data.fecha_nacimiento, 4));
                            $("#genero_idRep").val(data.genero_id).trigger("change");
                        }
                    } catch (error) {
                        console.log(error);
                    }
                }
            });
        }
    }


    $(".fecha").datetimepicker({format: "DD/MM/YYYY"});
    $("#fileIdentificacion").change(function (e) {
        $("#labelIdentifRepresentante").html("<b>Archivo: </b>" + e.target.files[0].name + "");
    });
    $("#fileCedula").change(function (e) {
        $("#labelCedula").html("<b>Archivo: </b>" + e.target.files[0].name + "");
    });
    $("#fileInstrumento").change(function (e) {
        $("#labelInstrumentoRepresentante").html("<b>Archivo: </b>" + e.target.files[0].name + "");
    });
    $("#btnFinalizarRatificacion").on("click", function () {
        location.href = "solicitudes/consulta/" + $("#solicitud_id").val();
    });
    $('.upper').on('keyup', function () {
        var valor = $(this).val();
        $(this).val(valor.toUpperCase());
    });
    $('input[type=radio][name=aradioNotificacion1]').change(function () {
        if ($("#aradioNotificacionB2").is(":checked")) {
            $("#divFechaCita").show();
        } else {
            $("#divFechaCita").hide();

        }
    });
    $(".dateBirth").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: "c-80:",
        format: 'dd/mm/yyyy',
    });

    // $("#estatus_solicitud_id").change(function () {
    //     var estatus = $(this).val();
    //     $(".estatus").removeClass('selectedButton');
    //     $("#estatus" + estatus).addClass('selectedButton');
    // });
    $(".estatus").click(function(){
        $(".estatus").removeClass('selectedButton');
        $(this).addClass('selectedButton');
    });
    function guardarUrlVirtual() {
        if ($("#url_virtual").val() != "") {
            $.ajax({
                url: "/guardarUrlVirtual",
                type: "POST",
                dataType: "json",
                data: {
                    url_virtual: $("#url_virtual").val(),
                    solicitud_id: $("#solicitud_id").val(),
                    _token: "{{ csrf_token() }}"
                },
                async: true,
                success: function (data) {
                    try {
                        if (data.success) {
                            swal({title: 'Éxito', text: 'Url guardada correctamente', icon: 'success'});
                            $("#div_confirmacion").show();
                            $("#btnVirtual").show();
                        } else {
                            swal({title: 'Error', text: 'No se pudo guardar la url', icon: 'error'});
                        }
                    } catch (error) {
                        console.log(error);
                    }
                }
            });
        } else {
            swal({title: 'Error', text: 'Es necesario ingresar la url', icon: 'error'});
        }
    }

</script>
@endpush
