@extends('layouts.default', ['paceTop' => true])

@section('title', 'Solicitudes')

@include('includes.component.datatables')
@include('includes.component.pickers')


@section('content')
    <!-- begin breadcrumb -->
    @if(auth()->check())

        <ol class="breadcrumb float-xl-right">
            <li class="breadcrumb-item"><a href="">Home</a></li>
            <li class="breadcrumb-item"><a href="{!! route("solicitudes.index") !!}">Solicitudes</a></li>
            <li class="breadcrumb-item"><a href="javascript:;">Crear Solicitud</a></li>
        </ol>
    @endif
    <!-- end breadcrumb -->

    <div class="panel panel-inverse">
        <div class="panel-body">
            
<style>
    .inputError {
        border: 1px red solid;
    }
    .needed:after {
      color:darkred;
      content: " (*)";
   }
    .highlighted{
        background-color: #FFFF00;
        color: #000 !important;
    }
    .select2-results__option--highlighted{
        background: #348fe2 !important;
        color: #fff !important;
    }
    .select2-results__options {
        max-height: 400px;
    }
    .wizard-steps li.active, .wizard-steps li.current, .wizard-steps li.success {
        background-color: #9D2449;
        color: #fff;
        height: 70px !important;
        top: 0;
    }
    .loading-results {
        background-image: url('/assets/img/spinner.gif');
        background-repeat: no-repeat;
        padding-left: 10px;
        background-position: 120px 50%;
    }
    .upper{
        text-transform: uppercase;
    }
    .ui-accordion-content{
        height:100% !important;
    }
    .card-header{
        border: 1px solid #B38E5D !important;
        background: #B38E5D !important;
        color: white !important;
        font-size: 65% !important;
        padding: 4px !important;
        width: 100%;
        text-align: left !important;
    }
</style>
@if(auth()->user())
    <input type="hidden" id="externo" value="0">
@else
    <input type="hidden" id="externo" value="1">
@endif
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a href="#default-tab-1" data-toggle="tab" class="nav-link active">
            <span class="d-sm-none">Sol</span>
            <span class="d-sm-block d-none">Solicitud</span>
        </a>
    </li>
    @if (isset($audiencias))
        @if (count($audiencias) > 0)
        <li class="nav-item">
                <a href="#default-tab-2" data-toggle="tab" class="nav-link">
                    <span class="d-sm-none">Aud</span>
                    <span class="d-sm-block d-none">Audiencia</span>
                </a>
            </li>
        @endif
    @endif
    @if(isset($documentos))
        <li class="nav-item">
            <a href="#default-tab-3" data-toggle="tab" class="nav-link">
                <span class="d-sm-none">Doc</span>
                <span class="d-sm-block d-none">Documentos</span>
            </a>
        </li>
    @endif
</ul>
<div class="tab-content" style="background: #f2f3f4 !important;">
    <div class="tab-pane fade active show" id="default-tab-1">
        <div class="tab-content" style="background: #f2f3f4 !important;">
        <div class="tab-pane fade active show" id="default-tab-1">
            <div class="col-md-12">
                    <hr class="red">
                    <h2>Datos generales de la Solicitud</h2>
                    <div id="divSolicitudMod">
                    </div>
                    <hr class="red">
                    <h2>Solicitantes</h2>
                    <div id="divSolicitantesMod">
                    </div>
                    <hr class="red">
                    <h2>Citados</h2>
                    <div id="divCitadosMod">
                    </div>
            </div>
        </div>
    </div>
    </div>
    <div class="tab-pane fade row"  id="default-tab-2">
        <div class="content">
            @if (isset($audiencias))
                    
                @if(Count($audiencias) > 0)
                    <table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
                        <thead>
                            <tr>
                                <th>Folio de audiencia</th>
                                <th>Fecha de audiencia</th>
                                <th>Hora de audiencia</th>
                                <th>Conciliador</th>
                                <th>Estatus</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($audiencias as $audiencia)
                            <tr class="odd gradeX">
                                <td width="1%" class="f-s-600 text-inverse">{{$audiencia->folio}}/{{$audiencia->anio}}</td>
                                <td>{{date('d/m/Y', strtotime($audiencia->fecha_audiencia))}}</td>
                                <td>{{\Carbon\Carbon::createFromFormat('H:i:s',$audiencia->hora_inicio)->format('h:i')}} - {{\Carbon\Carbon::createFromFormat('H:i:s',$audiencia->hora_fin)->format('h:i')}}</td>
                                
                                @if($audiencia->conciliador->persona->tipo_persona_id == 1)
                                    <td>{{$audiencia->conciliador->persona->nombre}} {{$audiencia->conciliador->persona->primer_apellido}} {{$audiencia->conciliador->persona->segundo_apellido}}</td>
                                @else
                                    <td>{{isset($audiencia->conciliador->persona->nombre_comercial)}}</td>
                                @endif

                                <td>{{$audiencia->finalizada ? "Concluida":"Pendiente"}}</td>
                                <td class="all">
                                    <div style="display: inline-block;">
                                        @if($audiencia->etapas_resolucion_audiencia_count > 0)
                                            <div style="display: inline-block;" class="m-2"><a title="Detalle" href="{!! route("audiencias.edit",$audiencia->id) !!}" class="btn btn-xs btn-primary"><i class="fa fa-gavel"></i></a></div>
                                        @endif
                                        @if($audiencia->finalizada == false)
                                            @if(auth()->user()->hasRole("Personal conciliador"))
                                                @if($solicitud->tipo_solicitud_id == 1)
                                                    <div style="display: inline-block;" class="m-2"><a title="Iniciar proceso de audiencia" href="{!! route("guiaAudiencia",["id"=>"$audiencia->id"]) !!}" class="btn btn-xs btn-primary"><i class="fa fa-tasks"></i></a></div>
                                                @else
                                                    <div style="display: inline-block;" class="m-2"><a title="Iniciar proceso de audiencia" href="{!! route("resolucionColectiva",["id"=>"$audiencia->id"]) !!}" class="btn btn-xs btn-primary"><i class="fa fa-tasks"></i></a></div>
                                                @endif
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
                @else
                <div> <h1> Audiencia disponible despues de ratificaci&oacute;n </h1> </div>
            @endif
        </div>
    </div>
    <div class="tab-pane fade row" id="default-tab-3">
        @if(isset($documentos))
            @include('expediente.expediente.documentos',$documentos)
        @endif
    </div>
