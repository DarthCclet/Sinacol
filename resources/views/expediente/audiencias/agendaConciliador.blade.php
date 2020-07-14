@extends('layouts.default')

@section('title', 'Calendar')

@include('includes.component.datatables')
@include('includes.component.pickers')
@include('includes.component.calendar')
@push('styles')
<style>
    .fc-event{
        height:60px !important;
    }
</style>
@endpush
@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item active"><a href="{!! route("audiencias.index") !!}">Audiencia</a></li>
        <li class="breadcrumb-item active"><a href="javascript:;">Agenda</a></li>

    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="h2">Calendario de audiencias <small>Audiencias por celebrar</small></h1>
    <hr class="red">
    <!-- end page-header -->
    <!-- begin vertical-box -->
    <div class="vertical-box">
    <!-- end event-list -->
    <!-- begin calendar -->
    <div id="calendar" class="vertical-box-column calendar"></div>
    <!-- end calendar -->
    </div>
@endsection

@push('scripts')
        <script>
            $(document).ready(function(){
                $.ajax({
                    url:"getAudienciaConciliador",
                    type:"GET",
                    dataType:"json",
                    async:true,
                    success:function(data){
                        construirCalendario(data);
                    }
                });
            });
            function construirCalendario(audiencias){
                $('#external-events .fc-event').each(function() {
                    // store data so the calendar knows to render an event upon drop
                    $(this).data('event', {
                            title: $.trim($(this).text()), // use the element's text as the event title
                            stick: true // maintain when user navigates (see docs on the renderEvent method)
                    });
                });
                $('#calendar').fullCalendar({
                    header: {
                        left: 'agendaWeek,agendaDay',
                        center: 'title',
                        right: 'prev,today,next '
                    },
                    selectable: true,
                    defaultView:'agendaDay',
                    select: function(start, end,a,b) {
                        $('#calendar').fullCalendar('unselect');
                    },
                    selectOverlap: function(event) {
                        return event.rendering !== 'background';
                    },
                    allDaySlot:false,
                    events: audiencias,
                    eventConstraint: "businessHours",
                    eventClick: function(info) {
                        swal({
                            title: '¿Está seguro?',
                            text: 'Al oprimir el botón de aceptar se dará inicio a la audiencia',
                            icon: 'warning',
                            buttons: {
                                cancel: {
                                    text: 'Cancelar',
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
                                
                            }
                        });
                      }
                });
            }
            
        </script>
@endpush
