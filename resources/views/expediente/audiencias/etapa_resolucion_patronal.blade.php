@extends('layouts.default')

@section('title', 'Calendario')

@include('includes.component.datatables')
@include('includes.component.pickers')
@include('includes.component.calendar')
@include('includes.component.dropzone')
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
        .upper{
            text-transform: uppercase;
        }
        .needed:after {
            color:darkred;
            content: " (*)";
        }
        #ui-datepicker-div {z-index:9999 !important}
    </style>
@section('content')
<!-- begin breadcrumb -->
<ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="javascript:;">Inicio</a></li>
    <li class="breadcrumb-item"><a href="javascript:;">Audiencias</a></li>
    <li class="breadcrumb-item active">Guía Audiencia</li>
</ol>
<!-- end breadcrumb -->
<!-- begin page-header -->
<h1 class="page-header">Gu&iacute;a Resoluci&oacute;n <small>pasos para cumplir la audiencia</small></h1>
<!-- end page-header -->
<h1 class="badge badge-secondary col-md-2 offset-10" style="position: fixed; font-size: 2rem; z-index:999;" onclick="startTimer();"><span class="countdown">00:00:00</span></h1>
<input type="hidden" id="audiencia_id" name="audiencia_id" value="{{$audiencia->id}}" />
<input type="hidden" id="atiende_virtual" name="atiende_virtual" value="{{$atiende_virtual}}" />
<input type="hidden" id="virtual" name="virtual" value="{{$virtual}}" />

@if(auth()->user()->persona_id == $conciliador->persona_id)
<!-- begin timeline -->
<div id="confirmacion_virtual" class="col-md-12 row" style="{{($atiende_virtual && $virtual) ? '' : 'display:none'}}">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <h5>Proceso virtual</h5>
        <hr class="red">
        <input type="text" id="url_virtual" class="form-control" placeholder="Url virtual" value="{{$url_virtual}}">
        <p class="help-block needed">Url virtual</p>
        <button class="btn btn-primary" onclick="guardarUrlVirtual()">Guardar</button>
    </div>
