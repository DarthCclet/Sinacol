@extends('layouts.default')

@section('title', 'Calendar')

@include('includes.component.datatables')
@include('includes.component.pickers')
@include('includes.component.calendar')
@push('styles')
<style>
    .clickThrough{
        border-color: red;
    }
</style>
@endpush
@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item active">Calendar</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Calendario de audiencias <small>Audiencias por celebrar</small></h1>
    <!-- end page-header -->
    <hr />
    <!-- begin vertical-box -->
    <div class="vertical-box">
    <!-- end event-list -->
    <!-- begin calendar -->
    <div id="calendar" class="vertical-box-column calendar"></div>
    <!-- end calendar -->
    </div>
    <!-- end vertical-box -->
<!-- inicio Modal de disponibilidad-->
<div class="modal" id="modal-asignar" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Asignar audiencia</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-muted">
                    - Selecciona el conciliador y la sala donde se celebrará la audiencia
                </div>
                <div id="divAsignarUno">
                    <div class="col-md-12 row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="conciliador_id" class="col-sm-6 control-label">Conciliador</label>
                                <div class="col-sm-10">
                                    <select id="conciliador_id" class="form-control">
                                        <option value="">-- Selecciona un centro</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sala_id" class="col-sm-6 control-label">Sala</label>
                                <div class="col-sm-10">
                                    <select id="sala_id" class="form-control">
                                        <option value="">-- Selecciona un centro</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="divAsignarDos">
                    <div class="col-md-12 row">
                        <div class="col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Solicitante</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="conciliador_solicitante_id" class="col-sm-6 control-label">Conciliador</label>
                                            <div class="col-sm-10">
                                                <select id="conciliador_solicitante_id" class="form-control">
                                                    <option value="">-- Selecciona un centro</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="sala_solicitante_id" class="col-sm-6 control-label">Sala</label>
                                            <div class="col-sm-10">
                                                <select id="sala_solicitante_id" class="form-control">
                                                    <option value="">-- Selecciona un centro</option>
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
                                    <h4 class="panel-title">Solicitado</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="conciliador_solicitado_id" class="col-sm-6 control-label">Conciliador</label>
                                            <div class="col-sm-10">
                                                <select id="conciliador_solicitado_id" class="form-control">
                                                    <option value="">-- Selecciona un centro</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="sala_solicitado_id" class="col-sm-6 control-label">Sala</label>
                                            <div class="col-sm-10">
                                                <select id="sala_solicitado_id" class="form-control">
                                                    <option value="">-- Selecciona un centro</option>
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
                    <button class="btn btn-primary btn-sm m-l-5" id="btnGuardar"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="fecha_audiencia">
<input type="hidden" id="hora_inicio">
<input type="hidden" id="hora_fin">
<input type="hidden" id="tipoAsignacion">
<!-- Fin Modal de disponibilidad-->
@endsection

