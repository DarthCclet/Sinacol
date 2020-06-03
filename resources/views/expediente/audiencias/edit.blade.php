@extends('layouts.default', ['paceTop' => true])

@section('title', 'Audiencias')

@include('includes.component.datatables')
@include('includes.component.pickers')
@include('includes.component.dropzone')
@include('includes.component.calendar')

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item active"><a href="{!! route("audiencias.index") !!}">Audiencia</a></li>
        <li class="breadcrumb-item active"><a href="javascript:;">Editar Audiencia</a></li>

    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Administrar Audiencias <small>Resolución de Audiencias</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
    <a href="{!! route('audiencias.index') !!}" class="btn btn-primary btn-sm pull-right"><i class="fa fa-arrow-alt-circle-left"></i> Regresar</a>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a href="#default-tab-1" data-toggle="tab" class="nav-link active">
                <span class="d-sm-none">Aud</span>
                <span class="d-sm-block d-none">Audiencia</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#default-tab-2" data-toggle="tab" class="nav-link">
                <span class="d-sm-none">Docs</span>
                <span class="d-sm-block d-none">Documentos</span>
            </a>
        </li>
        <li class="nav-item tab-Comparecientes">
            <a href="#default-tab-3" data-toggle="tab" class="nav-link">
                <span class="d-sm-none">Comp</span>
                <span class="d-sm-block d-none">Comparecientes</span>
            </a>
        </li>
        <li class="nav-item tab-Resoluciones">
            <a href="#default-tab-4" data-toggle="tab" class="nav-link">
                <span class="d-sm-none">Res</span>
                <span class="d-sm-block d-none">Resoluciones</span>
            </a>
        </li>
    </ul>
    <div class="tab-content" style="background: #f2f3f4 !important;">
        <!-- begin tab-pane -->
        <div class="tab-pane fade active show" id="default-tab-1">
            @include('expediente.audiencias._form')
            <div class="text-right">
                <a href="{!! route('audiencias.index') !!}" class="btn btn-white btn-sm"><i class="fa fa-times"></i> Cancelar</a>
                <button class="btn btn-primary btn-sm m-l-5" id='btnGuardarResolucion'><i class="fa fa-save"></i> Guardar resolución</button>
            </div>
        </div>
        <div class="tab-pane fade show row" id="default-tab-2">
            <div class="col-md-12">
                <div class="text-right">
                    <button class="btn btn-primary btn-sm m-l-5" id='btnAgregarArchivo'><i class="fa fa-plus"></i> Agregar documento</button>
                </div>
            </div><br>
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
                                    <dt class="text-inverse">File Name:</dt>
                                    <dd class="name">{%=file.name%}</dd>
                                    <dt class="text-inverse m-t-10">File Size:</dt>
                                    <dd class="size">Processing...</dd>
                                </dl>
                            </div>
                            <strong class="error text-danger h-auto d-block text-left"></strong>
                        </td>
                        <td>
                            <select class="form-control tipo_documento" name="tipo_documento_id[]">
                                <option value="1">Audiencia 1</option>
                                <option value="2">Audiencia 2</option>
                            </select>
                        </td>
                        <td>
                            <dl>
                                <dt class="text-inverse m-t-3">Progress:</dt>
                                <dd class="m-t-5">
                                    <div class="progress progress-sm progress-striped active rounded-corner"><div class="progress-bar progress-bar-primary" style="width:0%; min-width: 40px;">0%</div></div>
                                </dd>
                            </dl>
                        </td>
                        <td nowrap>
                            {% if (!i && !o.options.autoUpload) { %}
                                <button class="btn btn-primary start width-100 p-r-20 m-r-3" disabled>
                                    <i class="fa fa-upload fa-fw text-inverse"></i>
                                    <span>Start</span>
                                </button>
                            {% } %}
                            {% if (!i) { %}
                                <button class="btn btn-default cancel width-100 p-r-20">
                                    <i class="fa fa-trash fa-fw text-muted"></i>
                                    <span>Cancel</span>
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
                                    <dt class="text-inverse">File Name:</dt>
                                    <dd class="name">
                                        {% if (file.url) { %}
                                            <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                                        {% } else { %}
                                            <span>{%=file.name%}</span>
                                        {% } %}
                                    </dd>
                                    <dt class="text-inverse m-t-10">File Size:</dt>
                                    <dd class="size">{%=o.formatFileSize(file.size)%}</dd>
                                </dl>
                                {% if (file.error) { %}
                                    <div><span class="label label-danger">ERROR</span> {%=file.error%}</div>
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
                                    <span>Cancel</span>
                                </button>
                            {% } %}
                        </td>
                    </tr>
                {% } %}
            </script>
        </div>
        <div class="tab-pane fade show" id="default-tab-3">
            <div class="col-md-12" id="divTableComparecientes">
                <table class="table table-striped table-bordered table-td-valign-middle" id="table">
                    <thead>
                        <tr>
                            <th class="text-nowrap">Tipo Parte</th>
                            <th class="text-nowrap">Nombre</th>
                            <th class="text-nowrap">Primer apellido</th>
                            <th class="text-nowrap">Segundo apellido</th>
                            <th class="text-nowrap">Representante Legal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($audiencia->comparecientes as $compareciente)
                        <tr>
                            <td>{{$compareciente->parte->tipoParte->nombre}}</td>
                            <td>{{$compareciente->parte->nombre}}</td>
                            <td>{{$compareciente->parte->primer_apellido}}</td>
                            <td>{{$compareciente->parte->segundo_apellido}}</td>
                            @if($compareciente->parte->tipo_parte_id == 3 && $compareciente->parte->parte_representada_id != null)
                                @if($compareciente->parte->parteRepresentada->tipo_persona_id == 1)
                                    <td>Si ({{$compareciente->parte->parteRepresentada->nombre}} {{$compareciente->parte->parteRepresentada->primer_apellido}} {{$compareciente->parte->parteRepresentada->segundo_apellido}})</td>
                                @else
                                    <td>Si ({{$compareciente->parte->parteRepresentada->nombre_comercial}})</td>
                                @endif
                            @else
                            <td>No</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade show" id="default-tab-4">
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-td-valign-middle" id="table">
                    <thead>
                        <tr>
                            <th class="text-nowrap">Solicitante</th>
                            <th class="text-nowrap">Solicitado</th>
                            <th class="text-nowrap">Resolucion</th>
                            <th class="text-nowrap">Motivo de archivado</th>
                            <th class="text-nowrap">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($audiencia->resolucionPartes as $resolucion)
                        <tr>
                            @if($resolucion->parteSolicitante->tipo_persona_id == 1)
                                <td>{{$resolucion->parteSolicitante->nombre}} {{$resolucion->parteSolicitante->primer_apellido}} {{$resolucion->parteSolicitante->segundo_apellido}}</td>
                            @else
                                <td>{{$resolucion->parteSolicitante->nombre_comercial}}</td>
                            @endif
                            @if($resolucion->parteSolicitada->tipo_persona_id == 1)
                                <td>{{$resolucion->parteSolicitada->nombre}} {{$resolucion->parteSolicitada->primer_apellido}} {{$resolucion->parteSolicitada->segundo_apellido}}</td>
                            @else
                                <td>{{$resolucion->parteSolicitada->nombre_comercial}}</td>
                            @endif
                            <td>{{$resolucion->resolucion->nombre}}</td>
                            <td>{{$resolucion->motivoArchivado->descripcion}}</td>
                            <td></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="text-right">
                    <button class="btn btn-primary btn-sm m-l-5" id='btnNuevaAudiencia'><i class="fa fa-plus"></i> Nueva Audiencia</button>
                </div>
            </div>
        </div>
    </div>
    <!-- inicio Modal cargar archivos-->
    <div class="modal" id="modal-archivos" aria-hidden="true" style="display:none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Archivos de Audiencia</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form id="fileupload" action="/api/documentoAudiencia" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="audiencia_id[]" id='audiencia_id'/>
                        <div class="row fileupload-buttonbar">
                            <div class="col-xl-12">
                                    <span class="btn btn-primary fileinput-button m-r-3">
                                            <i class="fa fa-fw fa-plus"></i>
                                            <span>Agregar...</span>
                                            <input type="file" name="files[]" multiple>
                                    </span>
                                    <button type="submit" class="btn btn-primary start m-r-3">
                                            <i class="fa fa-fw fa-upload"></i>
                                            <span>Cargar</span>
                                    </button>
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
                                        <th>PROGRESO</th>
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
                        <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-sign-out"></i> Cerrar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin Modal de cargar archivos-->
    <!-- Inicio Modal de ver archivos PDF-->
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
    <!-- Fin Modal de ver archivos PDF-->
    <!-- Inicio Modal de representante legal-->
    <div class="modal" id="modal-representante" aria-hidden="true" style="display:none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Representante legal</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <h5>Datos del Representante legal</h5>
                    <div class="col-md-12 row">
                        <div class="col-md-6 ">
                            <div class="form-group">
                                <label for="curp" class="control-label">CURP</label>
                                <input type="text" id="curp" maxlength="18" onblur="validaCURP(this.value);" class="form-control" placeholder="CURP del representante legal">
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="form-group">
                                <label for="nombre" class="control-label">Nombre</label>
                                <input type="text" id="nombre" class="form-control" placeholder="Nombre del representante legal">
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="form-group">
                                <label for="primer_apellido" class="control-label">Primer apellido</label>
                                <input type="text" id="primer_apellido" class="form-control" placeholder="Primer apellido del representante">
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="form-group">
                                <label for="segundo_apellido" class="control-label">Segundo apellido</label>
                                <input type="text" id="segundo_apellido" class="form-control" placeholder="Segundo apellido representante">
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="form-group">
                                <label for="fecha_nacimiento" class="control-label">Fecha de nacimiento</label>
                                <input type="text" id="fecha_nacimiento" class="form-control fecha" placeholder="Fecha de nacimiento del representante">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="genero_id" class="col-sm-6 control-label">Genero</label>
                            <select id="genero_id" class="form-control select-element">
                                <option value="">-- Selecciona un genero</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <h5>Datos de comprobante como representante legal</h5>
                    <div class="col-md-12 row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="instrumento" class="control-label">Instrumento</label>
                                <input type="text" id="instrumento" class="form-control" placeholder="Instrumento que acredita la representatividad">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="feha_instrumento" class="control-label">Fecha de instrumento</label>
                                <input type="text" id="feha_instrumento" class="form-control fecha" placeholder="Fecha en que se extiende el instrumento">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numero_notaria" class="control-label">Número</label>
                                <input type="text" id="numero_notaria" class="form-control" placeholder="Número de la notaría">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre_notario" class="control-label">Nombre del Notario</label>
                                <input type="text" id="nombre_notario" class="form-control" placeholder="Nombre del notario que acredita">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="localidad_notaria" class="control-label">Localidad</label>
                                <input type="text" id="localidad_notaria" class="form-control" placeholder="Localidad de la notaría">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h5>Datos de contacto</h5>
                    <div class="col-md-12 row">
                        <div class="col-md-5">
                            <label for="tipo_contacto_id" class="col-sm-6 control-label">Tipo de contacto</label>
                            <select id="tipo_contacto_id" class="form-control select-element">
                                <option value="">-- Selecciona un genero</option>
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
    <!-- Inicio Modal de comparecientes y resolución individual-->
    <div class="modal" id="modal-comparecientes" style="display:none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Comparecientes</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning col-md-12">
                        <h5><i class="fa fa-warning"></i> Atención</h5>
                        <p>
                            Esta a punto de dar resolucion a una audiencia, indique la forma de proceder<br>
                            - Marque en la tabla las personas que se presentaron a la audiencia<br>
                            - Una resolución: La resolución aplica para todas las relaciones solicitante-solicitado<br>
                            - Configurar: Podrá agregar las excepciones a la resolución
                        </p>
                    </div>
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
                    </div>
                    <div id="resolucionVarias">
                        <hr>
                        <h5>Registro de resoluciones extraordinarias</h5>
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
                                    <label for="parte_solicitado_id" class="col-sm-6 control-label labelResolucion">Solicitado</label>
                                    <div class="col-sm-10">
                                        <select id="parte_solicitado_id" class="form-control select-element">
                                            <option value="">-- Selecciona un solicitado</option>
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="resolucion_individual_id" class="col-sm-6 control-label labelResolucion">Resolución</label>
                                    <div class="col-sm-10">
                                        <select id="resolucion_individual_id" class="form-control select-element">
                                            <option value="">-- Selecciona una resolución</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
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
                                        <th>Solicitado</th>
                                        <th>Resolución</th>
                                        <th>Motivo de archivo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyResolucionesIndividuales">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="text-right">
                        <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</a>
                        <button class="btn btn-white btn-sm" id="btnCancelarVarias"><i class="fa fa-times"></i> Cancelar</button>
                        <button class="btn btn-warning btn-sm m-l-5" id="btnGuardarResolucionMuchas"><i class="fa fa-save"></i> Guardar</button>
                        <button class="btn btn-warning btn-sm m-l-5" id="btnConfigurarResoluciones"><i class="fa fa-cog"></i> Configurar</button>
                        <button class="btn btn-warning btn-sm m-l-5" id="btnGuardarResolucionUna"><i class="fa fa-save"></i> Guardar para todos</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin Modal de comparecientes y resolución individual-->
    <!-- Inicio Modal de ver archivos PDF-->
    <div class="modal fade bd-example-modal-xl" tabindex="-1" id="modalNuevaAudiencia" aria-hidden="true" style="display:none;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Creacion de nuevas audiencias</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning col-md-12">
                        <h5><i class="fa fa-warning"></i> Atención</h5>
                        <p>
                            Esta opción permite crear audiencias a partir de otra audiencia derivado de una resolición que así lo requiera<br>
                            - Seleccione las partes que deberán ser programadas para la nueva audiencia<br>
                            - Al desactivar el campo calendarizar se generará la audiencia con la calendarizacion actual dado que ya no se requiere agendar una nueva<br>
                            - En caso de requerir una nueva audiencia seleccione en el calendario las nuevas fechas y la forma en la que se llevará a cabo la audiencia<br>
                        </p>
                    </div>
                    <div class="col-md-12">
                        <h5>Relaciones con conflicto</h5>
                        <hr>
                        <div class="col-md-12">
                            <table class="table table-striped table-bordered table-td-valign-middle" id="table">
                                <thead>
                                    <tr>
                                        <th class="text-nowrap" style="width: 10%;"></th>
                                        <th class="text-nowrap">Solicitante</th>
                                        <th class="text-nowrap">Solicitado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($audiencia->resolucionPartes as $resolucion)
                                        @if($resolucion->resolucion_id == 2)
                                        <tr>
                                            <td>
                                            @if(!$resolucion->nuevaAudiencia)
                                                <div class="col-md-2">
                                                    <input type="checkbox" data-render="switchery" data-theme="warning" class="switchPartes"
                                                    data-parte_solicitante_id="{{$resolucion->parteSolicitante->id}}"
                                                    data-parte_solicitada_id="{{$resolucion->parteSolicitada->id}}"
                                                    data-id="{{$resolucion->id}}"/>
                                                </div>
                                            @else
                                                Ya asignado a otra audiencia
                                            @endif
                                            </td>
                                            @if($resolucion->parteSolicitante->tipo_persona_id == 1)
                                                <td>{{$resolucion->parteSolicitante->nombre}} {{$resolucion->parteSolicitante->primer_apellido}} {{$resolucion->parteSolicitante->segundo_apellido}}</td>
                                            @else
                                                <td>{{$resolucion->parteSolicitante->nombre_comercial}}</td>
                                            @endif
                                            @if($resolucion->parteSolicitada->tipo_persona_id == 1)
                                                <td>{{$resolucion->parteSolicitada->nombre}} {{$resolucion->parteSolicitada->primer_apellido}} {{$resolucion->parteSolicitada->segundo_apellido}}</td>
                                            @else
                                                <td>{{$resolucion->parteSolicitada->nombre_comercial}}</td>
                                            @endif
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <h5>Agenda</h5>
                        <hr>
                        <div class="col-md-12 row">
                            <div class="col-md-1">
                                <h6>Calendarizar</h6>
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" data-id="" data-render="switchery" data-theme="default" id="calendarizar" name="calendarizar"/>
                            </div>
                        </div>
                        <div class="col-md-12 row">
                            <div id="divResolucionInmediata"></div>
                            <div id="divAgendarNuevaAudiencia" style="display: none;">
                                @include('expediente.audiencias.calendarioWizard')
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="text-right">
                        <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-ban"></i> Cancelar</a>
                        <button class="btn btn-warning btn-sm" id="btnCrearNuevaAudiencia"><i class="fa fa-save"></i> Crear nueva Audiencia</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin Modal de ver archivos PDF-->
    <input type="hidden" id="parte_id">
    <input type="hidden" id="parte_representada_id">
