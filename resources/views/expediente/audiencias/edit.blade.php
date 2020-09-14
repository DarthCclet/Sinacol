@extends('layouts.default', ['paceTop' => true])

@section('title', 'Audiencias')

@include('includes.component.datatables')
@include('includes.component.pickers')
@include('includes.component.dropzone')
@include('includes.component.calendar')
@push('styles')
@endpush
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

    </style>
@section('content')

    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Inicio</a></li>
        <li class="breadcrumb-item active"><a href="{!! route("audiencias.index") !!}">Audiencia</a></li>
        <li class="breadcrumb-item active"><a href="javascript:;">Editar Audiencia</a></li>

    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="h2">Administrar Audiencias <small>Resolución de Audiencias</small></h1>
    <hr class="red">
    <!-- end page-header -->
    <!-- begin panel -->
    {{-- <a href="{!! route('audiencias.index') !!}" class="btn btn-primary btn-sm pull-right"><i class="fa fa-arrow-alt-circle-left"></i> Regresar</a> --}}
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
        <li class="nav-item">
            <a href="#default-tab-5" data-toggle="tab" class="nav-link">
                <span class="d-sm-none">Noti</span>
                <span class="d-sm-block d-none">Notificaciones</span>
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
            @if($audiencia->solictud_cancelcacion && !$audiencia->cancelacion_atendida)
            <div class="alert alert-warning col-md-12">
                <h5><i class="fa fa-warning"></i> Solicitud de nueva fecha</h5>
                <div class="row">
                    <div class="col-md-12">

                    El Solicitante de esta audiencia ha solicitado su reprogramación, revisar el justificante y determinar si debe ser aprobada o negada.<br>
                    </div>
                    <div class="col-md-12">
                    <div class="pull-right">
                        <button href="/api/documentos/getFile/{{$audiencia->justificante_id}}" class="btn btn-primary" data-toggle="iframe" data-gallery="example-gallery-pdf" data-type="url">
                            <i class="fa fa-file-pdf"></i>&nbsp;&nbsp;Ver Justificante
                        </button>
                        <button class="btn btn-success" id='btnAprobarCancelacion'><i class="fa fa-check"></i>&nbsp;&nbsp;Aprobar</button>
                        <button class="btn btn-danger" id='btnNegarCancelacion'><i class="fa fa-times"></i>&nbsp;&nbsp;Negar</button>
                    </div>
                    </div>
                </div>
            </div>
            @endif
            @include('expediente.audiencias._form')
            <h3 class="h3">Administrar Audiencias <small>Resolución de Audiencias</small></h3>
            <hr class="red">
            <div class="col-md-12">
                <ul class="timeline">
                    @foreach($etapa_resolucion as $etapa)
                        @if($etapa->paso == 1)
                        <li style="" id="step{{$etapa->paso}}">
                        @else
                        <li style="display:none;" id="step{{$etapa->paso}}">
                        @endif
                            <!-- begin timeline-time -->
                            <div class="timeline-time">
                                <span class="time">{{$etapa->paso}}.  {{$etapa->nombre}}</span>
                            <span class="date showTime{{$etapa->paso}}"></span>
                            </div>
                            <!-- end timeline-time -->
                            <!-- begin timeline-icon -->
                            <div class="timeline-icon">
                            <a href="javascript:;" id="icon{{$etapa->paso}}">&nbsp;</a>
                            </div>
                            <!-- end timeline-icon -->
                            <!-- begin timeline-body -->
                        <div class="timeline-body" style="border: 1px solid black; margin-right: 10% !important">
                                <div class="timeline-header">
                                <span class="username"><a href="javascript:;">{{$etapa->nombre}}</a> <small></small></span>
                                <span class="views showTime{{$etapa->paso}}"></span>
                                </div>
                            <div class="timeline-content" id="contentStep{{$etapa->paso}}">
                                    <p>
                                        @switch($etapa->paso)
                                            @case(1)
                                                <p>Comparecientes</p>
                                                <div class=" col-md-12 ">
                                                    <table style="font-size: small;" class="table table-striped table-bordered table-td-valign-middle table-responsive" id="table">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-nowrap">Tipo parte</th>
                                                                <th class="text-nowrap">Nombre</th>
                                                                <th class="text-nowrap">Primer apellido</th>
                                                                <th class="text-nowrap">Segundo apellido</th>
                                                                <th class="text-nowrap">Representante legal</th>
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
                                                @break
                                            @case(2)
                                                <label>Recuerde explicar los aspectos importantes de la audiencia de conciliación:</label>
                                                <ul>
                                                    <li>No se reconoce carácter de representante legal de la parte trabajadora, aunque podrá comparecer con acompañante.</li>
                                                    <li>Lo dicho en la audiencia de conciliación es confidencial y no constituye prueba en ningún procedimiento jurisdiccional.</li>
                                                    <li>Las características de la conciliación, enfatizar sus beneficios.</li>
                                                    <li>La explicación de lo anterior ya está precargada en el sistema, usted solamente debe confirmar que revisó estos puntos con las partes, no es necesario que escriba un resumen de este preámbulo.</li>
                                                </ul>
                                                <br/>
                                                <br/>
                                                <input type="hidden" id="evidencia{{$etapa->paso}}" value="true" />
                                                <div class="col-md-12">
                                                    <div class="col-md-12" style="margin-bottom: 5%">
                                                        <div >
                                                            <span class="text-muted m-l-5 m-r-20" for='switch1'>El acta fue explicada por el conciliador a las partes</span>
                                                        </div>
                                                        <div >
                                                            <input type="hidden" />
                                                            <input type="checkbox" value="1" data-render="switchery" data-theme="default" id="explico_acta" name='solicita_traductor_solicitante' />
                                                        </div>
                                                    </div>
                                                </div>
                                            @break
                                            @case(3)
                                                <p>Darle la palabra a la parte solicitante y luego a la parte citada. </p>
                                                <p>Recordando que la conciliación es un proceso sin formalismos, podrán hablar ambas partes las veces necesarias. </p>
                                                <p>Al final es necesario que redacte usted en el espacio indicado el resumen de las manifestaciones de las partes, y que estén las partes de acuerdo con este resumen, que se transcribirá por sistema en el acta de audiencia. </p>
                                                <textarea class="form-control textarea" placeholder="Describir resumen de lo sucedido ..." type="text" id="evidencia{{$etapa->paso}}" >
                                                </textarea>
                                            @break
                                            @case(4)
                                            <div class="accordion" id="accordionExample">
                                                <p>
                                                    El sistema le muestra 2 opciones de propuestas de convenio:
                                                    <ol>
                                                        <li>El cálculo del 100% considerando indemnización, partes proporcionales de prestaciones y prima de antigüedad. </li>
                                                        <li>El mismo cálculo con 50% de la indemnización constitucional. </li><br>
                                                    </ol>
                                                    Usted puede escoger una de estas alternativas o bien modificar las tablas. Lo que deja confirmado en el sistema será la propuesta de arreglo que se mostrará en el acta de audiencia.
                                                </p>
                                                @foreach($audiencia->solicitantes as $solicitante)
                                                {{-- <pre>{{$solicitante->parte->id}}</pre> --}}
                                                    <div class="card">
                                                    <div class="card-header" id="headingOne">
                                                        <h2 class="mb-0">
                                                        <button id='coll{{$solicitante->parte->id}}' class="btn btn-link card-header collapseSolicitante" idSolicitante="{{$solicitante->parte->id}}" type="button" data-toggle="collapse" data-target="#collapse{{$solicitante->parte->id}}" aria-expanded="true" aria-controls="collapseOne" parteSelect="{{$solicitante->parte->id}}" onclick="getDatosLaboralesParte({{$solicitante->parte->id}})" >
                                                            @if($solicitante->parte->tipo_persona_id == 1)
                                                                {{ $solicitante->parte->nombre }} {{ $solicitante->parte->primer_apellido }} {{ $solicitante->parte->segundo_apellido }}
                                                            @else
                                                                {{ $solicitante->parte->nombre_comercial }}
                                                            @endif
                                                        </button>
                                                        </h2>
                                                    </div>

                                                    <div id="collapse{{$solicitante->parte->id}}" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                                                        <div class="card-body">
                                                            <input type="hidden" id="remuneracionDiaria" />
                                                            <input type="hidden" id="salarioMinimo"/>
                                                            <input type="hidden" id="antiguedad"/>
                                                            <input type="hidden" id="idSolicitante"/>
                                                            <div>
                                                                <div>
                                                                    <table class="table">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Prestaci&oacute;n</th><th>Propuesta completa</th><th>Propuesta 45 d&iacute;as</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="tbodyPropuestas{{$solicitante->parte->id}}">
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class=" col-md-12" style="margin:5%;">
                                                                    <h5 class="col-form-label col-sm-10 pt-0">Seleccione una propuesta por favor</h5>
                                                                    <div class="row">
                                                                        <div class="col-sm-10 ">
                                                                        <div class="form-check row" style="margin-top: 2%;">
                                                                            <input class="form-check-input" type="radio" name="radiosPropuesta{{$solicitante->parte->id}}" id="gridRadios1" value="completa" checked>
                                                                            <label class="form-check-label" for="gridRadios1">
                                                                                100% de indemnizaci&oacute;n
                                                                            </label>
                                                                        </div>
                                                                        <div class="form-check row" style="margin-top: 2%;">
                                                                            <input class="form-check-input" type="radio" name="radiosPropuesta{{$solicitante->parte->id}}" id="gridRadios2" value="al50">
                                                                            <label class="form-check-label" for="gridRadios2">
                                                                                50% de indemnizaci&oacute;n
                                                                            </label>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="form-check col-md-1 " style="margin-top: 2%;">
                                                                                <input class="form-check-input" type="radio" onclick="cargarConfigConceptos();" name="radiosPropuesta{{$solicitante->parte->id}}" id="propuesta_otro" value="otra">
                                                                                <label class="form-check-label" for="gridRadios3">
                                                                                    Otro
                                                                                </label>
                                                                            </div>
                                                                            <div class="form-group col-md-4">
                                                                                <a onclick="cargarConfigConceptos(); $('#propuesta_otro').prop('checked',true);" class="btn btn-primary">Configurar</a>
                                                                            </div>
                                                                        </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <table class="table table-bordered" >
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Concepto</th>
                                                                            <th>Dias</th>
                                                                            <th>Monto</th>
                                                                            <th>Otro</th>
                                                                            <th>Acciones</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="tbodyConceptoPrincipal{{$solicitante->parte->id}}">
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </div>
                                                @endforeach
                                                </div>
                                                <br>

                                                <textarea class="form-control textarea" placeholder="Comentarios ..." type="text" id="evidencia{{$etapa->paso}}" >
                                                </textarea>
                                            @break
                                            @case(5)
                                                <p>Darle la palabra a la parte solicitante y luego a la parte citada. </p>
                                                <p>Recordando que la conciliación es un proceso sin formalismos, podrán hablar ambas partes las veces necesarias. </p>
                                                <p>Al final es necesario que redacte usted en el espacio indicado el resumen de las manifestaciones de las partes, y que estén las partes de acuerdo con este resumen, que se transcribirá por sistema en el acta de audiencia. </p>
                                                <textarea class="form-control textarea" placeholder="Describir resumen de lo sucedido ..." type="text" id="evidencia{{$etapa->paso}}" >
                                                </textarea>
                                            @break
                                            @case(6)
                                                <label>Debe indicar cuál de las siguientes resoluciones de audiencia procede:</label>
                                                <ul>
                                                    <li>Convenio.</li>
                                                    <li>Agendar segunda audiencia.</li>
                                                    <li>Constancia de no conciliación.</li>
                                                    <li>Las terminaciones ya están cargadas en el sistema, solamente es necesario indicar el modo de terminación de la audiencia y el resultado respecto a cada par de solicitante-citado para que el sistema coloque la terminación correcta al final del acta de audiencia.</li>
                                                </ul>
                                                <div class="col-md-offset-3 col-md-6 ">
                                                    <div class="form-group">
                                                        <label for="resolucion_id" class="col-sm-6 control-label">Resolución</label>
                                                        <div class="col-sm-10">
                                                            {!! Form::select('resolucion_id', isset($resoluciones) ? $resoluciones : [] , null, ['id'=>'resolucion_id', 'required','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12" style="margin-bottom: 5%">
                                                    <div >
                                                        <span class="text-muted m-l-5 m-r-20" for='switchAdicionales'>Existen elementos adicionales para el cumplimiento de prestaciones o prestaciones adicionales.</span>
                                                    </div>
                                                    <div >
                                                    <input type="checkbox" data-render="switchery" data-theme="default" id="switchAdicionales" name='elementosAdicionales' onchange=" if($('#switchAdicionales').is(':checked')){ $('#textAdicional').show();}else{$('#textAdicional').hide();}"/>
                                                    </div>
                                                </div>
                                                <div id="textAdicional" style="display:none">
                                                    <textarea class="form-control textarea" placeholder="Describir..." type="text" id="evidencia{{$etapa->paso}}">
                                                    </textarea>
                                                </div>
                                            @break
                                            @default

                                        @endswitch
                                    </p>
                                </div>
                                <div class="timeline-footer">
                                </div>
                            </div>
                            <!-- end timeline-body -->
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="tab-pane fade row" id="default-tab-2">
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
                                @foreach($clasificacion_archivos as $key => $nombre)
                                    <option value="{{$key}}">{{$nombre}}</option>
                                @endforeach
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
        <div class="tab-pane fade" id="default-tab-4">
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-td-valign-middle" id="table">
                    <thead>
                        <tr>
                            <th class="text-nowrap">Solicitante</th>
                            <th class="text-nowrap">Citado</th>
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
                            <td>{{$resolucion->terminacion_bilateral->nombre}}</td>
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
        <div class="tab-pane fade" id="default-tab-5">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered" >
                        <thead>
                            <tr>
                                <th>Citado</th>
                                <th>RFC</th>
                                <th>Tipo de notificación</th>
                                <th>Estado</th>
                                <th>Fecha de Notificación</th>
                                <th>Conclusión de notificación</th>
                            </tr>
                    @foreach($audiencia->partes as $parte)
                        @if($parte->tipo_parte_id == 2)
                            <tr>
                                @if($parte->tipo_persona_id == 1)
                                <td>{{ $parte->nombre }} {{ $parte->primer_apellido }} {{ $parte->segundo_apellido }}</td>
                                @else
                                <td>{{ $parte->nombre_comercial }}</td>
                                @endif
                                <td>{{ $parte->rfc }}</td>
                                <td>{{ $parte->tipo_notificacion->nombre }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endif
                    @endforeach
                        </thead>
                        <tbody id="tbodyPartesFisicas">
                        </tbody>
                    </table>
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
                            <label for="genero_id" class="col-sm-6 control-label">Género</label>
                            <select id="genero_id" class="form-control select-element">
                                <option value="">-- Selecciona un género</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <h5>Datos de comprobante como representante legal</h5>
                    <div class="col-md-12 row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clasificacion_archivo_id" class="control-label">Instrumento</label>
                                <select id="clasificacion_archivo_id" class="form-control select-element">
                                    <option value="">-- Selecciona un género</option>
                                    @foreach($clasificacion_archivos_Representante as $clasificacion)
                                    <option value="{{$clasificacion->id}}">{{$clasificacion->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="feha_instrumento" class="control-label">Fecha de instrumento</label>
                                <input type="text" id="feha_instrumento" class="form-control fecha" placeholder="Fecha en que se extiende el instrumento">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="detalle_instrumento" class="control-label">Detalle del instrumento notarial</label>
                                <textarea type="text" id="detalle_instrumento" class="form-control" placeholder=""></textarea>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h5>Datos de contacto</h5>
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
    <!-- Inicio Modal de datos laborales-->
    <div class="modal" id="modal-dato-laboral" aria-hidden="true" style="display:none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Datos Laborales</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12 row">
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
                            - Una resolución: La resolución aplica para todas las relaciones solicitante-citado<br>
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
                            <div class="col-md-12" id="divConceptosAcordados" style="display: none;">
                                <label>Conceptos de pago para resolucion</label>
                                <div class="form-group">
                                    <label for="concepto_pago_resoluciones_id" class="col-sm-6 control-label labelResolucion">Concepto de pago</label>
                                    <div class="col-sm-10">

                                        <select id="concepto_pago_resoluciones_id" class="form-control select-element">
                                            <option value="">-- Selecciona un concepto de pago</option>
                                            @foreach($concepto_pago_resoluciones as $concepto)
                                                <option value="{{ $concepto->id }}">{{ $concepto->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="dias" class="col-sm-6 control-label labelResolucion">D&iacute;as a pagar</label>
                                        <div class="col-sm-12">
                                            <input type="text" id="dias" placeholder="D&iacute;as a pagar" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="monto" class="col-sm-6 control-label labelResolucion">Monto a pagar</label>
                                        <div class="col-sm-12">
                                            <input type="text" id="monto" placeholder="Monto a pagar" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="otro" class="col-sm-6 control-label labelResolucion">Descripci&oacute;n Concepto</label>
                                        <div class="col-sm-12">
                                            <input type="text" id="otro" placeholder="Descripci&oacute;n Concepto" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-center">
                                        <button class="btn btn-warning text-white btn-sm" id='btnAgregarConcepto'><i class="fa fa-plus"></i> Agregar Concepto</button>
                                    </div>
                                </div>
                                <div class="col-md-8 offset-md-2">
                                    <table class="table table-bordered" >
                                        <thead>
                                            <tr>
                                                <th>Concepto</th>
                                                <th>Dias</th>
                                                <th>Monto</th>
                                                <th>Otro</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyConcepto">
                                        </tbody>
                                    </table>
                                </div>
                                <hr>
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
                                        <th class="text-nowrap">Citado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($audiencia->resolucionPartes as $resolucion)
                                        @if($resolucion->terminacion_bilateral_id == 2)
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
    <!-- Inicio Modal de recalendarizar audiencia-->
    <div class="modal fade bd-example-modal-xl" tabindex="-1" id="modal-reprogramacion" aria-hidden="true" style="display:none;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Reprogramación de audiencia</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    @include('expediente.audiencias.agendaConciliadorReagendar')
                </div>
                <div class="modal-footer">
                    <div class="text-right">
                        <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-ban"></i> Cancelar</a>
                        <button class="btn btn-warning btn-sm" id="btnReprogramarAudiencia"><i class="fa fa-save"></i> Reprogramar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin Modal de ver recalendarizar audiencia-->
    <div class="modal" id="modal-asignarAudiencia" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Asignar audiencia</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-muted">
                    - Selecciona el conciliador y la sala donde se celebrará la audiencia<br>
                    - La fecha limite para notificar será 5 días habiles previo a la fecha de audiencia (<span id="lableFechaInicio"></span>>)
                </div>
                <div id="divAsignarUno">
                    <div class="col-md-12 row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="sala_cambio_id" class="col-sm-6 control-label">Sala</label>
                                <div class="col-sm-10">
                                    <select id="sala_cambio_id" class="form-control">
                                        <option value="">-- Selecciona una sala</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5" id="btnGuardarNuevaFecha"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
    <input type="hidden" id="parte_id">
    <input type="hidden" id="parte_representada_id">
@endsection
@push('scripts')
    <script>
        var listaContactos=[];
        var listaConcepto=[];
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
            getEtapasAudiencia();
            $.ajax({
                url:"/resolucion-audiencia",
                type:"GET",
                dataType:"json",
                success:function(data){
                    if(data.data.data != null && data.data.data != ""){
                        $("#resolucion_id,#resolucion_individual_id").html("<option value=''>-- Selecciona una resolucion</option>");
//                        $("#resolucion_id,#resolucion_individual_id").html("<option value=''>-- Selecciona una resolucion</option>");
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
            $('.textarea').wysihtml5({locale: 'es-ES'});
        });
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
                url:"/audiencia/documentos/"+$("#audiencia_id").val(),
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
                url:"/generos",
                type:"GET",
                dataType:"json",
                success:function(data){
                    if(data.data != null && data.data != ""){
                        $("#genero_id").html("<option value=''>-- Selecciona un género</option>");
                        $.each(data.data,function(index,element){
                            $("#genero_id").append("<option value='"+element.id+"'>"+element.nombre+"</option>");
                        });
                    }else{
                        $("#genero_id").html("<option value=''>-- Selecciona una opción</option>");
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
        function AgregarRepresentante(parte_id){
            $.ajax({
                url:"/partes/representante/"+parte_id,
                type:"GET",
                dataType:"json",
                success:function(data){
                    if(data != null && data != ""){
                        data = data[0];
                        $("#curp").val(data.curp);
                        $("#nombre").val(data.nombre);
                        $("#primer_apellido").val(data.primer_apellido);
                        $("#segundo_apellido").val((data.segundo_apellido|| ""));
                        $("#fecha_nacimiento").val(dateFormat(data.fecha_nacimiento,4));
                        $("#genero_id").val(data.genero_id).trigger("change");
                        $("#clasificacion_archivo_id").val(data.clasificacion_archivo_id).change();
                        $("#feha_instrumento").val(dateFormat(data.feha_instrumento,4));
                        $("#detalle_instrumento").val(data.detalle_instrumento);
                        $("#parte_id").val(data.id);
                        listaContactos = data.contactos;
                    }else{
                        $("#curp").val("");
                        $("#nombre").val("");
                        $("#primer_apellido").val("");
                        $("#segundo_apellido").val("");
                        $("#fecha_nacimiento").val("");
                        $("#genero_id").val("").trigger("change");
                        $("#clasificacion_archivo_id").val("").change();
                        $("#feha_instrumento").val("");
                        $("#detalle_instrumento").val("");
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
        });
        $("#btnAgregarConcepto").on("click",function(){
            if(($("#otro").val() != "" || ($("#dias").val() != "" && $("#monto").val() != "")) && $("#concepto_pago_resoluciones_id").val() != "" ){

                listaConcepto.push({
                    concepto_pago_resoluciones_id:$("#concepto_pago_resoluciones_id").val(),
                    dias:$("#dias").val(),
                    monto:$("#monto").val(),
                    otro:$("#otro").val(),
                });
                limpiarConcepto();
                cargarTablaConcepto();
            }else{
                if($("#concepto_pago_resoluciones_id").val() == ""){
                    swal({title: 'Error',text: 'Debe seleccionar el concepto de pago',icon: 'warning'});
                }else{
                    swal({title: 'Error',text: 'Debe ingresar dias y monto ó descripción del concepto',icon: 'warning'});

                }
            }

        });
        function cargarTablaConcepto(){
            var table = '';
            $.each(listaConcepto,function(index,concepto){
                table +='<tr>';
                    $("#concepto_pago_resoluciones_id").val(concepto.concepto_pago_resoluciones_id);
                    table +='<td>'+$("#concepto_pago_resoluciones_id option:selected").text()+'</td>';
                    $("#concepto_pago_resoluciones_id").val("");
                    table +='<td>'+concepto.dias+'</td>';
                    table +='<td>'+concepto.monto+'</td>';
                    table +='<td>'+concepto.otro+'</td>';
                    table +='<td>';
                        table +='<button onclick="eliminarConcepto('+index+')" class="btn btn-xs btn-warning" title="Eliminar">';
                            table +='<i class="fa fa-trash"></i>';
                        table +='</button>';
                    table +='</td>';
                table +='</tr>';
            });
            $("#tbodyConcepto").html(table);
        }

        function eliminarConcepto(indice){
            listaConcepto.splice(indice,1);
            cargarTablaConcepto();
        }
        function limpiarConcepto(){
            $("#concepto_pago_resoluciones_id").val("");
            $("#concepto_pago_resoluciones_id").trigger("change");
            $("#dias").val("");
            $("#monto").val("");
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
                listaContactos.splice(indice,1);
                cargarContactos();
            }
        }
        $("#btnGuardarRepresentante").on("click",function(){
            if(!validarRepresentante()){
                $.ajax({
                    url:"/partes/representante",
                    type:"POST",
                    dataType:"json",
                    data:{
                        curp:$("#curp").val(),
                        nombre:$("#nombre").val(),
                        primer_apellido:$("#primer_apellido").val(),
                        segundo_apellido:$("#segundo_apellido").val(),
                        fecha_nacimiento:dateFormat($("#fecha_nacimiento").val()),
                        genero_id:$("#genero_id").val(),
                        clasificacion_archivo_id:$("#clasificacion_archivo_id").val(),
                        feha_instrumento:dateFormat($("#feha_instrumento").val()),
                        detalle_instrumento:$("#detalle_instrumento").val(),
                        parte_id:$("#parte_id").val(),
                        parte_representada_id:$("#parte_representada_id").val(),
                        audiencia_id:$("#audiencia_id").val(),
                        listaContactos:listaContactos,
                        _token:"{{ csrf_token() }}"
                    },
                    success:function(data){
                        if(data != null && data != ""){
                            swal({title: 'Éxito',text: 'Se agregó el representante',icon: 'success'});
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
            if($("#clasificacion_archivo_id").val() == ""){
                $("#clasificacion_archivo_id").prev().css("color","red");
                error = true;
            }
            if($("#feha_instrumento").val() == ""){
                $("#feha_instrumento").prev().css("color","red");
                error = true;
            }
            if($("#detalle_instrumento").val() == ""){
                $("#detalle_instrumento").prev().css("color","red");
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
                url:"/audiencia/fisicas/{{ $audiencia->id }}",
                type:"GET",
                dataType:"json",
                success:function(data){
                    var table = "";
                    $.each(data, function(index,element){
                        table +='<tr>';
                        table +='   <td>'+element.tipo_parte.nombre+'</td>';
                        table +='   <td>'+element.nombre+'</td>';
                        table +='   <td>'+element.primer_apellido+'</td>';
                        table +='   <td>'+(element.segundo_apellido|| "")+'</td>';
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
        function CargarFinalizacion(){
            if(finalizada){
                $("#btnGuardarResolucion").hide();
                $("#btnGuardarRepresentante").hide();
                $("#btnGuardarDatoLaboral").hide();
                $("#btnAgregarContacto").hide();
                $(".tab-Comparecientes").show();
                $(".tab-Resoluciones").show();
            }else{
                $("#btnGuardarResolucion").show();
                $("#btnGuardarRepresentante").show();
                $("#btnGuardarDatoLaboral").show();
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
                            url:"/audiencia/nuevaAudiencia",
                            type:"POST",
                            dataType:"json",
                            data:{
                                audiencia_id:'{{ $audiencia->id }}',
                                nuevaCalendarizacion:'N',
                                listaRelaciones:validacion.listaRelaciones,
                                _token:"{{ csrf_token() }}"
                            },
                            success:function(data){
                                if(data != null && data != ""){
                                    confirmVista(data.id);
                                }else{
                                    swal({
                                        title: 'Algo salió mal',
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
                title: 'ÉXITO',
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

        //cargar giros comerciales
        function highlightText(string){
            return string.replace($("#term").val().trim(),'<span class="highlighted">'+$("#term").val().trim()+"</span>");
        }

        $("#giro_comercial_solicitante").select2({
            ajax: {
                url: '/giros_comerciales/filtrarGirosComerciales',
                type:"POST",
                dataType:"json",
                delay: 400,
                async:false,
                data:function (params) {
                    $("#term").val(params.term);
                    var data = {
                        nombre: params.term,
                        _token:"{{ csrf_token() }}"
                    }
                    return data;
                },
                processResults:function(json){
                    $.each(json.data, function (key, node) {
                        var html = '';
                        html += '<table>';
                        var ancestors = node.ancestors.reverse();
                        html += '<tr><th colspan="2"><h5>* '+highlightText(node.nombre)+'</h5><th></tr>';
                        $.each(ancestors, function (index, ancestor) {
                            if(ancestor.id != 1){
                                var tab = '&nbsp;&nbsp;&nbsp;&nbsp;'.repeat(index);
                                html += '<tr><td ><b>'+ancestor.codigo+'</b></td>'+' <td style="border-left:1px solid;">'+tab+highlightText(ancestor.nombre)+'</td></tr>';
                            }
                        });
                        var tab = '&nbsp;&nbsp;&nbsp;&nbsp;'.repeat(node.ancestors.length);
                        html += '<tr><td><b>'+node.codigo+'</b></td>'+'<td style="border-left:1px solid;"> '+ tab+highlightText(node.nombre)+'</td></tr>';
                        html += '</table>';
                        json.data[key].html = html;
                    });
                    return {
                        results: json.data
                    };
                }
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            },
            escapeMarkup: function(markup) {
                return markup;
            },
            templateResult: function(data) {
                return data.html;
            },templateSelection: function(data) {
                console.log(data);
                if(data.id != ""){
                    return "<b>"+data.codigo+"</b>&nbsp;&nbsp;"+data.nombre;
                }
                return data.text;
            },
            placeholder:'Seleccione una opción',
            minimumInputLength:4,
            allowClear: true,
            language: "es"
        });
        $("#giro_comercial_solicitante").change(function(){
            $("#giro_comercial_hidden").val($(this).val());
        });
        //
        // Funciones de datos laborales

        function validarDatosLaborales(){
            var error=false;
            $(".datoLaboral").each(function(){
                if($(this).val() == ""){
                    $(this).prev().css("color","red");
                    error = true;
                }
            });

            return error;
        }

        $("#btnGuardarDatoLaboral").on("click",function(){
            if(!validarDatosLaborales()){
                $.ajax({
                    url:"/partes/datoLaboral",
                    type:"POST",
                    dataType:"json",
                    data:{
                        id : $("#dato_laboral_id").val(),
                        puesto : $("#puesto").val(),
                        ocupacion_id : $("#ocupacion_id").val(),
                        nss : $("#nss").val(),
                        remuneracion : $("#remuneracion").val(),
                        periodicidad_id : $("#periodicidad_id").val(),
                        labora_actualmente : $("#labora_actualmente").is(":checked"),
                        fecha_ingreso : dateFormat($("#fecha_ingreso").val()),
                        fecha_salida : dateFormat($("#fecha_salida").val()),
                        jornada_id : $("#jornada_id").val(),
                        horas_semanales : $("#horas_semanales").val(),
                        parte_id:$("#parte_id").val(),
                        resolucion:$("#resolucion_dato_laboral").val(),
                        comida_dentro:0,
                        _token:"{{ csrf_token() }}"
                    },
                    success:function(data){
                        if(data != null && data != ""){
                            swal({title: 'ÉXITO',text: 'Se modificaron los datos laborales correctamente',icon: 'success'});
                            $("#modal-dato-laboral").modal("hide");
                        }else{
                            swal({title: 'Error',text: 'Algo salió mal',icon: 'warning'});
                        }
                    },error:function(data){
                        console.log(data);
                        var mensajes = "";
                        $.each(data.responseJSON.errors, function (key, value) {
                            console.log(key.split("."));
                            console.log(value);
                            var origen = key.split(".");

                            mensajes += "- "+value[0]+ " del "+origen[0].slice(0,-1)+" "+(parseInt(origen[1])+1)+" \n";
                        });
                        swal({
                            title: 'Error',
                            text: 'Es necesario validar los siguientes campos \n'+mensajes,
                            icon: 'error'
                        });
                    }
                });
            }else{
                swal({title: 'Error',text: 'Llena todos los campos',icon: 'warning'});
            }
        });

        /*
        Funcion para esconder o mostrar fecha de salida en funsion a campo labora actualmente
        */
        $("#labora_actualmente").change(function(){
            if($("#labora_actualmente").is(":checked")){
                $("#divFechaSalida").hide();
                $("#fecha_salida").removeAttr("required");
            }else{
                $("#fecha_salida").attr("required","");
                $("#divFechaSalida").show();
            }
        });

        function DatosLaborales(parte_id){
            $("#parte_id").val(parte_id);
            $.ajax({
                url:"/partes/datoLaboral/"+parte_id,
                type:"GET",
                dataType:"json",
                success:function(data){
                    if(data != null && data != ""){
                        $("#dato_laboral_id").val(data.id);
                        // getGiroEditar("solicitante");
                        $("#ocupacion_id").val(data.ocupacion_id);
                        $("#nss").val(data.nss);
                        $("#no_issste").val(data.no_issste);
                        $("#remuneracion").val(data.remuneracion);
                        $("#periodicidad_id").val(data.periodicidad_id);
                        if(data.labora_actualmente != $("#labora_actualmente").is(":checked")){
                            $("#labora_actualmente").click();
                            $("#labora_actualmente").trigger("change");
                        }
                        $("#puesto").val(data.labora_actualmente);
                        $("#fecha_ingreso").val(dateFormat(data.fecha_ingreso,4));
                        $("#fecha_salida").val(dateFormat(data.fecha_salida,4));
                        console.log(data.jornada_id);
                        $("#jornada_id").val(data.jornada_id);
                        $("#horas_semanales").val(data.horas_semanales);
                        $("#resolucion_dato_laboral").val(data.resolucion);
                        $(".catSelect").trigger('change')
                    }
                    $("#modal-dato-laboral").modal("show");
                }
            });
        }
        $("#btnNegarCancelacion").on("click",function(){
           swal({
                title: 'Advertencia',
                text: 'Al oprimir aceptar se negará la reprogramación de la audiencia, ¿Esta seguro de continuar?',
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
                        url:"/audiencia/negarCancelacion/{{ $audiencia->id }}",
                        type:"GET",
                        dataType:"json",
                        success:function(data){
                            if(data != null && data != ""){
                                swal({
                                    title: 'Correcto',
                                    text: 'Se negó la reprogramación',
                                    icon: 'success'
                                });
                                location.reload();
                            }else{
                                swal({
                                    title: 'Algo salió mal',
                                    text: 'No se guardo el registro',
                                    icon: 'warning'
                                });
                            }
                        }
                    });
                }
            });
        });
        $("#btnAprobarCancelacion").on("click",function(){
           $("#modal-reprogramacion").modal("show");
        });

        function getEtapasAudiencia(){
            $.ajax({
                url:'/api/etapa_resolucion_audiencia/audiencia/'+$("#audiencia_id").val(),
                type:"GET",
                dataType:"json",
                async:false,
                data:{
                },
                success:function(data){
                    try{
                        setPasosAudiencia(data)
                    }catch(error){
                        console.log(error);
                    }
                }
            });
        }
        function setPasosAudiencia(etapas){
            $.each(etapas, function (key, value) {
                var pasoActual = value.etapa_resolucion_id;
                $(".showTime"+pasoActual).text(value.updated_at);
                $("#step"+pasoActual).show();
                if(pasoActual != 1){
                    console.log(typeof value.evidencia);
                    if(value.evidencia == "true"){
                        if(pasoActual == 2){
                            if(!$("#explico_acta").is(":checked")){
                                $("#explico_acta").click();
                            }
                        }
                    }else{
                        $("#evidencia"+pasoActual).val(value.evidencia)
                    }
                }
                $("#icon"+pasoActual).css("background","lightgreen");
            });
        }
        function getDatosLaboralesParte(idParte){
            $.ajax({
                url:"/api/conceptos-resolucion/getLaboralesConceptos",
                type:"POST",
                dataType:"json",
                data:{
                    // audiencia_id:'{{ $audiencia->id }}',idParte
                    solicitante_id:idParte
                    // solicitante_id:$("#parte_solicitante_id").val()
                },
                success:function(datos){
                    let dato = datos.data;

                    $('#remuneracionDiaria').val(dato.remuneracionDiaria);
                    $('#salarioMinimo').val(dato.salarioMinimo);
                    $('#antiguedad').val(dato.antiguedad);
                    $('#idSolicitante').val(dato.idParte);

                    let table = "";
                    table+=" <tr>";
                    table+=' <th>Indemnización constitucional</th><td class="amount"> $'+ (dato.completa.indemnizacion).toLocaleString("en-US")+'</td><td class="amount" > $'+ (dato.al50.indemnizacion).toLocaleString("en-US") +'</td>';
                    table+=" </tr>";
                    table+=" <tr>";
                    table+=' <th>Aguinaldo</th><td class="amount"> $'+ (dato.completa.aguinaldo ).toLocaleString("en-US") +'</td><td class="amount"> $'+ (dato.al50.aguinaldo).toLocaleString("en-US").toLocaleString("en-US") +"</td>";
                    table+=" </tr>";
                    table+=" <tr>";
                    table+=' <th>Vacaciones</th><td class="amount"> $'+ (dato.completa.vacaciones).toLocaleString("en-US") +'</td><td class="amount"> $'+ (dato.al50.vacaciones).toLocaleString("en-US").toLocaleString("en-US") +"</td>";
                    table+=" </tr>";
                    table+=" <tr>";
                    table+=' <th>Prima vacacional</th><td class="amount"> $'+ (dato.completa.prima_vacacional ).toLocaleString("en-US") +'</td><td class="amount"> $'+ (dato.al50.prima_vacacional).toLocaleString("en-US") +"</td>";
                    table+=" </tr>";
                    table+=" <tr>";
                    table+=' <th>Prima antigüedad</th><td class="amount"> $'+ (dato.completa.prima_antiguedad ).toLocaleString("en-US") +'</td><td class="amount"> $'+ (dato.al50.prima_antiguedad).toLocaleString("en-US") +"</td>";
                    table+=" </tr>";
                    table+=" <tr>";
                    table+=' <th style=> TOTAL PRESTACIONES LEGALES</th><td class="amount"> $'+ (dato.completa.total ).toLocaleString("en-US") +'</td><td class="amount"> $'+ (dato.al50.total).toLocaleString("en-US") +"</td>";
                    table+=" </tr>";
                    $('#tbodyPropuestas'+dato.idParte).html(table);
                }
            });
        }
    </script>
@endpush
