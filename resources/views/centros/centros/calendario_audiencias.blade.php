@extends('layouts.default', ['paceTop' => true])

@section('title', 'Audiencias programadas')

@include('includes.component.datatables')
@include('includes.component.pickers')
@include('includes.component.calendar')
@include('includes.component.dropzone')

@section('content')
<ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="">Calendario</a></li>
</ol>
<h1 class="h2">Audiencias <small>Calendario del centro</small></h1>
<hr class="red">
<div class="panel panel-default">
    <div class="panel-header">
        <div class="row col-md-12">
            <button class="btn btn-primary btn-sm m-l-5" class="pull-right" id="btnNoAudiencia">
                <i class="fa fa-times"></i> No calendarizadas <span class="badge" style="background-color: #B38E5D;" id="spanNumeroAudiencias">{{count($audiencias)}}</span>
            </button>
            <form action="{{route('descargaCalendario')}}" method="POST" id="frmDescargaCalendario">
                @csrf
                <input type="hidden" name="vista" id="vista">
                <input type="hidden" name="fecha_inicio" id="fecha_inicio">
                <input type="hidden" name="fecha_fin" id="fecha_fin">
                <button type="submit" class="btn btn-primary btn-sm m-l-5" class="pull-right" id="btnDescargarCalendario">
                    <i class="fa fa-file-excel"></i> Descarga
                </button>
            </form>
        </div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <div class="vertical-box">
                    <!-- begin calendar -->
                    <div id="calendarioAgenda" class="vertical-box-column calendar"></div>
                    <!-- end calendar -->
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="modal-ratificacion-success" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title col-md-12">
                    <h4 >Audiencia <small>Datos de la audiencia</small></h4>
                    <hr class="red">
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
                                <th>Parte</th>
                                <th>Conciliador</th>
                                <th>Sala</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="col-md-12" id="calendarioReagendar" style="display:none;">
                    @include('expediente.audiencias.calendarioCambiarAudiencia')
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <label id="labelFinalizada" style="color: red;font-size: 1.2em;">Esta audiencia ya fue finalizada</label>
                    <button class="btn btn-primary btn-sm m-l-5" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-arrow-down"></i> Cerrar</button>
                    @if(session('rolActual')->name == 'Super Usuario' || session('rolActual')->name == 'Administrador del centro' || session('rolActual')->name == 'Supervisor de conciliación')
                    <button class="btn btn-primary btn-sm m-l-5" id="btnCambiarConciliador"><i class="fa fa-user-friends"></i> Cambiar Conciliador</button>
                    <button class="btn btn-primary btn-sm m-l-5" id="btnPago"><i class="fa fa-money-bill"></i> Registrar pago</button>
                    <button class="btn btn-primary btn-sm m-l-5" id="btnFinalizarRatificacion"><i class="fa fa-calendar"></i> Reprogramar</button>
                    <button class="btn btn-primary btn-sm m-l-5" id="btnSuspension" title="suspensión de audiencia vía remota por falta de aceptación del citado"><i class="fa fa-calendar"></i> Suspensión de audiencia</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="modal-audiencias" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Audiencias no agendadas</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-muted">
                    Las siguientes audiencias no pudieron encontrar disponibilidad en el plazo de asignación automatica
                </div>
                <div class="col-md-12">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Solicitante(s)</th>
                                <th>Citado(s)</th>
                                <th>Asignar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($audiencias as $audiencia)
                            <tr>
                                <td>{{$audiencia->folio}}/{{$audiencia->anio}}</td>
                                <td>
                                @foreach($audiencia->audienciaParte as $parte)
                                    @if($parte->parte->tipo_parte_id == 1)
                                        @if($parte->parte->tipo_persona_id == 1)
                                        -{{$parte->parte->nombre}} {{$parte->parte->primer_apellido}} {{$parte->parte->segundo_apellido}}<br>
                                        @elseif($parte->parte->tipo_persona_id == 2)
                                        -{{$parte->parte->nombre_comercial}}<br>
                                        @endif
                                    @endif
                                @endforeach
                                </td>
                                <td>
                                @foreach($audiencia->audienciaParte as $parte)
                                    @if($parte->parte->tipo_parte_id == 2)
                                        @if($parte->parte->tipo_persona_id == 1)
                                        -{{$parte->parte->nombre}} {{$parte->parte->primer_apellido}} {{$parte->parte->segundo_apellido}}<br>
                                        @elseif($parte->parte->tipo_persona_id == 2)
                                        -{{$parte->parte->nombre_comercial}}<br>
                                        @endif
                                    @endif
                                @endforeach
                                </td>
                                <td><button class="btn btn-sm btn-primary" title="Asignar" onclick="obtenerAudiencia({{$audiencia->id}},'NoCalendarizada')"><i class="fa fa-calendar"></i></button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="modal-cambiar-conciliador" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Asignar audiencia</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-muted">
                    - Selecciona el conciliador y la sala donde se celebrará la audiencia<br>
                    - La fecha limite para notificar será 5 días habiles previo a la fecha de audiencia (<span id="lableFechaInicio"></span>)
                </div>
                <div id="divAsignarUnoCambiar">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="conciliador_id" class="col-sm-6 control-label">Conciliador</label>
                            <select id="conciliador_cambio_id" class="form-control">
                                <option value="">-- Selecciona un conciliador</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="divAsignarDosCambiar">
                    <div class="col-md-12 row">
                        <div class="col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Solicitante</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="conciliador_cambio_solicitante_id" class="col-sm-6 control-label">Conciliador</label>
                                            <div class="col-sm-10">
                                                <select id="conciliador_cambio_solicitante_id" class="form-control">
                                                    <option value="">-- Selecciona un conciliador</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Citado</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="conciliador_cambio_solicitado_id" class="col-sm-6 control-label">Conciliador</label>
                                            <div class="col-sm-10">
                                                <select id="conciliador_cambio_solicitado_id" class="form-control">
                                                    <option value="">-- Selecciona un conciliador</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5" id="btnGuardarConciliadorCambio"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@include('includes.component.modal-caduco')
