@extends('layouts.default', ['paceTop' => true])

@section('title', 'Centros de conciliación')

@include('includes.component.datatables')
@include('includes.component.pickers')

@section('content')

    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item active"><a href="javascript:;">Centros</a></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Administrar centros de conciliación <small>Listado de notificaciones</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Listado de notificaciones</h4>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover" id="data-table-default">
                <thead>
                    <tr>
                        <th>Solicitud</th>
                        <th>Partes a notificar</th>
                        <th>Tipo de notificación</th>
                        <th>Evento origén</th>
                        <th>Fecha de petición</th>
                        <th>Notificada</th>
                        <th>Envio de petición</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($solicitudes as $solicitud)
                    @if($solicitud->fecha_peticion_notificacion != null)
                    <tr>
                    @else
                    <tr style="background-color: rgb(157 36 73 / 0.28);">
                    @endif
                        <td>
                            <strong>Solicitud: </strong>{{$solicitud->folio}}/{{$solicitud->anio}}<br>
                            <strong>Expediente: </strong>{{$solicitud->expediente->folio}}<br>
                            <strong>Audiencia: </strong>{{$solicitud->audiencia}}<br>
                        </td>
                        <td>
                            @foreach($solicitud->partes as $parte)
                                @if($parte->tipo_parte_id == 2)
                                    @if($parte->tipo_persona_id == 1)
                                    - {{$parte->nombre}} {{$parte->primer_apellido}} {{$parte->segundo_apellido}}<br>
                                    @else
                                        - {{$parte->nombre_comercial}}<br>
                                    @endif
                                @endif
                            @endforeach
                        </td>
                        <td>{{$solicitud->tipo_notificacion}}</td>
                        <td>{{$solicitud->etapa_notificacion}}</td>
                        <td>{{$solicitud->fecha_peticion_notificacion ?? 'No enviada'}}</td>
                        @if($solicitud->notificada)
                            <td>Si</td>
                        @else
                            <td>No</td>
                        @endif
                        @if($solicitud->fecha_peticion_notificacion != null)
                            <td>Enviada
                            <button onclick="enviar_notificacion({{$solicitud->id}})" class="btn btn-xs btn-primary incidencia" title="Reenviar notificación">
                                <i class="fa fa-share-square"></i>
                            </button>
                            </td>
                        @else
                            <td>Pendiente
                            <button onclick="enviar_notificacion({{$solicitud->id}})" class="btn btn-xs btn-primary incidencia" title="Enviar notificación">
                                <i class="fa fa-paper-plane"></i>
                            </button>
                            </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#data-table-default').DataTable({language: {url: "/assets/plugins/datatables.net/dataTable.es.json"}});
        });
        function enviar_notificacion(solicitud_id){
            swal({
                title: '¿Está seguro?',
                text: 'Al oprimir el botón de aceptar se enviara la solicitud de notificación',
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
                    $.ajax({
                        url:"/notificaciones/enviar/"+solicitud_id,
                        type:"GET",
                        dataType:"json",
                        async:true,
                        success:function(data){
                            try{
                                if(data.fecha_peticion_notificacion != null && data.fecha_peticion_notificacion != ""){
                                    swal({
                                        title: 'Éxito',
                                        text: 'Se Envio la notificación',
                                        icon: 'success'
                                    });
                                    location.reload();
                                }else{
                                    swal({
                                        title: 'Error',
                                        text: 'No se Envio la notificación',
                                        icon: 'success'
                                    });
                                    
                                }
                            }catch(error){
                                swal({
                                    title: 'Error',
                                    text: 'Algo salio mal con la solicitud',
                                    icon: 'success'
                                });
                                
                            }
                        }
                    });
                }
            });
        }
    </script>
@endpush
