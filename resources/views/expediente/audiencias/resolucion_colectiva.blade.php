@extends('layouts.default')

@section('title', 'Audiencia Colectiva')

@include('includes.component.datatables')
@include('includes.component.pickers')
@include('includes.component.calendar')
@include('includes.component.dropzone')

@push('styles')
<style>
    .fc-event{
        height:60px !important;
    }
    .ui-accordion-content{
        height:100% !important;
    }
    .card-header{
        border: 1px solid #9c2449 !important;
        background: #9c2449 !important;
        color: white !important;
        font-size: 65% !important;
        padding: 4px !important;
        width: 100%;
        text-align: left !important;
    }
    .amount{
        text-align: right;
    }

    .upper{
        text-transform: uppercase;
    }
    .needed:after {
        color:darkred;
        content: " (*)";
    }
    #ui-datepicker-div {z-index:9999 !important}
</style>
@endpush

@section('content')
<!-- begin breadcrumb -->
<div class="col-md-12">
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:;">Audiencias</a></li>
        <li class="breadcrumb-item active">Patron/Sindicato</li>
    </ol>
</div>
<!-- end breadcrumb -->
<!-- begin page-header -->

<!-- end page-header -->
<div class="panel panel-default col-md-12">
    <div class="panel-heading">
        @if($solicitud->tipo_solicitud_id == 2 )
            <h2 class="">Audiencia / Patron Individual</small></h2>
        @elseif($solicitud->tipo_solicitud_id == 3)
            <h2 > Audiencia / Patron Colectiva</small></h2>
        @elseif($solicitud->tipo_solicitud_id == 4)
            <h2 > Audiencia / Sindicato </small></h2>
        @endif
    </div>
    <div class="panel-body">
       
    </div>
</div>

<h2>Comparecientes</h2>
<div class="col-md-8 offset-2 " id="contentCompareciente">
    <table style="font-size: small;" class="table table-striped table-bordered table-td-valign-middle">
        <thead>
            <tr>
                <th class="text-nowrap">Tipo Parte</th>
                <th class="text-nowrap">Nombre de la parte</th>
                <th class="text-nowrap" >Accion</th>
            </tr>
        </thead>
        <tbody>
        @foreach($audiencia->partes as $parte)
            @if($parte->tipo_parte_id != 3)
                <tr>
                    <td class="text-nowrap">{{ $parte->tipoParte->nombre }}</td>
                    @if($parte->tipo_persona_id == 1)
                        <td class="text-nowrap">{{ $parte->nombre }} {{ $parte->primer_apellido }} {{ $parte->segundo_apellido }}</td>
                    @else
                        <td class="text-nowrap">{{ $parte->nombre_comercial }}</td>
                    @endif
                    <td>
                        @if(($parte->tipo_persona_id == 2) || ($parte->tipo_parte_id == 2 && $parte->tipo_persona_id == 1))
                        <div class="md-2" style="display: inline-block;">
                            <button onclick="AgregarRepresentante({{$parte->id}})" class="btn btn-xs btn-primary btnAgregarRepresentante" title="Agregar Representante Legal" data-toggle="tooltip" data-placement="top">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                        @endif
                    </td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
    <button class="btn btn-primary" align="center" id="btnCargarComparecientes">Continuar </button>