@push('scripts')
        <script>
            $(document).ready(function(){
                $.ajax({
                    url:"/api/audiencia/getCalendario",
                    type:"POST",
                    data:{
                        centro_id:1
                    },
                    dataType:"json",
                    success:function(data){
                        construirCalendario(data);
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
                $('#calendar').fullCalendar({
                    header: {
                        left: 'month,agendaWeek',
                        center: 'title',
                        right: 'prev,today,next '
                    },
                    droppable: true, // this allows things to be dropped onto the calendar
                    drop: function() {
                        $(this).remove();
                    },
                    selectable: true,
                    selectHelper: true,
                    slotDuration:arregloGeneral.duracionPromedio,
                    eventConstraint: {
                        start: moment().format('YYYY-MM-DD'),
                        end: '2100-01-01' // hard coded goodness unfortunately
                    },
                    select: function(start, end,a,b) {
                        var ahora = new Date();
                        end=moment(end).format('Y-MM-DD HH:mm:ss');
                        start=moment(start).format('Y-MM-DD HH:mm:ss');
                        var startVal = new Date(start);
                        if(startVal > ahora){ //validar si la fecha es mayor que hoy
                            if(b.type == "month"){ // si es la vista de mes, abrir la vista de semana
                                $('#calendar').fullCalendar("gotoDate",start);
                                $(".fc-agendaWeek-button").click();
                                $("#fecha_audiencia").val(start);
                            }else{
                                SolicitarAudiencia(start,end);
                            }
                        }
                        $('#calendar').fullCalendar('unselect');
                    },
                    selectOverlap: function(event) {
                        return ($('#calendar').fullCalendar('getView').name == "week");
                    },
                    editable: false,
                    allDaySlot:false,
                    eventLimit: true,
                    businessHours: arregloGeneral.laboresCentro,
                    events: arregloGeneral.incidenciasCentro,
                });
            }
            function SolicitarAudiencia(inicio,fin){
                swal({
                    title: '¿Concilian juntos?',
                    text: 'Al oprimir aceptar se asignará solo un consiliador y una sola sala para solicitante y solicitado',
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: 'Separados',
                            value: null,
                            visible: true,
                            className: 'btn btn-default',
                            closeModal: true,
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
                        CargarModal(1,inicio,fin);
                    }else{
                        CargarModal(2,inicio,fin);
                    }
                });
            }
            function CargarModal(aux,inicio,fin){
                console.log(inicio);
                console.log(fin);
                if(aux == 1){
                    $("#divAsignarUno").show();
                    $("#divAsignarDos").hide();
                    $("#tipoAsignacion").val(1);
                }else{
                    $("#divAsignarUno").hide();
                    $("#divAsignarDos").show();
                    $("#tipoAsignacion").val(2);
                }
                getConciliadores(inicio,fin);
                getSalas(inicio,fin);
                $("#hora_inicio").val(inicio);
                $("#hora_fin").val(fin);
                $("#modal-asignar").modal("show");
            }
            getConciliadores = function(fechaInicio,fechaFin){
                $.ajax({
                    url:"/api/audiencia/ConciliadoresDisponibles",
                    type:"POST",
                    data:{
                        fechaInicio:fechaInicio,
                        fechaFin:fechaFin,
                        centro_id:1
                    },
                    dataType:"json",
                    success:function(data){
                        if(data != null && data != ""){
                            $("#conciliador_id,#conciliador_solicitado_id,#conciliador_solicitante_id").html("<option value=''>-- Selecciona un centro</option>");
                            $.each(data,function(index,element){
                                $("#conciliador_id,#conciliador_solicitado_id,#conciliador_solicitante_id").append("<option value='"+element.id+"'>"+element.persona.nombre+" "+element.persona.primer_apellido+" "+element.persona.segundo_apellido+"</option>");
                            });
                        }else{
                            $("#conciliador_id,#conciliador_solicitado_id,#conciliador_solicitante_id").html("<option value=''>-- Selecciona un centro</option>");
                        }
                        $("#conciliador_id,#conciliador_solicitado_id,#conciliador_solicitante_id").select2();
                    }
                });
            };
            function getSalas(fechaInicio,fechaFin){
                $.ajax({
                    url:"/api/audiencia/SalasDisponibles",
                    type:"POST",
                    data:{
                        fechaInicio:fechaInicio,
                        fechaFin:fechaFin,
                        centro_id:1
                    },
                    dataType:"json",
                    success:function(data){
                        $("#sala_id,#sala_solicitado_id,#sala_solicitante_id").html("<option value=''>-- Selecciona una sala</option>");
                        if(data != null && data != ""){
                            $.each(data,function(index,element){
                                $("#sala_id,#sala_solicitado_id,#sala_solicitante_id").append("<option value='"+element.id+"'>"+element.sala+"</option>");
                            });
                        }
                        $("#sala_id,#sala_solicitado_id,#sala_solicitante_id").select2();
                    }
                });
            }
            $("#btnGuardar").on("click",function(){
                var validacion = validarAsignacion();
                console.log(validacion);
                if(!validacion.error){
                    $.ajax({
                        url:"/api/audiencia/calendarizar",
                        type:"POST",
                        data:{
                            fecha_audiencia:new Date($("#fecha_audiencia").val()).toISOString(),
                            hora_inicio:$("#hora_inicio").val(),
                            hora_fin:$("#hora_fin").val(),
                            audiencia_id:1,
                            asignacion:validacion.arrayEnvio
                        },
                        dataType:"json",
                        success:function(data){
                            console.log(data);
                            if(data != null && data != ""){
                                window.location.href = "{{ route('audiencias.index')}}";
                            }else{
                                swal({
                                    title: 'Algo salio mal',
                                    text: 'No se registro la audiencia',
                                    icon: 'warning'
                                });
                            }
                        }
                    });
                }else{
                    swal({
                        title: 'Algo salio mal',
                        text: validacion.msg,
                        icon: 'warning'
                    });
                }
            });
            function validarAsignacion(){
                var error = false;
                var msg="";
                var arrayEnvio = new Array();
                if($("#tipoAsignacion").val() == 1){
                    if($("#sala_id").val() == ""){
                        error = true;
                        msg = "Selecciona una sala";
                    }
                    if($("#conciliador_id").val() == ""){
                        error = true;
                        msg = "Selecciona un conciliador";
                    }
                    if(!error){
                        arrayEnvio.push({sala:$("#sala_id").val(),conciliador:$("#conciliador_id").val(),resolucion:true});
                    }
                }else{
                    if(($("#sala_solicitante_id").val() == "" || $("#sala_solicitado_id").val() == "") || ($("#sala_solicitante_id").val() == $("#sala_solicitado_id").val())){
                        error = true;
                        msg = "Asegurate de seleccionar las salas y que no sea la misma";
                    }
                    if(($("#conciliador_solicitante_id").val() == "" || $("#conciliador_solicitado_id").val() == "") || ($("#conciliador_solicitante_id").val() == $("#conciliador_solicitado_id").val())){
                        error = true;
                        msg = "Asegurate de seleccionar los conciliadores y que no sean el mismo";
                    }
                    if(!error){
                        arrayEnvio.push({sala:$("#sala_solicitante_id").val(),conciliador:$("#conciliador_solicitante_id").val(),resolucion:true});
                        arrayEnvio.push({sala:$("#sala_solicitado_id").val(),conciliador:$("#conciliador_solicitado_id").val(),resolucion:false});
                    }
                }
                var array = [];
                array.error=error;
                array.msg=msg;
                array.arrayEnvio=arrayEnvio;
                return array;
            }
        </script>
@endpush
