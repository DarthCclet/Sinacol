@extends('layouts.default')

@section('title', 'Calendar')

@include('includes.component.datatables')
@include('includes.component.pickers')
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
    <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
    <li class="breadcrumb-item"><a href="javascript:;">Audiencias</a></li>
    <li class="breadcrumb-item active">Guia Audiencia</li>
</ol>
<!-- end breadcrumb -->
<!-- begin page-header -->
<h1 class="page-header">Gu&iacute;a Resoluci&oacute;n <small>pasos para cumplir la audiencia</small></h1>
<!-- end page-header -->
<h1 class="badge badge-secondary col-md-2 offset-10" style="position: fixed; font-size: 2rem; z-index:999;" onclick="startTimer();"><span class="countdown">00:00:00</span></h1>
<input type="hidden" id="audiencia_id" name="audiencia_id" value="{{$audiencia->id}}" />
<!-- begin timeline -->
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
        <div class="timeline-body" style="border: 1px solid black;">
                <div class="timeline-header">
                <span class="username"><a href="javascript:;">{{$etapa->nombre}}</a> <small></small></span>
                <span class="views showTime{{$etapa->paso}}"></span>
                </div>
            <div class="timeline-content" id="contentStep{{$etapa->paso}}">
                    <p>
                        @switch($etapa->paso)
                            @case(1)
                                <p>Comparecientes</p>
                                <div class="col-md-offset-3 col-md-12 ">
                                    <table class="table table-striped table-bordered table-td-valign-middle">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap">Tipo Parte</th>
                                                <th class="text-nowrap">Nombre de la parte</th>
                                                <th class="text-nowrap" style="width: 10%;">Representante Legal</th>
                                                <th class="text-nowrap" style="width: 10%;">Datos Laborales</th>
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
                                                        <div style="display: inline-block;">
                                                            <button onclick="AgregarRepresentante({{$parte->id}})" class="btn btn-xs btn-primary btnAgregarRepresentante" title="Agregar">
                                                                <i class="fa fa-plus"></i>
                                                            </button>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($parte->tipo_parte_id == 1)
                                                        <div style="display: inline-block;">
                                                            <button onclick="DatosLaborales({{$parte->id}})" class="btn btn-xs btn-primary btnAgregarRepresentante" title="Datos Laborales">
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
                                        <u><i>EL CONCILIADOR LEERÁ EL SIGUIENTE TEXTO, DIRIGIENDOSE AL TRABAJADOR</i></u> “La conciliación es personal. Aunque asista el trabajador con el apoyo de cualquier persona de su confianza, el trabajador mismo es quien decide lo que pide, lo que negocia y lo que acepta o no en este proceso.”. 
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
                                        <u><i>EL CONCILIADOR LEERÁ A LAS PARTES</i></u> “La conciliación es confidencial. Lo que se dice y se habla en esta audiencia es confidencial, no puede afectar sus derechos, ni puede ser una prueba en cualquier juicio.”. 
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
                                        Explicar las características de la conciliación y los derechos de las partes en ella. Recuerde que el proceso de conciliación se realiza en conformidad con los principios constitucionales de legalidad, imparcialidad, confiabilidad, eficacia, objetividad, profesionalismo, transparencia y publicidad.
                                    </p>
                                    <p>
                                        <u><i>EL CONCILIADOR LEERÁ A LAS PARTES</i></u> “La conciliación es un proceso ágil, objetivo, imparcial, transparente y eficaz. Cada una de las partes tendrá derecho de hablar y de ser escuchada, de plantear, de negociar y de responder. Es un proceso voluntario, no se obligará a nadie a un acuerdo que no quiere. Nos trataremos todos con respeto en esta audiencia”.
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
                                <p>Al final es necesario que redacte usted en el espacio indicado el resumen de las manifestaciones de las partes, y que estén las partes de acuerdo con este resumen, que se transcribirá por sistema en el acta de audiencia. </p>
                                <textarea class="form-control textarea" placeholder="Describir resumen de lo sucedido ..." type="text" id="evidencia{{$etapa->paso}}" >
                                </textarea>
                                <button class="btn btn-primary btnPaso{{$etapa->paso}}" onclick="nextStep({{$etapa->paso}})">Continuar </button>

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
                                        <button id='coll{{$solicitante->parte->id}}' class="btn btn-link card-header collapseSolicitante" idSolicitante="{{$solicitante->parte->id}}" type="button" data-toggle="collapse" data-target="#collapse{{$solicitante->parte->id}}" aria-expanded="true" aria-controls="collapseOne" parteSelect={{$solicitante->parte->id}} onclick="getDatosLaboralesParte({{$solicitante->parte->id}});" >
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
                                <p> El conciliador debe incluir una explicación y motivación breve de la propuesta configurada que fue negociada con las partes. En caso de que el acuerdo entre las partes sea por menos de la propuesta de 45 días más prestaciones, es fundamental incluir una descripción de las circunstancias específicas que explican y justifican el convenio y la cuantía acordada.</p>
                                <textarea class="form-control textarea" placeholder="Comentarios ..." type="text" id="evidencia{{$etapa->paso}}" >
                                </textarea>
                                <button class="btn btn-primary btnPaso{{$etapa->paso}}" onclick="nextStep({{$etapa->paso}})">Continuar </button>
                            @break
                            @case(5)
                                <p>Darle la palabra a la parte solicitante y luego a la parte citada. </p>
                                <p>Recordando que la conciliación es un proceso sin formalismos, podrán hablar ambas partes las veces necesarias. </p>
                                <p>Al final es necesario que redacte usted en el espacio indicado el resumen de las manifestaciones de las partes, y que estén las partes de acuerdo con este resumen, que se transcribirá por sistema en el acta de audiencia. </p>
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
                                        @foreach($audiencia->partes as $parte)
                                            @if($parte->tipo_parte_id == 1)
                                                <tr>
                                                    <td class="text-nowrap">{{ $parte->tipoParte->nombre }}</td>
                                                    @if($parte->tipo_persona_id == 1)
                                                        <td class="text-nowrap">{{ $parte->nombre }} {{ $parte->primer_apellido }} {{ $parte->segundo_apellido }}</td>
                                                    @else
                                                        <td class="text-nowrap">{{ $parte->nombre_comercial }}</td>
                                                    @endif
                                                    <td>
                                                        @if($parte->tipo_parte_id == 1)
                                                        <div style="display: inline-block;">
                                                            <button onclick="DatosLaborales({{$parte->id}},true)" class="btn btn-xs btn-primary btnAgregarRepresentante" title="Datos Laborales">
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
                                <button class="btn btn-primary btnPaso{{$etapa->paso}}" onclick="finalizar({{$etapa->paso}})">Finalizar </button>
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
                        <h5>Conceptos de pago para resolucion</h5>
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
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5" id="btnGuardarPropuestaConvenio"><i class="fa fa-save"></i> Guardar</button>
                </div>
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
                            <label for="clasificacion_archivo_id_representante" class="control-label">Instrumento</label>
                            <select id="clasificacion_archivo_id_representante" class="form-control select-element">
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
                    <div class="col-md-12">
                        <input class="form-control datoLaboral" id="nombre_jefe_directo" placeholder="Nombre del jefe directo" type="text" value="">
                        <p class="help-block">Nombre del Jefe directo</p>
                    </div>
                    <div class="col-md-12 form-group row">
                        <input type="hidden" id="term">
                        <div class="col-md-12 ">
                            <select name="giro_comercial_solicitante " placeholder="Seleccione" id="giro_comercial_solicitante" class="form-control datoLaboral"></select>
                        </div>
                        <div class="col-md-12">
                            <p class="help-block needed">Giro comercial</p>
                        <label id="giro_solicitante"></label>
                        </div>
                    </div>
                    {!! Form::select('giro_comercial_hidden', isset($giros_comerciales) ? $giros_comerciales : [] , null, ['id'=>'giro_comercial_hidden','placeholder' => 'Seleccione una opción','style'=>'display:none;']);  !!}
                    <div class="col-md-12 row">
                        <div class="col-md-4">
                            {!! Form::select('ocupacion_id', isset($ocupaciones) ? $ocupaciones : [] , null, ['id'=>'ocupacion_id', 'required','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect datoLaboral']);  !!}
                            {!! $errors->first('ocupacion_id', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block needed">Categoria/Puesto</p>
                        </div>
                        <div class="col-md-4">
                            <input class="form-control numero datoLaboral" data-parsley-type='integer' id="nss" placeholder="No. IMSS"  type="text" value="">
                            <p class="help-block ">No. IMSS</p>
                        </div>
                        <div class="col-md-4">
                            <input class="form-control numero datoLaboral" data-parsley-type='integer' id="no_issste" placeholder="No. ISSSTE"  type="text" value="">
                            <p class="help-block">No. ISSSTE</p>
                        </div>
                    </div>
                    <div class="col-md-12 row">
                        <div class="col-md-4">
                            <input class="form-control numero "datoLaboral required data-parsley-type='number' id="remuneracion" max="99999999" placeholder="Remuneraci&oacute;n (pago)" type="text" value="">
                            <p class="help-block needed">Remuneraci&oacute;n (pago)</p>
                        </div>
                        <div class="col-md-4">
                            {!! Form::select('periodicidad_id', isset($periodicidades) ? $periodicidades : [] , null, ['id'=>'periodicidad_id','placeholder' => 'Seleccione una opción','required', 'class' => 'form-control catSelect datoLaboral']);  !!}
                            {!! $errors->first('periodicidad_id', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block needed">Periodicidad</p>
                        </div>
                        <div class="col-md-4">
                            <input class="form-control numero datoLaboral" required data-parsley-type='integer' id="horas_semanales" placeholder="Horas semanales" type="text" value="">
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
                            <input class="form-control date datoLaboral" required id="fecha_ingreso" placeholder="Fecha de ingreso" type="text" value="">
                            <p class="help-block needed">Fecha de ingreso</p>
                        </div>
                        <div class="col-md-4" id="divFechaSalida">
                            <input class="form-control date datoLaboral" id="fecha_salida" placeholder="Fecha salida" type="text" value="">
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
                            <input class="form-control datoLaboralExtra" id="dias_descanso" placeholder="n días, los cuales correspondían a dddd" type="text" value="">
                            <p class="help-block needed">Indica número y días de descanso</p>
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
<!-- Inicio Modal de comparecientes y resolución individual-->
<div class="modal" id="modal-comparecientes" data-backdrop="static" data-keyboard="false" style="display:none;">
    <div class="modal-dialog modal-lg">
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
{{-- <div class="modal" id="modal-relaciones" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Relaciones homologadas</h4>
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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="resolucion_individual_id" class="col-sm-6 control-label labelResolucion">Resolución</label>
                            <div class="col-sm-10">
                                {!! Form::select('resolucion_individual_id', isset($resoluciones) ? $resoluciones : [] , null, ['id'=>'resolucion_individual_id', 'required','placeholder' => 'Seleccione una opción', 'class' => 'form-control select-element']);  !!}
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
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</a>
                    <button class="btn btn-warning btn-sm m-l-5" id="btnGuardarResolucionMuchas"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div> --}}
<div class="modal" id="modal-relaciones" style="display:none;">
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
                    {{-- <div class="col-md-6">
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
                    </div> --}}
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
                                {{-- {!! Form::select('resolucion_individual_id', isset($resoluciones) ? $resoluciones : [] , null, ['id'=>'resolucion_individual_id', 'required','placeholder' => 'Seleccione una opcion', 'class' => 'form-control select-element']);  !!} --}}
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-md-6" id="divMotivoArchivo" style="display: none;">
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
                    </div> --}}
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
                                    {{-- <th>Solicitante</th> --}}
                                    <th>Solicitado</th>
                                    <th>Rol</th>
                                    {{-- <th>Motivo de archivo</th> --}}
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
<!-- Fin Modal de comparecientes y resolución individual-->
<input type="hidden" id="parte_id">
<input type="hidden" id="parte_representada_id">
@endsection
@push('scripts')
<script>
    var listaContactos = [];
    var listaPropuestas={};
    var listaConfigConceptos= {};
    var listaResolucionesIndividuales = [];
    var lastTimeStamp = "";
    $(document).ready(function(){
        $( "#accordion" ).accordion();

        $(".tipo_documento,.select-element,.catSelect").select2();
        $(".fecha").datetimepicker({format:"DD/MM/YYYY"});
        cargarGeneros();
        getEtapasAudiencia();
        cargarTipoContactos();
    });
    function nextStep(pasoActual){
        var success = guardarEvidenciaEtapa(pasoActual);
        if(success){

            var siguiente = pasoActual+1;
            $("#icon"+pasoActual).css("background","lightgreen");
            $('html,body').animate({
                scrollTop: $("#contentStep"+pasoActual).offset().top
            }, 'slow');
            $("#step"+siguiente).show();
        }else{
            swal({title: 'Error',text: 'No se pudo guardar el registro',icon: 'error'});
        }
    }

    function guardarEvidenciaEtapa(etapa){
        var respuesta = true;
        $.ajax({
            url:'/etapa_resolucion_audiencia',
            type:"POST",
            dataType:"json",
            async:false,
            data:{
                etapa_resolucion_id:etapa,
                audiencia_id:$("#audiencia_id").val(),
                evidencia: $("#evidencia"+etapa).val(),
                elementos_adicionales: $('#switchAdicionales').is(':checked'),
                _token:"{{ csrf_token() }}"
            },
            success:function(data){
                try{
                    respuesta = true;
                    
                    // console.log(data.data.updated_at);
                    $(".showTime"+etapa).text(data.data.created_at);
                }catch(error){
                    // console.log(error);
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
        console.log(etapas);
        $.each(etapas, function (key, value) {
            var pasoActual = value.etapa_resolucion_id;
            $(".showTime"+pasoActual).text(value.updated_at);
            if(value.updated_at != ""){
                $('.btnPaso'+pasoActual).hide();
            }
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
                    
                    break;
                default:
                    
                    $("#evidencia"+pasoActual).data("wysihtml5").editor.setValue(value.evidencia);
                    break;
            }
            
                
            $("#icon"+pasoActual).css("background","lightgreen");
            // $("#contentStep"+pasoActual).hide();
            $("#step"+siguiente).show();
            lastTimeStamp = value.created_at;
            
        });
    }
    $('.textarea').wysihtml5({locale: 'es-ES'});

    /*
     * Aqui inician las funciones para administrar el paso 1
     *
     */
    $("#btnCargarComparecientes").on("click",function(){
        $.ajax({
            url:"/audiencia/validar_partes/{{ $audiencia->id }}",
            type:"GET",
            dataType:"json",
            success:function(data){
                // console.log(data.pasa);
                if(data.pasa){
                    getPersonasComparecer();
                }else{
                    swal({title: 'Error',text: 'Debes agregar el representante legal de todas las personas Morales',icon: 'error'});
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
                    swal({
                        title: 'Éxito',
                        text: 'Se han registrado los comparecientes',
                        icon: 'success'
                    });
                    startTimer();
                    nextStep(1);
                },
                error:function(data){
                    swal({
                        title: 'Algo salio mal',
                        text: 'No se guardo el registro',
                        icon: 'warning'
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
                var html="<table class='table table-bordered table-striped table-hover'>";
                html +='<tr>';
                html +='    <th>Tipo de parte</th>';
                html +='    <th>Nombre</th>';
                html +='    <th>Curp</th>';
                html +='    <th>Es representante</th>';
                html +='</tr>';
                $.each(data,function(index,element){
                    html +='<tr>';
                    html +='    <td>'+element.parte.tipoParte+'</td>';
                    html +='    <td>'+element.parte.nombre+' '+element.parte.primer_apellido+' '+element.parte.segundo_apellido+'</td>';
                    html +='    <td>'+element.parte.curp+'</td>';
                    if(element.parte.tipo_parte_id == 3 && element.parte.parte_representada_id != null){
                        if(element.parte.parteRepresentada.tipo_persona_id == 1){
                            html +='<td>Si ('+element.parte.parteRepresentadanombre+' '+element.parte.parteRepresentada.primer_apellido+' '+element.parte.parteRepresentada.segundo_apellido+')</td>';
                        }else{
                            html +='<td>Si ('+element.parte.parteRepresentada.nombre_comercial+')</td>';
                        }
                    }else{
                        html +='<td>No</td>';
                    }
                    html +='</tr>';
                });
                html +='</table>';
                $("#contentStep1").html(html);
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
            swal({title: 'Error',text: 'No has agregado comparecientes',icon: 'warning'});
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
                $("#btnConfigurarResoluciones").show();
                $("#btnGuardarResolucionUna").show();
                $("#modal-comparecientes").modal("show");
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
                    $("#segundo_apellido").val(data.segundo_apellido);
                    $("#fecha_nacimiento").val(dateFormat(data.fecha_nacimiento,4));
                    $("#genero_id").val(data.genero_id).trigger("change");
                    $("#clasificacion_archivo_id_representante").val(data.clasificacion_archivo_id).change();
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
                    $("#clasificacion_archivo_id_representante").val("").change();
                    $("#feha_instrumento").val("");
                    $("#detalle_instrumento").val("");
                    $("#parte_id").val("");
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
    $("#btnGuardarPropuestaConvenio").on("click",function(){
        var objeto_propuesta = {};
        objeto_propuesta.indemnizacion90 = $("#indemnizacion90").val();
        objeto_propuesta.indemnizacion45 = $("#indemnizacion45").val();
        objeto_propuesta.aguinaldo = $("#aguinaldo").val();
        objeto_propuesta.vacaciones = $("#vacaciones").val();
        objeto_propuesta.prima_vacacional = $("#prima_vacacional").val();
        objeto_propuesta.prima_antiguedad = $("#prima_antiguedad").val();
        objeto_propuesta.prestaciones_legales = $("#prestaciones_legales").val();
        objeto_propuesta.prestaciones_45 = $("#prestaciones_45").val();
        // console.log(objeto_propuesta);
        $("#tableOtro").show();
        $("#modal-propuesta-convenio").modal('hide')
        // formarTablaPropuestaConvenio();
    });

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
                    clasificacion_archivo_id:$("#clasificacion_archivo_id_representante").val(),
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
        if($("#clasificacion_archivo_id_representante").val() == ""){
            $("#clasificacion_archivo_id_representante").prev().css("color","red");
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
                if(data != null && data != ""){
                    $("#dato_laboral_id").val(data.id);
                    // $("#giro_comercial_solicitante").val(data.giro_comercial_id).trigger("change");
                    $("#giro_comercial_hidden").val(data.giro_comercial_id)
                    $("#giro_solicitante").html("<b> *"+$("#giro_comercial_hidden :selected").text() + "</b>");
                    // getGiroEditar("solicitante");
                    $("#nombre_jefe_directo").val(data.nombre_jefe_directo);
                    $("#ocupacion_id").val(data.ocupacion_id);
                    $("#nss").val(data.nss);
                    $("#no_issste").val(data.no_issste);
                    $("#remuneracion").val(data.remuneracion);
                    $("#periodicidad_id").val(data.periodicidad_id);
                    if(data.labora_actualmente != $("#labora_actualmente").is(":checked")){
                        $("#labora_actualmente").click();
                        $("#labora_actualmente").trigger("change");
                    }
                    $("#fecha_ingreso").val(dateFormat(data.fecha_ingreso,4));
                    $("#fecha_salida").val(dateFormat(data.fecha_salida,4));
                    // console.log(data.jornada_id);
                    $("#jornada_id").val(data.jornada_id);
                    $("#horas_semanales").val(data.horas_semanales);
                    $("#resolucion_dato_laboral").val(data.resolucion);
                    $(".catSelect").trigger('change')
                }
                $("#modal-dato-laboral").modal("show");
                if(extra){
                    $('#datosBasicos').hide();
                    $('#datosExtras').show();
                }else{
                    $('#datosBasicos').show();
                    $('#datosExtras').hide();
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
    function validarDatosLaborales(){
        var error=false;
        // if($('#resolucion_id').val() == 1){
        //     var error=false;
        //     $(".datoLaboral").each(function(){
        //         if($(this).val() == ""){
        //             $(this).prev().css("color","red");
        //             error = true;
        //         }
        //     });
            // 
            //     $(".datoLaboralExtra").each(function(){
            //         if($(this).val() == ""){
            //             $(this).prev().css("color","red");
            //             error = true;
            //         }
            //     });
        // }else{

        // }
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
                    nombre_jefe_directo : $("#nombre_jefe_directo").val(),
                    ocupacion_id : $("#ocupacion_id").val(),
                    nss : $("#nss").val(),
                    no_issste : $("#no_issste").val(),
                    remuneracion : $("#remuneracion").val(),
                    periodicidad_id : $("#periodicidad_id").val(),
                    labora_actualmente : $("#labora_actualmente").is(":checked"),
                    fecha_ingreso : dateFormat($("#fecha_ingreso").val()),
                    fecha_salida : dateFormat($("#fecha_salida").val()),
                    jornada_id : $("#jornada_id").val(),
                    horas_semanales : $("#horas_semanales").val(),
                    giro_comercial_id : $("#giro_comercial_hidden").val(),
                    parte_id:$("#parte_id").val(),
                    resolucion:$("#resolucion_dato_laboral").val(),
                    //datos laborales extra
                    horario_laboral:$("#horario_laboral").val(),
                    horario_comida:$("#horario_comida").val(),
                    comida_dentro:$("#comida_dentro").val(),
                    dias_descanso:$("#dias_descanso").val(),
                    dias_vacaciones:$("#dias_vacaciones").val(),
                    dias_aguinaldo:$("#dias_aguinaldo").val(),
                    prestaciones_adicionales:$("#prestaciones_adicionales").val(),
                    _token:"{{ csrf_token() }}"
                },
                success:function(data){
                    if(data != null && data != ""){
                        swal({title: 'Exito',text: 'Se modificaron los datos laborales correctamente',icon: 'success'});
                        $("#modal-dato-laboral").modal("hide");
                    }else{
                        swal({title: 'Error',text: 'Algo salio mal',icon: 'warning'});
                    }
                },error:function(data){
                    // console.log(data);
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
                }
            });
        }else{
            swal({title: 'Error',text: 'Llena todos los campos',icon: 'warning'});
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
        if( $("#concepto_pago_resoluciones_id").val() != "" ){
            let idSolicitante =$("#idSolicitante").val();
            if( $("#concepto_pago_resoluciones_id").val() == 7 || ( ($("#otro").val() != "") || ($("#dias").val() != "" && $("#monto").val() != "") ) ){
                let existe = false;
                $.each(listaConfigConceptos[idSolicitante],function(index,concepto){
                    if(concepto.concepto_pago_resoluciones_id == $("#concepto_pago_resoluciones_id").val() ){
                        existe= true;
                    }
                });
                if(existe){
                    swal({title: 'Error',text: 'El concepto de pago ya se encuentra registrado',icon: 'warning'});
                }else{
                    if(listaConfigConceptos[idSolicitante] == undefined ){
                        listaConfigConceptos[idSolicitante] = [];
                    }
                    listaConfigConceptos[idSolicitante].push({
                        idSolicitante:$("#idSolicitante").val(),
                        concepto_pago_resoluciones_id:$("#concepto_pago_resoluciones_id").val(),
                        dias:$("#dias").val(),
                        monto:$("#monto").val(),
                        otro:$("#otro").val(),
                    });
                    limpiarConcepto();
                    cargarTablaConcepto(listaConfigConceptos[[idSolicitante]]);
                }
            }else{
                swal({title: 'Error',text: 'Debe ingresar dias y monto ó descripción del concepto',icon: 'warning'});
            }
        }else{
            swal({title: 'Error',text: 'Debe seleccionar el concepto de pago',icon: 'warning'});
        }
    });
        function cargarTablaConcepto(listaConfigConceptos){
            let table = '';
            let idSolicitante = '';

            $.each(listaConfigConceptos,function(index,concepto){
                idSolicitante = concepto.idSolicitante;

                table +='<tr>';
                    $("#concepto_pago_resoluciones_id").val(concepto.concepto_pago_resoluciones_id);
                    table +='<td>'+$("#concepto_pago_resoluciones_id option:selected").text()+'</td>';
                    $("#concepto_pago_resoluciones_id").val("");
                    table +='<td>'+concepto.dias+'</td>';
                    table +='<td>'+concepto.monto+'</td>';
                    table +='<td>'+concepto.otro+'</td>';
                    table +='<td>';
                        table +='<button onclick="eliminarConcepto('+idSolicitante+','+index+')" class="btn btn-xs btn-warning" title="Eliminar">';
                            table +='<i class="fa fa-trash"></i>';
                        table +='</button>';
                    table +='</td>';
                table +='</tr>';
            });
            $("#tbodyConcepto").html(table);
            $("#tbodyConceptoPrincipal"+idSolicitante).html(table);
        }

        function eliminarConcepto(idSolicitante,indice){
            listaConfigConceptos[idSolicitante].splice(indice,1);
            cargarTablaConcepto(listaConfigConceptos[idSolicitante]);
        }
        function limpiarConcepto(){
            $("#concepto_pago_resoluciones_id").val("");
            $("#concepto_pago_resoluciones_id").trigger("change");
            $("#dias").val("");
            $("#monto").val("");
        }

        function cargarConfigConceptos(){
            $("#tbodyConcepto").html("");
            $('#modal-propuesta-convenio').modal('show');
            let table = '';
            let idSolicitante = $('#idSolicitante').val();
            $.each(listaConfigConceptos[idSolicitante],function(index,concepto){
                table +='<tr>';
                    $("#concepto_pago_resoluciones_id").val(concepto.concepto_pago_resoluciones_id);
                    table +='<td>'+$("#concepto_pago_resoluciones_id option:selected").text()+'</td>';
                    $("#concepto_pago_resoluciones_id").val("");
                    table +='<td>'+concepto.dias+'</td>';
                    table +='<td class="amount" > $'+concepto.monto+'</td>';
                    table +='<td>'+concepto.otro+'</td>';
                    table +='<td>';
                        table +='<button onclick="eliminarConcepto('+idSolicitante+','+index+')" class="btn btn-xs btn-warning" title="Eliminar">';
                            table +='<i class="fa fa-trash"></i>';
                        table +='</button>';
                    table +='</td>';
                table +='</tr>';
            });
            $("#concepto_pago_resoluciones_id").val("");
            $("#concepto_pago_resoluciones_id").trigger("change");
            $("#tbodyConcepto").html(table);
            // $("#dias").val("");
            // $("#monto").val("");
        }
        /*
     * Aqui inician las funciones para administrar el paso 6
     *
     */
     function finalizar(pasoActual){
    // $("#btnFinalizar").on("click",function(){
        swal({
            title: 'Finalización de audiencia',
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
                    text: 'Si',
                    value: true,
                    visible: true,
                    className: 'btn btn-warning',
                    closeModal: true
                }
            }
        }).then(function(isConfirm){
            if(isConfirm){

                var success = guardarEvidenciaEtapa(pasoActual);
                if(success){
                    // var siguiente = pasoActual+1;
                    $("#icon"+pasoActual).css("background","lightgreen");
                    $('html,body').animate({
                        scrollTop: $("#contentStep"+pasoActual).offset().top
                    }, 'slow');
                    // $("#step"+siguiente).show();
                    listaResolucionesIndividuales = [];
                    $("#btnGuardarResolucionMuchas").click();
                }else{
                    swal({title: 'Error',text: 'No se pudo guardar el registro',icon: 'error'});
                }
            
                
            }else{
                // cargarModalRelaciones();
            }
        });
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
                let dato = datos.data;
                listaPropuestas[dato.idParte]= [];
                listaPropuestas[dato.idParte]['completa'] = [];
                listaPropuestas[dato.idParte]['al50'] = [];
                $.each(dato.propuestaCompleta,function(index,propuesta){
                    listaPropuestas[propuesta.idSolicitante]['completa'].push({
                        'idSolicitante':propuesta.idSolicitante,
                        'concepto_pago_resoluciones_id':propuesta.concepto_pago_resoluciones_id,
                        'dias':propuesta.dias,
                        'monto':propuesta.monto,
                        'otro':''
                    });
                });
                $.each(dato.propuestaAl50,function(index,propuesta){
                    listaPropuestas[propuesta.idSolicitante]['al50'].push({
                        'idSolicitante':propuesta.idSolicitante,
                        'concepto_pago_resoluciones_id':propuesta.concepto_pago_resoluciones_id,
                        'dias':propuesta.dias,
                        'monto':propuesta.monto,
                        'otro':''
                    });
                });

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

    $("#resolucion_individual_id").change(function(){
        $("#divConceptosAcordados").hide();
        $("#divMotivoArchivo").hide();
        if($("#resolucion_individual_id").val() == 4){
            $("#divMotivoArchivo").show();
        }else if($("#resolucion_individual_id").val() == 1){
            //Datos laborales para conceptos
            // $.ajax({
            //     url:"/api/conceptos-resolucion/getLaboralesConceptos",
            //     type:"POST",
            //     dataType:"json",
            //     data:{
            //         // audiencia_id:'{{ $audiencia->id }}',
            //         solicitante_id:$("#parte_solicitante_id").val()
            //     },
            //     success:function(datos){
            //         console.log(datos);
            //         $('#remuneracionDiaria').val(datos.data.remuneracionDiaria);
            //         $('#salarioMinimo').val(datos.data.salarioMinimo);
            //         $('#antiguedad').val(datos.data.antiguedad);
            //     }
            // });
            $("#divConceptosAcordados").show();

        }
        else{
            $("#divMotivoArchivo").hide();
        }
    });
        $("#concepto_pago_resoluciones_id").on("change",function(){
            concepto = $("#concepto_pago_resoluciones_id").val();
            // $('#remuneracionDiaria').val(130);
            // $('#antiguedad').val(3.2);
            // $('#salarioMinimo').val(123.22);
            // concepto = $("#concepto_pago_resoluciones_id").val();
            pagoDia = $('#remuneracionDiaria').val();
            antiguedad = $('#antiguedad').val();
            salarioMinimo = $('#salarioMinimo').val();

            $('#monto').val('');
            $('#dias').val('');
            $('#otro').val('');
            switch (concepto) {
                case '7': // Prima topada por antiguedad
                    $('#monto').attr('disabled',true);
                    $('#dias').attr('disabled',true);
                    $('#otro').attr('disabled',true);
                    if(antiguedad > 0){
                        if(pagoDia <= (2*salarioMinimo)){
                            monto = antiguedad * 12 * pagoDia;
                        }else{
                            monto = antiguedad * 12 * (2*salarioMinimo);
                        }
                    }
                    $('#monto').val(monto);
                    break;
                case '8':    //Gratificacion D
                    $('#monto').attr('disabled',true);
                    $('#dias').attr('disabled',true);
                    $('#otro').removeAttr('disabled');
                    break;
                default: //Dias de sueldo, Dias de vacaciones
                    $('#monto').attr('disabled',true);
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
                    if(dias <15){
                        swal({title: 'Error',text: 'El numero de dias para aguinaldo debe ser mayor o igual a 15',icon: 'warning'});
                    }else{
                        monto = dias * pagoDia;
                    }
                    break;
                case '7': // Prima topada por antiguedad

                    break;
                case '8': //Gratificación D otro
                    break;
                default: //Dias de sueldo, Dias de vacaciones

                    monto = dias * pagoDia;
                    break;
            }
            monto = (monto >0 )? monto : "";
            $('#monto').val(monto);
        });

//     $("#btnAgregarResolucion").on("click",function(){
//         if(validarResolucionIndividual()){
//             var motivo_id = "";
//             var motivo_nombre = "";
//             if($("#resolucion_individual_id").val() == 4){
//                 motivo_id = $("#motivo_archivado_id").val();
//                 motivo_nombre = $("#motivo_archivado_id option:selected").text();
//             }
//             listaResolucionesIndividuales.push({
//                 parte_solicitante_id:$("#parte_solicitante_id").val(),
//                 parte_solicitante_nombre:$("#parte_solicitante_id option:selected").text(),
//                 parte_solicitado_id:$("#parte_solicitado_id").val(),
//                 parte_solicitado_nombre:$("#parte_solicitado_id option:selected").text(),
//                 resolucion_individual_id:$("#resolucion_individual_id").val(),
//                 resolucion_individual_nombre:$("#resolucion_individual_id option:selected").text(),
//                 motivo_archivado_id:motivo_id,
//                 motivo_archivado_nombre:motivo_nombre
//             });
//             $("#parte_solicitante_id").val("").trigger("change");
//             $("#parte_solicitado_id").val("").trigger("change");
//             $("#resolucion_individual_id").val("").trigger("change");
//             $("#motivo_archivado_id").val("").trigger("change");
// //                limpiarConcepto();
// //                cargarTablaConcepto();
//             cargarTablaResolucionesIndividuales();
//         }
//     });

    $("#btnAgregarResolucion").on("click",function(){
        if(validarResolucionIndividual()){
            var motivo_id = "";
            var motivo_nombre = "";
            // if($("#resolucion_individual_id").val() == 4){
            //     motivo_id = $("#motivo_archivado_id").val();
            //     motivo_nombre = $("#motivo_archivado_id option:selected").text();
            // }
            listaResolucionesIndividuales.push({
                // parte_solicitante_id:$("#parte_solicitante_id").val(),
                // parte_solicitante_nombre:$("#parte_solicitante_id option:selected").text(),
                parte_solicitado_id:$("#parte_solicitado_id").val(),
                parte_solicitado_nombre:$("#parte_solicitado_id option:selected").text(),
                rol_solicitante_id:$("#rol_solicitante_id").val(),
                rol_solicitante_nombre:$("#rol_solicitante_id option:selected").text(),
                // motivo_archivado_id:motivo_id,
                // motivo_archivado_nombre:motivo_nombre
            });
            $("#parte_solicitante_id").val("").trigger("change");
            $("#parte_solicitado_id").val("").trigger("change");
            $("#rol_solicitante_id").val("").trigger("change");
            // $("#motivo_archivado_id").val("").trigger("change");
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
                // table +='<td>'+e.parte_solicitante_nombre+'</td>';
                table +='<td>'+e.parte_solicitado_nombre+'</td>';
                table +='<td>'+e.rol_solicitante_nombre+'</td>';
                // table +='<td>'+e.motivo_archivado_nombre+'</td>';
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
    $("#btnGuardarResolucionMuchas").on("click",function(){
        let listaPropuestaConceptos = {};
        $('.collapseSolicitante').each(function() {
            idSol=$(this).attr('idSolicitante');
            if($("input[name='radiosPropuesta"+idSol+"']:checked"). val()=='otra'){
                listaPropuestaConceptos[idSol] = listaConfigConceptos[idSol];
            }else if($("input[name='radiosPropuesta"+idSol+"']:checked"). val()=='completa'){
                listaPropuestaConceptos[idSol]=listaPropuestas[idSol].completa;
            }else{
                listaPropuestaConceptos[idSol]=listaPropuestas[idSol].al50;
            }
        });
        // console.log(listaPropuestaConceptos);

        $.ajax({
            url:"/audiencia/resolucion",
            type:"POST",
            dataType:"json",
            data:{
                audiencia_id:'{{ $audiencia->id }}',
                convenio:$("#convenio").val(),
                desahogo:$("#desahogo").val(),
                resolucion_id:$("#resolucion_id").val(),
                timeline:true,
                listaRelacion:listaResolucionesIndividuales,
                listaConceptos:listaPropuestaConceptos,
                _token:"{{ csrf_token() }}"
            },
            success:function(data){
                if(data != null && data != ""){
                    window.location = "/audiencias/"+data.id+"/edit"
                }else{
                    swal({
                        title: 'Algo salio mal',
                        text: 'No se guardo el registro',
                        icon: 'warning'
                    });
                }
            }
        });
    });

    var timer = 0;
    function startTimer(){
        var days = "";
        if(lastTimeStamp != ""){
            lastTimeStamp = moment(lastTimeStamp)
            var actualDate = moment();
            var seconds = actualDate.diff(lastTimeStamp,"seconds");
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
                if(days != ""){
                    dias = days.toString().split(".")[0]+ "dias "
                }
                $('.countdown').text(dias + formatNumberTimer(timestamp.getHours())+':'+formatNumberTimer(timestamp.getMinutes())+':'+formatNumberTimer(timestamp.getSeconds()));
            }, 1000);
        }
    }

    function formatNumberTimer(n){
        return n > 9 ? "" + n: "0" + n;
    }
</script>
<script src="/assets/js/demo/timeline.demo.js"></script>
@endpush