</div>
<!-- end wizard -->

<!-- inicio Modal Domicilio-->

<div class="modal" id="modal-jornada" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="display:none;">
    <div class="modal-dialog ">
        <div class="modal-content">

            <div class="modal-body" >
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h5>Para determinar tu tipo de jornada, debes considerar las primeras 8 horas que laboras en un día.</h5>
                <p style="font-size:large;">
                    <ol>
                        <li>Si estas 8 horas transcurren entre 6 am y 8 pm, es una jornada "DIURNA".</li>
                        <li>Si estas primeras 8 horas incluyen 3 horas o menos dentro del horario 8 pm - 6 am, es una jornada "MIXTA"</li>
                        <li>Si estas 8 horas incluyen 3.5 o más horas dentro del horario 8 pm - 6 am, es una jornada NOCTURNA. </li>
                        <li>En caso de que tengas algunas jornadas diurnas y otras mixtas o nocturnas, debes poner una jornada "MIXTA".</li>
                    </ol>
                </p>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-primary btn-sm" class="close" data-dismiss="modal" aria-hidden="true" ><i class="fa fa-times"></i> Aceptar</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Fin Modal de Domicilio-->

<!-- inicio Modal Alerta Giro-->

<div class="modal" id="modal-giro" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Advertencia<i class="fa fa-warning"></i></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body" >
                
                <p style="font-size:large;">
                    El sistema indica que la actividad principal del patrón es de competencia local, no federal.
                </p>
                <p style="font-size:large;">
                    Acuda al Centro de Conciliación local de su entidad para realizar la solicitud, si no tiene la posibilidad de realizar a tiempo su solicitud en el Centro de Conciliación local, puede continuar la solicitud en el sistema federal y en el momento de ratificación su solicitud será revisada por un funcionario del CFCRL, quien determinará una corrección de la actividad principal o la emisión de una constancia de incompetencia y el envío de su solicitud al Centro de Conciliación competente.
                </p>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-primary btn-sm" class="close" data-dismiss="modal" aria-hidden="true" ><i class="fa fa-times"></i> Aceptar</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Fin Modal de Alerta Giro-->

{{-- Modal confirma falta de correo --}}
<div class="modal" id="modal_valida_correo" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="display:none;">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h5>No captur&oacute; correo electr&oacute;nico, tome en cuenta que el correo electr&oacute;nico es muy importante para el seguimiento del proceso de conciliaci&oacute;n</h5>
                <div>
                    <label for="sin_correo">Seleccione si no tiene correo electr&oacute;nico</label>
                    <input type="checkbox" value="1" onchange="if($('#sin_correo').is(':checked')){ $('#btnContinuarCorreo').removeAttr('disabled'); }else{ $('#btnContinuarCorreo').attr('disabled', true);  }" data-render="switchery" data-theme="default" id="sin_correo" />
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal" ><i class="fa fa-times"></i> Capturar correo</a>
                    <button class="btn btn-primary btn-sm m-l-5" disabled data-dismiss="modal" onclick="$('#divMapaSolicitante').show();$('#continuar2').hide();"  id='btnContinuarCorreo'><i class="fa fa-save"></i> Continuar</button>
                </div>
            </div>
        </div>
    </div>