</div>
<ul class="timeline" id="timeline_etapas" style="display:none">
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
        <div class="timeline-body" style="border: 1px solid black;">
                <div class="timeline-header">
                <span class="username"><a href="javascript:;">{{$etapa->nombre}}</a> <small></small></span>
                <span class="views showTime{{$etapa->paso}}"></span>
                </div>
            <div class="timeline-content" id="contentStep{{$etapa->paso}}">
                    <p>
                        @switch($etapa->paso)
                            @case(1)
                                {{-- <button class="btn btn-primary" style="float: right;" onclick="$('#modal-parte').modal('show')">Agregar Nuevo compareciente <em class="fa fa-plus"></em> </button> --}}
                                <p>Comparecientes</p>
                                <div class="col-md-12 ">
                                    <table style="font-size: small;" class="table table-striped table-bordered table-td-valign-middle">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap">Tipo Parte</th>
                                                <th class="text-nowrap">Nombre de la parte</th>
                                                <th class="text-nowrap" >Acci&oacute;n</th>
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
                                                        @if(($parte->tipo_persona_id == 2) || ($parte->tipo_parte_id == 2 && $parte->tipo_persona_id == 2))
                                                        <div class="md-2" style="display: inline-block;">
                                                            <button onclick="AgregarRepresentante({{$parte->id}})" class="btn btn-xs btn-primary btnAgregarRepresentante" title="Agregar Representante Legal" data-toggle="tooltip" data-placement="top">
                                                                <i class="fa fa-plus"></i>
                                                            </button>
                                                        </div>
                                                        @endif
                                                        @if($parte->tipo_parte_id == 2)
                                                        <div class="md-2" style="display: inline-block;">
                                                            <button onclick="DatosLaborales({{$parte->id}})" class="btn btn-xs btn-primary btnAgregarRepresentante" title="Verificar Datos Laborales" data-toggle="tooltip" data-placement="top">
                                                                <i class="fa fa-briefcase"></i>
                                                            </button>
                                                        </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>



                                <!--<input type="text" id="evidencia{{$etapa->paso}}" />-->
                                <button class="btn btn-primary" align="center" id="btnCargarComparecientes">Continuar </button>
                                @break
                            @case(2)
                                <div id="divPaso1" class="col-md-12">
                                    <b><h5>Paso 1: Principio de conciliación personal</h5></b>
                                    <p>
                                        <i>Para el conciliador:</i>
                                        No es necesario que la parte trabajadora acuda con representante legal y si acudiera no se le reconocerá como tal; aunque podrá comparecer como acompañante.
                                    </p>
                                    <p>
                                        <u><i>EL CONCILIADOR LEERÁ EL SIGUIENTE TEXTO, DIRIGIÉNDOSE AL TRABAJADOR</i></u> “La conciliación es personal. Aunque asista el trabajador con el apoyo de cualquier persona de su confianza, el trabajador mismo es quien decide lo que pide, lo que negocia y lo que acepta o no en este proceso.”.
                                    </p>
                                    <div >
                                        {{-- <input type="hidden" /> --}}
                                        {{-- onclick="if($('#explico_acta').is(':checked')){nextStep({{$etapa->paso}})}else{swal({title: 'Error',text: 'Es necesario seleccionar la opción para continuar',icon: 'error'});}" --}}
                                        <input type="checkbox" value="1" data-render="switchery" data-theme="default" id="paso1" name='paso1' onchange="if( $('#paso1').is(':checked')){ $('#divPaso2').show() }else{ $('#divPaso5').hide(); swal({title: 'Error',text: 'Es necesario validar la sección para continuar',icon: 'error'});}"/>
                                    </div>
                                    <hr/>
                                </div>
                                <div id="divPaso2" class="col-md-12" style="display: none">
                                    <b><h5>Paso 2: Principio de conciliación confidencial</h5></b>
                                    <p>
                                        <i>Para el conciliador:</i>
                                        Lo dicho en la audiencia de conciliación es confidencial y no constituye prueba en ningún procedimiento jurisdiccional.
                                    </p>
                                    <p>
                                        <u><i>EL CONCILIADOR LEERÁ A LAS PARTES</i></u>“La conciliación es confidencial. Lo que se dice y se habla en esta audiencia es confidencial, no puede afectar sus derechos, ni puede ser una prueba en cualquier juicio.”.
                                    </p>
                                    <div >
                                        <input type="checkbox" value="1" data-render="switchery" data-theme="default" id="paso2" name='paso2' onchange="if( $('#paso2').is(':checked')){ $('#divPaso3').show() }else{ $('#divPaso5').hide(); swal({title: 'Error',text: 'Es necesario validar la sección para continuar',icon: 'error'});}"/>
                                    </div>
                                    <hr/>
                                </div>
                                <div id="divPaso3" class="col-md-12" style="display: none">
                                    <b><h5>Paso 3: Los principios y derechos en el proceso de la conciliación</h5></b>
                                    <p>
                                        <i>Para el conciliador:</i>
                                        Explicar las características de la conciliación y los derechos de las partes en ella. Recuerde que el proceso de conciliación se realiza en conformidad con los principios constitucionales de legalidad, imparcialidad, confiabilidad, eficacia, objetividad, profesionalismo, transparencia y publicidad. Es importante mencionar a las partes que en algunas ocasiones el conciliador considera necesario hablar con cada una de las partes por separado durante el proceso de negociación. Se realizan estas consultas con cada una de las partes para coadyuvar con el proceso de conciliación, conforme a los principios antes mencionados de este procedimento.
                                    </p>
                                    <p>
                                        <u><i>EL CONCILIADOR LEERÁ A LAS PARTES</i></u> “La conciliación es un proceso ágil, objetivo, imparcial, transparente y eficaz. Cada una de las partes tendrá derecho de hablar y de ser escuchada, de plantear, de negociar y de responder. Es un proceso voluntario, no se obligará a nadie a un acuerdo que no quiere. Nos trataremos todos con respeto en esta audiencia. En algún momento de la audiencia es posible que para avanzar la conciliación se tenga que hablar por separado con cada una de las partes, lo que se realiza en algunos casos con el ánimo de arreglar el conflicto, siguiendo los mismos principios mencionados de la conciliación.”.
                                    </p>
                                    <div >
                                        <input type="checkbox" value="1" data-render="switchery" data-theme="default" id="paso3" name='paso3' onchange="if( $('#paso3').is(':checked')){ $('#divPaso4').show() }else{ $('#divPaso5').hide(); swal({title: 'Error',text: 'Es necesario validar la sección para continuar',icon: 'error'});}"/>
                                    </div>
                                    <hr/>
                                </div>
                                <div id="divPaso4" class="col-md-12" style="display: none">
                                    <b><h5>Paso 4: Los beneficios de la conciliación: </h5></b>
                                    <p>
                                        <i>Para el conciliador:</i>
                                        Debe explicar los beneficios de la conciliación en comparación con los costos, la incertidumbre y el desgaste de una demanda.
                                    </p>
                                    <p>
                                        <u><i>EL CONCILIADOR LEERÁ A LAS PARTES</i></u> “La conciliación busca solucionar conflictos de interés entre las partes.
                                        <ul>
                                            <li>Buscamos un acuerdo que beneficie a ambos.</li>
                                            <li>El proceso de una demanda implica tiempo y esfuerzo. En una demanda se debe comprobar lo que se afirma, por esto, ni el patrón ni el trabajador puede estar seguro de ganar el juicio.</li>
                                            <li>Tanto para el trabajador como para el patrón, conviene la conciliación para evitar los costos y el desgaste.” </li>
                                        </ul>
                                    </p>
                                    <div >
                                        <input type="checkbox" value="1" data-render="switchery" data-theme="default" id="paso4" name='paso4' onchange="if( $('#paso4').is(':checked')){ $('#divPaso5').show() }else{ $('#divPaso5').hide(); swal({title: 'Error',text: 'Es necesario validar la sección para continuar',icon: 'error'});}"/>
                                    </div>
                                    <hr/>
                                </div>
                                <input type="hidden" id="evidencia{{$etapa->paso}}" value="true" />
                                <div class="col-md-12" id="divPaso5" style="display: none">
                                    <div class="col-md-12" style="margin-bottom: 5%">
                                        <div >
                                            <span class="text-muted m-l-5 m-r-20" for='switch1'>Los principios, derechos y beneficios de la conciliación fueron explicados por el conciliador a las partes.</span>
                                        </div>
                                        <div >
                                            <input type="hidden" />
                                            <input type="checkbox" value="1" data-render="switchery" data-theme="default" id="explico_acta" name='explico_acta'/>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btnPaso{{$etapa->paso}}" onclick="if($('#explico_acta').is(':checked')){nextStep({{$etapa->paso}})}else{swal({title: 'Error',text: 'Es necesario validar la explicación para continuar',icon: 'error'});}">Continuar </button>
                                </div>
                            @break
                            @case(3)
                                <p>Darle la palabra a la parte solicitante y luego a la parte citada. </p>
                                <p>Recordando que la conciliación es un proceso sin formalismos, podrán hablar ambas partes las veces necesarias. </p>
                                <p>Al terminar las partes sus primeras manifestaciones usted debe redactar en el espacio indicado un resumen de lo dicho. Las partes deben estar de acuerdo con este resumen, que se transcribirá por sistema en el acta de audiencia. </p>
                                <textarea class="form-control textarea" placeholder="Describir resumen de lo sucedido ..." type="text" id="evidencia{{$etapa->paso}}" >
                                </textarea>
                                <button class="btn btn-primary btnPaso{{$etapa->paso}}" onclick="nextStep({{$etapa->paso}})">Continuar </button>

                            @break
                            @case(4)
                            <div class="accordion" id="accordionExample">
                                <input type="hidden" id="totalCompleta" />
                                <input type="hidden" id="totalAl50" />
                                <input type="hidden" id="totalConfig" />
                                {{-- <input type="hidden" id="remuneracionDiaria" /> --}}
                                <p>
                                    El sistema le muestra 2 opciones de propuestas de convenio:
                                    <ol>
                                        <li>El cálculo del 100% considerando indemnización, partes proporcionales de prestaciones y prima de antigüedad. </li>
                                        <li>El mismo cálculo con 50% de la indemnización constitucional, 50% de la prima de antigüedad y el 100% de las partes proporcionales de vacaciones, prima vacacional y aguinaldo. </li><br>
                                    </ol>
                                    Usted puede escoger una de estas alternativas precargadas, la alternativa de un convenio de reinstalación, o puede escoger una propuesta que configurará con base en las negociaciones entre las partes. Al escoger la propuesta OTRA, tendrá que indicar cada una de las prestaciones que se incluirá, y el número de días o monto de cada prestación. El sistema creará una tabla para mostrar el monto de cada prestación seleccionada y el total de la propuesta. La opción que usted selecciona será la propuesta de arreglo que se mostrará en el acta de audiencia.
                                </p>
                                @foreach($audiencia->citadosComparecientes as $solicitante)
                                {{-- <pre>{{$solicitante->parte->id}}</pre> --}}
                                    <div class="card">
                                    <div class="card-header" id="headingOne">
                                        <h2 class="mb-0">
                                        <button id='coll{{$solicitante->parte->id}}' class="btn btn-link card-header collapseSolicitante" idCitado="{{$solicitante->parte->id}}" type="button" data-toggle="collapse" data-target="#collapse{{$solicitante->parte->id}}" aria-expanded="true" aria-controls="collapseOne" parteSelect={{$solicitante->parte->id}} onclick="getDatosLaboralesParte({{$solicitante->parte->id}});" >
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
                                            <input type="hidden" id="tiempoVencido"/>
                                            <input type="hidden" id="idCitado"/>
                                            <div>
                                                Datos laborales:
                                                <ul>
                                                    <li id="salario{{$solicitante->parte->id}}"> </li>
                                                    <li id="fechaIngreso{{$solicitante->parte->id}}"> </li>
                                                    <li id="fechaSalida{{$solicitante->parte->id}}"> </li>
                                                </ul>
                                            </div>
                                            <br>
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
                                                            <input class="form-check-input" type="radio" name="radiosPropuesta{{$solicitante->parte->id}}" id="radioCompleta" value="completa">
                                                            <label class="form-check-label" for="radioCompleta">
                                                                100% de indemnizaci&oacute;n
                                                            </label>
                                                        </div>
                                                        <div class="form-check row" style="margin-top: 2%;">
                                                            <input class="form-check-input" type="radio" name="radiosPropuesta{{$solicitante->parte->id}}" id="radioAl50" value="al50">
                                                            <label class="form-check-label" for="radioAl50">
                                                                50% de indemnizaci&oacute;n
                                                            </label>
                                                        </div>
                                                        <div class="row">
                                                            <div class="form-check col-md-1 " style="margin-top: 2%;">
                                                                <input class="form-check-input radiosPropuestas" type="radio" name="radiosPropuesta{{$solicitante->parte->id}}" id="radioOtro" value="otra">
                                                                <label class="form-check-label" for="radioOtro">
                                                                    Otro
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="form-check row" style="margin-top: 2%;">
                                                            <input class="form-check-input radiosPropuestas" type="radio" name="radiosPropuesta{{$solicitante->parte->id}}" id="radioReinstalacion" value="reinstalacion">
                                                            <label class="form-check-label" for="radioReinstalacion">
                                                                Reinstalaci&oacute;n
                                                            </label>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-2 offset-10" style="" id="btnConfig">
                                                    <a onclick="cargarConfigConceptos();" class="btn btn-primary">Configurar</a>
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
                                <p> El conciliador debe incluir una explicación y motivación breve de la propuesta que fue negociada entre las partes, acreditando que en este caso no hubo renuncia de derechos.</p>
                                <textarea class="form-control textarea" placeholder="Comentarios ..." type="text" id="evidencia{{$etapa->paso}}" >
                                </textarea>
                                <button class="btn btn-primary btnPaso{{$etapa->paso}}" onclick="nextStep({{$etapa->paso}})">Continuar </button>
                            @break
                            @case(5)
                                <p>Darle la palabra a la parte solicitante y luego a la parte citada. </p>
                                <p>Recordando que la conciliación es un proceso sin formalismos, podrán hablar ambas partes las veces necesarias. </p>
                                <p>Al terminar las partes sus segundas manifestaciones, usted debe redactar en el espacio indicado un resumen de lo dicho. Las partes deben estar de acuerdo con este resumen,  que se transcribirá por sistema en el acta de audiencia. </p>
                                <textarea class="form-control textarea" placeholder="Describir resumen de lo sucedido ..." type="text" id="evidencia{{$etapa->paso}}" >
                                </textarea>
                                <button class="btn btn-primary btnPaso{{$etapa->paso}}" onclick="nextStep({{$etapa->paso}})">Continuar </button>
                            @break
                            @case(6)
                                <label>Debe indicar cuál de las siguientes resoluciones de audiencia procede:</label>
                                <ul>
                                    <li>Convenio.</li>
                                    <li>Agendar segunda audiencia.</li>
                                    <li>Constancia de no conciliación.</li>
                                </ul>
                                <div class="col-md-offset-3 col-md-6 ">
                                    <div class="form-group">
                                        <label for="resolucion_id" class="col-sm-6 control-label">Resolución</label>
                                        <div class="col-sm-10">
                                            {!! Form::select('resolucion_id', isset($resoluciones) ? $resoluciones : [] ,isset($audiencia->resolucion_id) ? $audiencia->resolucion_id :null , ['id'=>'resolucion_id', 'required','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-offset-3 col-md-12" id="divLaboralesExtras" style="display: none">
                                    <table class="table table-striped table-bordered table-td-valign-middle">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap">Tipo Parte</th>
                                                <th class="text-nowrap">Nombre de la parte</th>
                                                <th class="text-nowrap" style="width: 10%;">Datos Laborales</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($audiencia->citadosComparecientes as $solicitante)
                                            {{-- @if($parte->tipo_parte_id == 1) --}}
                                                <tr>
                                                    <td class="text-nowrap">{{ $solicitante->parte->tipoParte->nombre }}</td>
                                                    @if($solicitante->parte->tipo_persona_id == 1)
                                                        <td class="text-nowrap">{{ $solicitante->parte->nombre }} {{ $solicitante->parte->primer_apellido }} {{ $solicitante->parte->segundo_apellido }}</td>
                                                    @else
                                                        <td class="text-nowrap">{{ $solicitante->parte->nombre_comercial }}</td>
                                                    @endif
                                                    <td>
                                                        @if($solicitante->parte->tipo_parte_id == 2)
                                                        <div style="display: inline-block;">
                                                            <button onclick="DatosLaborales({{$solicitante->parte->id}},true)" class="btn btn-xs btn-primary" title="Datos Laborales">
                                                                <i class="fa fa-briefcase"></i>
                                                            </button>
                                                        </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            {{-- @endif --}}
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12" style="margin-bottom: 5%">
                                    <div >
                                    <input type="checkbox" data-render="switchery" data-theme="default" id="switchAdicionales" name='elementosAdicionales' onchange=" if($('#switchAdicionales').is(':checked')){ $('#modal-pago-diferido').modal('show'); $('#textAdicional').show(); $('#pagosDiferidos').show(); }else{$('#textAdicional').hide(); $('#pagosDiferidos').hide();}"/>
                                    </div>
                                    <div >
                                        <span class="text-muted m-l-5 m-r-20" for='switchAdicionales'>Señalar en este espacio las fechas de pagos diferidos con el monto a pagar en cada fecha. Se permiten fechas diferidas hasta un mes natural a partir de la fecha de convenio.</span>
                                        {{-- $('#textAdicional').show();  --}}
                                    </div>
                                </div>
                                <div id="textAdicional" style="display:none">
                                    <textarea class="form-control textarea" placeholder="Describir..." type="text" id="evidencia{{$etapa->paso}}">
                                    </textarea>
                                </div>
                                <div id="pagosDiferidos" style="display:none">
                                    <div class="col-md-12" id="divPagosAcordados" >
                                        <input type="hidden" id="totalPagosDiferidos">
                                        <div class="form-group col-md-2 offset-10" id="btnConfigPagos">
                                            <a onclick="$('#modal-pago-diferido').modal('show');" class="btn btn-primary">Configurar</a>
                                        </div>
                                        <br>
                                        <div class="col-md-8 offset-md-2">
                                            <table class="table table-bordered" >
                                                <thead>
                                                    <tr>
                                                        <th>Citado</th>
                                                        <th>Fecha de pago</th>
                                                        <th>Monto</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbodyFechaPagoPrincipal">
                                                </tbody>
                                            </table>
                                        </div>
                                        <hr>
                                    </div>
                                </div>
                                {{-- <button class="btn btn-primary btnPaso{{$etapa->paso}}" onclick="finalizar({{$etapa->paso}})">Finalizar </button> --}}
                                <button class="btn btn-primary" onclick="finalizar()">Finalizar</button>
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
<!-- end timeline -->


<!--inicio modal para propuesta convenio-->
<div class="modal" id="modal-propuesta-convenio" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Propuesta de convenio</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h5></h5>
                <div class="col-md-12 row">
                    <div class="col-md-12" id="divConceptosAcordados" >
                        <h5>Conceptos de pago para resolucion</h5><br>
                        <label class="col-md-12 control-label">Ingresa los días a pagar o el monto por cada concepto de la propuesta. </label>
                        <div class="form-group">
                            <label for="concepto_pago_resoluciones_id" class="col-sm-6 control-label labelResolucion needed">Concepto de pago</label>
                            <div class="col-sm-10  select-otro" >

                                <select id="concepto_pago_resoluciones_id" class="form-control select-element conceptosPago" >
                                    <option value="">-- Selecciona un concepto de pago</option>
                                    @foreach($concepto_pago_resoluciones as $concepto)
                                        <option value="{{ $concepto->id }}">{{ $concepto->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-10 select-reinstalacion">
                                <select id="concepto_pago_reinstalacion_id" class="form-control select-element conceptosPago" style="display:none">
                                    <option value="">-- Selecciona un concepto de pago</option>
                                    @foreach($concepto_pago_reinstalacion as $concepto)
                                        <option value="{{ $concepto->id }}">{{ $concepto->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="dias" class="col-sm-6 control-label labelResolucion">D&iacute;as a pagar</label>
                                <div class="col-sm-12">
                                    <input type="text" id="dias" placeholder="D&iacute;as a pagar" class="form-control numero" />
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="monto" class="col-sm-6 control-label labelResolucion">Monto a pagar</label>
                                <div class="col-sm-12">
                                    <input type="text" id="monto" placeholder="Monto a pagar" class="form-control numero" />
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
                        <br>
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
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5" id="btnGuardarPropuestaConvenio"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Fin de modal propuesta convenio-->
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
                    <div class="col-md-12 row">
                        <div class="col-md-6">
                            <label >C&eacute;dula Profesional</label><br>
                            <span class="btn btn-primary fileinput-button m-r-3">
                                <i class="fa fa-fw fa-plus"></i>
                                <span>Seleccionar c&eacute;dula</span>
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
                                <option class='{{($clasificacion->tipo_archivo_id == 10) ? "archivo_sindical" : ""}}' value="{{$clasificacion->id}}">{{$clasificacion->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label id="labelInstrumento" class="needed">Documento de Instrumento</label>
                        <span class="btn btn-primary fileinput-button m-r-3">
                            <i class="fa fa-fw fa-plus"></i>
                            <span>Seleccionar instrumento</span>
                            <input type="file" id="fileInstrumento" name="files" max-size="32154">
                        </span>
                        <p style="margin-top: 1%;" id="labelInstrumentoRepresentante"></p>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="feha_instrumento" class="control-label needed">Fecha de instrumento</label>
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
                                <th style="width:20%; text-align: center;">Acción</th>
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
                            <input class="form-control datoLaboralExtra" data-parsley-type='integer' id="dias_aguinaldo" placeholder="Días de aguinaldo por año" type="text" value="">
                            <p class="help-block needed">Días de aguinaldo</p>
                        </div>
                    </div>
                    <div class="col-md-12 row">
                        <div class="col-md-12">
                            <input class="form-control datoLaboralExtra" id="prestaciones_adicionales" placeholder="Prestaciones adicionales" type="text" value="">
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
                            <label for="parte_solicitante_id" class="col-sm-6 control-label labelResolucion">Citado</label>
                            <div class="col-sm-10">
                                <select id="parte_solicitante_id" class="form-control select-element">
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
                    <button class="btn btn-warning btn-sm m-l-5" onclick="revisarDocumentos()"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- <div class="modal" id="modal-relaciones2" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Roles Solicitantes</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <hr>
                <h5>Registro de roles solicitantes</h5>
                <div class="col-md-12 row">
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
                            <label for="rol_solicitante_id" class="col-sm-6 control-label labelResolucion">Rol Solicitante</label>
                            <div class="col-sm-10">
                                <select id="rol_solicitante_id" class="form-control select-element">
                                    <option value="{{ $parte->parte->id }}">Responsable principal</option>
                                    <option value="{{ $parte->parte->id }}">Responsable solidario en el convenio</option>
                                    <option value="{{ $parte->parte->id }}">Testigo de la celebracion del convenio</option>
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
                                    <th>Solicitado</th>
                                    <th>Rol</th>
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
</div> --}}
<!-- Fin Modal de comparecientes y resolución individual-->
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
                            <strong>Fecha de audiencia: </strong><span id="spanFechaAudiencia"></span><br>
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

<!--Inicio modal para fechas de pagos diferidos convenio-->
<div class="modal" id="modal-pago-diferido" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Fechas para Pagos diferidos</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                {{-- <div class="col-md-12">
                    <h5>Total a pagar: </h5>
                    <label for="">5,000</label>
                </div><br> --}}
                <div class="col-md-12 row">
                    <div class="col-md-12" id="divPagosDiferidos" >
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pago_citado_id" class="col-sm-6 control-label labelResolucion">Citado</label>
                                    <div class="col-sm-12">
                                        <select id="pago_citado_id" class="form-control select-element">
                                            <option value="">-- Selecciona un citado --</option>
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
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="fecha_pago" class="col-sm-6 control-label labelResolucion">Fecha de pago</label>
                                <div class="col-sm-12">
                                    <input type="text" id="fecha_pago" placeholder="Fecha de pago" class="form-control" autocomplete="off" />
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="monto_pago" class="col-sm-6 control-label labelResolucion">Monto a pagar</label>
                                <div class="col-sm-12">
                                    <input type="text" id="monto_pago" placeholder="Monto a pagar" class="form-control numero" />
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="text-center">
                                <button class="btn btn-warning text-white btn-sm" id='btnAgregarFechaPago'><i class="fa fa-plus"></i> Agregar Fecha</button>
                            </div>
                        </div>
                        <br>
                        <div class="col-md-8 offset-md-2">
                            <table class="table table-bordered" >
                                <thead>
                                    <tr>
                                        <th>Citado</th>
                                        <th>Fecha</th>
                                        <th>Monto</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyFechaPago">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5" id="btnGuardarFechasPago"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Fin de modal propuesta convenio-->

<!--Inicio modal para fechas de pagos diferidos convenio-->
<div class="modal" id="modal-parte" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                @include('expediente.solicitudes.agregarParte',['tipo_solicitud_id'=>0])
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5" id="btnGuardarParte"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Fin de modal pagos diferidos-->

<!-- inicio Modal Preview-->
<div class="modal" id="modal-preview" data-backdrop="static" data-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <input type="hidden" id="totalDocumentos">
            <input type="hidden" id="noDocumento">
            <div class="modal-body" >
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                {{-- <h5>Valide los datos del documento.</h5> --}}
                <div id="documentoPreviewHtml" style="margin:0 5% 0 5%; max-height:600px; border:1px solid black; overflow: scroll; padding:2%;">

                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right row">
                    <a class="btn btn-white btn-sm" class="close" data-dismiss="modal" aria-hidden="true" ><i class="fa fa-times"></i> cancelar</a>
                    <a class="btn btn-primary btn-sm" id="sigDocumento" onclick="siguienteVistaPrevia()"  > Siguiente</a>
                    <a class="btn btn-primary btn-sm" id="guardarResolucion" onclick="GuardarResolucionMuchas()"  > Aceptar</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Fin Modal de Preview-->

<input type="hidden" id="parte_id">
<input type="hidden" id="parte_representada_id">
@else
    <div style="margin: 5%;" >
        <div >
            <a href="{{ url()->previous() }}" class="btn btn-primary btn-sm"><i class="fa fa-arrow-alt-circle-left"></i> Regresar</a>
        </div>
        <h1>El conciliador asignado para esta audiencia es: <strong>{{$conciliador->persona->nombre}} {{$conciliador->persona->primer_apellido}} {{$conciliador->persona->segundo_apellido}}</strong> . Si usted requiere realizar esta audiencia favor de solicitar la reasignación.</h1>
    </div>
@endif

@endsection
@push('scripts')
<script>
    var listaContactos = [];
    var listaPropuestas={};
    var listaConfigConceptos= {};
    var listaConfigFechas= [];
    var listaResolucionesIndividuales = [];
    var firstTimeStamp = "";
    var listVistaPrevia = [];
    $(document).ready(function(){
        $( "#accordion" ).accordion();

        $(".tipo_documento,.select-element,.catSelect").select2();
        $(".fecha").datetimepicker({format:"DD/MM/YYYY"});

        cargarGeneros();
        getEtapasAudiencia();
        cargarTipoContactos();
        FormMultipleUpload.init();
        Gallery.init();
        if($("#atiende_virtual").val() == 1){
            if($("#virtual").val() == 1 && $("#url_virtual").val() == ""){
                $("#timeline_etapas").hide();
            }else{
                $("#timeline_etapas").show();
            }
        }else{
            $("#timeline_etapas").show();
        }
    });
    $(".dateBirth").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: "c-80:",
        format:'dd/mm/yyyy',
    });
    function nextStep(pasoActual){
        let valido = true;
        if(pasoActual == 3 || pasoActual == 4 || pasoActual == 5 ){
            evidencia = $("#evidencia"+pasoActual).val();
            if(evidencia == ""){
                valido = false;
                mensaje = 'Debe registrar la justificación y/o manifestación correspondiente.';
            }
            // if(pasoActual == 4){
            //     valido = !validarPropuestas();
            //     mensaje = 'Debe seleccionar una propuesta para cada solicitante';
            // }
        }
        if(valido){
            var success = guardarEvidenciaEtapa(pasoActual);
            if(success){

                var siguiente = pasoActual+1;
                $("#icon"+pasoActual).css("background","lightgreen");
                $(".btnPaso"+pasoActual).text("Actualizar");
                $("#step"+siguiente).show();
                $('html,body').animate({
                    scrollTop: $("#contentStep"+siguiente).offset().top
                }, 'slow');
            }else{
                swal({title: 'Error',text: 'No se pudo guardar el registro',icon: 'error'});
            }
        }else{
            swal({title: 'Error',text: mensaje ,icon: 'error'});
        }
    }

    function continuarComparecencia(){
        getPersonasComparecer();
        $("#modal-comparecientes").modal("show");
    }

    $("#btnAgregarArchivo").on("click",function(){
        $("#btnCancelFiles").click();
        $("#modal-archivos").modal("show");
    });

    //files functions
    var handleJqueryFileUpload = function() {
        // Initialize the jQuery File Upload widget:
        $('#fileupload').fileupload({
            autoUpload: false,
            disableImageResize: /Android(?!.*Chrome)|Opera/.test(window.navigator.userAgent),
            maxFileSize: 5000000,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png|pdf)$/i,
            stop: function(e,data){
                cargarDocumentos();
            //   $("#modal-archivos").modal("hide");
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
        $('#fileupload').on('fileuploadsend', function (e, data) {

            // if(){
            //     e.preventDefault();
            // }
        })
        $('#fileupload').bind('fileuploadadd', function(e, data) {
            $('#fileupload [data-id="empty"]').hide();
            $(".catSelectFile").select2();
        });
        $('#fileupload').bind('fileuploaddone', function(e, data) {
            // console.log("add");
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
                $(this).fileupload('option', 'done')
                .call(this, $.Event('done'), {result: result});
        });
    };
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
    // end files function

    function cargarDocumentos(){
            $.ajax({
                url:"/audiencia/documentos/"+$("#audiencia_id").val(),
                type:"GET",
                dataType:"json",
                async:true,
                success:function(data){
                    try{
                        if(data != null && data != ""){
                            var html = "";
                            $.each(data, function (key, value) {
                                if(value.documentable_type == "App\\Parte"){
                                        // var parte = arraySolicitantes.find(x=>x.id == value.documentable_id);
                                        // if(parte != undefined){
                                            html += "<tr>";
                                            html += "<td>"+value.parte+"</td>";
                                            html += "<td>"+ value.clasificacion_archivo.nombre+"</td>";
                                            html += "</tr>";
                                            ratifican = true;
                                        // }
                                }
                            });
                            $("#tbodyRatificacion").html(html);
                            var table = "";
                            var div = "";
                            $.each(data, function(index,element){
                                div += '<div class="image gallery-group-1">';
                                div += '    <div class="image-inner" style="position: relative;">';
                                if(element.tipo == 'pdf' || element.tipo == 'PDF'){
                                    div += '            <a href="/api/documentos/getFile/'+element.uuid+'" data-toggle="iframe" data-gallery="example-gallery-pdf" data-type="url">';
                                    div += '                <div class="img" align="center">';
                                    div += '                    <i class="fa fa-file-pdf fa-4x" style="color:black;margin: 0;position: absolute;top: 50%;transform: translateX(-50%);"></i>';
                                    div += '                </div>';
                                    div += '            </a>';
                                }else{
                                    div += '            <a href="/api/documentos/getFile/'+element.uuid+'" data-toggle="lightbox" data-gallery="example-gallery" data-type="image">';
                                    div += '                <div class="img" style="background-image: url(\'/api/documentos/getFile/'+element.uuid+'\')"></div>';
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
                    }catch(error){
                        console.log(error);
                    }
                }
            });
        }

    function guardarEvidenciaEtapa(etapa){
        if(etapa==6){
            evidencia = ($('#switchAdicionales').is(':checked')) ? $("#evidencia"+etapa).val(): 'false';
        }else{
            evidencia = $("#evidencia"+etapa).val();
        }
        var respuesta = true;
        $.ajax({
            url:'/etapa_resolucion_audiencia',
            type:"POST",
            dataType:"json",
            async:false,
            data:{
                etapa_resolucion_id:etapa,
                audiencia_id:$("#audiencia_id").val(),
                evidencia: evidencia,
                elementos_adicionales: $('#switchAdicionales').is(':checked'),
                _token:"{{ csrf_token() }}"
            },
            success:function(data){
                try{
                    if(etapa==1){
                        location.reload();
                    }
                    respuesta = true;

                    $(".showTime"+etapa).text(data.data.created_at);
                }catch(error){
                }
            },
            error:function(error){
                // console.log(error);
                respuesta = false;
            }
        });
        return respuesta;
    }
    function getEtapasAudiencia(){
        $.ajax({
            url:'/api/etapa_resolucion_audiencia/audiencia/'+$("#audiencia_id").val(),
            type:"GET",
            dataType:"json",
            global:false,
            async:false,
            data:{
            },
            success:function(data){
                try{
                    setPasosAudiencia(data)
                }catch(error){
                    // console.log(error);
                }
            }
        });
    }
    function setPasosAudiencia(etapas){
        $.each(etapas, function (key, value) {
            var pasoActual = value.etapa_resolucion_id;
            $(".showTime"+pasoActual).text(value.updated_at);
            // if(value.updated_at != ""){
                // $('.btnPaso'+pasoActual).hide();
            // }
            var siguiente = pasoActual+1;
            switch (pasoActual) {
                case 1:
                    cargarComparecientes();
                    break;
                case 2://"checked":""
                    if(value.evidencia == "true"){
                        if(!$("#explico_acta").is(":checked")){
                            $("#explico_acta").click();
                        }
                        for (i=0; i<=5; i++) {
                            $("#paso"+i).click();
                        }
                    }

                    // $('.btnPaso').hide();
                    break;
                case 6:
                if(value.elementos_adicionales == "true"){
                        if(!$("#switchAdicionales").is(":checked")){
                            $("#switchAdicionales").click();
                        }
                        for (i=0; i<=5; i++) {
                            $("#paso"+i).click();
                        }
                    }
                    break;
                default:

                    $("#evidencia"+pasoActual).data("wysihtml5").editor.setValue(value.evidencia);
                    break;
            }


            $("#icon"+pasoActual).css("background","lightgreen");
            // $("#contentStep"+pasoActual).hide();
            $("#step"+siguiente).show();
            if(pasoActual == 1){
                firstTimeStamp = value.created_at;
            }

        });
        startTimer();
    }
    $('.textarea').wysihtml5({locale: 'es-ES'});

    /*
     * Aqui inician las funciones para administrar el paso 1
     *
     */
    $("#btnCargarComparecientes").on("click",function(){
        $.ajax({
            url:"/audiencia/validar_partes/{{$audiencia->id}}",
            type:"GET",
            dataType:"json",
            success:function(data){
                try{
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
                }catch(error){
                    console.log(error);
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
                    try{
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
                            startTimer();
                            nextStep(1);
                        }else if(data.data.tipo == 5){
                            swal({
                                title: '¿Qué deseas hacer?',
                                text: 'Detectamos que no todos los citados comparecieron, ¿Deseas continuar con el proceso de audiencia?, de indicar que no, se generará una nueva audiencia',
                                icon: 'info',
                                buttons: {
                                    roll: {
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
                                        startTimer();
                                        nextStep(1);
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
                    }catch(error){
                        console.log(error);
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
                title: '¿Está seguro?',
                text: 'No ha seleccionado ningun compareciente, el expediente se archivará por no comparecencia del solicitante',
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
                            try{
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
                            }catch(error){
                                console.log(error);
                            }
                        },
                        error:function(data){
                            swal({
                                title: 'Algo salió mal',
                                text: 'No se guardó el registro',
                                icon: 'warning'
                            });
                        }
                    });
                }
            });
        }
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
                try{
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
                }catch(error){
                    console.log(error);
                }
            }
        });
    }
    function cargarComparecientes(){
        $.ajax({
            url:"/audiencia/comparecientes/{{ $audiencia->id }}",
            type:"GET",
            dataType:"json",
            success:function(data){
                try{
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
                                htmlSolicitantes += "<option value='"+element.parte.id+"'>"+element.parte.nombre+' '+element.parte.primer_apellido+' '+(element.parte.segundo_apellido || "")+"</option>"
                            }else if(element.parte.tipo_parte_id == 2){
                                htmlCitados += "<option value='"+element.parte.id+"'>"+element.parte.nombre+' '+element.parte.primer_apellido+' '+(element.parte.segundo_apellido || "")+"</option>"
                            }

                            if(element.parte.tipo_parte_id == 3 && element.parte.parte_representada_id != null){
                                if(element.parte.parteRepresentada.tipo_parte_id == 1){
                                    htmlSolicitantes += "<option value='"+element.parte.id+"'>"+element.parte.nombre+' '+element.parte.primer_apellido+' '+(element.parte.segundo_apellido || "")+"</option>"
                                }else{
                                    htmlCitados += "<option value='"+element.parte.id+"'>"+element.parte.nombre+' '+element.parte.primer_apellido+' '+(element.parte.segundo_apellido || "")+"</option>"
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
                                window.location = "/audiencias/{{ $audiencia->id }}/edit";
                            }
                        }
                    }
                }catch(error){
                    console.log(error);
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
            return {error:true,comparecientes:[]};
        }
    }
    // Funciones para representante legal(Etapa 1)
    function getPersonasComparecer(){
        $.ajax({
            url:"/audiencia/fisicas/{{ $audiencia->id }}",
            type:"GET",
            dataType:"json",
            success:function(data){
                try{
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
                            table +='   <td>Falta identificación</td>';
                        }
                        table +='   </td>';
                        table +='</tr>';
                        options += '<option value="'+element.id+'">'+element.nombre+' '+element.primer_apellido+' '+element.segundo_apellido+'</option>';
                    });
                    $("#parte_relacionada").empty();
                    $("#parte_relacionada").html(options);
                    $("#tbodyPartesFisicas").html(table);
                    $("#resolucionVarias").hide();
                    $("#btnCancelarVarias").hide();
                    $("#btnConfigurarResoluciones").show();
                    $("#btnGuardarResolucionUna").show();
                    $("#modal-comparecientes").modal("show");
                }catch(error){
                    console.log(error);
                }
            }
        });
    }
    function AgregarRepresentante(parte_id){
        $.ajax({
            url:"/partes/representante/"+parte_id,
            type:"GET",
            dataType:"json",
            success:function(data){
                try{
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
                }catch(error){
                    console.log(error);
                }
            }
        });
    }
    function actualizarPartes(){
            $.ajax({
                url:"/partes/getComboDocumentos/{{isset($solicitud_id) ? $solicitud_id: '' }}",
                type:"GET",
                dataType:"json",
                success:function(data){
                    try{
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
                                        '            @if(isset($solicitud_id))';
                                        $.each(data, function(index,element){
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
                }catch(error){
                    console.log(error);
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
            global:false,
            dataType:"json",
            success:function(data){
                try{

                    $("#genero_id").html("<option value=''>-- Selecciona un género</option>");
                    if(data.data.length > 0){
                        $.each(data.data,function(index,element){
                            $("#genero_id").append("<option value='"+element.id+"'>"+element.nombre+"</option>");
                        });
                    }
                    $("#genero_id").trigger("change");
                }catch(error){
                    console.log(error);
                }
            }
        });
    }
    function cargarTipoContactos(){
        $.ajax({
            url:"/tipos_contactos",
            type:"GET",
            global:false,
            dataType:"json",
            success:function(data){
                try{
                    if(data.data.total > 0){
                        $("#tipo_contacto_id").html("<option value=''>-- Selecciona un tipo de contacto</option>");
                        $.each(data.data.data,function(index,element){
                            $("#tipo_contacto_id").append("<option value='"+element.id+"'>"+element.nombre+"</option>");
                        });
                    }else{
                        $("#tipo_contacto_id").html("<option value=''>-- Selecciona un tipo de contacto</option>");
                    }
                    $("#tipo_contacto_id").trigger("change");
                }catch(error){
                    console.log(error);
                }
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
                        text: 'El contacto debe tener 10 dígitos de tipo número',
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
                    global:false,
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
    $("#btnGuardarPropuestaConvenio").on("click",function(){
        let idSol = parseInt($("#idCitado").val());
        if((Object.keys(listaConfigConceptos).length  <= 0) || (typeof(listaConfigConceptos[idSol])!=='undefined') && (Object.keys(listaConfigConceptos[idSol]).length <= 0)) {
            swal({
                title: 'Error',
                text: 'Debe registrar al menos un concepto de pago para esta propuesta ',
                icon: 'error',
            });
        }else{
            var objeto_propuesta = {};
            objeto_propuesta.indemnizacion90 = $("#indemnizacion90").val();
            objeto_propuesta.indemnizacion45 = $("#indemnizacion45").val();
            objeto_propuesta.aguinaldo = $("#aguinaldo").val();
            objeto_propuesta.vacaciones = $("#vacaciones").val();
            objeto_propuesta.prima_vacacional = $("#prima_vacacional").val();
            objeto_propuesta.prima_antiguedad = $("#prima_antiguedad").val();
            objeto_propuesta.prestaciones_legales = $("#prestaciones_legales").val();
            objeto_propuesta.prestaciones_45 = $("#prestaciones_45").val();
            $("#tableOtro").show();
            $("#modal-propuesta-convenio").modal('hide')
            // formarTablaPropuestaConvenio();
        }
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
            formData.append('solicitud_id', '{{$solicitud_id}}');
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
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    var progreso = 0;
                     // Download progress
                     xhr.addEventListener("progress", function(evt){
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            // Do something with download progress
                            console.log(percentComplete);
                            $('#progress-bar').show();
                            var percent = parseInt(percentComplete * 100)
                            $("#progressbar-ajax-value").text(percent+"%");;
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
                    xhr.upload.addEventListener("progress", function(evt){
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            //Do something with upload progress
                            console.log(percentComplete);
                            $('#progress-bar').show();
                            var percent = parseInt(percentComplete * 100)
                            $("#progressbar-ajax-value").text(percent+"%");;
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
                url:"/partes/representante",
                type:"POST",
                dataType:"json",
                processData: false,
                contentType: false,
                data:formData,
                success:function(data){
                    try{
                        if(data != null && data != ""){
                            swal({title: 'ÉXITO',text: 'Se agregó el representante',icon: 'success'});
                            actualizarPartes();
                            $("#modal-representante").modal("hide");
                        }else{
                            swal({title: 'Error',text: 'Error al guardar representante',icon: 'warning'});
                        }
                    }catch(error){
                        console.log(error);
                        swal({title: 'Error',text: 'Error al guardar representante',icon: 'warning'});
                    }
                },
                error: function(){
                    swal({title: 'Error',text: 'No se pudo capturar el representante legal, revisa que el tamaño de tus documentos nos sea mayo a 10M ',icon: 'warning'});
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
        if($("#parte_id").val() == ""){
            if($("#fileIdentificacion").val() == "" ){
                $("#labelIdentificacion").css("color","red");
                error = true;
            }
            if($("#fileInstrumento").val() == ""){
                $("#labelInstrumento").css("color","red");
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
    $("#resolucion_id").on("change",function(){
        if($("#resolucion_id").val() == 1){
            $('#divLaboralesExtras').show();
        }else{
            $('#divLaboralesExtras').hide();
        }
    });


    // Funciones para Datos laborales(Etapa 1,6)
    function DatosLaborales(parte_id,extra=null){
        $("#parte_id").val(parte_id);
        $.ajax({
            url:"/partes/datoLaboral/"+parte_id,
            type:"GET",
            dataType:"json",
            success:function(data){
                try{
                    if(data != null && data != ""){
                        $("#dato_laboral_id").val(data.id);
                        // getGiroEditar("solicitante");
                        $("#ocupacion_id").val(data.ocupacion_id);
                        $("#nss").val(data.nss);
                        //$("#no_issste").val(data.no_issste);
                        $("#remuneracion").val(data.remuneracion);
                        $("#periodicidad_id").val(data.periodicidad_id);
                        if(data.labora_actualmente != $("#labora_actualmente").is(":checked")){
                            $("#labora_actualmente").click();
                            $("#labora_actualmente").trigger("change");
                        }
                        $("#puesto").val(data.puesto);
                        $("#fecha_ingreso").val(dateFormat(data.fecha_ingreso,4));
                        $("#fecha_salida").val(dateFormat(data.fecha_salida,4));
                        // console.log(data.jornada_id);
                        $("#jornada_id").val(data.jornada_id);
                        $("#horas_semanales").val(data.horas_semanales);
                        $("#resolucion_dato_laboral").val(data.resolucion);
                        $(".catSelect").trigger('change');

                        //datos laborales extra
                        $("#horario_laboral").val(data.horario_laboral);
                        $("#horario_comida").val(data.horario_comida);
                        $("#comida_dentro").val(data.comida_dentro);
                        $("#dias_descanso").val(data.dias_descanso);
                        $("#dias_vacaciones").val(data.dias_vacaciones);
                        $("#dias_aguinaldo").val(data.dias_aguinaldo);
                        $("#prestaciones_adicionales").val(data.prestaciones_adicionales);
                    }
                    $("#modal-dato-laboral").modal("show");
                    if(extra){
                        $('#datosBasicos').hide();
                        $('#datosExtras').show();
                    }else{
                        $('#datosBasicos').show();
                        $('#datosExtras').hide();
                    }
                }catch(error){
                    console.log(error);
                }
            }
        });
    }
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
            // console.log(data);
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

    function validarPropuestas(){
        let listaPropuestaConceptos = {};        
        error =false;
        $('.collapseSolicitante').each(function() {
            idSol=$(this).attr('idCitado');
            if ($('input[name="radiosPropuesta'+idSol+'"]:checked').length > 0) {
                if($("input[name='radiosPropuesta"+idSol+"']:checked"). val()=='otra' || $("input[name='radiosPropuesta"+idSol+"']:checked"). val()=='reinstalacion'){
                    listaPropuestaConceptos[idSol] = listaConfigConceptos[idSol];
                    if(Object.keys(listaConfigConceptos).length <= 0){
                        error =true;
                        swal({title: 'Error',text: 'Debe agregar conceptos de pago para la propuesta seleccionada.',icon: 'error'});
                    }
                }else if($("input[name='radiosPropuesta"+idSol+"']:checked"). val()=='completa'){
                    listaPropuestaConceptos[idSol]=listaPropuestas[idSol].completa;
                }else{
                    listaPropuestaConceptos[idSol]=listaPropuestas[idSol].al50;
                }
            }else{
                error =true;
                swal({title: 'Error',text: 'Debe seleccionar una propuesta para cada solicitante',icon: 'error'});
            }
        });
        console.log(listaPropuestaConceptos);
        return error;
    }

    function validarPagos(){
        let listaPropuestaConceptos = {};
        totalConceptosPago = 0;
        error =false;
        $('.collapseSolicitante').each(function() {
            // let idCitado =$("#idCitado").val();
            idSol=$(this).attr('idCitado');
            if ($('input[name="radiosPropuesta'+idSol+'"]:checked').length > 0) {
                if($("input[name='radiosPropuesta"+idSol+"']:checked"). val()=='otra' || $("input[name='radiosPropuesta"+idSol+"']:checked"). val()=='reinstalacion'){
                    listaPropuestaConceptos[idSol] = listaConfigConceptos[idSol];
                    if(Object.keys(listaConfigConceptos).length <= 0){
                        error =true;
                        swal({title: 'Error',text: 'Debe agregar conceptos de pago para la propuesta seleccionada.',icon: 'error'});
                    }
                }else if($("input[name='radiosPropuesta"+idSol+"']:checked"). val()=='completa'){
                    listaPropuestaConceptos[idSol]=listaPropuestas[idSol].completa;
                }else{
                    listaPropuestaConceptos[idSol]=listaPropuestas[idSol].al50;
                }
            }else{
                error =true;
                swal({title: 'Error',text: 'Debe seleccionar una propuesta para cada solicitante',icon: 'error'});
            }
        });
        //total pagos diferidos
        $.each(listaPropuestaConceptos, function (key, propuestaSolicitante) {
            totalConceptosPago = 0;
            $.each(listaPropuestaConceptos, function (key, propuestaConcepto) {
                $.each(propuestaConcepto, function (key, concepto) {
                    if(concepto.concepto_pago_resoluciones_id != 9 ){
                        totalConceptosPago += parseFloat(concepto.monto);
                    }
                });
            });
        });
        let totalConceptos = parseInt(totalConceptosPago);
        let totalDiferidos = parseInt($("#totalPagosDiferidos").val());
        // if(totalConceptos == totalDiferidos ){
        // }else{
        //     error =true;
        //     swal({title: 'Error',text: 'El monto total de pagos diferidos debe ser igual al total convenido',icon: 'error'});
        // }
        return error;
    }

    function validarDatosLaborales(tipo=null){
        var error=false;
        // if(tipo == 1){
        if( $('#datosExtras').is( ":visible" ) || tipo ==1){
            if($('#resolucion_id').val() == "1"){
                $(".datoLaboralExtra").each(function(){
                    if($(this).val() == ""){
                        $(this).prev().css("color","red");
                        error = true;
                        msj = "Es necesario llenar todos los datos laborales para el convenio."
                    }
                });
            }
        }else{
            $(".datoLaboral").each(function(){
                if($(this).val() == ""){
                    $(this).prev().css("color","red");
                    error = true;
                    msj = "Es necesario llenar los datos laborales."
                }
            });
        }
        if(error){
            swal({title: 'Error',text: msj,icon: 'warning'});
        }
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
                    ocupacion_id : $("#ocupacion_id").val(),
                    puesto : $("#puesto").val(),
                    nss : $("#nss").val(),
                    no_issste : "",
                    remuneracion : $("#remuneracion").val(),
                    periodicidad_id : $("#periodicidad_id").val(),
                    labora_actualmente : $("#labora_actualmente").is(":checked"),
                    fecha_ingreso : dateFormat($("#fecha_ingreso").val()),
                    fecha_salida : dateFormat($("#fecha_salida").val()),
                    jornada_id : $("#jornada_id").val(),
                    horas_semanales : $("#horas_semanales").val(),
                    parte_id:$("#parte_id").val(),
                    resolucion:$("#resolucion_dato_laboral").val(),
                    //datos laborales extra
                    horario_laboral:$("#horario_laboral").val(),
                    horario_comida:$("#horario_comida").val(),
                    comida_dentro:$("#comida_dentro").is(":checked"),
                    dias_descanso:$("#dias_descanso").val(),
                    dias_vacaciones:$("#dias_vacaciones").val(),
                    dias_aguinaldo:$("#dias_aguinaldo").val(),
                    prestaciones_adicionales:$("#prestaciones_adicionales").val(),
                    _token:"{{ csrf_token() }}"
                },
                success:function(data){
                    try{

                        if(data != null && data != ""){
                            getDatosLaboralesParte($("#parte_id").val());
                            swal({title: 'ÉXITO',text: 'Se modificaron los datos laborales correctamente',icon: 'success'});
                            $("#modal-dato-laboral").modal("hide");
                        }else{
                            swal({title: 'Error',text: 'Algo salió mal',icon: 'warning'});
                        }
                    }catch(error){
                        console.log(error);
                    }
                },error:function(data){
                    // console.log(data);
                    try{

                        var mensajes = "";
                        $.each(data.responseJSON.errors, function (key, value) {
                            // console.log(key.split("."));
                            // console.log(value);
                            var origen = key.split(".");

                            mensajes += "- "+value[0]+ " del "+origen[0].slice(0,-1)+" "+(parseInt(origen[1])+1)+" \n";
                        });
                        swal({
                            title: 'Error',
                            text: 'Es necesario validar los siguientes campos \n'+mensajes,
                            icon: 'error'
                        });
                    }catch(error){
                        console.log(error);
                    }
                }
            });
        }
    });
    $("#labora_actualmente").change(function(){
        if($("#labora_actualmente").is(":checked")){
            $("#divFechaSalida").hide();
            $("#fecha_salida").removeAttr("required");
        }else{
            $("#fecha_salida").attr("required","");
            $("#divFechaSalida").show();
        }
    });
    $("#btnAgregarConcepto").on("click",function(){
        if( $('#radioReinstalacion').is(':checked') ){
            comboConceptos = $("#concepto_pago_reinstalacion_id");
        }else{
            comboConceptos = $("#concepto_pago_resoluciones_id");
        }
        if( comboConceptos.val() != "" ){
            let idCitado =$("#idCitado").val();
            if( comboConceptos.val() == 7 || comboConceptos.val() == 10 || ( ($("#otro").val() != "") || ($("#monto").val() != "")  || ($("#monto").val() != "" && comboConceptos.val() == 8) ) ){
                let existe = false;
                $.each(listaConfigConceptos[idCitado],function(index,concepto){
                    if(concepto.concepto_pago_resoluciones_id == comboConceptos.val() ){
                        existe= true;
                    }
                });
                if(existe){
                    swal({title: 'Error',text: 'El concepto de pago ya se encuentra registrado',icon: 'warning'});
                }else{
                    if(isNaN($("#monto").val()) ){
                        swal({
                            title: 'Alerta',
                            text: 'El campo monto a pagar sólo puede contener números y punto decimal',
                            icon: 'warning'
                        });
                    }else{
                        if(listaConfigConceptos[idCitado] == undefined ){
                            listaConfigConceptos[idCitado] = [];
                        }
                        listaConfigConceptos[idCitado].push({
                            idCitado:$("#idCitado").val(),
                            concepto_pago_resoluciones_id:comboConceptos.val(),
                            dias:$("#dias").val(),
                            monto:$("#monto").val(),
                            otro:$("#otro").val(),
                        });
                        cargarTablaConcepto(listaConfigConceptos[[idCitado]],idCitado);
                    }
                    limpiarConcepto();
                }
            }else{
                swal({title: 'Error',text: 'Debe ingresar monto ó descripción del concepto',icon: 'warning'});
            }
        }else{
            swal({title: 'Error',text: 'Debe seleccionar el concepto de pago',icon: 'warning'});
        }
    });
        function cargarTablaConcepto(listaConfigConceptos,idCitado){
            // $("#tbodyConcepto").html("");
            // $("#tbodyConceptoPrincipal"+idCitado).html("");
            let table = '';
            //let idCitado = '';
            if( $('#radioReinstalacion').is(':checked') ){
                comboConceptos = 'concepto_pago_reinstalacion_id';
            }else{
                comboConceptos = "concepto_pago_resoluciones_id";
            }
            totalConceptos = 0 ;
            $.each(listaConfigConceptos,function(index,concepto){
                //idCitado = concepto.idCitado;

                table +='<tr>';
                    $("#"+comboConceptos).val(concepto.concepto_pago_resoluciones_id);
                    table +='<td>'+$("#"+comboConceptos+" option:selected").text()+'</td>';
                    $("#"+comboConceptos).val("");
                    table +='<td>'+concepto.dias+'</td>';
                    conceptoMonto = (concepto.monto != "") ? parseFloat(concepto.monto).toFixed(2): ""
                    table +='<td class="amount">'+ conceptoMonto +'</td>';
                    table +='<td>'+concepto.otro+'</td>';
                    table +='<td>';
                        table +='<button onclick="eliminarConcepto('+idCitado+','+index+')" class="btn btn-xs btn-warning" title="Eliminar">';
                            table +='<i class="fa fa-trash"></i>';
                        table +='</button>';
                    table +='</td>';
                table +='</tr>';
                totalConceptos+= (concepto.monto != "") ? parseFloat(concepto.monto): 0;
            });
            $("#totalConfig").val(totalConceptos.toFixed(2));
            tableTotal = '<tr><b><td>TOTAL</td><td colspan="4" class="amount"zoozoo>'+totalConceptos.toFixed(2)+'</td></b></tr>';
            $("#tbodyConcepto").html(table);
            $("#tbodyConceptoPrincipal"+idCitado).html(table+tableTotal);
        }

        function eliminarConcepto(idCitado,indice){
            listaConfigConceptos[idCitado].splice(indice,1);
            cargarTablaConcepto(listaConfigConceptos[idCitado],idCitado);
        }
        function limpiarConcepto(){
            $("#concepto_pago_resoluciones_id").val("");
            $("#concepto_pago_resoluciones_id").trigger("change");
            $("#dias").val("");
            $("#monto").val("");
        }

        var radioRO = '';
        $(".radiosPropuestas").click(function(e){
            $('#btnConfig').show();
            // if(radioRO == $(this).val()){
            //     return false;
            // }
            if(radioRO != $(this).val()){
                var esReinstalacion = false;
                if(radioRO == 'reinstalacion'){
                    esReinstalacion = true;
                }
                var actual = $(this).val();

                nombrePropuesta = ($('#radioReinstalacion').is(':checked')) ? "Configurada" : "de Reinstalacion";
                if(Object.keys(listaConfigConceptos).length > 0){
                    swal({
                        title: 'Descartar propuesta',
                        text: '¿Está seguro que desea descartar la propuesta '+nombrePropuesta+'?',
                        icon: 'warning',
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
                            listaConfigConceptos = {};
                            cargarConfigConceptos();
                            let idCitado = $('#idCitado').val();
                            $("#tbodyConceptoPrincipal"+idCitado).html("");
                            radioRO = actual;
                        }else{
                            if(esReinstalacion){
                                $('#radioReinstalacion').prop('checked',true);
                                radioRO = 'reinstalacion';
                            }else{
                                $('#radioOtro').prop('checked',true);
                                radioRO = 'otra'
                            }
                        }
                    });
                }else{
                    cargarConfigConceptos();
                    radioRO = actual;
                }
            }
        });

        function cargarConfigConceptos(){
            let idCitado = $('#idCitado').val();
            if( $('input[name="radiosPropuesta'+idCitado+'"]:checked').length > 0 ){
                $("#tbodyConcepto").html("");
                $('#modal-propuesta-convenio').modal('show');
                if( $('#radioReinstalacion').is(':checked') ){ //si es reinstalacion
                    $(".select-reinstalacion").show();
                    $(".select-otro").hide();
                }else{
                    $(".select-otro").show();
                    $(".select-reinstalacion").hide();
                }
                let table = '';

                $.each(listaConfigConceptos[idCitado],function(index,concepto){
                    table +='<tr>';
                        $("#concepto_pago_resoluciones_id").val(concepto.concepto_pago_resoluciones_id);
                        table +='<td>'+$("#concepto_pago_resoluciones_id option:selected").text()+'</td>';
                        $("#concepto_pago_resoluciones_id").val("");
                        table +='<td>'+concepto.dias+'</td>';
                        table +='<td class="amount" > $'+concepto.monto+'</td>';
                        table +='<td>'+concepto.otro+'</td>';
                        table +='<td>';
                            table +='<button onclick="eliminarConcepto('+idCitado+','+index+')" class="btn btn-xs btn-warning" title="Eliminar">';
                                table +='<i class="fa fa-trash"></i>';
                            table +='</button>';
                        table +='</td>';
                    table +='</tr>';
                });
                $("#concepto_pago_resoluciones_id").val("");
                $("#concepto_pago_resoluciones_id").trigger("change");
                $("#tbodyConcepto").html(table);
            }else{
                swal({title: 'Error',text: 'Debe seleccionar una propuesta para configurar',icon: 'error'});
            }
            // $("#dias").val("");
            // $("#monto").val("");
        }
        /*
     * Aqui inician las funciones para administrar el paso 6
     *
     */
    //  function finalizar(pasoActual){
    //     $("#btnGuardarResolucionMuchas").click();
    //  }
     function finalizar(pasoActual){
    // $("#btnFinalizar").on("click",function(){
        let listaPropuestaConceptos = {};
        error =false;
        mensaje = "";
        $('.collapseSolicitante').each(function() {
            idSol=$(this).attr('idCitado');
            if ($('input[name="radiosPropuesta'+idSol+'"]:checked').length > 0) {
                if($("input[name='radiosPropuesta"+idSol+"']:checked"). val()=='otra' || $("input[name='radiosPropuesta"+idSol+"']:checked"). val()=='reinstalacion'){
                    listaPropuestaConceptos[idSol] = listaConfigConceptos[idSol];
                    if(Object.keys(listaConfigConceptos).length <= 0){
                        error =true;
                        mensaje = 'Debe agregar conceptos de pago para la propuesta seleccionada.';
                    }
                }else if($("input[name='radiosPropuesta"+idSol+"']:checked"). val()=='completa'){
                    listaPropuestaConceptos[idSol]=listaPropuestas[idSol].completa;
                }else{
                    listaPropuestaConceptos[idSol]=listaPropuestas[idSol].al50;
                }
            }else{
                error =true;
                mensaje = 'Debe seleccionar una propuesta para cada solicitante.';
            }
        });
        if(!error){
            var resolucion = $("#resolucion_id").val();
            if(resolucion != "" ){

                if(resolucion == 1){//hubo convenio
                    errorDatosConvenio = validarDatosLaborales(1);
                    errorPagos = $('#switchAdicionales').is(':checked') ? validarPagos() : false;
                    if(!errorDatosConvenio && !errorPagos){
                        swal({
                            title: 'Se convino con todos los comparecientes?',
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

                                //var success = guardarEvidenciaEtapa(pasoActual);
                                //if(success){
                                    $("#icon"+pasoActual).css("background","lightgreen");
                                    listaResolucionesIndividuales = [];
                                    // $("#btnGuardarResolucionMuchas").click();
                                    revisarDocumentos();
                                // }else{
                                //     swal({title: 'Error',text: 'No se pudo guardar el registro',icon: 'error'});
                                // }
                            }else{
                                cargarModalRelaciones();
                            }
                        });
                    }
                }else{//No hubo convenio

                    swal({
                        title: '',
                        text: '¿Está seguro que desea terminar la audiencia?',
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

                            // var success = guardarEvidenciaEtapa(pasoActual);
                            // if(success){
                                $("#icon"+pasoActual).css("background","lightgreen");
                                listaResolucionesIndividuales = [];
                                // $("#btnGuardarResolucionMuchas").click();
                                revisarDocumentos()
                            // }else{
                            //     swal({title: 'Error',text: 'No se pudo guardar el registro',icon: 'error'});
                            // }
                        }
                    });
                }
            }else{
                swal({
                    title: 'Alerta',
                    text: 'Seleccione una resolución para la audiencia',
                    icon: 'warning'
                });
            }
        }else{
            swal({title: 'Alerta',text: mensaje ,icon: 'warning'});
        }
    }

    function cargarModalRelaciones(){
        $("#modal-relaciones").modal("show");
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
                try{
                    let dato = datos.data;
                    listaPropuestas[dato.idParte]= [];
                    listaPropuestas[dato.idParte]['completa'] = [];
                    listaPropuestas[dato.idParte]['al50'] = [];
                    $.each(dato.propuestaCompleta,function(index,propuesta){
                        console.log(propuesta);
                        listaPropuestas[propuesta.idSolicitante]['completa'].push({
                            'idCitado':propuesta.idSolicitante,
                            'concepto_pago_resoluciones_id':propuesta.concepto_pago_resoluciones_id,
                            'dias':propuesta.dias,
                            'monto':propuesta.monto,
                            'otro':''
                        });
                    });
                    $.each(dato.propuestaAl50,function(index,propuesta){
                        listaPropuestas[propuesta.idSolicitante]['al50'].push({
                            'idCitado':propuesta.idSolicitante,
                            'concepto_pago_resoluciones_id':propuesta.concepto_pago_resoluciones_id,
                            'dias':propuesta.dias,
                            'monto':propuesta.monto,
                            'otro':''
                        });
                    });

                    $('#remuneracionDiaria').val(dato.remuneracionDiaria);
                    $('#salarioMinimo').val(dato.salarioMinimo);
                    $('#antiguedad').val(dato.antiguedad);
                    $('#tiempoVencido').val(dato.tiempoVencido);
                    $('#idCitado').val(dato.idParte);

                    $('#salario'+idParte).html(" Remuneraci&oacute;n "+ dato.salario);
                    $('#fechaIngreso'+idParte).html(" Fecha de ingreso: " + dato.fechaIngreso);
                    $('#fechaSalida'+idParte).html(" Fecha de salida: " + dato.fechaSalida);

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
                    $('#totalCompleta').val(dato.completa.total);
                    $('#totalAl50').val(dato.al50.total);
                    $('#tbodyPropuestas'+dato.idParte).html(table);
                }catch(error){
                    console.log(error);
                }
            }
        });
    }

    $(".conceptosPago").on("change",function(){
        if( $('#radioReinstalacion').is(':checked') ){ //si es reinstalacion
            concepto = $("#concepto_pago_reinstalacion_id").val();
        }else{
            concepto = $("#concepto_pago_resoluciones_id").val();
        }
    // $("#concepto_pago_resoluciones_id").on("change",function(){
        // $('#remuneracionDiaria').val(130);
        // $('#antiguedad').val(3.2);
        // $('#salarioMinimo').val(123.22);
        // concepto = $("#concepto_pago_resoluciones_id").val();
        pagoDia = $('#remuneracionDiaria').val();
        antiguedad = $('#antiguedad').val();
        salarioMinimo = $('#salarioMinimo').val();
        tiempoVencido = $('#tiempoVencido').val();

        $('#monto').val('');
        $('#dias').val('');
        $('#otro').val('');
        switch (concepto) {
            case '7': // Prima topada por antiguedad
                //$('#monto').attr('disabled',true);
                $('#dias').attr('disabled',true);
                $('#otro').attr('disabled',true);
                if(antiguedad > 0){
                    if(pagoDia <= (2*salarioMinimo)){
                        monto = antiguedad * 12 * pagoDia;
                    }else{
                        monto = antiguedad * 12 * (2*salarioMinimo);
                    }
                }
                monto = monto.toFixed(2);
                $('#monto').val(monto);
                break;
            case '8':    //Gratificacion D
                //$('#monto').removeAttr('disabled');
                $('#dias').attr('disabled',true);
                $('#otro').attr('disabled',true);
                break;
            case '9':    //Gratificacion E
                //$('#monto').attr('disabled',true);
                $('#dias').attr('disabled',true);
                $('#otro').removeAttr('disabled');
                break;
            case '10':    //Salarios vencidos
                monto = (tiempoVencido * pagoDia).toFixed(2);
                //$('#monto').val(monto);
                //$('#monto').attr('disabled',true);
                // $('#dias').attr('disabled',true);
                $('#otro').attr('disabled',true);
                break;
            default: //Dias de sueldo, Dias de vacaciones
                //$('#monto').attr('disabled',true);
                $('#otro').attr('disabled',true);
                $('#dias').removeAttr('disabled');
                break;
        }
    });

    $("#dias").on("change",function(){
        concepto = $("#concepto_pago_resoluciones_id").val();
        dias = $('#dias').val();
        pagoDia = $('#remuneracionDiaria').val();
        antiguedad = $('#antiguedad').val();
        salarioMinimo = $('#salarioMinimo').val();
        switch (concepto) {
            case '4': //Dias de aguinaldo
                // if(dias <15){
                //     swal({title: 'Error',text: 'El numero de dias para aguinaldo debe ser mayor o igual a 15',icon: 'warning'});
                // }else{
                    monto = dias * pagoDia;
                // }
                break;
            case '7': // Prima topada por antiguedad

                break;
            case '8': //Gratificación D otro
                break;
            default: //Dias de sueldo, Dias de vacaciones

                monto = dias * pagoDia;
                break;
        }
        monto = (monto >0 )? monto.toFixed(2) : "";
        $('#monto').val(monto);
    });

    $("#monto").on("change",function(){
        $('#dias').val("");
    });

    $("#btnAgregarFechaPago").on("click",function(){
        if(listaConfigFechas.length < 6){
            var hoy = new Date();
            var _45dias = hoy.setDate(hoy.getDate() + 180);
            // var unMes = hoy.setMonth(hoy.getMonth() + 1);
            let fechaP = $("#fecha_pago").val().split("/");
            var fpago = new Date(fechaP[1]+'/'+fechaP[0]+'/'+fechaP[2]);

            if(fpago <= _45dias){
                let idCitado =$("#pago_citado_id").val();
                if( $("#fecha_pago").val() != "" && $("#monto_pago").val() != ""){
                    let existe = false;
                    $.each(listaConfigFechas,function(index,fecha){
                        if(fecha.fecha_pago == $("#fecha_pago").val() && fecha.idCitado ==idCitado ){
                            existe= true;
                        }
                    });
                    if(existe){
                        swal({title: 'Error',text: 'La fecha de pago para esta parte ya se encuentra registrada',icon: 'warning'});
                    }else{
                        if(listaConfigFechas == undefined ){
                            listaConfigFechas = [];
                        }
                        if(isNaN($("#monto_pago").val()) ){
                            swal({
                                title: 'Alerta',
                                text: 'El campo monto a pagar sólo puede contener números y punto decimal',
                                icon: 'warning'
                            });
                        }else{
                            listaConfigFechas.push({
                                idCitado:$("#pago_citado_id").val(),
                                fecha_pago:$("#fecha_pago").val(),
                                monto_pago:$("#monto_pago").val(),
                            });
                        }
                        $("#fecha_pago").val('');
                        $("#monto_pago").val('');
                        cargarTablaFechasPago(listaConfigFechas);
                    }
                }else{
                    swal({title: 'Error',text: 'Debe ingresar fecha y monto de pago',icon: 'warning'});
                }
            }else{
                swal({title: 'Error',text: 'La fecha de pago no puede exceder 45 d&iacute;as',icon: 'warning'});
            }
        }else{
            swal({title: 'Error',text: 'El número máximo de pagos permitidos es cinco.' ,icon: 'warning'});
        }
    });

    function cargarTablaFechasPago(listaConfigFechas){
        let table = '';
        let idCitado = '';
        let totalPagoFechas = 0;

        $.each(listaConfigFechas,function(index,fechaPago){

            idCitado = fechaPago.idCitado;
            $("#pago_citado_id").val(idCitado);
            table +='<tr>';
                table +='<td>'+$("#pago_citado_id option:selected").text(),+'</td>';
                table +='<td>'+fechaPago.fecha_pago+'</td>';
                table +='<td>'+(fechaPago.monto_pago)+'</td>';
                table +='<td>';
                // table +='<button onclick="eliminarFechaPago('+idCitado+','+index+')" class="btn btn-xs btn-success btnConfirmarPago" title="Registrar pago" style="display:none;">';
                //     table +='<i class="fa fa-eye"></i>';
                // table +='</button>';
                table +='<button onclick="eliminarFechaPago('+idCitado+','+index+')" class="btn btn-xs btn-warning" title="Eliminar">';
                    table +='<i class="fa fa-trash"></i>';
                table +='</button>';
                table +='</td>';
            table +='</tr>';
            totalPagoFechas+=parseFloat(fechaPago.monto_pago);
        });

        $("#totalPagosDiferidos").val(totalPagoFechas);
        $("#tbodyFechaPago").html(table);
        $("#tbodyFechaPagoPrincipal").html(table);
    }
    function eliminarFechaPago(idCitado,indice){
        listaConfigFechas.splice(indice,1);
        cargarTablaFechasPago(listaConfigFechas);
    }

    $("#btnGuardarFechasPago").on("click",function(){
        $(".btnConfirmarPago").show();
        $("#modal-pago-diferido").modal('hide');
    });

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
               limpiarConcepto();
               cargarTablaConcepto();
            cargarTablaResolucionesIndividuales();
        }
    });

//     $("#btnAgregarResolucion").on("click",function(){
//         if(validarResolucionIndividual()){
//             var motivo_id = "";
//             var motivo_nombre = "";
//             // if($("#terminacion_bilateral_id").val() == 4){
//             //     motivo_id = $("#motivo_archivado_id").val();
//             //     motivo_nombre = $("#motivo_archivado_id option:selected").text();
//             // }
//             listaResolucionesIndividuales.push({
//                 // parte_solicitante_id:$("#parte_solicitante_id").val(),
//                 // parte_solicitante_nombre:$("#parte_solicitante_id option:selected").text(),
//                 parte_solicitado_id:$("#parte_solicitado_id").val(),
//                 parte_solicitado_nombre:$("#parte_solicitado_id option:selected").text(),
//                 rol_solicitante_id:$("#parte_solicitado_id").val(),
//                 rol_solicitante_nombre:$("#parte_solicitado_id option:selected").text(),
//                 // motivo_archivado_id:motivo_id,
//                 // motivo_archivado_nombre:motivo_nombre
//             });
//             $("#parte_solicitante_id").val("").trigger("change");
//             $("#parte_solicitado_id").val("").trigger("change");
//             // $("#motivo_archivado_id").val("").trigger("change");
// //                limpiarConcepto();
// //                cargarTablaConcepto();
//             cargarTablaResolucionesIndividuales();
//         }
//     });
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
                swal({title: 'Error',text: 'Ya ha agregado esta relación',icon: 'error'});
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
                table +='<td>'+e.parte_solicitante_nombre+'</td>';
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
    //$("#btnGuardarResolucionMuchas").on("click",function(){
    function GuardarResolucionMuchas(){
        let listaPropuestaConceptos = {};
        // totalConceptosPago = 0;
        error =false;
        $('.collapseSolicitante').each(function() {
            // let idCitado =$("#idCitado").val();
            idSol=$(this).attr('idCitado');
            if ($('input[name="radiosPropuesta'+idSol+'"]:checked').length > 0) {
                if($("input[name='radiosPropuesta"+idSol+"']:checked"). val()=='otra' || $("input[name='radiosPropuesta"+idSol+"']:checked"). val()=='reinstalacion'){
                    listaPropuestaConceptos[idSol] = listaConfigConceptos[idSol];
                    if(Object.keys(listaConfigConceptos).length <= 0){
                        error =true;
                        swal({title: 'Error',text: 'Debe agregar conceptos de pago para la propuesta seleccionada.',icon: 'error'});
                    }
                }else if($("input[name='radiosPropuesta"+idSol+"']:checked"). val()=='completa'){
                    listaPropuestaConceptos[idSol]=listaPropuestas[idSol].completa;
                }else{
                    listaPropuestaConceptos[idSol]=listaPropuestas[idSol].al50;
                }
            }else{
                error =true;
                swal({title: 'Error',text: 'Debe seleccionar una propuesta para cada solicitante',icon: 'error'});
            }
        });

        if(!error){
            $.ajax({
                url:"/audiencia/resolucionPatronal",
                type:"POST",
                dataType:"json",
                global:true,
                data:{
                    audiencia_id:'{{ $audiencia->id }}',
                    convenio:$("#convenio").val(),
                    desahogo:$("#desahogo").val(),
                    evidencia:$("#evidencia6").val(),
                    resolucion_id:$("#resolucion_id").val(),
                    timeline:true,
                    listaRelacion:listaResolucionesIndividuales,
                    listaConceptos:listaPropuestaConceptos,
                    listaFechasPago:listaConfigFechas,
                    _token:"{{ csrf_token() }}"
                },
                success:function(data){
                    try{

                        if(data != null && data != ""){
                            if($("#resolucion_id").val() == 2){
                                window.location = "/audiencias/"+data.id+"/edit#default-tab-4"
                            }else{
                                window.location = "/audiencias/"+data.id+"/edit"
                            }
                        }else{
                            swal({
                                title: 'Algo salió mal',
                                text: 'No se guardo el registro',
                                icon: 'warning'
                            });
                        }
                    }catch(error){
                        console.log(error);
                    }
                }
            });
        }else{
            swal({title: 'Error',text: 'Debe seleccionar una propuesta para cada solicitante',icon: 'error'});
        }
    //});
    }

    function revisarDocumentos(){
        var resolucion = $("#resolucion_id").val();
        if(resolucion != "" ){
            //solicitados = $audiencia->solicitados;
            listVistaPrevia = [];
            listVistaPrevia.push({
                plantilla_id : 3, // acta de audiencia
                parte_solicitante_id: null,
                parte_solicitado_id: null,
            });
            if(resolucion == 1){//hubo convenio
                if(listaResolucionesIndividuales.length == 0){ // se convino con todos los citados
                    $('#parte_solicitado_id > option').each(function() { //mostrar convenio por cada solicitante
                        if( this.value !=null && this.value !="" ){
                            listVistaPrevia.push({
                                plantilla_id : 14, // convenio
                                parte_solicitante_id: null,
                                parte_solicitado_id: this.value,
                            });
                        }
                    });
                }else{
                    $.each(listaResolucionesIndividuales, function (key, value) {
                        listVistaPrevia.push({
                            plantilla_id : 14, // convenio
                            parte_solicitante_id: null,
                            parte_solicitado_id: value.parte_solicitado_id,
                        });
                    });
                }
            }else if(resolucion==3){//no hubo convenio
                $('#parte_solicitante_id > option').each(function() {
                    let solicitadoId = this.value;
                    if( solicitadoId !=null && solicitadoId !="" ){
                        solicitadoId = getParteSolicitud(solicitadoId);
                        $('#parte_solicitado_id > option').each(function() { //mostrar convenio por cada solicitado
                            let solicitanteId = this.value;
                            if( solicitanteId !=null && solicitanteId !="" ){
                                solicitanteId = getParteSolicitud(solicitanteId);
                                listVistaPrevia.push({
                                    plantilla_id : 1, // acta de no conciliacion
                                    parte_solicitante_id: solicitanteId,
                                    parte_solicitado_id: solicitadoId,
                                });
                            }
                        });
                    }
                });
            }
            //mostrar documentos
            if(listVistaPrevia.length > 0){ // mostrar primer documento
                $('#noDocumento').val(0);
                siguienteVistaPrevia();
            }
            $('#modal-preview').modal("show");
        }else{
            swal({title: 'Error',text: 'Debe seleccionar una resolucion',icon: 'error'});
        }
    }

    function siguienteVistaPrevia(){
        numDoc = parseInt($('#noDocumento').val());
        //numDoc = parseInt(('#noDocumento').val());
        if((numDoc+1) == listVistaPrevia.length){//mostar boton Aceptar guardar resolucion
            $('#guardarResolucion').show();
            $('#sigDocumento').hide();
        }else{
            $('#guardarResolucion').hide();
            $('#sigDocumento').show();
        }
        vistaPrevia(listVistaPrevia[numDoc].plantilla_id, listVistaPrevia[numDoc].parte_solicitante_id, listVistaPrevia[numDoc].parte_solicitado_id );
        $('#noDocumento').val(numDoc+1);
    }

    function getParteSolicitud(idParte){
        var idParteSolicitud = idParte;
        $.ajax({
            url:"/partes/getParteSolicitud/"+idParte,
            type:"GET",
            dataType:"json",
            async:false,
            success:function(data){
                try{
                    idParteSolicitud = data;
                }catch(error){
                    console.log(error);
                }
            }
        });
        return idParteSolicitud;
    }

    function vistaPrevia(plantilla_id,solicitante_id = null,citado_id = null){
        console.log(plantilla_id);
        console.log(solicitante_id);
        console.log(citado_id);

        let listaPropuestaConceptos = {};
        error =false;
        $('.collapseSolicitante').each(function() {
            // let idCitado =$("#idCitado").val();
            idSol=$(this).attr('idCitado');
            if ($('input[name="radiosPropuesta'+idSol+'"]:checked').length > 0) {
                if($("input[name='radiosPropuesta"+idSol+"']:checked"). val()=='otra' || $("input[name='radiosPropuesta"+idSol+"']:checked"). val()=='reinstalacion'){
                    listaPropuestaConceptos[idSol] = listaConfigConceptos[idSol];
                    if(Object.keys(listaConfigConceptos).length <= 0){
                        error =true;
                        swal({title: 'Error',text: 'Debe agregar conceptos de pago para la propuesta seleccionada.',icon: 'error'});
                    }
                }else if($("input[name='radiosPropuesta"+idSol+"']:checked"). val()=='completa'){
                    listaPropuestaConceptos[idSol]=listaPropuestas[idSol].completa;
                }else{
                    listaPropuestaConceptos[idSol]=listaPropuestas[idSol].al50;
                }
            }else{
                error =true;
                swal({title: 'Error',text: 'Debe seleccionar una propuesta para cada solicitante',icon: 'error'});
            }
        });

        $.ajax({
            url:"/plantilla-documento/previewDocumento",
            type:"POST",
            dataType:"json",
            async:false,
            data:{
                audiencia_id:'{{ $audiencia->id }}',
                solicitud_id:'{{ $solicitud_id }}',
                solicitante_id:solicitante_id,
                citado_id:citado_id,
                plantilla_id: plantilla_id,
                convenio:$("#convenio").val(),
                desahogo:$("#desahogo").val(),
                resolucion_id:$("#resolucion_id").val(),
                timeline:true,
                listaRelacion:listaResolucionesIndividuales,
                listaConceptos:listaPropuestaConceptos,
                listaFechasPago:listaConfigFechas,
                elementos_adicionales: $('#switchAdicionales').is(':checked'),
                _token:"{{ csrf_token() }}"
            },
            success:function(data){
                try{

                    $('#documentoPreviewHtml').html("");
                    $('#documentoPreviewHtml').html(data.data);
                    $('#modal-preview').animate({
                        scrollTop: $("#documentoPreviewHtml").offset().top
                    }, 'slow');
                }catch(error){
                    console.log(error);
                }
            }
        });
    }

    var timer = 0;
    function startTimer(){
        var days = "";
        if(firstTimeStamp != ""){
            firstTimeStamp = moment(firstTimeStamp)
            var actualDate = moment();
            var seconds = actualDate.diff(firstTimeStamp,"seconds");
            var minutes = seconds / 60;
            var hours = minutes /60;
            days = hours / 24;
            // console.log(days);
            var timestamp = new Date(0,0,0,0,0,seconds);
        }else{
            var timestamp = new Date(0,0,0,0,0,0);

        }
        var interval = 1;
        if(timer == 0){

            setInterval(function () {
                timer = 1;
                timestamp = new Date(timestamp.getTime() + interval*1000);
                var dias = "";
                if(days != "" && parseInt(days) != 0){
                    dias = days.toString().split(".")[0]+ " dias "
                }
                $('.countdown').text(dias + formatNumberTimer(timestamp.getHours())+':'+formatNumberTimer(timestamp.getMinutes())+':'+formatNumberTimer(timestamp.getSeconds()));
            }, 1000);
        }
    }

    function formatNumberTimer(n){
        return n > 9 ? "" + n: "0" + n;
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
                    try{
                        if(data != null && data != ""){
                            $("#nombre").val(data.nombre);
                            $("#primer_apellido").val(data.primer_apellido);
                            $("#segundo_apellido").val(data.segundo_apellido);
                            $("#fecha_nacimiento").val(dateFormat(data.fecha_nacimiento,4));
                            $("#genero_id").val(data.genero_id).trigger("change");
                        }
                    }catch(error){
                        console.log(error);
                    }
                }
            });
        }
    }
    function guardarUrlVirtual(){
        if($("#url_virtual").val() != ""){
            $.ajax({
                url:"/guardarUrlVirtual",
                type:"POST",
                dataType:"json",
                data:{
                    url_virtual:$("#url_virtual").val(),
                    solicitud_id:'{{$solicitud_id}}',
                    _token:"{{ csrf_token() }}"
                },
                async:true,
                success:function(data){
                    try{
                        if(data.success){
                            swal({ title: 'Éxito', text: 'Url guardada correctamente', icon: 'success'});
                            $("#timeline_etapas").show();
                        }else{
                            swal({title: 'Error',text: 'No se pudo guardar la url',icon: 'error'});
                        }
                    }catch(error){
                        console.log(error);
                    }
                }
            });
        }else{
            swal({title: 'Error',text: 'Es necesario ingresar la url',icon: 'error'});
        }
    }
    $('.upper').on('keyup', function () {
        var valor = $(this).val();
        $(this).val(valor.toUpperCase());
    });
    $('[data-toggle="tooltip"]').tooltip();
    $("#btnFinalizarRatificacion").on("click",function(){
        location.href = "/solicitudes/consulta/{{$solicitud_id}}";
    });
    $(".dateBirth").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: "c-80:",
        format:'dd/mm/yyyy',
    });
    $(".fecha").datetimepicker({format:"DD/MM/YYYY"});
    $("#fecha_pago").datetimepicker({
        locale: 'es',
        inline: true,
        sideBySide: true,
        minDate: new Date(),
        format: 'DD/MM/YYYY hh:mm',
        });
</script>
<script src="/assets/js/demo/timeline.demo.js"></script>
@endpush
