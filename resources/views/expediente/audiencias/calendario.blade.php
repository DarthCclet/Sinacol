@extends('layouts.default')

@section('title', 'Calendar')

@include('includes.component.calendar')

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
                <div class="col-md-12 row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="centro_id" class="col-sm-6 control-label">Conciliador</label>
                            <div class="col-sm-10">
                                <select id="conciliador_id" class="form-control">
                                    <option value="">-- Selecciona un centro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="centro_id" class="col-sm-6 control-label">Sala</label>
                            <div class="col-sm-10">
                                <select id="sala_id" class="form-control">
                                    <option value="">-- Selecciona un centro</option>
                                </select>
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
<!-- Fin Modal de disponibilidad-->
@endsection

@push('scripts')
        <script>
            $(document).ready(function(){
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
                    slotDuration:"01:00:00",
                    select: function(start, end,a,b) {
                        var ahora = new Date();
                        if(start > ahora){ //validar si la fecha es mayor que hoy
                            if(b.type == "month"){ // si es la vista de mes, abrir la vista de semana
                                $('#calendar').fullCalendar("gotoDate",start);
                                $(".fc-agendaWeek-button").click();
                            }else{
                                start=moment(start).format('Y-MM-DD HH:mm:ss');
                                end=moment(end).format('Y-MM-DD HH:mm:ss');
                                getConciliadores(start,end);
//                                var title = prompt('Event Title:');
//                                var eventData;
//                                if (title) {
//                                    eventData = {
//                                            title: title,
//                                            start: start,
//                                            end: end
//                                    };
//                                    $('#calendar').fullCalendar('renderEvent', eventData, true); // stick? = true
//                                }
                            }
                            $('#calendar').fullCalendar('unselect');
                        }
                    },
                    editable: true,
                    eventLimit: true, // allow "more" link when too many events
                    events: [{
                        title:'hola',
                        start: '2020-02-11T00:00:00',
                        end: '2020-02-11T23:00:00',
                    },{
                        title:'hola2',
                        start: '2020-02-11T23:00:00',
                        end: '2020-02-12T00:00:00',
                    }
                ],
                });
            });
            getConciliadores = function(fechaInicio,fechaFin){
//                $.ajax({
//                    url:"/api/conciliadoresDisponibles",
//                    type:"POST",
//                    data:{
//                        fechaInicio:fechaInicio,
//                        fechaFin:fechaFin,
//                        centro_id:1
//                    },
//                    dataType:"json",
//                    success:function(data){
//                        console.log(data);
                        $("#modal-asignar").modal("show");
//                        if(data != null && data != ""){
//                            $("#conciliador_id").html("<option value=''>-- Selecciona un centro</option>");
//                            $.each(data.data.data,function(index,element){
//                                $("#conciliador_id").append("<option value='"+element.id+"'>"+element.nombre+"</option>");
//                            });
//                        }else{
//                            $("#conciliador_id").html("<option value=''>-- Selecciona un centro</option>");
//                        }
//                        $("#conciliador_id").select2();
//                        return "hola";
//                    }
//                });
            };
            function getSalas(fechaInicio,fechaFin){
                
            }
        </script>
@endpush