</div>
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


<!--Fin de modal de representante legal-->
<input type="hidden" id="expediente_id">
<!--</div>-->
@push('scripts')

<script>
    // Se declaran las variables globales
    var arraySolicitados = []; //Lista de citados
    var objAyudaCitado = {recibo_oficial:"",recibo_pago:""}; //Lista de citados
    var arraySolicitantes = []; //Lista de solicitantes
    var arrayDomiciliosSolicitante = []; // Array de domicilios para el solicitante
    var arrayDomiciliosSolicitado = []; // Array de domicilios para el citado
    var arrayObjetoSolicitudes = []; // Array de objeto_solicitude para el citado
    var arrayContactoSolicitantes = []; // Array de objeto_solicitude para el citado
    var arrayContactoSolicitados = []; // Array de objeto_solicitude para el citado
    // var arraySolicitanteExcepcion = {}; // Array de solicitante excepción
    var ratifican = false;; // Array de solicitante excepción
    var listaContactos=[];

    $(document).ready(function() {
        
        var solicitud='{{ $solicitud->id ?? ""}}';
        consultarSolicitud(solicitud);
        $("#solicitud_id").val(solicitud);
        $("#solicitud_id_modal").val(solicitud);
        $("#solicitud_id_excepcion").val(solicitud);
            
    });
    
    history.pushState(null, document.title, location.href);
    history.back();
    history.forward();
    window.onpopstate = function () {
        history.go(1);
    };

    
    function getSolicitudFromBD(solicitud){
        arraySolicitados = []; //Lista de citados
        arraySolicitantes = []; //Lista de solicitantes
        arrayDomiciliosSolicitante = []; // Array de domicilios para el solicitante
        arrayDomiciliosSolicitado = []; // Array de domicilios para el citado
        arrayObjetoSolicitudes = []; // Array de objeto_solicitude para el citado
        solicitudObj = {}; // Array de objeto_solicitude para el citado
        ratifican = false;; // Array de solicitante excepción
        $.ajax({
            url:'/solicitudes/'+solicitud,
            type:"GET",
            dataType:"json",
            async:false,
            data:{},
            success:function(data){
                try{
                    $("#datosIdentificacionSolicitado").show();
                    arraySolicitados = Object.values(data.solicitados);
                    arraySolicitantes = Object.values(data.solicitantes);
                    $.each(arraySolicitantes ,function(key,value){
                        if(arraySolicitantes[key].dato_laboral){
                            if($.isArray(arraySolicitantes[key].dato_laboral)){
                                arraySolicitantes[key].dato_laboral = arraySolicitantes[key].dato_laboral[0];
                            }
                        }
                    })

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
                    solicitudObj.estatus_solicitud = data.estatusSolicitud.nombre;
                    solicitudObj.fecha_ratificacion = dateFormat(data.fecha_ratificacion,2);
                    solicitudObj.fecha_recepcion = dateFormat(data.fecha_recepcion,2);
                    solicitudObj.fecha_conflicto = dateFormat(data.fecha_conflicto,4);
                    solicitudObj.giro_comercial_id = data.giro_comercial_id;
                    solicitudObj.giro_comercial = data.giroComercial.nombre;
                    solicitudObj.observaciones = data.observaciones;
                    if(data.expediente){
                        solicitudObj.expediente = data.expediente.folio;
                    }
                    solicitudObj.folio = data.folio;
                    solicitudObj.anio = data.anio;
                    solicitudObj.centro = data.centro.nombre;
                    
                }catch(error){
                    console.log(error);
                }
            }
        });
    }

    $(".catSelect").select2({width: '100%'});
    $(".dateBirth").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: "c-80:",
        format:'dd/mm/yyyy',
    });


 var getDate = function (input) {
    return new Date(input.date.valueOf());
 }

    $('#fecha_ingreso').datepicker({
        format: "dd/mm/yyyy",
        changeMonth: true,
        changeYear: true,
        maxDate:0,
        yearRange: "c-80:",
        language: 'es',
        autoclose: true,
    });
    var a = $('#fecha_ingreso').datepicker("getDate");
    $('#fecha_salida').datepicker({
        format: "dd/mm/yyyy",
        language: "es",
        maxDate:0,
        yearRange: "c-80:",
        changeMonth: true,
        changeYear: true,
        autoclose: true
    });

    $('#fecha_ingreso').datepicker().on('change', function (ev) {
        var date2 = $('#fecha_ingreso').datepicker('getDate');
        date2.setDate(date2.getDate()+1);
        $('#fecha_salida').datepicker("option", "minDate", date2);
    });


    $(".date").datepicker({useCurrent: false,format:'dd/mm/yyyy'});
    $(".dateTime").datetimepicker({useCurrent: false,format:'DD/MM/YYYY HH:mm:ss'});
    $(".date").keypress(function(event){
        event.preventDefault();
    });


    $('.upper').on('keyup', function () {
        var valor = $(this).val();
        $(this).val(valor.toUpperCase());
    });

    function consultarSolicitud(solicitud_id){
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
    function formatoSolicitud(){
        var html = "";
        html += "<div class='col-md-10 offset-1'>";
            html += "<div class='col-md-12 row'>";
                html += "<div class='col-md-12 text-right'>";
                    html += "<h4><b>Estatus:</b> "+solicitudObj.estatus_solicitud+ "<br></h4>";
                html += "</div>";
                if(solicitudObj.ratificada == true){
                    html += "<div class='col-md-6'>";
                        html += "<b>Expediente:</b> "+solicitudObj.expediente+ "<br>";
                    html += "</div>";
                    html += "<div class='col-md-6'>";
                        html += "<b>Centro:</b> "+solicitudObj.centro+ "<br>";
                    html += "</div>";
                }
                if(solicitudObj.ratificada == true){
                    html += "<div class='col-md-6'>";
                        html += "<b>Fecha de ratificaci&oacute;n:</b> "+solicitudObj.fecha_ratificacion+ "<br>";
                    html += "</div>";
                }
                html += "<div class='col-md-6'>";
                    html += "<b>Folio de la solicitud:</b> "+solicitudObj.folio + "/" + solicitudObj.anio + "<br>";
                html += "</div>";
                html += "<div class='col-md-6'>";
                    html += "<b>Fecha de recepci&oacute;n:</b> "+solicitudObj.fecha_recepcion+ "<br>";
                html += "</div>";
                html += "<div class='col-md-6'>";
                    html += "<b>Fecha de conflicto:</b> "+solicitudObj.fecha_conflicto+ "<br>";
                html += "</div>";
                
                html += "<div class='col-md-12'>";
                    html += "<b>Giro comercial</b><br> "+solicitudObj.giro_comercial+ "<br>";
                html += "</div>";
                html += "<div class='col-md-12'>";
                    html += "<b>Objetos de la solicitud</b>";
                    html += "<ul>";
                    $.each(arrayObjetoSolicitudes, function (key, value) {
                        html += "<li>"+value.nombre+"</li>";
                    });
                    html += "</ul>";
                html += "</div>";
                html += "<div class='col-md-12'>";
                    html += "<b>Observaciones:</b>";
                        html += "<p>"+solicitudObj.observaciones+"</p>";
                html += "</div>";
            html += "</div>";
        html += "</div>";
        
        return html;
    }
    function formarSolicitantes(){
        var html = "";
        html += '<div class="accordion col-md-10 offset-1" id="accordionExample">';
        $.each(arraySolicitantes,function(key, value){
            html+='<div class="card">';
                html+='<div class="card-header" id="headingOne">';
                    html+='<h2 class="mb-0">';
                        html+='<button id="collSol'+value.id+'" class="btn btn-link card-header " i type="button" data-toggle="collapse" data-target="#collapseSol'+value.id+'" aria-expanded="true" aria-controls="collapseOne"  ><i style="font-size: large;" class="fa fa-angle-down"></i> ';
                        if(value.tipo_persona_id == 1){
                            html+=' '+value.nombre + " " + value.primer_apellido+" "+(value.segundo_apellido|| "");
                        }else{
                            html+=' '+value.nombre_comercial;
                        }
                        html+=' </button>';
                    html+='</h2>';
                html+='</div>';

                html+='<div id="collapseSol'+value.id+'" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">';
                    html+='<div class="card-body">';
                        html+='<div >';
                            html+='<div>';
                                if(value.curp){
                                    html+='<div>';
                                        html+='<label><b>CURP:</b>'+value.curp+'</label>';
                                    html+='</div>';
                                }
                                if(value.solicita_traductor){
                                    html+='<div>';
                                        html+='<label><b>Solicita traductor</b> </label><p>Lengua: ('+value.lengua_indigena.nombre+')</p>';
                                    html+='</div>';
                                }
                                
                                if(value.rfc){
                                    html+='<div>';
                                        html+='<label><b>RFC:</b>'+value.rfc+'</label>';
                                    html+='</div>';
                                }
                                html+='<div>';
                                    html+="<b>Direccion:</b><br> &nbsp;&nbsp;&nbsp;&nbsp;"+value.domicilios[0].tipo_vialidad+" "+value.domicilios[0].vialidad+", "+value.domicilios[0].asentamiento+", "+value.domicilios[0].municipio+", "+value.domicilios[0].estado.toUpperCase();
                                html+='</div>';
                                html+='<div>';
                                    html+='<label><b>Contactos:</b></label>';
                                    html+='<ul>';
                                    $.each(value.contactos,function(indice, contacto){
                                        html+="<li>"+contacto.contacto+"</li>";
                                    });
                                    html+='</ul>';
                                html+='</div>';
                                if(value.dato_laboral){

                                    html+='<div class="col-md-12 row">';
                                        html+='<label class="col-md-12"><b>Datos Laborales</b></label><br>';
                                        html+='<label class="col-md-6"><b> &nbsp;&nbsp;&nbsp;&nbsp;Puesto:</b>'+value.dato_laboral.puesto+'</label><br>';
                                        if(value.dato_laboral.nss){
                                            html+='<label class="col-md-6"><b> &nbsp;&nbsp;&nbsp;&nbsp;N&uacute;mero de seguro social:</b>'+(value.dato_laboral.nss|| "")+'</label><br>';
                                        }
                                        html+='<label class="col-md-6"><b> &nbsp;&nbsp;&nbsp;&nbsp;Fecha de Ingreso:</b>'+dateFormat(value.dato_laboral.fecha_ingreso,4)+'</label><br>';
                                        if(!value.dato_laboral.labora_actualmente){
                                            html+='<label class="col-md-6"><b> &nbsp;&nbsp;&nbsp;&nbsp;Fecha de Salida:</b>'+dateFormat(value.dato_laboral.fecha_salida,4)+'</label><br>';
                                        }
                                    html+='</div>';
                                }
                            html+='</div>';
                        html+='</div>';
                    html+='</div>';
                html+='</div>';
            html+='</div>';
        });
        html += '</div>';
        return html;
    }

    function formarCitados(){
        var html = "";
        html += '<div class="accordion col-md-10 offset-1" id="accordionCitados">';
        $.each(arraySolicitados,function(key, value){
            html+='<div class="card">';
                html+='<div class="card-header" id="headingTwo">';
                    html+='<h2 class="mb-0">';
                        html+='<button id="collCit'+value.id+'" class="btn btn-link card-header " i type="button" data-toggle="collapse" data-target="#collapseCit'+value.id+'" aria-expanded="true" aria-controls="collapseOne"  ><i style="font-size: large;" class="fa fa-angle-down"></i> ';
                        if(value.tipo_persona_id == 1){
                            html+=' '+value.nombre + " " + value.primer_apellido+" "+(value.segundo_apellido|| "");
                        }else{
                            html+=' '+value.nombre_comercial;
                        }
                        html+=' </button>';
                    html+='</h2>';
                html+='</div>';

                html+='<div id="collapseCit'+value.id+'" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionCitados">';
                    html+='<div class="card-body">';
                        html+='<div >';
                            html+='<div>';
                                if(value.curp){
                                    html+='<div>';
                                        html+='<label><b>CURP:</b>'+value.curp+'</label>';
                                    html+='</div>';
                                }
                                if(value.rfc){
                                    html+='<div>';
                                        html+='<label><b>RFC:</b>'+value.rfc+'</label>';
                                    html+='</div>';
                                }
                                html+='<div>';
                                    html+="<b>Direccion:</b><br> &nbsp;&nbsp;&nbsp;&nbsp;"+value.domicilios[0].tipo_vialidad+" "+value.domicilios[0].vialidad+", "+value.domicilios[0].asentamiento+", "+value.domicilios[0].municipio+", "+value.domicilios[0].estado.toUpperCase();
                                html+='</div>';
                                html+='<div>';
                                    html+='<label><b>Contactos:</b></label>';
                                    html+='<ul>';
                                    $.each(value.contactos,function(indice, contacto){
                                        html+="<li>"+contacto.contacto+"</li>";
                                    });
                                    html+='</ul>';
                                html+='</div>';
                            html+='</div>';
                        html+='</div>';
                    html+='</div>';
                html+='</div>';
            html+='</div>';
        });
        html += '</div>';
        return html;
    }
</script>

@endpush

        </div>
    </div>
@endsection
@push('scripts')
    <script>
        var edit = false;
    </script>
@endpush