@endsection
@push('scripts')
    <script>
        var listaContactos=[];
        var finalizada=false;
        var listaResolucionesIndividuales=[];
        var origen = 'audiencias';
        $(document).ready(function() {
            $("#audiencia_id").val('{{ $audiencia->id }}');
            finalizada = '{{ $audiencia->finalizada }}';
            $("#duracionAudiencia").datetimepicker({format:"HH:mm"});
            $(".fecha").datetimepicker({format:"DD/MM/YYYY"});
            $('#convenio').wysihtml5({locale: 'es-ES'});
            $('#desahogo').wysihtml5({locale: 'es-ES'});
            $(".tipo_documento,.select-element").select2();
            cargarDocumentos();
            cargarGeneros();
            cargarTipoContactos();
            $.ajax({
                url:"/api/resoluciones",
                type:"GET",
                dataType:"json",
                success:function(data){
                    if(data.data.data != null && data.data.data != ""){
                        $("#resolucion_id,#resolucion_individual_id").html("<option value=''>-- Selecciona una resolucion</option>");
                        $("#resolucion_id,#resolucion_individual_id").html("<option value=''>-- Selecciona una resolucion</option>");
                        $.each(data.data.data,function(index,element){
                            $("#resolucion_id,#resolucion_individual_id").append("<option value='"+element.id+"'>"+element.nombre+"</option>");
                        });
                    }else{
                        $("#resolucion_id,#resolucion_individual_id").html("<option value=''>-- Selecciona una resolucion</option>");
                    }
                    $("#resolucion_id").val('{{ $audiencia->resolucion_id }}');
                    $("#resolucion_id,#resolucion_individual_id").trigger("change");
                }
            });
            CargarFinalizacion();

            FormMultipleUpload.init();
            Gallery.init();
        });
        $("#btnGuardarResolucion").on("click",function(){
            if(!validarResolucion()){
                $.ajax({
                    url:"/api/audiencia/validar_partes/{{ $audiencia->id }}",
                    type:"GET",
                    dataType:"json",
                    success:function(data){
                        console.log(data.pasa);
                        if(data.pasa){
                            getPersonasComparecer();
                        }else{
                            swal({title: 'Error',text: 'Debes agregar el representante legal de todas las personas Morales',icon: 'error'});
                        }
                    }
                });
            }
        });
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
                swal({title: 'Error',text: 'No has agregado comparecientes',icon: 'warning'});
                return {error:true,comparecientes:[]};
            }
        }
        function validarResolucion(){
            if($("#convenio").val() == ""){
                swal({title: 'Error',text: 'Describe el convenio',icon: 'warning'});
                return true;
            }
            if($("#desahogo").val() == ""){
                swal({title: 'Error',text: 'Describe el desahogo',icon: 'warning'});
                return true;
            }
            if($("#resolucion_id").val() == ""){
                swal({title: 'Error',text: 'Selecciona una resolucion',icon: 'warning'});
                return true;
            }
            return false;
        }
        $("#btnAgregarArchivo").on("click",function(){
            $("#btnCancelFiles").click();
            $("#modal-archivos").modal("show");
        });
        var handleJqueryFileUpload = function() {
            // Initialize the jQuery File Upload widget:
            $('#fileupload').fileupload({
                autoUpload: false,
                disableImageResize: /Android(?!.*Chrome)|Opera/.test(window.navigator.userAgent),
                maxFileSize: 5000000,
                acceptFileTypes: /(\.|\/)(gif|jpe?g|png|pdf)$/i,
                stop: function(e,data){
                  cargarDocumentos();
                  $("#modal-archivos").modal("hide");
                }
                // Uncomment the following to send cross-domain cookies:
                //xhrFields: {withCCOLOR_REDentials: true},
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
            $('#fileupload').bind('fileuploadadd', function(e, data) {
                $('#fileupload [data-id="empty"]').hide();
                $(".tipo_documento").select2();
            });
            $('#fileupload').bind('fileuploaddone', function(e, data) {
                console.log("add");
            });

            // show empty row text
            $('#fileupload').bind('fileuploadfail', function(e, data) {
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
                console.log(result);
                    $(this).fileupload('option', 'done')
                    .call(this, $.Event('done'), {result: result});
            });
        };
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
        var handleIsotopesGallery = function() {
            var container = $('#gallery');

            $(window).on('resize', function() {
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
        function cargarDocumentos(){
            $.ajax({
                url:"/api/audiencia/documentos/"+$("#audiencia_id").val(),
                type:"GET",
                dataType:"json",
                async:true,
                success:function(data){
                    if(data != null && data != ""){
                        var table = "";
                        var div = "";
                        $.each(data, function(index,element){
                            console.log(element);
                            div += '<div class="image gallery-group-1">';
                            div += '    <div class="image-inner" style="position: relative;">';
                            if(element.tipo == 'pdf' || element.tipo == 'PDF'){
                                div += '            <a href="/api/documentos/getFile/'+element.id+'" data-toggle="iframe" data-gallery="example-gallery-pdf" data-type="url">';
                                div += '                <div class="img" align="center">';
                                div += '                    <i class="fa fa-file-pdf fa-4x" style="color:black;margin: 0;position: absolute;top: 50%;transform: translateX(-50%);"></i>';
                                div += '                </div>';
                                div += '            </a>';
                            }else{
                                div += '            <a href="/api/documentos/getFile/'+element.id+'" data-toggle="lightbox" data-gallery="example-gallery" data-type="image">';
                                div += '                <div class="img" style="background-image: url(\'/api/documentos/getFile/'+element.id+'\')"></div>';
                                div += '            </a>';
                            }
                            div += '            <p class="image-caption">';
                            div += '                '+element.longitud+' kb';
                            div += '            </p>';
                            div += '    </div>';
                            div += '    <div class="image-info">';
                            div += '            <h5 class="title">'+element.nombre_original+'</h5>';
                            div += '            <div class="desc">';
                            div += '                <strong>Documento: </strong>'+element.clasificacionArchivo.nombre;
                            div +=                  element.descripcion+'<br>';
                            div += '            </div>';
                            div += '    </div>';
                            div += '</div>';
                        });
                        $("#gallery").html(div);
                    }else{

                    }
                }
            });
        }
        $(document).on('click', '[data-toggle="lightbox"]', function(event) {
            event.preventDefault();
            $(this).ekkoLightbox({
                alwaysShowClose: false,
                onShown: function() {
                    console.log('Checking our the events huh?');
                },
                onNavigate: function(direction, itemIndex){
                    console.log('Navigating '+direction+'. Current item: '+itemIndex);
                }
            });
        });
        $(document).on('click', '[data-toggle="iframe"]',function(event){
            event.preventDefault();
            var pdf_link = $(this).attr('href');
            var iframe = "";
            iframe +='    <div id="Iframe-Cicis-Menu-To-Go" class="set-margin-cicis-menu-to-go set-padding-cicis-menu-to-go set-border-cicis-menu-to-go set-box-shadow-cicis-menu-to-go center-block-horiz">';
            iframe +='        <div class="responsive-wrapper responsive-wrapper-padding-bottom-90pct" style="-webkit-overflow-scrolling: touch; overflow: auto;">';
            iframe +='            <iframe src="'+pdf_link+'"></iframe>';
            iframe +='        </div>';
            iframe +='    </div>';

            $("#bodyArchivo").html(iframe);
            $("#modal-visor").modal("show");

            return false;
        });

        // Funciones de Representantes legales
        function cargarGeneros(){
            $.ajax({
                url:"/api/generos",
                type:"GET",
                dataType:"json",
                success:function(data){
                    if(data != null && data != ""){
                        $("#genero_id").html("<option value=''>-- Selecciona un genero</option>");
                        $.each(data,function(index,element){
                            $("#genero_id").append("<option value='"+element.id+"'>"+element.nombre+"</option>");
                        });
                    }else{
                        $("#genero_id").html("<option value=''>-- Selecciona una opcion</option>");
                    }
                    $("#genero_id").trigger("change");
                }
            });
        }
        function cargarTipoContactos(){
            $.ajax({
                url:"/api/tipo_contactos",
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
        function AgregarRepresentante(parte_id){
            $.ajax({
                url:"/api/partes/representante/"+parte_id,
                type:"GET",
                dataType:"json",
                success:function(data){
                    if(data != null && data != ""){
                        data = data[0];
                        $("#curp").val(data.curp);
                        $("#nombre").val(data.nombre);
                        $("#primer_apellido").val(data.primer_apellido);
                        $("#segundo_apellido").val(data.segundo_apellido);
                        $("#fecha_nacimiento").val(dateFormat(data.fecha_nacimiento,0));
                        $("#genero_id").val(data.genero_id).trigger("change");
                        $("#instrumento").val(data.instrumento);
                        $("#feha_instrumento").val(dateFormat(data.feha_instrumento,0));
                        $("#numero_notaria").val(data.numero_notaria);
                        $("#nombre_notario").val(data.nombre_notario);
                        $("#localidad_notaria").val(data.localidad_notaria);
                        $("#parte_id").val(data.id);
                        listaContactos = data.contactos;
                    }else{
                        $("#curp").val("");
                        $("#nombre").val("");
                        $("#primer_apellido").val("");
                        $("#segundo_apellido").val("");
                        $("#fecha_nacimiento").val("");
                        $("#genero_id").val("").trigger("change");
                        $("#instrumento").val("");
                        $("#feha_instrumento").val("");
                        $("#numero_notaria").val("");
                        $("#nombre_notario").val("");
                        $("#localidad_notaria").val("");
                        $("#parte_id").val("");
                        listaContactos = [];
                    }
                    $("#parte_representada_id").val(parte_id);
                    cargarContactos();
                    $("#modal-representante").modal("show");
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
        $("#btnAgregarContacto").on("click",function(){
            if($("#parte_id").val() != ""){
                $.ajax({
                    url:"/api/partes/representante/contacto",
                    type:"POST",
                    dataType:"json",
                    data:{
                        tipo_contacto_id:$("#tipo_contacto_id").val(),
                        contacto:$("#contacto").val(),
                        parte_id:$("#parte_id").val()
                    },
                    success:function(data){
                        if(data != null && data != ""){
                            listaContactos = data;
                            cargarContactos();
                        }else{
                            swal({title: 'Error',text: 'Algo salio mal',icon: 'warning'});
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
        });
        function eliminarContacto(indice){
            if(listaContactos[indice].id != null){
                $.ajax({
                    url:"/api/partes/representante/contacto/eliminar",
                    type:"POST",
                    dataType:"json",
                    data:{
                        contacto_id:listaContactos[indice].id,
                        parte_id:$("#parte_id").val()
                    },
                    success:function(data){
                        if(data != null && data != ""){
                            listaContactos = data;
                            cargarContactos();
                        }else{
                            swal({title: 'Error',text: 'Algo salio mal',icon: 'warning'});
                        }
                    }
                });
            }else{
                listaContactos.splice(indice,1);
                cargarContactos();
            }
        }
        $("#btnGuardarRepresentante").on("click",function(){
            if(!validarRepresentante()){
                $.ajax({
                    url:"/api/partes/representante",
                    type:"POST",
                    dataType:"json",
                    data:{
                        curp:$("#curp").val(),
                        nombre:$("#nombre").val(),
                        primer_apellido:$("#primer_apellido").val(),
                        segundo_apellido:$("#segundo_apellido").val(),
                        fecha_nacimiento:dateFormat($("#fecha_nacimiento").val()),
                        genero_id:$("#genero_id").val(),
                        instrumento:$("#instrumento").val(),
                        feha_instrumento:dateFormat($("#feha_instrumento").val()),
                        numero_notaria:$("#numero_notaria").val(),
                        nombre_notario:$("#nombre_notario").val(),
                        localidad_notaria:$("#localidad_notaria").val(),
                        parte_id:$("#parte_id").val(),
                        parte_representada_id:$("#parte_representada_id").val(),
                        audiencia_id:$("#audiencia_id").val(),
                        listaContactos:listaContactos
                    },
                    success:function(data){
                        if(data != null && data != ""){
                            swal({title: 'Exito',text: 'Se agrego el representante',icon: 'success'});
                            $("#modal-representante").modal("hide");
                        }else{
                            swal({title: 'Error',text: 'Algo salio mal',icon: 'warning'});
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
            if($("#segundo_apellido").val() == ""){
                $("#segundo_apellido").prev().css("color","red");
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
            if($("#instrumento").val() == ""){
                $("#instrumento").prev().css("color","red");
                error = true;
            }
            if($("#feha_instrumento").val() == ""){
                $("#feha_instrumento").prev().css("color","red");
                error = true;
            }
            if($("#numero_notaria").val() == ""){
                $("#numero_notaria").prev().css("color","red");
                error = true;
            }
            if($("#nombre_notario").val() == ""){
                $("#nombre_notario").prev().css("color","red");
                error = true;
            }
            if($("#localidad_notaria").val() == ""){
                $("#localidad_notaria").prev().css("color","red");
                error = true;
            }
            console.log(listaContactos.length);
            if(listaContactos.length == 0){
                $("#contacto").prev().css("color","red");
                $("#tipo_contacto_id").prev().css("color","red");
                error = true;
                error = true;
            }
            return error;
        }
        function getPersonasComparecer(){
            $.ajax({
                url:"/api/audiencia/fisicas/{{ $audiencia->id }}",
                type:"GET",
                dataType:"json",
                success:function(data){
                    var table = "";
                    $.each(data, function(index,element){
                        table +='<tr>';
                        table +='   <td>'+element.tipo_parte.nombre+'</td>';
                        table +='   <td>'+element.nombre+'</td>';
                        table +='   <td>'+element.primer_apellido+'</td>';
                        table +='   <td>'+element.segundo_apellido+'</td>';
                        table +='   <td>';
                        table +='       <div class="col-md-2">';
                        table +='           <input type="checkbox" value="1" data-parte_id="'+element.id+'" class="checkCompareciente" name="switch1"/>';
                        table +='       </div>';
                        table +='   </td>';
                        table +='</tr>';
                    });
                    $("#tbodyPartesFisicas").html(table);
                    $("#resolucionVarias").hide();
                    $("#btnCancelarVarias").hide();
                    $("#btnGuardarResolucionMuchas").hide();
                    $("#btnConfigurarResoluciones").show();
                    $("#btnGuardarResolucionUna").show();
                    $("#modal-comparecientes").modal("show");
                }
            });
        }
        $("#btnCancelarVarias").on("click",function(){
            $("#resolucionVarias").hide();
            $("#btnCancelarVarias").hide();
            $("#btnGuardarResolucionMuchas").hide();
            $("#btnConfigurarResoluciones").show();
            $("#btnGuardarResolucionUna").show();
        });
        $("#btnConfigurarResoluciones").on("click",function(){
            $("#resolucionVarias").show();
            $("#btnCancelarVarias").show();
            $("#btnGuardarResolucionMuchas").show();
            $("#btnConfigurarResoluciones").hide();
            $("#btnGuardarResolucionUna").hide();
        });
        $("#btnGuardarResolucionMuchas").on("click",function(){
            var validar = validarResolucionComparecientes();
            if(!validar.error){
                $.ajax({
                    url:"/api/audiencia/resolucion",
                    type:"POST",
                    dataType:"json",
                    data:{
                        audiencia_id:'{{ $audiencia->id }}',
                        convenio:$("#convenio").val(),
                        desahogo:$("#desahogo").val(),
                        resolucion_id:$("#resolucion_id").val(),
                        comparecientes:validar.comparecientes,
                        listaRelacion:listaResolucionesIndividuales
                    },
                    success:function(data){
                        if(data != null && data != ""){
                            location.reload();
                        }else{
                            swal({
                                title: 'Algo salio mal',
                                text: 'No se guardo el registro',
                                icon: 'warning'
                            });
                        }
                    }
                });
            }
        });
        $("#btnAgregarResolucion").on("click",function(){
            if(validarResolucionIndividual()){
                var motivo_id = "";
                var motivo_nombre = "";
                if($("#resolucion_individual_id").val() == 4){
                    motivo_id = $("#motivo_archivado_id").val();
                    motivo_nombre = $("#motivo_archivado_id option:selected").text();
                }
                listaResolucionesIndividuales.push({
                    parte_solicitante_id:$("#parte_solicitante_id").val(),
                    parte_solicitante_nombre:$("#parte_solicitante_id option:selected").text(),
                    parte_solicitado_id:$("#parte_solicitado_id").val(),
                    parte_solicitado_nombre:$("#parte_solicitado_id option:selected").text(),
                    resolucion_individual_id:$("#resolucion_individual_id").val(),
                    resolucion_individual_nombre:$("#resolucion_individual_id option:selected").text(),
                    motivo_archivado_id:motivo_id,
                    motivo_archivado_nombre:motivo_nombre
                });
                $("#parte_solicitante_id").val("").trigger("change");
                $("#parte_solicitado_id").val("").trigger("change");
                $("#resolucion_individual_id").val("").trigger("change");
                $("#motivo_archivado_id").val("").trigger("change");
                cargarTablaResolucionesIndividuales();
            }
        });
        $("#btnGuardarResolucionUna").on("click",function(){
            var validar = validarResolucionComparecientes();
            if(!validar.error){
                $.ajax({
                    url:"/api/audiencia/resolucion",
                    type:"POST",
                    dataType:"json",
                    data:{
                        audiencia_id:'{{ $audiencia->id }}',
                        convenio:$("#convenio").val(),
                        desahogo:$("#desahogo").val(),
                        resolucion_id:$("#resolucion_id").val(),
                        comparecientes:validar.comparecientes,
                        listaRelacion:[]
                    },
                    success:function(data){
                        if(data != null && data != ""){
                            location.reload();
                        }else{
                            swal({
                                title: 'Algo salio mal',
                                text: 'No se guardo el registro',
                                icon: 'warning'
                            });
                        }
                    }
                });
            }
        });
        function CargarFinalizacion(){
            if(finalizada){
                $("#btnGuardarResolucion").hide();
                $("#btnGuardarRepresentante").hide();
                $("#btnAgregarContacto").hide();
                $(".tab-Comparecientes").show();
                $(".tab-Resoluciones").show();
            }else{
                $("#btnGuardarResolucion").show();
                $("#btnGuardarRepresentante").show();
                $("#btnAgregarContacto").show();
                $(".tab-Comparecientes").hide();
                $(".tab-Resoluciones").hide();
            }
        }
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
            if($("#resolucion_individual_id").val() == ""){
                error = false;
                $("#resolucion_individual_id").parent().prev().css("color","red");
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
                    table +='<td>'+e.parte_solicitante_nombre+'</td>';
                    table +='<td>'+e.parte_solicitado_nombre+'</td>';
                    table +='<td>'+e.resolucion_individual_nombre+'</td>';
                    table +='<td>'+e.motivo_archivado_nombre+'</td>';
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
            console.log(indice);
            listaResolucionesIndividuales.splice(indice,1);
            cargarTablaResolucionesIndividuales();
        }

        $("#btnNuevaAudiencia").on("click",function(){
            $("#modalNuevaAudiencia").modal("show");
        });
        $("#calendarizar").on("change",function(){
            if(!$(this).is(":checked")){
                $("#divResolucionInmediata").show();
                $("#divAgendarNuevaAudiencia").hide();
            }else{
                $("#divResolucionInmediata").hide();
                $("#divAgendarNuevaAudiencia").show();
            }
        });
        $("#btnCrearNuevaAudiencia").on("click",function(){
            var validacion = validarCrearNuevaAudiencia();
            if(validacion.pasa){
                swal({
                    title: 'Advertencia',
                    text: 'Al oprimir aceptar se creara una nueva audiencia por celebrar, ¿Esta seguro de continuar?',
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            value: null,
                            visible: true,
                            className: 'btn btn-default',
                            closeModal: true
                        },
                        confirm: {
                            text: 'Aceptar',
                            value: true,
                            visible: true,
                            className: 'btn btn-warning',
                            closeModal: true
                        }
                    }
                }).then(function(isConfirm){
                    if(isConfirm){
                        $.ajax({
                            url:"/api/audiencia/nuevaAudiencia",
                            type:"POST",
                            dataType:"json",
                            data:{
                                audiencia_id:'{{ $audiencia->id }}',
                                nuevaCalendarizacion:'N',
                                listaRelaciones:validacion.listaRelaciones
                            },
                            success:function(data){
                                if(data != null && data != ""){
                                    confirmVista(data.id);
                                }else{
                                    swal({
                                        title: 'Algo salio mal',
                                        text: 'No se guardo el registro',
                                        icon: 'warning'
                                    });
                                }
                            }
                        });
                    }
                });
            }else{

            }
        });
        function validarCrearNuevaAudiencia(){
            var pasa =true;
            var listaRelaciones = [];
            $(".switchPartes").each(function(index){
                if($(this).is(":checked")){
                    listaRelaciones.push({
                        parte_solicitante_id:$(this).data("parte_solicitante_id"),
                        parte_solicitada_id:$(this).data("parte_solicitada_id"),
                        id:$(this).data("id")
                    });
                }
            });
            if(listaRelaciones.length == 0){
                pasa = false;
                swal({title: 'Error',text: 'Selecciona una relación al menos',icon: 'warning'});
            }
            return {
                pasa:pasa,
                listaRelaciones:listaRelaciones
            };
        }
        function confirmVista(idAudiencia){
            swal({
                title: 'Exito',
                text: 'Se ha creado la nueva audiencia, ¿Qué deseas hacer?',
                icon: 'success',
                buttons: {
                    cancel: {
                        text: 'Quedarme aquí',
                        value: null,
                        visible: true,
                        className: 'btn btn-default',
                        closeModal: true
                    },
                    confirm: {
                        text: 'Ir a la audiencia creada',
                        value: true,
                        visible: true,
                        className: 'btn btn-success',
                        closeModal: true
                    }
                }
            }).then(function(isConfirm){
                if(isConfirm){
                    window.location.href = "../../audiencias/"+idAudiencia+"/edit";
                }else{
                    location.reload();
                }
            });
        }
    </script>
@endpush