</div>
@if($audiencia->finalizada == false)
<div style="display: none;" id="divAudienciaColectiva">
    <div id="divAudiencia" style="display: none">
        <h2>Audiencia</h2>
        <input type="hidden"  name='audiencia_id' value='{{(isset($audiencia->id))? $audiencia->id:''}}' />
        <input type="hidden" name='solicitud_id' value='{{(isset($solicitud->id))? $solicitud->id:''}}' />
        <div class="row" style="margin-top: 5%;">
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <table style="width: 100%; border-collapse: collapse; margin-left: auto; margin-right: auto;" border="0">
                    <tbody>
                    <tr>
                    <td class="celda-logo" style="width: 35.1477%;"><img src="/assets/img/logo/LOGO_cfcrl.png" height="70" /></td>
                    <td class="celda-centro" style="width: 13.3335%;">&nbsp;</td>
                    <td class="celda-derecha" style="width: 51.5187%; text-align: center;">&nbsp;</td>
                    </tr>
                    </tbody>
                </table>
                <div class="header_document">
                   {!!isset($plantilla['plantilla_header']) ? $plantilla['plantilla_header']: "" !!}
                </div>
                <div id="audiencia_body" name="audiencia_body" class="sectionPlantilla" style="border:solid 2px lightgray; max-height:600px; height:600px; overflow: scroll;" contenteditable="true">{!! isset($plantilla['plantilla_body']) ? $plantilla['plantilla_body'] : "<br><br>" !!}</div>
            </div>
            <div class="col-md-2"></div>
        </div> 
        <div class="col-md-offset-3 col-md-6 ">
            <div class="form-group">
                <label for="resolucion_id" class="col-sm-6 control-label">Resolución</label>
                <div class="col-sm-10">
                    {!! Form::select('resolucion_id', isset($resoluciones) ? $resoluciones : [] ,isset($audiencia->resolucion_id) ? $audiencia->resolucion_id :null , ['id'=>'resolucion_id', 'required','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                </div>
            </div>
        </div>
    </div> 
    <div id="divHuboConvenio" style="display: none">
        <h2>Convenio</h2>
        <div class="row" style="margin-top: 5%;">
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <table style="width: 100%; border-collapse: collapse; margin-left: auto; margin-right: auto;" border="0">
                    <tbody>
                    <tr>
                    <td class="celda-logo" style="width: 35.1477%;"><img src="/assets/img/logo/LOGO_cfcrl.png" height="70" /></td>
                    <td class="celda-centro" style="width: 13.3335%;">&nbsp;</td>
                    <td class="celda-derecha" style="width: 51.5187%; text-align: center;">&nbsp;</td>
                    </tr>
                    </tbody>
                </table>
                <div class="header_document">
                    {!!isset($plantilla['plantilla_header']) ? $plantilla['plantilla_header']: "" !!}
                </div>
                <div id="convenio_body" name="convenio_body" class="sectionPlantilla" style="border:solid 1px lightgray; max-height:600px; height:600px; overflow: scroll;" contenteditable="true"></div>
            </div>
            <div class="col-md-2"></div>
        </div> 
    </div>

    <div id="divNoComparece" style="display: none">
        <h2>No comparecencia</h2>
        <div class="row" style="margin-top: 5%;">
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <table style="width: 100%; border-collapse: collapse; margin-left: auto; margin-right: auto;" border="0">
                    <tbody>
                    <tr>
                    <td class="celda-logo" style="width: 35.1477%;"><img src="/assets/img/logo/LOGO_cfcrl.png" height="70" /></td>
                    <td class="celda-centro" style="width: 13.3335%;">&nbsp;</td>
                    <td class="celda-derecha" style="width: 51.5187%; text-align: center;">&nbsp;</td>
                    </tr>
                    </tbody>
                </table>
                <div class="header_document">
                    {!!isset($plantilla['plantilla_header']) ? $plantilla['plantilla_header']: "" !!}
                 </div>
                <div id="no_comparece_body" name="no_comparece_body" class="sectionPlantilla" style="border:solid 1px lightgray; max-height:500px; height:500px; overflow: scroll;" contenteditable="true">{!! isset($plantilla['plantilla_body']) ? $plantilla['plantilla_body'] : "<br><br>" !!}</div>
            </div>
            <div class="col-md-2"></div>
        </div> 
    </div>

    <div class="text-right" style="margin-right: 1%; margin-top:2%;">
        <button class="btn btn-primary btn-sm m-l-5" onclick="finalizar()" id='btnGuardarResolucion'><i class="fa fa-save"></i> Guardar</button>
    </div>
    
</div>

    <!-- Inicio Modal de comparecientes y resolución individual-->
<div class="modal" id="modal-comparecientes" data-backdrop="static" data-keyboard="false" style="display:none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Comparecientes</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered" >
                        <thead>
                            <tr>
                                <th>Tipo Parte</th>
                                <th>Nombre</th>
                                <th>Primer Apellido</th>
                                <th>Segundo Apellido</th>
                                <th>Comparecio</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyPartesFisicas">
                        </tbody>
                    </table>
                    <button class="btn btn-primary btn-sm m-l-5" id='btnAgregarArchivo'><i class="fa fa-plus"></i> Agregar documento</button>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <button class="btn btn-danger btn-borrar" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary" id="btnGuardarComparecientes">Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- inicio Modal cargar archivos-->
<div class="modal" id="modal-archivos" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Documentos de identificaci&oacute;n</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form id="fileupload" action="/api/comparecientes/documentos" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="audiencia_id_comparece" name="audiencia_idC[]" value="{{$audiencia->id}}" />
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
                                    <td colspan="4" class="text-center text-muted p-t-30 p-b-30">
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
                    <a class="btn btn-primary btn-sm" data-dismiss="modal" onclick="continuarComparecencia()"><i class="fa fa-sign-out"></i> Continuar a Comparecencias</a>
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-sign-out"></i> Cerrar</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="display:none;">
    <div class="text-right">
        <button class="btn btn-primary btn-sm m-l-5" id='btnAgregarArchivo'><i class="fa fa-plus"></i> Agregar documento</button>
    </div>
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
                            <dt class="text-inverse m-t-10">Tama&ntilde;o del archivo::</dt>
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
                                <option value="{{$clasificacion->id}}">{{$clasificacion->nombre}}</option>
                            @endforeach
                        @endif
                    </select>
                </td>
                <td>
                    <select class="form-control catSelectFile parte_relacionada" id="parte_relacionada" name="parte[]">
                        <option value="">Seleccione una opci&oacute;n</option>
                        @if(isset($audiencia->partes))
                            @foreach($audiencia->partes as $parte)
                                @if($parte->tipo_persona_id == 1)
                                    <option value="{{$parte->id}}">{{$parte->nombre_comercial}}{{$parte->nombre}} {{$parte->primer_apellido}} {{$parte->segundo_apellido}}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </td>
                <td>
                    <dl>
                        <dt class="text-inverse m-t-3">Progress:</dt>
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
                            <span>Canceal</span>
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
                            <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}" class="rounded"></a>
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
                            <dt class="text-inverse m-t-10">Tama&ntilde;o del archivo::</dt>
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
<!-- Fin Modal de cargar archivos-->
<!--inicio modal para representante legal-->
<div class="modal" id="modal-representante" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Representante legal</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h5>Datos del Representante legal</h5>
                <input type="hidden" id="id_representante">
                <div class="col-md-12 row">
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <label for="curp" class="control-label needed">CURP</label>
                            <input type="text" id="curp" maxlength="18" onblur="getParteCurp(this.value)" class="form-control upper" placeholder="CURP del representante legal">
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <label for="nombre" class="control-label needed">Nombre</label>
                            <input type="text" id="nombre" class="form-control upper" placeholder="Nombre del representante legal">
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <label for="primer_apellido" class="control-label needed">Primer apellido</label>
                            <input type="text" id="primer_apellido" class="form-control upper" placeholder="Primer apellido del representante">
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <label for="segundo_apellido" class="control-label">Segundo apellido</label>
                            <input type="text" id="segundo_apellido" class="form-control upper" placeholder="Segundo apellido representante">
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <label for="fecha_nacimiento" class="control-label needed">Fecha de nacimiento</label>
                            <input type="text" id="fecha_nacimiento" class="form-control fecha" placeholder="Fecha de nacimiento del representante">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="genero_id" class="col-sm-6 control-label needed">Género</label>
                        <select id="genero_id" class="form-control select-element">
                            <option value="">-- Selecciona un género</option>
                        </select>
                    </div>
                    <div class="col-md-12 row">
                        <div class="col-md-6">
                            <label class=" needed">Documento de identificaci&oacute;n</label> 
                            <span class="btn btn-primary fileinput-button m-r-3">
                                <i class="fa fa-fw fa-plus"></i>
                                <span>Seleccionar identificaci&oacute;n</span>
                                <input type="file" id="fileIdentificacion" name="files">
                            </span>
                            <p style="margin-top: 1%;" id="labelIdentifRepresentante"></p>
                        </div>
                        <div class="col-md-6">
                            <label for="clasificacion_archivo_id_representante" class="control-label  needed">Tipo de documento</label>
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
                            <label >Cedula Profesional</label>
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
                <h5>Datos de comprobante como representante legal</h5>
                <div class="col-md-12 row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="clasificacion_archivo_id_representante" class="control-label needed">Instrumento</label>
                            <select id="clasificacion_archivo_id_representante" class="form-control select-element">
                                <option value="">-- Selecciona un instrumento</option>
                                @foreach($clasificacion_archivos_Representante as $clasificacion)
                                <option value="{{$clasificacion->id}}">{{$clasificacion->nombre}}</option>
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
                        <label class=" needed">Documento de Instrumento</label> 
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
                        <select id="tipo_contacto_id" class="form-control select-element">
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
                                <th style="width:20%; text-align: center;">Accion</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyContacto">
                        </tbody>
                    </table>
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
<!--Fin de modal de representante legal-->
<!--inicio modal para representante legal-->
<div class="modal" id="modal-dato-laboral" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Datos Laborales</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12 row" id="datosBasicos">
                    <input type="hidden" id="dato_laboral_id">
                    <input type="hidden" id="resolucion_dato_laboral">
                    <input type="hidden" id="giro_comercial_hidden">
                    
                    <div class="col-md-6">
                        <input class="form-control numero" maxlength="11" minlength="11" length="11" data-parsley-type='integer' id="nss" placeholder="N&uacute;mero de seguro social"  type="text" value="">
                        <p class="help-block ">N&uacute;mero de seguro social</p>
                    </div>
                    <div class="col-md-12 row">
                        <div class="col-md-6">
                            <input class="form-control upper" required id="puesto" placeholder="Puesto" type="text" value="">
                            <p class="help-block needed">Puesto</p>
                        </div>
                        <div class="col-md-6" >
                            {!! Form::select('ocupacion_id', isset($ocupaciones) ? $ocupaciones : [] , null, ['id'=>'ocupacion_id','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                            {!! $errors->first('ocupacion_id', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block ">&iquest;En caso de desempeñar un oficio que cuenta con salario mínimo distinto al general, escoja del catálogo. Si no, deja vacío.</p>
                        </div>
                        {{-- <div class="col-md-4">
                            <input class="form-control numero" data-parsley-type='integer' id="no_issste" placeholder="No. ISSSTE"  type="text" value="">
                            <p class="help-block">No. ISSSTE</p>
                        </div> --}}
                    </div>
                    <div class="col-md-12 row">
                        <div class="col-md-4">
                            <input class="form-control numero requiredLaboral" required data-parsley-type='number' id="remuneracion" max="99999999" placeholder="¿Cu&aacute;nto te pagan?" type="text" value="">
                            <p class="help-block needed">&iquest;Cu&aacute;nto te pagan?</p>
                        </div>
                        <div class="col-md-4">
                            {!! Form::select('periodicidad_id', isset($periodicidades) ? $periodicidades : [] , null, ['id'=>'periodicidad_id','placeholder' => 'Seleccione una opción','required', 'class' => 'form-control catSelect requiredLaboral']);  !!}
                            {!! $errors->first('periodicidad_id', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block needed">&iquest;Cada cuándo te pagan?</p>
                        </div>
                        <div class="col-md-4">
                            <input class="form-control numero requiredLaboral" required data-parsley-type='integer' id="horas_semanales" placeholder="Horas semanales" type="text" value="">
                            <p class="help-block needed">Horas semanales</p>
                        </div>
                    </div>
                    <div class="col-md-12 row">

                        <div class="col-md-2">
                            <span class="text-muted m-l-5 m-r-20" for='switch1'>Labora actualmente</span>
                        </div>
                        <div class="col-md-2">
                            <input type="hidden" />
                            <input type="checkbox" value="1" data-render="switchery" data-theme="default" id="labora_actualmente" name='labora_actualmente'/>
                        </div>
                        <div class="col-md-4">
                            <input class="form-control requiredLaboral" required id="fecha_ingreso" placeholder="Fecha de ingreso" type="text" value="">
                            <p class="help-block needed">Fecha de ingreso</p>
                        </div>
                        <div class="col-md-4" id="divFechaSalida">
                            <input class="form-control requiredLaboral" required id="fecha_salida" placeholder="Fecha salida" type="text" value="">
                            <p class="help-block needed">Fecha salida</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        {!! Form::select('jornada_id', isset($jornadas) ? $jornadas : [] , null, ['id'=>'jornada_id','placeholder' => 'Seleccione una opción','required', 'class' => 'form-control catSelect datoLaboral']);  !!}
                        {!! $errors->first('jornada_id', '<span class=text-danger>:message</span>') !!}
                        <p class="help-block needed">Jornada</p>
                    </div>
                </div>
                <hr>
                <div class="col-md-12 row" id="datosExtras" style="display:none">
                    <div class="col-md-12 row">
                        <div class="col-md-4">
                            <input class="form-control datoLaboralExtra" id="horario_laboral" placeholder="HH:MM a HH:MM" type="text" value="">
                            <p class="help-block needed">Horario laboral</p>
                        </div>
                        <div class="col-md-4">
                            <input class="form-control datoLaboralExtra" id="horario_comida" placeholder="HH:MM a HH:MM" type="text" value="">
                            <p class="help-block needed">Horario de comida</p>
                        </div>
                        <div class="col-md-4">
                            <input type="checkbox" value="1"  class="datoLaboralExtra" data-render="switchery" data-theme="default" id="comida_dentro" name='comida_dentro'/>
                            <p class="help-block needed">Comida dentro de las instalaciones</p>
                        </div>
                    </div>
                    <div class="col-md-12 row">
                        <div class="col-md-4">
                            <input class="form-control datoLaboralExtra" id="dias_descanso" placeholder="N d&iacute;as, los cuales correspond&iacute;an a dddd" type="text" value="">
                            <p class="help-block needed">Indica número y días de descanso semanal</p>
                        </div>
                        <div class="col-md-4">
                            <input class="form-control numero datoLaboralExtra" required data-parsley-type='integer' id="dias_vacaciones" placeholder="Días de vacaciones por año" type="text" value="">
                            <p class="help-block needed">Días de vacaciones</p>
                        </div>
                        <div class="col-md-4">
                            <input class="form-control date datoLaboralExtra" data-parsley-type='integer' id="dias_aguinaldo" placeholder="Días de aguinaldo por año" type="text" value="">
                            <p class="help-block needed">Días de aguinaldo</p>
                        </div>
                    </div>
                    <div class="col-md-12 row">
                        <div class="col-md-12">
                            <input class="form-control date datoLaboralExtra" id="prestaciones_adicionales" placeholder="Prestaciones adicionales" type="text" value="">
                            <p class="help-block needed">Otras prestaciones en especie (bonos, vales de despensa, seguros de gastos médicos mayores etc)</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5" id="btnGuardarDatoLaboral"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Fin de modal de representante legal-->
<!-- modal de relaciones bilaterales-->
<div class="modal" id="modal-relaciones" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Relaciones binarias</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <hr>
                <h5>Registro de resoluciones homologadas</h5>
                <div class="col-md-12 row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="parte_solicitante_id" class="col-sm-6 control-label labelResolucion">Solicitante</label>
                            <div class="col-sm-10">
                                <select id="parte_solicitante_id" class="form-control select-element">
                                    <option value="">-- Selecciona un solicitante</option>
                                    @foreach($audiencia->solicitantes as $parte)
                                        @if($parte->parte->tipo_persona_id == 1)
                                            <option value="{{ $parte->parte->id }}">{{ $parte->parte->nombre }} {{ $parte->parte->primer_apellido }} {{ $parte->parte->segundo_apellido }}</option>
                                        @else
                                            <option value="{{ $parte->parte->id }}">{{ $parte->parte->nombre_comercial }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="parte_solicitado_id" class="col-sm-6 control-label labelResolucion">Citado</label>
                            <div class="col-sm-10">
                                <select id="parte_solicitado_id" class="form-control select-element">
                                    <option value="">-- Selecciona un citado</option>
                                    @foreach($audiencia->solicitados as $parte)
                                        @if($parte->parte->tipo_persona_id == 1)
                                            <option value="{{ $parte->parte->id }}">{{ $parte->parte->nombre }} {{ $parte->parte->primer_apellido }} {{ $parte->parte->segundo_apellido }}</option>
                                        @else
                                            <option value="{{ $parte->parte->id }}">{{ $parte->parte->nombre_comercial }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" id="divMotivoArchivo" style="display: none;">
                        <div class="form-group">
                            <label for="motivo_archivado_id" class="col-sm-6 control-label labelResolucion">Motivo de archivo</label>
                            <div class="col-sm-10">
                                <select id="motivo_archivado_id" class="form-control select-element">
                                    <option value="">-- Selecciona un motivo de archivado</option>
                                    @foreach($motivos_archivo as $motivo)
                                        <option value="{{ $motivo->id }}">{{ $motivo->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="col-md-12">
                        <div class="text-center">
                            <button class="btn btn-warning text-white btn-sm" id='btnAgregarResolucion'><i class="fa fa-plus"></i> Agregar</button>
                        </div>
                    </div>
                    <div class="col-md-12 row" style="padding-top: 1em">
                        <table class="table table-bordered" >
                            <thead>
                                <tr>
                                    <th>Solicitante</th>
                                    <th>Citado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyResolucionesIndividuales">
                            </tbody>
                        </table>
                    </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</a>
                    <button class="btn btn-warning btn-sm m-l-5" id="btnGuardarResolucionMuchas"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Fin de modal de relaciones bilaterales-->
<div class="modal" id="modal-ratificacion-success" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Audiencia generada</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-muted">
                    <p>
                        Debido a que no se presento uno o varios citados se generó una nueva audiencia con la siguiente información
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
                            <strong>Hora de termino: </strong><span id="spanHoraFin"></span><br>
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

<input type="hidden" id="parte_id">
<input type="hidden" id="parte_representada_id">
<input type="hidden" id="audiencia_id" value='{{(isset($audiencia->id))? $audiencia->id:''}}' />
<input type="hidden" id="solicitud_id" value='{{(isset($solicitud->id))? $solicitud->id:''}}' />
@else
<div class="row" style="margin: 5%;">
    <div class="col-md-12">
        <h1>Esta audiencia ya fue concluida</h1>
    </div>
    
    <button onclick='window.location = "/audiencias/{{ $audiencia->id }}/edit";' class="btn btn-primary btn-lg" title="Continuar"><i class="fa fa-arrow-right"></i> Continuar</button>
</div>
@endif

@endsection
@push('scripts')
<script src='/js/tinymce/tinymce.min.js'></script>
<script>
    var listaContactos = [];
    var listaResolucionesIndividuales = [];
    var noComparece = false;
    $("#resolucion_id").change(function(){
        if($(this).val() == 1){
            $("#divHuboConvenio").show();
        }else{
            $("#divHuboConvenio").hide();
        }
    });

    config_tmce = function(selector) {
        return {
            auto_focus: 'plantilla-body',
            selector: selector,
            language: 'es_MX',
            
            language_url: '/js/tinymce/languages/es_MX.js',
            inline: false,
            menubar: false,
            toolbar_items_size: 'large',
            plugins: [
                'noneditable advlist autolink lists link image imagetools preview',
                ' media table paste pagebreak'
            ],
            toolbar1: 'basicDateButton | mybutton | fontselect fontsizeselect | undo redo ' +
            '| bold italic underline| alignleft aligncenter alignright alignjustify | bullist numlist ' +
            '| outdent indent | link unlink image | table pagebreak forecolor backcolor',
            toolbar2: "",
            image_title: true,
            automatic_uploads: true,
            file_picker_types: 'image',
            font_formats: 'Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva',
            paste_as_text: true,
            file_picker_callback: function (cb, value, meta) {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.onchange = function () {
                    var file = this.files[0];
                    var reader = new FileReader();
                    reader.onload = function () {
                        var id = 'blobid' + (new Date()).getTime();
                        var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                        var base64 = reader.result.split(',')[1];
                        var blobInfo = blobCache.create(id, file, base64);
                        blobCache.add(blobInfo);
                        cb(blobInfo.blobUri(), {title: file.name});
                    };
                    reader.readAsDataURL(file);
                };
                input.click();
            },
            setup: function (editor) {
                editor.on('init', function (ed) {
                    ed.target.editorCommands.execCommand("fontName", false, "Arial");
                });
                // editor.ui.registry.addButton('mybutton', {
                //   text: 'My Custom Button',
                //   onAction: () => alert('Button clicked!')
                // });
            }
        };
    };
    tinymce.init(config_tmce('#convenio_body'));
    tinymce.init(config_tmce('#audiencia_body'));
    tinymce.init(config_tmce('#no_comparece_body'));

    $("#btnCargarComparecientes").on("click",function(){
        $.ajax({
            url:"/audiencia/validar_partes/{{$audiencia->id}}",
            type:"GET",
            dataType:"json",
            success:function(data){
                // console.log(data.pasa);
                if(data.pasa){
                    getPersonasComparecer();
                }else{
                    swal({
                        title: '¿Ya capturaste todos los representantes?',
                        text: 'Recuerde capturar a los representantes legales comparecientes para que aparezcan en la lista',
                        icon: '',
                        // showCancelButton: true,
                        buttons: {
                            cancel: {
                                text: 'No',
                                value: null,
                                visible: true,
                                className: 'btn btn-default',
                                closeModal: true
                            },
                            confirm: {
                                text: 'Sí',
                                value: true,
                                visible: true,
                                className: 'btn btn-warning',
                                closeModal: true
                            }
                        }
                    }).then(function(isConfirm){
                        if(isConfirm){
                            getPersonasComparecer();
                        }
                    });
                }
            }
        });
    });

    $("#btnGuardarComparecientes").on("click",function(){
        var validacion = validarResolucionComparecientes();
        if(!validacion.error){
            $.ajax({
                url:"/audiencia/comparecientes",
                type:"POST",
                dataType:"json",
                async:true,
                data:{
                    audiencia_id:'{{ $audiencia->id }}',
                    comparecientes:validacion.comparecientes,
                    _token:"{{ csrf_token() }}"
                },
                success:function(data){
                    $("#modal-comparecientes").modal("hide");
                    if(data.data.tipo == 1){
                        swal({
                            title: 'Éxito',
                            text: 'Se ha archivado la audiencia por falta de solicitantes',
                            icon: 'success',
                            buttons: {
                                confirm: {
                                    text: 'Aceptar',
                                    value: true,
                                    visible: true,
                                    className: 'btn btn-warning',
                                    closeModal: true
                                }
                            }
                        }).then(function(isConfirm){
                            window.location.href = "/audiencias/"+data.data.response.id+"/edit";
                        });
                    }else if(data.data.tipo == 2){
                        swal({
                            title: 'Éxito',
                            text: 'Se ha finalizado la audiencia, se generaron las actas de no conciliación y las actas de multa para los citados que no acudieron',
                            icon: 'success',
                            buttons: {
                                confirm: {
                                    text: 'Aceptar',
                                    value: true,
                                    visible: true,
                                    className: 'btn btn-warning',
                                    closeModal: true
                                }
                            }
                        }).then(function(isConfirm){
                            $("#btnFinalizarRatificacion").click();
                        });
                    }else if(data.data.tipo == 3){
                        $("#spanFolio").text(data.data.response.folio+"/"+data.data.response.anio);
                        $("#spanFechaAudiencia").text(dateFormat(data.data.response.fecha_audiencia,4));
                        $("#spanHoraInicio").text(data.data.response.hora_inicio);
                        $("#spanHoraFin").text(data.data.response.hora_fin);
                        var table="";
                        if(data.data.response.multiple){
                            $.each(data.data.response.conciliadores_audiencias,function(index,element){
                                table +='<tr>';
                                if(element.solicitante){
                                    table +='   <td>Solicitante(s)</td>';
                                }else{
                                    table +='   <td>Citado(s)</td>';
                                }
                                table +='   <td>'+element.conciliador.persona.nombre+' '+element.conciliador.persona.primer_apellido+' '+element.conciliador.persona.segundo_apellido+'</td>';
                                $.each(data.data.response.salas_audiencias,function(index2,element2){
                                    if(element2.solicitante == element.solicitante){
                                        table +='<td>'+element2.sala.sala+'</td>';
                                    }
                                });
                                table +='</tr>';
                            });
                        }else{
                            table +='<tr>';
                            table +='   <td>Solicitante(s) y citado(s)</td>';
                            table +='   <td>'+data.data.response.conciliadores_audiencias[0].conciliador.persona.nombre+' '+data.data.response.conciliadores_audiencias[0].conciliador.persona.primer_apellido+' '+data.data.response.conciliadores_audiencias[0].conciliador.persona.segundo_apellido+'</td>';
                            table +='   <td>'+data.data.response.salas_audiencias[0].sala.sala+'</td>';
                            table +='</tr>';
                        }
                        $("#tableAudienciaSuccess tbody").html(table);
                        $("#modalRatificacion").modal("hide");
                        $("#modal-ratificacion-success").modal({backdrop: 'static', keyboard: false});
                    }else if(data.data.tipo == 4){
                        swal({
                            title: 'Éxito',
                            text: 'Se han registrado los comparecientes',
                            icon: 'success'
                        });
                        cargarComparecientes();
//                        startTimer();
//                        nextStep(1);
                    }else if(data.data.tipo == 5){
                        swal({
                            title: '¿Qué deseas hacer?',
                            text: 'Detectamos que no todos los citados comparecieron, ¿Deseas continuar con el proceso de audiencia?, de indicar que no, se generará una nueva audiencia',
                            icon: 'info',
                            buttons: {
                                cancel: {
                                    text: 'Cancelar',
                                    value: null,
                                    visible: true,
                                    className: 'btn btn-default',
                                    closeModal: true,
                                },roll: {
                                    text: "No",
                                    value: 2,
                                    className: 'btn btn-warning',
                                    visible: true,
                                    closeModal: true
                                },confirm: {
                                    text: 'Si',
                                    value: 1,
                                    visible: true,
                                    className: 'btn btn-danger',
                                    closeModal: true
                                }
                            }
                        }).then(function(tipo){
                            if(tipo != null){
                                if(tipo == 1){
                                    cargarComparecientes();
//                                    startTimer();
//                                    nextStep(1);
                                }else if(tipo == 2){
                                    SolicitarNuevaAudiencia();
                                }
                            }
                        });
                    }else if(data.data.tipo == 6){
                        swal({
                            title: 'Error',
                            text: 'Esta audiencia ya fue finalizada',
                            icon: 'error',
                            buttons: {
                                confirm: {
                                    text: 'Aceptar',
                                    value: true,
                                    visible: true,
                                    className: 'btn btn-warning',
                                    closeModal: true
                            }
                            }
                        }).then(function(isConfirm){
                            $("#btnFinalizarRatificacion").click();
                        });
                    }
                },
                error:function(data){
                    swal({
                        title: 'Algo salió mal',
                        text: 'No se guardo el registro',
                        icon: 'warning'
                    });
                }
            });
        }else{
            swal({
                title: '¿Estás seguro?',
                text: 'No has seleccionado ningun compareciente, el expediente se archivará por no comparecencia del solicitante',
                icon: 'info',
                buttons: {
                    cancel: {
                        text: 'Cancelar',
                        value: null,
                        visible: true,
                        className: 'btn btn-default',
                        closeModal: true,
                    },confirm: {
                        text: 'Continuar',
                        value: 1,
                        visible: true,
                        className: 'btn btn-danger',
                        closeModal: true
                    }
                }
            }).then(function(tipo){
                if(tipo != null){
                    $.ajax({
                        url:"/audiencia/comparecientes",
                        type:"POST",
                        dataType:"json",
                        async:true,
                        data:{
                            audiencia_id:'{{ $audiencia->id }}',
                            comparecientes:validacion.comparecientes,
                            _token:"{{ csrf_token() }}"
                        },
                        success:function(data){
                            $("#modal-comparecientes").modal("hide");
                            if(data.data.tipo == 1){
                                swal({
                                    title: 'Éxito',
                                    text: 'Se ha archivado la audiencia por falta de solicitantes',
                                    icon: 'success',
                                    buttons: {
                                        confirm: {
                                            text: 'Aceptar',
                                            value: true,
                                            visible: true,
                                            className: 'btn btn-warning',
                                            closeModal: true
                                        }
                                    }
                                }).then(function(isConfirm){
                                    window.location.href = "/audiencias/"+data.data.response.id+"/edit";
                                });
                            }
                        },
                        error:function(data){
                            swal({
                                title: 'Algo salió mal',
                                text: 'No se guardo el registro',
                                icon: 'warning'
                            });
                        }
                    });
                }
            });
        }
    });
    function cargarComparecientes(){
        $.ajax({
            url:"/audiencia/comparecientes/{{ $audiencia->id }}",
            type:"GET",
            dataType:"json",
            success:function(data){
                
                if(data.length > 0){
                    $("#parte_solicitante_id").empty();
                    $("#parte_solicitado_id").empty();
                    var html="<table class='table table-bordered table-striped table-hover'>";
                    var htmlSolicitantes = "<option value=''>Seleccione una opci&oacute;n</option>";
                    var htmlCitados = "<option value=''>Seleccione una opci&oacute;n</option>";
                    html +='<tr>';
                    html +='    <th>Tipo de parte</th>';
                    html +='    <th>Nombre</th>';
                    html +='    <th>Curp</th>';
                    html +='    <th>Es representante</th>';
                    html +='</tr>';
                    $.each(data,function(index,element){
                        html +='<tr>';
                        html +='    <td>'+element.parte.tipoParte+'</td>';
                        html +='    <td>'+element.parte.nombre+' '+element.parte.primer_apellido+' '+(element.parte.segundo_apellido || "")+'</td>';
                        html +='    <td>'+element.parte.curp+'</td>';
                        if(element.parte.tipo_parte_id == 1){
                            htmlSolicitantes += "<option value='"+element.id+"'>"+element.parte.nombre+' '+element.parte.primer_apellido+' '+(element.parte.segundo_apellido || "")+"</option>"
                        }else if(element.parte.tipo_parte_id == 2){
                            htmlCitados += "<option value='"+element.id+"'>"+element.parte.nombre+' '+element.parte.primer_apellido+' '+(element.parte.segundo_apellido || "")+"</option>"
                        }

                        if(element.parte.tipo_parte_id == 3 && element.parte.parte_representada_id != null){
                            if(element.parte.parteRepresentada.tipo_parte_id == 1){
                                if(element.parte.parteRepresentada.tipo_persona_id == 1){
                                    htmlSolicitantes += "<option value='"+element.parte.parteRepresentada.id+"'>"+element.parte.parteRepresentada.nombre+' '+element.parte.parteRepresentada.primer_apellido+' '+(element.parte.parteRepresentada.segundo_apellido || "")+"</option>";
                                }else{
                                    htmlSolicitantes += "<option value='"+element.parte.parteRepresentada.id+"'>"+element.parte.parteRepresentada.nombre_comercial+"</option>";
                                }
                            }else{
                                if(element.parte.parteRepresentada.tipo_persona_id == 1){
                                    htmlCitados += "<option value='"+element.parte.parteRepresentada.id+"'>"+element.parte.parteRepresentada.nombre+' '+element.parte.parteRepresentada.primer_apellido+' '+(element.parte.parteRepresentada.segundo_apellido || "")+"</option>";
                                }else{
                                    htmlCitados += "<option value='"+element.parte.parteRepresentada.id+"'>"+element.parte.parteRepresentada.nombre_comercial+"</option>";

                                }
                            }
                            if(element.parte.parteRepresentada.tipo_persona_id == 1){
                                html +='<td>Si ('+element.parte.parteRepresentadanombre+' '+element.parte.parteRepresentada.primer_apellido+' '+(element.parte.parteRepresentada.segundo_apellido || '')+')</td>';
                                
                            }else{
                                html +='<td>Si ('+element.parte.parteRepresentada.nombre_comercial+')</td>';
                                
                            }
                        }else{
                            html +='<td>No</td>';
                        }
                        html +='</tr>';
                    });
                    html +='</table>';
                    $("#contentCompareciente").html(html);
                    $("#parte_solicitante_id").html(htmlSolicitantes);
                    $("#parte_solicitado_id").html(htmlCitados);
                    $("#divAudienciaColectiva").show();
                    $("#btnCargarComparecientes").hide();
                    if(data[0].citados && data[0].solicitantes){
                        $("#divAudiencia").show();
                    }else{
                        if(!data[0].solicitantes){
                            noComparece = true;
                            $("#divNoComparece").show();
                        }else{
                            // window.location = "/audiencias/{{ $audiencia->id }}/edit";
                        }
                    }
                }
            }
        });
    }
    function validarResolucionComparecientes(){
        var listaComparecientes = [];
        $(".checkCompareciente").each(function(index){
            if($(this).is(":checked")){
                listaComparecientes.push($(this).data("parte_id"));
            }
        });
        if(listaComparecientes.length > 0){
            return {error:false,comparecientes:listaComparecientes};
        }else{
//            swal({title: 'Error',text: 'No has agregado comparecientes',icon: 'warning'});
            return {error:true,comparecientes:listaComparecientes};
        }
    }

    // Funciones para representante legal(Etapa 1)
    function getPersonasComparecer(){
        $.ajax({
            url:"/audiencia/fisicas/{{ $audiencia->id }}",
            type:"GET",
            dataType:"json",
            success:function(data){
                var table = "";
                var options = "<option value=''>Seleccione una opci&oacute;n</option>";
                $.each(data, function(index,element){
                    table +='<tr>';
                    table +='   <td>'+element.tipo_parte.nombre+'</td>';
                    table +='   <td>'+element.nombre+'</td>';
                    table +='   <td>'+element.primer_apellido+'</td>';
                    table +='   <td>'+(element.segundo_apellido || "")+'</td>';
                    if(element.documentos.length >= 1){
                        table +='   <td>';
                        table +='       <div class="col-md-2">';
                        table +='           <input type="checkbox" value="1" data-parte_id="'+element.id+'" class="checkCompareciente" name="switch1"/>';
                        table +='</div>';
                    }else{
                        table +='   <td>Sin identificación</td>';
                    }
                    table +='   </td>';
                    table +='</tr>';
                    options += '<option value="'+element.id+'">'+element.nombre+' '+element.primer_apellido+' '+element.segundo_apellido+'</option>';
                });
                $("#parte_relacionada").empty();
                console.log(options);
                $("#parte_relacionada").html(options);
                $("#tbodyPartesFisicas").html(table);
                $("#resolucionVarias").hide();
                $("#btnCancelarVarias").hide();
                $("#btnConfigurarResoluciones").show();
                $("#btnGuardarResolucionUna").show();
                $("#modal-comparecientes").modal("show");
            }
        });
    }

    function continuarComparecencia(){
        getPersonasComparecer();
        $("#modal-comparecientes").modal("show");
    }

    function AgregarRepresentante(parte_id){
        $.ajax({
            url:"/partes/representante/"+parte_id,
            type:"GET",
            dataType:"json",
            success:function(data){
                if(data != null && data != ""){
                    data = data[0];
                    $("#id_representante").val(data.id);
                    $("#curp").val(data.curp);
                    $("#nombre").val(data.nombre);
                    $("#primer_apellido").val(data.primer_apellido);
                    $("#segundo_apellido").val(data.segundo_apellido);
                    $("#fecha_nacimiento").val(dateFormat(data.fecha_nacimiento,4));
                    $("#genero_id").val(data.genero_id).trigger("change");
                    $("#clasificacion_archivo_id_representante").val(data.clasificacion_archivo_id).trigger('change');
                    $("#feha_instrumento").val(dateFormat(data.feha_instrumento,4));
                    $("#detalle_instrumento").val(data.detalle_instrumento);
                    $("#parte_id").val(data.id);
                    listaContactos = data.contactos;
                    if(data.documentos && data.documentos.length > 0){
                        $.each(data.documentos,function(index,doc){
                            if(doc.tipo_archivo == 1){
                                if(doc.clasificacion_archivo_id == 3){
                                    $("#labelCedula").html("Cedula Profesional Capturada");
                                }else{
                                    $("#labelIdentifRepresentante").html("<b>Identificado con:</b> "+doc.descripcion);
                                    $("#tipo_documento_id").val(doc.clasificacion_archivo_id).trigger('change');
                                }
                            }else{
                                $("#labelInstrumentoRepresentante").html("<b>Identificado con:</b> "+doc.descripcion);
                                $("#clasificacion_archivo_id_representante").val(doc.clasificacion_archivo_id).trigger('change');
                            }
                        });
                        
                    }else{
                        $("#tipo_documento_id").val("").trigger("change");
                        $("#labelIdentifRepresentante").html("");
                        $("#clasificacion_archivo_id_representante").val("").trigger('change');
                        $("#labelInstrumentoRepresentante").html("");
                    }
                }else{
                    $("#id_representante").val("");
                    $("#curp").val("");
                    $("#nombre").val("");
                    $("#primer_apellido").val("");
                    $("#segundo_apellido").val("");
                    $("#fecha_nacimiento").val("");
                    $("#genero_id").val("").trigger("change");
                    $("#clasificacion_archivo_id_representante").val("").change();
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
                cargarContactos();
                $("#modal-representante").modal("show");
            }
        });
    }
    function actualizarPartes(){
            $.ajax({
                url:"/partes/getComboDocumentos/{{isset($solicitud->id) ? $solicitud->id: '' }}",
                type:"GET",
                dataType:"json",
                success:function(data){
                    if(data != null && data != ""){
                        var html="";
                        $('#fileupload').fileupload({
                            uploadTemplate: function (o) {
                                var rows = $();
                                $.each(o.files, function (index, file) {

                                    var html= '<tr class="template-upload fade show">'+
                                    '    <td>'+
                                    '        <span class="preview"></span>'+
                                    '    </td>'+
                                    '    <td>'+
                                    '        <div class="bg-light rounded p-10 mb-2">'+
                                    '            <dl class="m-b-0">'+
                                    '                <dt class="text-inverse">Nombre del documento:</dt>'+
                                    '                <dd class="name">'+file.name+'</dd>'+
                                    '                <dt class="text-inverse m-t-10">Tama&ntilde;o del archivo::</dt>'+
                                    '                <dd class="size">Processing...</dd>'+
                                    '            </dl>'+
                                    '        </div>'+
                                    '        <strong class="error text-danger h-auto d-block text-left"></strong>'+
                                    '    </td>'+
                                    '    <td>'+
                                    '        <select class="form-control catSelectFile" name="tipo_documento_id[]">'+
                                    '            <option value="">Seleccione una opci&oacute;n</option>'+
                                    '            @if(isset($clasificacion_archivo))'+
                                    '                @foreach($clasificacion_archivo as $clasificacion)'+
                                    '                    @if($clasificacion->tipo_archivo_id == 1 || $clasificacion->tipo_archivo_id == 9)'+
                                    '                    <option value="{{$clasificacion->id}}">{{$clasificacion->nombre}}</option>'+
                                    '                    @endif'+
                                    '                @endforeach'+
                                    '            @endif'+
                                    '        </select>'+
                                    '    </td>'+
                                    '    <td>'+
                                    '        <select class="form-control catSelectFile parteClass" name="parte[]">'+
                                    '            <option value="">Seleccione una opci&oacute;n</option>'+
                                    '            @if(isset($solicitud->id))';
                                    $.each(data, function(index,element){
                                        console.log(element);
                                        if(element.tipo_persona_id == 1){
                                            html +='<option value="'+element.id+'">'+element.nombre+' '+element.primer_apellido+' '+(element.segundo_apellido|| "")+'</option>';
                                        }
                                        // else{
                                        //     html +='<option value="'+element.id+'">'+element.nombre_comercial+'</option>';
                                        //     // html +='<option value="'+element.id+'">'+element.nombre_comercial+'</option>';
                                        // }
                                    });
                                    html +='    @endif'+
                                    '        </select>'+
                                    '    </td>'+
                                    '    <td>'+
                                    '        <dl>'+
                                    '            <dt class="text-inverse m-t-3">Progress:</dt>'+
                                    '            <dd class="m-t-5">'+
                                    '                <div class="progress progress-sm progress-striped active rounded-corner"><div class="progress-bar progress-bar-primary" style="width:0%; min-width: 0px;">0%</div></div>'+
                                    '            </dd>'+
                                    '        </dl>'+
                                    '    </td>'+
                                    '    <td nowrap>'+
                                    '            <button class="btn btn-primary start width-100 p-r-20 m-r-3" disabled>'+
                                    '                <i class="fa fa-upload fa-fw text-inverse"></i>'+
                                    '                <span>Guardar</span>'+
                                    '            </button>'+
                                    '    </td>'+
                                    '    <td nowrap>'+
                                    '            <button class="btn btn-default cancel width-100 p-r-20">'+
                                    '                <i class="fa fa-trash fa-fw text-muted"></i>'+
                                    '                <span>Cancelar</span>'+
                                    '            </button>'+
                                    '    </td>'+
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
                }else{
                    swal({title: 'Error',text: 'Algo salió mal',icon: 'warning'});
                }
            }
        });
    }
    function cargarContactos(){
        var table = "";
        $.each(listaContactos, function(index,element){
            table +='<tr>';
            table +='   <td>'+element.tipo_contacto.nombre+'</td>';
            table +='   <td>'+element.contacto+'</td>';
            table +='   <td style="text-align: center;">';
            table +='       <a class="btn btn-xs btn-warning" onclick="eliminarContacto('+index+')">'
            table +='           <i class="fa fa-trash" style="color:white;"></i>';
            table +='       </a>';
            table +='   </td>';
            table +='<tr>';
        });
        $("#tbodyContacto").html(table);
    }
    function cargarGeneros(){
        $.ajax({
            url:"/generos",
            type:"GET",
            dataType:"json",
            success:function(data){
                $("#genero_id").html("<option value=''>-- Selecciona un género</option>");
                if(data.data.length > 0){
                    $.each(data.data,function(index,element){
                        $("#genero_id").append("<option value='"+element.id+"'>"+element.nombre+"</option>");
                    });
                }
                $("#genero_id").trigger("change");
            }
        });
    }
    function cargarTipoContactos(){
        $.ajax({
            url:"/tipos_contactos",
            type:"GET",
            dataType:"json",
            success:function(data){
                if(data.data.total > 0){
                    $("#tipo_contacto_id").html("<option value=''>-- Selecciona un tipo de contacto</option>");
                    $.each(data.data.data,function(index,element){
                        $("#tipo_contacto_id").append("<option value='"+element.id+"'>"+element.nombre+"</option>");
                    });
                }else{
                    $("#tipo_contacto_id").html("<option value=''>-- Selecciona un tipo de contacto</option>");
                }
                $("#tipo_contacto_id").trigger("change");
            }
        });
    }
    $("#btnAgregarContacto").on("click",function(){
        if($("#contacto").val() != "" && $("#tipo_contacto_id").val() != ""){
            var contactoVal = $("#contacto").val();
            if($("#tipo_contacto_id").val() == 3){
                if(!validateEmail(contactoVal)){
                    swal({
                        title: 'Error',
                        text: 'El correo no tiene la estructura correcta',
                        icon: 'error',

                    });
                    return false;
                }

            }else{
                if(!/^[0-9]{10}$/.test(contactoVal)){
                    swal({
                        title: 'Error',
                        text: 'El contacto debe tener 10 digitos de tipo numero',
                        icon: 'error',

                    });
                    return false;
                }
            }
            if($("#parte_id").val() != ""){
                $.ajax({
                    url:"/partes/representante/contacto",
                    type:"POST",
                    dataType:"json",
                    data:{
                        tipo_contacto_id:$("#tipo_contacto_id").val(),
                        contacto:$("#contacto").val(),
                        parte_id:$("#parte_id").val(),
                        _token:"{{ csrf_token() }}"
                    },
                    success:function(data){
                        if(data != null && data != ""){
                            listaContactos = data;
                            cargarContactos();
                        }else{
                            swal({title: 'Error',text: 'Algo salió mal',icon: 'warning'});
                        }
                    }
                });
            }else{
                listaContactos.push({
                    tipo_contacto_id:$("#tipo_contacto_id").val(),
                    contacto:$("#contacto").val(),
                    id:null,
                    tipo_contacto:{
                        nombre:$("#tipo_contacto_id option:selected").text()
                    }
                });
            }
            cargarContactos();
            $("#contacto").val("");
            $("#tipo_contacto_id").val("").trigger("change");
        }else{
            swal({
                title: 'Error',
                text: 'Los campos Tipo de contacto y Contacto son obligatorios',
                icon: 'error',

            });
        }
    });

    $("#btnAgregarArchivo").on("click",function(){
        $("#btnCancelFiles").click();
        $("#modal-archivos").modal("show");
    });

    $("#fileIdentificacion").change(function(e){
        $("#labelIdentifRepresentante").html("<b>Archivo: </b>"+e.target.files[0].name+"");
    });
    $("#fileCedula").change(function(e){
        $("#labelCedula").html("<b>Archivo: </b>"+e.target.files[0].name+"");
    });
    $("#fileInstrumento").change(function(e){
        $("#labelInstrumentoRepresentante").html("<b>Archivo: </b>"+e.target.files[0].name+"");
    });
    $("#btnGuardarRepresentante").on("click",function(){
        if(!validarRepresentante()){
            
            var formData = new FormData(); // Currently empty
            if($("#fileIdentificacion").val() != ""){
                formData.append('fileIdentificacion', $("#fileIdentificacion")[0].files[0]);
            }
            if($("#fileCedula").val() != ""){
                formData.append('fileCedula', $("#fileCedula")[0].files[0]);
            }
            if($("#fileInstrumento").val() != ""){
                formData.append('fileInstrumento', $("#fileInstrumento")[0].files[0]);
            }
            formData.append('nombre', $("#nombre").val());
            formData.append('curp', $("#curp").val());
            formData.append('primer_apellido', $("#primer_apellido").val());
            formData.append('segundo_apellido', $("#segundo_apellido").val());
            formData.append('fecha_nacimiento', dateFormat($("#fecha_nacimiento").val()));
            formData.append('genero_id', $("#genero_id").val());
            formData.append('clasificacion_archivo_id', $("#clasificacion_archivo_id_representante").val());
            formData.append('feha_instrumento', dateFormat($("#feha_instrumento").val()));
            formData.append('detalle_instrumento',$("#detalle_instrumento").val());
            formData.append('parte_id', $("#parte_id").val());
            formData.append('parte_representada_id', $("#parte_representada_id").val());
            formData.append('audiencia_id', $("#audiencia_id").val());
            formData.append('solicitud_id', '{{$solicitud->id}}');
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
                url:"/partes/representante",
                type:"POST",
                dataType:"json",
                processData: false,
                contentType: false,
                data:formData,
                success:function(data){
                    if(data != null && data != ""){
                        swal({title: 'ÉXITO',text: 'Se agregó el representante',icon: 'success'});
                        actualizarPartes();
                        $("#modal-representante").modal("hide");
                    }else{
                        swal({title: 'Error',text: 'Algo salió mal',icon: 'warning'});
                    }
                }
            });
        }else{
            swal({title: 'Error',text: 'Llena todos los campos',icon: 'warning'});
        }
    });
    function validarRepresentante(){
        var error=false;
        $(".control-label").css("color","");
        if($("#curp").val() == ""){
            $("#curp").prev().css("color","red");
            error = true;
        }
        if($("#nombre").val() == ""){
            $("#nombre").prev().css("color","red");
            error = true;
        }
        if($("#primer_apellido").val() == ""){
            $("#primer_apellido").prev().css("color","red");
            error = true;
        }
        if($("#fecha_nacimiento").val() == ""){
            $("#fecha_nacimiento").prev().css("color","red");
            error = true;
        }
        if($("#genero_id").val() == ""){
            $("#genero_id").prev().css("color","red");
            error = true;
        }
        if($("#clasificacion_archivo_id_representante").val() == ""){
            $("#clasificacion_archivo_id_representante").prev().css("color","red");
            error = true;
        }
        if($("#fileIdentificacion").val() != ""){
            if($("#tipo_documento_id").val() == "" ){
                $("#tipo_documento_id").prev().css("color","red");
                error = true;
            }
        }
        if($("#fileInstrumento").val() != ""){
            if($("#clasificacion_archivo_id_representante").val() == "" ){
                $("#clasificacion_archivo_id_representante").prev().css("color","red");
                error = true;
            }
        }
        if($("#feha_instrumento").val() == ""){
            $("#feha_instrumento").prev().css("color","red");
            error = true;
        }
        // console.log(listaContactos.length);
        if(listaContactos.length == 0){
            $("#contacto").prev().css("color","red");
            $("#tipo_contacto_id").prev().css("color","red");
            error = true;
            error = true;
        }
        return error;
    }

    function getParteCurp(curp){
        if(validaCURP(curp) && $("#id_representante").val() == ""){
            $.ajax({
                url:"/partes/getParteCurp",
                type:"POST",
                dataType:"json",
                data:{
                    curp:curp,
                    _token:"{{ csrf_token() }}"
                },
                async:true,
                success:function(data){
                    if(data != null && data != ""){
                        $("#nombre").val(data.nombre);
                        $("#primer_apellido").val(data.primer_apellido);
                        $("#segundo_apellido").val(data.segundo_apellido);
                        $("#fecha_nacimiento").val(dateFormat(data.fecha_nacimiento,4));
                        $("#genero_id").val(data.genero_id).trigger("change");
                    }
                }
            });
        }
    }

    $("#btnAgregarResolucion").on("click",function(){
        if(validarResolucionIndividual()){
            var motivo_id = "";
            var motivo_nombre = "";
            listaResolucionesIndividuales.push({
                parte_solicitante_id:$("#parte_solicitante_id").val(),
                parte_solicitante_nombre:$("#parte_solicitante_id option:selected").text(),
                parte_solicitado_id:$("#parte_solicitado_id").val(),
                parte_solicitado_nombre:$("#parte_solicitado_id option:selected").text(),
            });
            $("#parte_solicitante_id").val("").trigger("change");
            $("#parte_solicitado_id").val("").trigger("change");
            $("#motivo_archivado_id").val("").trigger("change");
    //                limpiarConcepto();
    //                cargarTablaConcepto();
            cargarTablaResolucionesIndividuales();
        }
    });

    function validarResolucionIndividual(){
        var error = true;
        $(".labelResolucion").css("color","");
        if($("#parte_solicitante_id").val() == ""){
            error = false;
            $("#parte_solicitante_id").parent().prev().css("color","red");
        }
        if($("#parte_solicitado_id").val() == ""){
            error = false;
            $("#parte_solicitado_id").parent().prev().css("color","red");
        }
        $.each(listaResolucionesIndividuales,function(i,e){
            if(e.parte_solicitante_id == $("#parte_solicitante_id").val() && e.parte_solicitado_id == $("#parte_solicitado_id").val()){
                error = false;
                swal({title: 'Error',text: 'Ya has agregado esta relacion',icon: 'error'});
                return false;
            }
        });
        return error;
    }
    function cargarTablaResolucionesIndividuales(){
        var table = '';
        $.each(listaResolucionesIndividuales,function(i,e){
            table +='<tr>';
                // table +='<td>'+e.parte_solicitante_nombre+'</td>';
                table +='<td>'+e.parte_solicitado_nombre+'</td>';
                table +='<td>'+e.parte_solicitado_nombre+'</td>';
                table +='<td>';
                    table +='<button onclick="eliminarRelacion('+i+')" class="btn btn-xs btn-warning" title="Eliminar">';
                        table +='<i class="fa fa-trash"></i>';
                    table +='</button>';
                table +='</td>';
            table +='</tr>';
        });
        $("#tbodyResolucionesIndividuales").html(table);
    }
    function eliminarRelacion(indice){
        // console.log(indice);
        listaResolucionesIndividuales.splice(indice,1);
        cargarTablaResolucionesIndividuales();
    }

    function cargarModalRelaciones(){
        $("#modal-relaciones").modal("show");
    }

    function finalizar(){
        var resolucion = $("#resolucion_id").val();
        if(!noComparece){
            if(resolucion == 1){
                    swal({
                        title: 'Se convino con todos los citados?',
                        text: '',
                        icon: 'warning',
                        // showCancelButton: true,
                        buttons: {
                            cancel: {
                                text: 'No',
                                value: null,
                                visible: true,
                                className: 'btn btn-default',
                                closeModal: true
                            },
                            confirm: {
                                text: 'Sí',
                                value: true,
                                visible: true,
                                className: 'btn btn-warning',
                                closeModal: true
                            }
                        }
                    }).then(function(isConfirm){
                        if(isConfirm){
                            listaResolucionesIndividuales = [];
                            $("#btnGuardarResolucionMuchas").click();
                        }else{
                            cargarModalRelaciones();
                        }
                    });
            }else{

                swal({
                    title: '',
                    text: '¿Estas seguro que deseas terminar la audiencia?',
                    icon: 'warning',
                    // showCancelButton: true,
                    buttons: {
                        cancel: {
                            text: 'No',
                            value: null,
                            visible: true,
                            className: 'btn btn-default',
                            closeModal: true
                        },
                        confirm: {
                            text: 'Sí',
                            value: true,
                            visible: true,
                            className: 'btn btn-warning',
                            closeModal: true
                        }
                    }
                }).then(function(isConfirm){
                    if(isConfirm){
                        listaResolucionesIndividuales = [];
                        $("#btnGuardarResolucionMuchas").click();
                    }
                });
            }
        }else{
            swal({
                title: '',
                text: '¿Estas seguro que deseas terminar la audiencia?',
                icon: 'warning',
                // showCancelButton: true,
                buttons: {
                    cancel: {
                        text: 'No',
                        value: null,
                        visible: true,
                        className: 'btn btn-default',
                        closeModal: true
                    },
                    confirm: {
                        text: 'Sí',
                        value: true,
                        visible: true,
                        className: 'btn btn-warning',
                        closeModal: true
                    }
                }
            }).then(function(isConfirm){
                if(isConfirm){
                    listaResolucionesIndividuales = [];
                $("#btnGuardarResolucionMuchas").click();
                }
            });
        }
    }

    
    $("#btnGuardarResolucionMuchas").on("click",function(){
        let listaPropuestaConceptos = {};
        error =false;
        
        console.log(listaPropuestaConceptos);
        if(!error){
            $.ajax({
                url:"/audiencia/guardarAudienciaColectiva",
                type:"POST",
                dataType:"json",
                data:{
                    audiencia_id:'{{ $audiencia->id }}',
                    solicitud_id:'{{ $solicitud->id }}',
                    audiencia_body:tinyMCE.get('audiencia_body').getContent(),
                    convenio_body:tinyMCE.get('convenio_body').getContent(),
                    no_comparece_body:tinyMCE.get('no_comparece_body').getContent(),
                    resolucion_id:$("#resolucion_id").val(),
                    listaRelacion:listaResolucionesIndividuales,
                    _token:"{{ csrf_token() }}"
                },
                success:function(data){
                    if(data != null && data != ""){
                        window.location = "/audiencias/"+data.id+"/edit"
                    }else{
                        swal({
                            title: 'Algo salió mal',
                            text: 'No se guardo el registro',
                            icon: 'warning'
                        });
                    }
                }
            });
        }else{
            swal({title: 'Error',text: 'Debe seleccionar una propuesta para cada solicitante',icon: 'error'});
        }
    });
    $('.upper').on('keyup', function () {
        var valor = $(this).val();
        $(this).val(valor.toUpperCase());
    });
    $(".tipo_documento,.select-element,.catSelect").select2();
    $(".fecha").datetimepicker({format:"DD/MM/YYYY"});
    
    cargarGeneros();
    cargarTipoContactos();
    cargarComparecientes();
    $("#btnFinalizarRatificacion").on("click",function(){
        location.href = "/solicitudes/consulta/{{$audiencia->expediente->solicitud_id}}";
    });
    function SolicitarNuevaAudiencia(){
        $.ajax({
            url:"/audiencias/solicitar_nueva",
            type:"POST",
            dataType:"json",
            data:{
                audiencia_id:"{{ $audiencia->id }}",
                _token:"{{ csrf_token() }}"
            },
            success:function(data){
                $("#spanFolio").text(data.data.response.folio+"/"+data.data.response.anio);
                $("#spanFechaAudiencia").text(dateFormat(data.data.response.fecha_audiencia,4));
                $("#spanHoraInicio").text(data.data.response.hora_inicio);
                $("#spanHoraFin").text(data.data.response.hora_fin);
                var table="";
                if(data.data.response.multiple){
                    $.each(data.data.response.conciliadores_audiencias,function(index,element){
                        table +='<tr>';
                        if(element.solicitante){
                            table +='   <td>Solicitante(s)</td>';
                        }else{
                            table +='   <td>Citado(s)</td>';
                        }
                        table +='   <td>'+element.conciliador.persona.nombre+' '+element.conciliador.persona.primer_apellido+' '+element.conciliador.persona.segundo_apellido+'</td>';
                        $.each(data.data.response.salas_audiencias,function(index2,element2){
                            if(element2.solicitante == element.solicitante){
                                table +='<td>'+element2.sala.sala+'</td>';
                            }
                        });
                        table +='</tr>';
                    });
                }else{
                    table +='<tr>';
                    table +='   <td>Solicitante(s) y citado(s)</td>';
                    table +='   <td>'+data.data.response.conciliadores_audiencias[0].conciliador.persona.nombre+' '+data.data.response.conciliadores_audiencias[0].conciliador.persona.primer_apellido+' '+data.data.response.conciliadores_audiencias[0].conciliador.persona.segundo_apellido+'</td>';
                    table +='   <td>'+data.data.response.salas_audiencias[0].sala.sala+'</td>';
                    table +='</tr>';
                }
                $("#tableAudienciaSuccess tbody").html(table);
                $("#modalRatificacion").modal("hide");
                $("#modal-ratificacion-success").modal({backdrop: 'static', keyboard: false});
            }
        });
    }
    $(".dateBirth").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange: "c-80:",
            format:'dd/mm/yyyy',
        });
</script>
@endpush
