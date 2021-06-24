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
        <!--<div class="container">-->
        <form action="/notificaciones/search" method="GET" role="search" id="frmBuscar">
            <div class="col-md-12 row">
                <div class="col-md-9">&nbsp;</div>
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" class="form-control" name="q" id="q" value="{{$expediente ?? ''}}" placeholder="Buscar expediente">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-default">
                                <span class="fa fa-search"></span>
                            </button>
                        </span>
                        <span class="input-group-btn">
                            <button type="button" id="btnLimpiarFiltro" class="btn btn-default">
                                <span class="fa fa-eraser"></span>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </form>
        <!--</div>-->
        <table class="table table-bordered table-striped table-hover" id="data-table-default">
            <thead>
                <tr>
                    <th>Solicitud</th>
                    <th>Partes a notificar</th>
                    <th>Tipo de notificación</th>
                    <th>Evento origén</th>
                    <th>Fecha de petición</th>
                    <th>Notificada</th>
                    <th>Cambios</th>
                    <th>Envio de petición</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $parte)
                @if(isset($parte->parte))
                @if($parte->tipo_notificacion_id == 1)
                <tr style="background-color: #f59c1a59;">
                    @else
                    @if($parte->audiencia->expediente->solicitud->fecha_peticion_notificacion != null)
                <tr>
                    @else
                <tr style="background-color: rgb(157 36 73 / 0.28);">
                    @endif
                    @endif
                    <td>
                        <strong>Solicitud: </strong><a href="/solicitudes/consulta/{{$parte->audiencia->expediente->solicitud_id}}">{{$parte->audiencia->expediente->solicitud->folio}}/{{$parte->audiencia->expediente->solicitud->anio}}</a><br>
                        <strong>Expediente: </strong>{{$parte->audiencia->expediente->folio}}<br>
                        <strong>Audiencia: </strong><a href="/audiencias/{{$parte->audiencia->id}}/edit">{{$parte->audiencia->folio}}/{{$parte->audiencia->anio}}</a><br>
                    </td>
                    <td>
                        @if(isset($parte->parte))
                        @if($parte->parte->tipo_persona_id == 1)
                        - {{$parte->parte->nombre}} {{$parte->parte->primer_apellido}} {{$parte->parte->segundo_apellido}}<br>
                        @else
                        - {{$parte->parte->nombre_comercial}}<br>
                        @endif
                        @else
                        - Registro eliminado
                        @endif
                    </td>
                    <td>{{$parte->tipo_notificacion->nombre}}</td>
                    <td>{{$parte->audiencia->etapa_notificacion->etapa}}</td>
                    <td>{{$parte->audiencia->expediente->solicitud->fecha_peticion_notificacion ?? 'No enviada'}}</td>
                    @if($parte->parte->notificada)
                    <td>Si</td>
                    @else
                    <td>No</td>
                    @endif
                    <td>
                        <button onclick="cambiar_nombre({{$parte->parte_id}},{{$parte->id}},{{$parte->parte->tipo_persona_id}},'{{$parte->parte->nombre}}','{{$parte->parte->primer_apellido}}','{{$parte->parte->segundo_apellido}}','{{$parte->parte->nombre_comercial}}')" class="btn btn-xs btn-primary incidencia" title="Cambiar Nombre">
                            <i class="fa fa-user"></i>
                        </button>
                    </td>
                    <td>
                        @if($parte->audiencia->expediente->solicitud->fecha_peticion_notificacion != null)
                        Enviada
                        <button onclick="enviar_notificacion({{$parte->audiencia->id}},{{$parte->id}})" class="btn btn-xs btn-primary incidencia" title="Reenviar notificación">
                            <i class="fa fa-share-square"></i>
                        </button>
                        @else
                        Pendiente
                        <button onclick="enviar_notificacion({{$parte->audiencia->id}}, '')" class="btn btn-xs btn-primary incidencia" title="Enviar notificación">
                            <i class="fa fa-paper-plane"></i>
                        </button>
                        @endif
                        <button onclick="obtenerHistorial({{$parte->id}})" class="btn btn-xs btn-primary incidencia" title="Historial">
                            <i class="fa fa-history"></i>
                        </button>
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
        {!! $data->render() !!}
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
<div class="modal" id="modalCambioNombre" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Historial de notificación</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="row" id='divFisica'>
                    <div class=" col-md-4">
                        <div class="form-group">
                            <label for="nombre" class="control-label">Nombre</label>
                            {!! Form::text('nombre', null, ['class'=>'form-control', 'id'=>'nombre', 'placeholder'=>'Nombre']) !!}
                            <p class="help-block">Nombre del citado</p>
                        </div>
                    </div>
                    <div class=" col-md-4">
                        <div class="form-group">
                            <label for="primer_apellido" class="control-label">Primer apellido</label>
                            {!! Form::text('primer_apellido', null, ['class'=>'form-control', 'id'=>'primer_apellido', 'placeholder'=>'Primer apellido']) !!}
                            <p class="help-block">Primer apellido del citado</p>
                        </div>
                    </div>
                    <div class=" col-md-4">
                        <div class="form-group">
                            <label for="segundo_apellido" class="control-label">Segundo apellido</label>
                            {!! Form::text('segundo_apellido', null, ['class'=>'form-control', 'id'=>'segundo_apellido', 'placeholder'=>'Segundo apellido']) !!}
                            <p class="help-block">Segundo apellido del citado</p>
                        </div>
                    </div>
                </div>
                <div class="row" id='divMoral'>
                    <div class=" col-md-12">
                        <div class="form-group">
                            <label for="nombre_comercial" class="control-label">Nombre comercial</label>
                            {!! Form::text('nombre_comercial', null, ['class'=>'form-control', 'id'=>'nombre_comercial', 'placeholder'=>'Nombre comercial']) !!}
                            <p class="help-block">Nombre comercial del citado</p>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id='parte_id'>
            <input type="hidden" id='audiencia_parte_id'>
            <div class="modal-footer">
                <div class="text-right">
                    <button class="btn btn-primary btn-sm" id='btnModificarNombre'><i class="fa fa-save"></i>Modificar</button>
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
    $("#btnLimpiarFiltro").on("click", function(){
    $("#q").val("");
    $("#frmBuscar").submit();
    });
    function enviar_notificacion(audiencia_id, audiencia_parte_id){
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
    if (isConfirm){
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
            if (data.fecha_peticion_notificacion != null && data.fecha_peticion_notificacion != ""){
            swal({
            title: 'Éxito',
                    text: 'Se Envio la notificación',
                    icon: 'success'
            });
            location.reload();
            } else{
            swal({
            title: 'Error',
                    text: 'No se Envio la notificación',
                    icon: 'error'
            });
            }
            } catch (error){
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
    url:"/obtenerHistorial/" + audiencia_parte_id,
            type:"GET",
            dataType:"json",
            async:true,
            success:function(data){
            try{
            if (data != null){
            $("#divHistoricoCitatorio").hide();
            $("#divHistoricoMulta").hide();
            var div1 = "";
            var div2 = "";
            var div3 = "";
            var div4 = "";
            $.each(data, function(index, element){
            $.each(element.respuestas, function(index2, element2){
            if (element.tipo_notificacion == "citatorio"){
            if (element2.etapa_notificacion != null){
            div1 += "<tr>";
            div1 += "<td>" + element2.created_at + "</td>";
            div1 += ' <td>' + element2.etapa_notificacion.etapa + '</td>';
            div1 += ' <td>' + element2.fecha_peticion + '</td>';
            div1 += "</tr>";
            } else{
            div2 += "<tr>";
            div2 += "<td>" + element2.created_at + "</td>";
            div2 += ' <td>' + element2.finalizado + '</td>';
            div2 += ' <td>';
            div2 += '     <a href="/api/documentos/getFile/' + element2.documento.uuid + '" target="_blank" class="btn btn-xs btn-primary incidencia" title="Documento">';
            div2 += '         <i class="fa fa-file-pdf"></i>';
            div2 += '     </a>';
            div2 += ' </td>';
            div2 += "</tr>";
            }
            } else{
            if (element2.etapa_notificacion != null){
            div3 += "<tr>";
            div3 += "<td>" + element2.created_at + "</td>";
            div3 += ' <td>' + element2.etapa_notificacion.etapa + '</td>';
            div3 += ' <td>' + element2.fecha_peticion + '</td>';
            div3 += "</tr>";
            } else{
            div4 += "<tr>";
            div4 += "<td>" + element2.created_at + "</td>";
            div4 += ' <td>' + element2.finalizado + '</td>';
            div4 += ' <td>';
            div4 += '     <a href="/api/documentos/getFile/' + element2.documento.uuid + '" target="_blank" class="btn btn-xs btn-primary incidencia" title="Documento">';
            div4 += '         <i class="fa fa-file-pdf"></i>';
            div4 += '     </a>';
            div4 += ' </td>';
            div4 += "</tr>";
            }
            }
            });
            });
            $("#tableHistoricoCitatorioEnvios tbody").html(div1);
            $("#tableHistoricoCitatorioRespuestas tbody").html(div2);
            $("#tableHistoricoMultaEnvios tbody").html(div3);
            $("#tableHistoricoMultaRespuestas tbody").html(div4);
            if (div1 != "" || div2 != ""){
            $("#divHistoricoCitatorio").show();
            }
            if (div3 != "" || div4 != ""){
            $("#divHistoricoMulta").show();
            }
            $("#modal-historico").modal("show");
            } else{
            swal({
            title: 'Aviso',
                    text: 'No hay información de esta notificación',
                    icon: 'info'
            });
            }
            } catch (error){
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
    function cambiar_nombre(parte_id,audiencia_parte_id,tipo_persona_id,nombre,primer_apellido,segundo_apellido,nombre_comercial){
        if(tipo_persona_id == 1){
            $("#divFisica").show();
            $("#divMoral").hide();
        }else{
            $("#divFisica").hide();
            $("#divMoral").show();
        }
        $("#nombre").val(nombre);
        $("#primer_apellido").val(primer_apellido);
        $("#segundo_apellido").val(segundo_apellido);
        $("#nombre_comercial").val(nombre_comercial);
        $("#parte_id").val(parte_id);
        $("#audiencia_parte_id").val(audiencia_parte_id);
        $("#modalCambioNombre").modal("show");
    }
    $("#btnModificarNombre").on("click",function(){
        if(validarCambioNombre()){
                swal({
                    title: '¿Está seguro?',
                    text: 'Al oprimir el botón de aceptar se enviara la solicitud de notificación con los nuevos datos',
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
                    if (isConfirm){
                        $.ajax({
                            url:"/modificar_nombre",
                            type:"POST",
                            dataType:"json",
                            async:true,
                            data:{
                                parte_id:$("#parte_id").val(),
                                audiencia_parte_id:$("#audiencia_parte_id").val(),
                                nombre:$("#nombre").val(),
                                primer_apellido:$("#primer_apellido").val(),
                                segundo_apellido:$("#segundo_apellido").val(),
                                nombre_comercial:$("#nombre_comercial").val(),
                                _token:"{{ csrf_token() }}"
                            },
                            success:function(data){
                                try{
                                    if (data != null){
                                        swal("Exito!","Se realizo el cambio y se envio la notificación","success");
                                        location.reload();
                                    }
                                }catch(error){
                                        swal("Error!","Algo salio mal","error");
                                }
                            }
                        });
                    }
                });
            }else{
                swal("Error!","Llena todos los campos","error");
            }
        });
    function validarCambioNombre(){
        var pasa = true;
        if($("#tipo_persona_id").val() == 1){
            if($("#nombre").val() == "" || $("#primer_apellido").val() == "" || $("#segundo_apellido").val()){
                pasa = false;
            }
        }else{
            if($("#nombre_comercial").val() == ""){
                pasa = false;
            }
        }
        return pasa;
    }
</script>
@endpush
