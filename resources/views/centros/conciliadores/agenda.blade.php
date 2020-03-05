@extends('layouts.default')

@section('title', 'Calendar')

@include('includes.component.datatables')
@include('includes.component.pickers')
@include('includes.component.calendar')
@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item"><a href="{!! route('conciliadores.index') !!}">Conciliadores</a></li>
        <li class="breadcrumb-item active">Agenda</li>
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
<div class="modal" id="modal-audiencia" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Detalles de audiencia</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12 row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label"><strong>Expediente:</strong>&nbsp<span id="LabelExpediente"></span></label><br>
                            <label class="control-label"><strong>Sala:</strong>&nbsp<span id="LabelSala"></span></label><br>
                            <label class="control-label"><strong>Fecha:</strong>&nbsp<span id="LabelFecha"></span></label><br>
                            <label class="control-label"><strong>Hora Inicio:</strong>&nbsp<span id="LabelHoraInicio"></span></label><br>
                            <label class="control-label"><strong>Hora Fin:</strong>&nbsp<span id="LabelHoraFin"></span></label><br>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-striped table-bordered table-td-valign-middle" id="tablePartes">
                            <thead>
                                <tr>
                                    <th class="text-nowrap">Tipo Parte</th>
                                    <th class="text-nowrap">Nombre de la parte</th>
                                    <th class="text-nowrap">Conciliador</th>
                                    <th class="text-nowrap">Sala</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</a>
                    <a class="btn btn-white btn-sm" id="btnResolucion"><i class="fa fa-gavel"></i> Resolucion</a>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="id">
@endsection
@push('scripts')
    <script>
        $(document).ready(function(){
            $.ajax({
                url:"/api/audiencia/getAgenda",
                type:"POST",
                data:{
                    id:1
                },
                dataType:"json",
                success:function(data){
                    construirCalendario(data);
                }
            });
        });
        function construirCalendario(data){
            console.log(data);
            $('#calendar').fullCalendar({
                header: {
                    left: '',
                    center: 'title',
                    right: 'prev,today,next '
                },
                selectable: true,
                selectHelper: true,
                defaultView:"agendaWeek",
                selectable:false,
                selectOverlap: function(event) {
                    return event.rendering !== 'background' ;
                },
                editable: false,
                allDaySlot:false,
                eventLimit: false,
                eventConstraint: "businessHours",
                events:data,
                eventClick: function(info,a) {
                    modalAudiencia(info.audiencia);
                }
            });
        }
        function modalAudiencia(audiencia){
            $("#id").val(audiencia.id);
            $("#LabelExpediente").text(audiencia.folio+"/"+audiencia.anio);
            $("#LabelSala").text(audiencia.sala);
            $("#LabelFecha").text(audiencia.fecha_audiencia);
            $("#LabelHoraInicio").text(audiencia.hora_inicio);
            $("#LabelHoraFin").text(audiencia.hora_fin);
            $("#modal-audiencia").modal("show");
            var tbody="";
            $.each(audiencia.partes,function(index,element){
                tbody +="<tr>";
                tbody +="   <td>"+element.tipo_parte+"</td>";
                tbody +="   <td>"+element.nombre+" "+element.primer_apellido+" "+element.segundo_apellido+"</td>";
                tbody +="   <td>"+element.nombreConciliador+"</td>";
                tbody +="   <td>"+element.sala+"</td>";
                tbody +="</tr>";
            });
            $("#tablePartes tbody").html(tbody);
        }
        $("#btnResolucion").on("click",function(){
            window.location.href = "audiencias/"+$("#id").val()+"/edit";
        });
    </script>
@endpush