<input type="hidden" id="fecha_audiencia"/>
<input type="hidden" id="hora_inicio_audiencia"/>
<input type="hidden" id="hora_fin_audiencia"/>
<input type="hidden" id="fecha_audiencia"/>
<input type="hidden" id="audiencia_id"/>
<input type="hidden" id="multiple"/>
<input type="hidden" id="tipoAsignacionCambiar"/>
<input type="hidden" id="virtual"/>
@endsection

<!-- Fin Modal de disponibilidad-->
@push('scripts')
        <script>
            if({{ isset($mostrar_caducos) ? $mostrar_caducos : 'false' }}){
                $("#modal-caduco").modal("show");
            }
            var multiple = false;
            var audiencia_id = null;
            $(document).ready(function(){
                $.ajax({
                    url:"/audiencia/getCalendario",
                    type:"POST",
                    data:{
                        _token:"{{ csrf_token() }}"
                    },
                    dataType:"json",
                    success:function(data){
                        try{

                            if(data.minTime == "23:59:59" && data.maxtime == "00:00:00"){
                                swal({
                                    title: 'Error',
                                    text: 'No está configurada la disponibilidad del centro',
                                    icon: 'warning'
                                });
                            }
                            construirCalendario(data);
                        }catch(error){
                            console.log(error);
                        }
                    }
                });
            });
            function construirCalendario(arregloGeneral){
                $('#external-events .fc-event').each(function() {
                    // store data so the calendar knows to render an event upon drop
                    $(this).data('event', {
                            title: $.trim($(this).text()), // use the element's text as the event title
                            stick: true // maintain when user navigates (see docs on the renderEvent method)
                    });
                });
                $('#calendarioAgenda').fullCalendar({
                    header: {
                        left: 'month,agendaWeek',
                        center: 'title',
                        right: 'prev,today,next '
                    },
                    selectable: true,
                    selectHelper: true,
                    minTime: arregloGeneral.minTime,
                    maxTime: arregloGeneral.maxtime,
                    select: function(start, end,a,b) {
                        $('#calendarioAgenda').fullCalendar('unselect');
                        $("#fecha_audiencia").val(start);
                        if(b.type == "month"){ // si es la vista de mes, abrir la vista de semana
                            $('#calendarioAgenda').fullCalendar("gotoDate",start);
                            $(".fc-agendaWeek-button").click();
                        }
                    },
                    selectOverlap: function(event) {
                        return event.rendering !== 'background';
                    },
                    editable: false,
                    allDaySlot:false,
                    eventLimit: false,
                    businessHours: arregloGeneral.laboresCentro,
                    events: arregloGeneral.incidenciasCentro,
                    eventConstraint: "businessHours",
                    slotDuration:arregloGeneral.duracionPromedio,
                    eventClick: function(info) {
                        console.log(info);
                        if(info.audiencia_id != null){
                            audiencia_id = info.audiencia_id;
                            obtenerAudiencia(info.audiencia_id,info.tipo);
                            $("#calendarioReagendar").hide();
                        }
                    }
                });
            }
            $("#btnPago").on("click",function(){
                swal({
                    title: 'Confirmar',
                    text: '¿Desea proceder al registro del pago?',
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: 'No',
                            value: null,
                            visible: true,
                            className: 'btn btn-default',
                            closeModal: true,
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
                    location.href='/audiencias/'+audiencia_id+'/edit';
                });
            });
            function obtenerAudiencia(audiencia_id,tipo = "audiencia" ,fuente = "calendarizada"){
                $.ajax({
                    url:"/info_audiencia/"+audiencia_id,
                    type:"GET",
                    dataType:"json",
                    success:function(data){
                        try{
                            console.log(data);
                            if(data != null && data != ""){
                                if(tipo == "audiencia"){
                                    if(data.finalizada){
                                        $("#btnFinalizarRatificacion").hide();
                                        $("#btnCambiarConciliador").hide();
                                        $("#btnSuspension").hide();
                                        $("#labelFinalizada").show();
                                    }else{
                                        $("#btnFinalizarRatificacion").show();
                                        $("#btnCambiarConciliador").show();
                                        $("#labelFinalizada").hide();
                                        if(data.virtual){
                                            $("#btnSuspension").show()
                                        }else{
                                            $("#btnSuspension").hide()
                                        }
                                    }
                                    $("#btnPago").hide();
                                }else{
                                    $("#btnFinalizarRatificacion").hide();
                                    $("#btnCambiarConciliador").show();
                                    $("#btnSuspension").hide();
                                    $("#labelFinalizada").hide();
                                    $("#btnPago").show();
                                }
                                if(!data.multiple){
                                    $("#divAsignarUnoCambiar").show();
                                    $("#divAsignarDosCambiar").hide();
                                    $("#tipoAsignacionCambiar").val(1);
                                }else{
                                    $("#divAsignarUnoCambiar").hide();
                                    $("#divAsignarDosCambiar").show();
                                    $("#tipoAsignacionCambiar").val(2);
                                }
                                $("#audiencia_id").val(audiencia_id);
                                $("#spanFolio").text(data.folio+"/"+data.anio);
                                $("#virtual").val(data.virtual);
                                if(fuente == "NoCalendarizada"){
                                    $("#tableAudienciaSuccess").hide();
                                    $("#calendarioReagendar").show();
                                    $("#modal-audiencias").modal("hide");
                                    $("#modal-ratificacion-success").modal({backdrop: 'static', keyboard: false});
                                }else{
                                    $("#fecha_audiencia").val(data.id);
                                    $("#spanFechaAudiencia").text(dateFormat(data.fecha_audiencia,4));
                                    $("#spanHoraInicio").text(data.hora_inicio);
                                    $("#spanHoraFin").text(data.hora_fin);
                                    $("#hora_inicio_audiencia").val(data.fecha_audiencia+" "+data.hora_inicio);
                                    $("#hora_fin_audiencia").val(data.fecha_audiencia+" "+data.hora_fin);
                                    var table="";
                                    multiple = data.multiple;
                                        $.each(data.audiencia_parte,function(indexParte,elementParte){
                                            if(elementParte.tipo_parte_id != 3){
                                                table +='<tr>';
                                                table +='   <td>'+elementParte.parte.tipo_parte.nombre+'</td>';
                                                if(elementParte.parte.tipo_persona_id == 1){
                                                    table +='<td>'+elementParte.parte.nombre+' '+elementParte.parte.primer_apellido+' '+elementParte.parte.segundo_apellido+'</td>';
                                                }else{
                                                    table +='<td>'+elementParte.parte.nombre_comercial+'</td>';
                                                }
                                                if(data.multiple){
                                                    $.each(data.conciliadores_audiencias,function(index,element){
                                                        if(element.solicitante && elementParte.parte.tipo_parte_id == 1){
                                                            table +='   <td>'+element.conciliador.persona.nombre+' '+element.conciliador.persona.primer_apellido+' '+element.conciliador.persona.segundo_apellido+'</td>';
                                                        }else if(!element.solicitante && elementParte.parte.tipo_parte_id == 2){
                                                            table +='   <td>'+element.conciliador.persona.nombre+' '+element.conciliador.persona.primer_apellido+' '+element.conciliador.persona.segundo_apellido+'</td>';
                                                        }
                                                    });
                                                    $.each(data.salas_audiencias,function(index2,element2){
                                                        if(element2.solicitante && elementParte.parte.tipo_parte_id == 1){
                                                            table +='<td>'+element2.sala.sala+'</td>';
                                                        }else if(!element2.solicitante && elementParte.parte.tipo_parte_id == 2){
                                                            table +='<td>'+element2.sala.sala+'</td>';
                                                        }
                                                    });
                                                }else{
                                                    table +='   <td>'+data.conciliadores_audiencias[0].conciliador.persona.nombre+' '+data.conciliadores_audiencias[0].conciliador.persona.primer_apellido+' '+data.conciliadores_audiencias[0].conciliador.persona.segundo_apellido+'</td>';
                                                    table +='   <td>'+data.salas_audiencias[0].sala.sala+'</td>';
                                                }
                                                table +='</tr>';
                                            }
                                        });
                                    $("#tableAudienciaSuccess").show();
                                    $("#tableAudienciaSuccess tbody").html(table);
                                    $("#modalRatificacion").modal("hide");
                                    $("#modal-ratificacion-success").modal({backdrop: 'static', keyboard: false});
                                }
                            }else{
                                swal({
                                    title: 'Error',
                                    text: 'No se pudo obtener la información de la audiencia',
                                    icon: 'error'
                                });
                            }
                        }catch(error){
                            console.log(error);
                        }
                    }
                });
            }
            $("#btnFinalizarRatificacion").on("click",function(){
                $("#calendarioReagendar").show();
            });
            $("#btnNoAudiencia").on("click",function(){
                $("#modal-audiencias").modal("show");
            });
            $("#btnCambiarConciliador").on("click",function(){
                getConciliadores($("#hora_inicio_audiencia").val(),$("#hora_fin_audiencia").val());
                $("#modal-cambiar-conciliador").modal("show");
            });
            $("#btnGuardarConciliadorCambio").on("click",function(){
                var validacion = validarAsignacionCambio();
                if(!validacion.error){
                    var fecha_audiencia = $("#hora_inicio").val();
                    $.ajax({
                        url:'/audiencia/cambiar_conciliador',
                        type:"POST",
                        data:{
                            tipoAsignacion:$("#tipoAsignacion").val(),
                            asignacion:validacion.arrayEnvio,
                            audiencia_id:$("#audiencia_id").val(),
                            _token:"{{ csrf_token() }}"
                        },
                        dataType:"json",
                        success:function(data){
                            try{
                                console.log(data);
                                if(data != null && data != ""){
                                    swal({
                                        title: 'Correcto',
                                        text: 'Se cambio el conciliador de la audiencia',
                                        icon: 'success'
                                    });
                                    $('.modal').modal('hide');
                                    setTimeout('', 5000);
                                    location.reload();
                                }else{
                                    swal({
                                        title: 'Algo salió mal',
                                        text: 'No se registro la audiencia',
                                        icon: 'warning'
                                    });
                                }
                            }catch(error){
                                console.log(error);
                            }
                        }
                    });
                }else{
                    swal({
                        title: 'Algo salió mal',
                        text: validacion.msg,
                        icon: 'warning'
                    });
                }
            });
            function validarAsignacionCambio(){
                var error = false;
                var msg="";
                var arrayEnvio = new Array();
                if($("#tipoAsignacionCambiar").val() == 1){
                    if($("#conciliador_cambio_id").val() == ""){
                        error = true;
                        msg = "Selecciona un conciliador";
                    }
                    if(!error){
                        arrayEnvio.push({conciliador:$("#conciliador_cambio_id").val(),resolucion:true});
                    }
                }else{
                    if(($("#conciliador_cambio_solicitante_id").val() == "" || $("#conciliador_cambio_solicitado_id").val() == "") || ($("#conciliador_cambio_solicitante_id").val() == $("#conciliador_cambio_solicitado_id").val())){
                        error = true;
                        msg = "Asegurate de seleccionar los conciliadores y que no sean el mismo";
                    }
                    if(!error){
                        arrayEnvio.push({conciliador:$("#conciliador_cambio_solicitante_id").val(),resolucion:true});
                        arrayEnvio.push({conciliador:$("#conciliador_cambio_solicitado_id").val(),resolucion:false});
                    }
                }
                var array = [];
                array.error=error;
                array.msg=msg;
                array.arrayEnvio=arrayEnvio;
                return array;
            }
            $("#btnDescargarCalendario").on("click",function(e){
                e.preventDefault();
                //Obtenemos la vista del calendario
                var view = $('#calendarioAgenda').fullCalendar('getView');
                $("#vista").val($('#calendarioAgenda').fullCalendar('getDate'));
                //Obtenemos la fecha inicio en el calendario
                $("#fecha_inicio").val($('#calendarioAgenda').fullCalendar('getView').intervalStart.format('Y/MM/DD'));
                //Obtenemos la fecha fin del calendario
                $("#fecha_fin").val($('#calendarioAgenda').fullCalendar('getView').intervalEnd.subtract(1, 'd').format('Y/MM/DD'));
                $("#frmDescargaCalendario").submit();
            });
            $("#btnSuspension").on("click",function(){
                swal({
                    title: 'Advertencia',
                    text: 'Al oprimir aceptar se eliminará la fecha de audiencia, junto con el conciliador y la sala, la audiencia deberá ser reprogramar en la opción no calendarizadas',
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: 'No',
                            value: null,
                            visible: true,
                            className: 'btn btn-default',
                            closeModal: true,
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
                        $.ajax({
                        url:'/audiencia/suspension/'+$("#audiencia_id").val(),
                        type:"GET",
                        dataType:"json",
                        success:function(data){
                            try{
                                swal({
                                    title: 'Correcto',
                                    text: 'Se suspendió la audiencia',
                                    icon: 'success'
                                });
                                $('.modal').modal('hide');
                                setTimeout('', 5000);
                                location.reload();
                            }catch(error){
                                
                            }
                        }
                    });
                    }else{
                        
                    }
                });
            });
        </script>
@endpush
