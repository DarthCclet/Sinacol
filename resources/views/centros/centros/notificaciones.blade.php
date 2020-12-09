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
                        <th>Expediente</th>
                        <th>Partes a notificar</th>
                        <th>Fecha de ratificación</th>
                        <th>Envio de petición</th>
                        <th>Fecha de petición</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($solicitudes as $solicitud)
                    <tr>
                        <td>{{$solicitud->folio}}/{{$solicitud->anio}}</td>
                        <td>{{$solicitud->expediente->folio}}</td>
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
                        <td>{{\Carbon\Carbon::parse($solicitud->fecha_ratificacion)->format('d/m/Y H:i')}}</td>
                        @if($solicitud->fecha_peticion_notificacion != null)
                            <td>Enviada</td>
                        @else
                            <td>Pendiente</td>
                        @endif
                        <td>{{$solicitud->fecha_peticion_notificacion ?? 'No enviada'}}</td>
                        <td>
                        @if($solicitud->fecha_peticion_notificacion == null)
                            <button onclick="enviar_notificacion({{$solicitud->id}})" class="btn btn-xs btn-primary incidencia" title="Enviar notificación">
                                <i class="fa fa-plane"></i>
                            </button>
                        @endif
                        </td>
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
