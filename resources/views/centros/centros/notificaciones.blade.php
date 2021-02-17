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
                        @if($solicitud['tipo_notificacion_id'] == 1)
                            <tr style="background-color: #f59c1a59;">
                        @else
                            @if($solicitud['fecha_peticion_notificacion'] != null)
                                <tr>
                            @else
                                <tr style="background-color: rgb(157 36 73 / 0.28);">
                            @endif
                        @endif
                        <td>
                            <strong>Solicitud: </strong><a href="/solicitudes/consulta/{{$solicitud['id']}}">{{$solicitud["folio"]}}</a><br>
                            <strong>Expediente: </strong>{{$solicitud["expediente"]}}<br>
                            <strong>Audiencia: </strong><a href="/audiencias/{{$solicitud['audiencia_id']}}/edit">{{$solicitud['audiencia']}}</a><br>
                        </td>
                        <td>
                            @if($solicitud['parte']->tipo_persona_id == 1)
                                - {{$solicitud['parte']->nombre}} {{$solicitud['parte']->primer_apellido}} {{$solicitud['parte']->segundo_apellido}}<br>
                            @else
                                - {{$solicitud['parte']->nombre_comercial}}<br>
                            @endif
                        </td>
                        <td>{{$solicitud['tipo_notificacion']}}</td>
                        <td>{{$solicitud['etapa_notificacion']}}</td>
                        <td>{{$solicitud['fecha_peticion_notificacion'] ?? 'No enviada'}}</td>
                        @if($solicitud['notificada'])
                            <td>Si</td>
                        @else
                            <td>No</td>
                        @endif
                        <td>
                        @if($solicitud['fecha_peticion_notificacion'] != null)
                            Enviada
                            <button onclick="enviar_notificacion({{$solicitud['audiencia_id']}},{{$solicitud['parte']->audiencia_parte_id}})" class="btn btn-xs btn-primary incidencia" title="Reenviar notificación">
                                <i class="fa fa-share-square"></i>
                            </button>
                        @else
                            Pendiente
                            <button onclick="enviar_notificacion({{$solicitud['audiencia_id']}},'')" class="btn btn-xs btn-primary incidencia" title="Enviar notificación">
                                <i class="fa fa-paper-plane"></i>
                            </button>
                        @endif
                            <button onclick="obtenerHistorial({{$solicitud['parte']->audiencia_parte_id}})" class="btn btn-xs btn-primary incidencia" title="Historial">
                                <i class="fa fa-history"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal" id="modal-historico" aria-hidden="true" style="display:none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Historial de notificación</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div id="divHistoricoCitatorio">
                        <h4>Citatorio</h4>
                        <hr class="red">
                        <div class="col-md-12 row">
                            <div class="col-md-6">
                                <h5>Envios</h5>
                                <table class="table table-striped table-bordered table-hover" id="tableHistoricoCitatorioEnvios">
                                    <thead>
                                        <tr>
                                            <td>Fecha y hora</td>
                                            <td>Evento origen</td>
                                            <td>Fecha de petición</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div id="divHistoricoCitatorioRespuestas" class="col-md-6">
                                <h5>Respuestas</h5>
                                <table class="table table-striped table-bordered table-hover" id="tableHistoricoCitatorioRespuestas">
                                    <thead>
                                        <tr>
                                            <td>Fecha y hora</td>
                                            <td>Respuesta</td>
                                            <td>Documento</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="divHistoricoMulta">
                        <h4>Multa</h4>
                        <hr class="red">
                        <div class="col-md-12 row">
                            <div class="col-md-6">
                                <h5>Envios</h5>
                                <table class="table table-striped table-bordered table-hover" id="tableHistoricoMultaEnvios">
                                    <thead>
                                        <tr>
                                            <td>Fecha y hora</td>
                                            <td>Evento origen</td>
                                            <td>Fecha de petición</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div id="divHistoricoCitatorioRespuestas" class="col-md-6">
                                <h5>Respuestas</h5>
                                <table class="table table-striped table-bordered table-hover" id="tableHistoricoMultaRespuestas">
                                    <thead>
                                        <tr>
                                            <td>Fecha y hora</td>
                                            <td>Respuesta</td>
                                            <td>Documento</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="text-right">
                        <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#data-table-default').DataTable({language: {url: "/assets/plugins/datatables.net/dataTable.es.json"}});
        });
        function enviar_notificacion(audiencia_id,audiencia_parte_id){
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
                        url:"/notificaciones/enviar",
                        type:"POST",
                        data:{
                            "audiencia_id":audiencia_id,
                            "audiencia_parte_id":audiencia_parte_id,
                            "_token":"{{ csrf_token() }}"
                        },
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
        function obtenerHistorial(audiencia_parte_id){
            $.ajax({
                url:"/obtenerHistorial/"+audiencia_parte_id,
                type:"GET",
                dataType:"json",
                async:true,
                success:function(data){
                    try{
                        if(data != null){
                            $("#divHistoricoCitatorio").hide();
                            $("#divHistoricoMulta").hide();
                            var div1="";
                            var div2="";
                            var div3="";
                            var div4="";
                            $.each(data,function(index,element){
                                $.each(element.respuestas,function(index2,element2){
                                    if(element.tipo_notificacion == "citatorio"){
                                        if(element2.etapa_notificacion != null){
                                            div1 +="<tr>";
                                            div1 +="<td>"+element2.created_at+"</td>";
                                            div1 +=' <td>'+element2.etapa_notificacion.etapa+'</td>';
                                            div1 +=' <td>'+element2.fecha_peticion+'</td>';
                                            div1 +="</tr>";
                                        }else{
                                            div2 +="<tr>";
                                            div2 +="<td>"+element2.created_at+"</td>";
                                            div2 +=' <td>'+element2.finalizado+'</td>';
                                            div2 +=' <td>';
                                            div2 +='     <a href="/api/documentos/getFile/'+element2.documento.uuid+'" target="_blank" class="btn btn-xs btn-primary incidencia" title="Documento">';
                                            div2 +='         <i class="fa fa-file-pdf"></i>';
                                            div2 +='     </a>';
                                            div2 +=' </td>';
                                            div2 +="</tr>";
                                            
                                        }
                                    }else{
                                        if(element2.etapa_notificacion != null){
                                            div3 +="<tr>";
                                            div3 +="<td>"+element2.created_at+"</td>";
                                            div3 +=' <td>'+element2.etapa_notificacion.etapa+'</td>';
                                            div3 +=' <td>'+element2.fecha_peticion+'</td>';
                                            div3 +="</tr>";
                                        }else{
                                            div4 +="<tr>";
                                            div4 +="<td>"+element2.created_at+"</td>";
                                            div4 +=' <td>'+element2.finalizado+'</td>';
                                            div4 +=' <td>';
                                            div4 +='     <a href="/api/documentos/getFile/'+element2.documento.uuid+'" target="_blank" class="btn btn-xs btn-primary incidencia" title="Documento">';
                                            div4 +='         <i class="fa fa-file-pdf"></i>';
                                            div4 +='     </a>';
                                            div4 +=' </td>';
                                            div4 +="</tr>";
                                        }
                                    }
                                });
                            });
                            $("#tableHistoricoCitatorioEnvios tbody").html(div1);
                            $("#tableHistoricoCitatorioRespuestas tbody").html(div2);
                            $("#tableHistoricoMultaEnvios tbody").html(div3);
                            $("#tableHistoricoMultaRespuestas tbody").html(div4);
                            if(div1 != "" || div2 != ""){
                                $("#divHistoricoCitatorio").show();
                            }
                            if(div3 != "" || div4 != ""){
                                $("#divHistoricoMulta").show();
                            }
                            $("#modal-historico").modal("show");
                        }else{
                            swal({
                                title: 'Aviso',
                                text: 'No hay información de esta notificación',
                                icon: 'info'
                            });
                        }
                    }catch(error){
                    console.log(error);
                        swal({
                            title: 'Error',
                            text: 'Algo salio mal con la solicitud',
                            icon: 'success'
                        });

                    }
                }
            });
        }
    </script>
@endpush
