@extends('layouts.default', ['paceTop' => true])

@section('title', 'expedientes')

@include('includes.component.datatables')
@include('includes.component.pickers')

@section('content')
<div class="panel panel-default" style="margin: 0 5% 0 5%;">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:;">Audiencias</a></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Incidencias Solicitudes</h1>
    <!-- end page-header -->
    <!-- begin panel -->
    <div class="panel panel-default" id="lista-incidencias">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Solicitudes con incidencias</h4>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body"> 
            <div class="col-md-12 text-right" style="margin:1%;">
                <button class="btn btn-primary" type="button" onclick="nuevaIncidencia();" > <i class="fa fa-plus-circle"></i> Agregar Incidencia</button>
            </div>
            @if (isset($solicitudes))
                    
            @if(Count($solicitudes) > 0)
                <table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
                    <thead>
                        <tr>
                            <th>Folio de solicitud</th>
                            <th>Expediente</th>
                            <th>Centro</th>
                            <th>Partes</th>
                            <th>Raz&oacute;n de la Incidencia</th>
                            <th>Justificaci&oacute;n</th>
                            <th>Solicitud asociada</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($solicitudes as $solicitud)
                        <tr class="odd gradeX">
                            <td width="1%" class="f-s-600 text-inverse">{{$solicitud->folio}}/{{$solicitud->anio}}</td>
                            <td>{{$solicitud->expediente ? $solicitud->expediente->folio: ""}}</td>
                            <td>{{$solicitud->centro ? $solicitud->centro->nombre : "" }}</td>
                            <td >
                                
                                @foreach ($solicitud->partes as $parte)
                                    @if($parte->tipo_parte_id == 1 || $parte->tipo_parte_id == 2)
                                        @if($parte->tipo_persona_id == 1)
                                           - {{$parte->nombre}} {{$parte->primer_apellido}} {{$parte->segundo_apellido}}   
                                        @else
                                           - {{$parte->nombre_comercial}}
                                        @endif
                                    @endif
                                    <br>
                                @endforeach
                            </td>
                            <td>{{$solicitud->tipoIncidenciaSolicitud ? $solicitud->tipoIncidenciaSolicitud->nombre: ""}}</td>
                            <td>{{$solicitud->justificacion_incidencia ? substr($solicitud->justificacion_incidencia,0,50).'...'   :"" }}</td>
                            <td>{{$solicitud->solicitud ? $solicitud->solicitud->folio."/".$solicitud->solicitud->anio : "" }}</td>
                            <td>
                                <button title="Detalle de la incidencia" data-toggle="tooltip" data-placement="top" class="btn btn-xs btn-warning btn-primary" onclick="getIncidencia('{{$solicitud->id}}')"><i class="fa fa-eye"></i></button>
                                <a title="Ver datos de la solicitud" data-toggle="tooltip" data-placement="top" class="btn btn-xs btn-warning btn-primary" href="{!! route('solicitudes.consulta',$solicitud->id) !!}" ><i class="fa fa-search"></i></a>
                                <button title="Eliminar incidencia" data-toggle="tooltip" data-placement="top" class="btn btn-xs btn-warning btn-borrar" onclick="borrarIncidencia('{{$solicitud->id}}')"><i class="fa fa-trash btn-borrar"></i></button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
            @else
            <div> <h1> Solicitudes no disponibles  </h1> </div>
        @endif
        </div>
    </div>
    <div class="panel panel-default" id="nueva-incidencia" style="display: none;">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Selecciona el la solicitud a la que deseas agregar una incidencia</h4>
            <div class="panel-heading-btn">
            </div>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            <div class="col-md-12 text-right">
                <button class="btn btn-primary" type="button" onclick="cancelarCaptura();" > <i class="fa fa-times"></i> Cancelar</button>
            </div>
            <input type="hidden" id="solicitud_id" value="" />
            <input type="hidden" id="solicitud_asociada_id" value="" />
            <input type="hidden" id="solicitud_id_aux" value="" />
            <div>
                <div id="divSolicitud" >
                    <div class="col-md-12 row">
                        <div class="offset-2">
                            <div class="col-md-12 row" style="mar">
                                <input class="form-control numero col-md-5 md-2" id="folio_solicitud" placeholder="Folio de solicitud" type="text" value="">
                                <input class="form-control numero col-md-5 md-2" maxlength="4" style="margin-left:2%;" id="anio_solicitud" placeholder="A&ntilde;o de solicitud" type="text" value="">
                            </div>
                            <label class="help-block neweded">Folio de solicitud</label>
                        </div>
                        <div >
                            <button class="btn btn-primary" onclick="consultarSolicitud();">Buscar</button>
                        </div>
                    </div>
                    <div id="SolicitudInfo" class="card col-md-8 offset-2" style="margin-top: 5%;">
                        <div class="col-md-12">
                            <div id="divSolicitudMod">
                            </div>
                            <div id="divSolicitantesMod">
                            </div>
                            <div id="divCitadosMod">
                            </div>
                            <h4 style="color: #9d2449;" id="folio_solicitud_asociar"></h4>
                        </div>
                        <div style="display: none; margin: 2%;" class="col-md-12 row" id="divBtnSol" >
                            <div class="col-md-12 row">
                                <button class="btn btn-primary" style="margin-left: 1%;" onclick="$('#modal-incidencia').modal('show')">Marcar incidencia</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>

    <!-- inicio Modal Preview-->
<div class="modal" id="modal-incidencia" data-backdrop="static" data-keyboard="false" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Capture la justificaci&oacute;n de su incidencia</h2>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body" >
                <div class="col-md-12 row">
                    <div class="col-md-9">
                        <h4 style="color: #9d2449;" id="folio_solicitud_incidencia"></h4>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary btn-sm" id="quitar_relacion" style="margin-left: 1%; display:none;" onclick="limpiarSolicitudAsociada()">No relacionar</button>
                    </div>
                </div>
                <div class="">
                    {!! Form::select('tipo_incidencia_solicitud_id', isset($tipoIncidenciaSolicitud) ? $tipoIncidenciaSolicitud : [] , null, ['id'=>'tipo_incidencia_solicitud_id','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                    {!! $errors->first('tipo_incidencia_solicitud_id', '<span class=text-danger>:message</span>') !!}
                    <p class="help-block needed">Tipo de incidencia</p>
                </div>
                <div>
                    <textarea rows="4" class="form-control" id="justificacion_incidencia" ></textarea>
                    <p class="help-block"> Incidencia</p>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right col-md-12 row">
                    <a class="btn btn-white btn-sm" class="close" data-dismiss="modal" aria-hidden="true" ><i class="fa fa-times"></i> cancelar</a>
                    <a class="btn btn-primary btn-sm" style="margin-left: 1%;" onclick="marcarIncidencia()"  > Aceptar</a>
                    <button class="btn btn-primary btn-sm" style="margin-left: 1%;" onclick="$('#modal-solicitud').modal('show')">Relacionar a Solicitud</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Fin Modal de Preview-->

<!-- inicio Modal Preview-->
<div class="modal" id="modal-consulta-incidencia" data-backdrop="static" data-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Solicitud con incidencia: <span id="folio_consulta"></span></h2>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body" >
                <div class="col-md-12 row">
                    <div class="col-md-12">
                        <h5>Razón:</h5>
                        <label id="razon_incidencia"></label>
                    </div>
                    <div class="col-md-12">
                        <h5>Justificacion:</h5>
                        <label id="justificacion_consulta" style="border: 1px solid gray; padding:2%; max-height:250px; width:100%; overflow:scroll;"></label>
                    </div>
                </div>
                <div class="text-right">
                    <button class="btn btn-primary btn-sm m-l-5" data-dismiss="modal" aria-hidden="true"> Aceptar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Fin Modal de Preview-->

<!-- inicio Modal Preview-->
<div class="modal" id="modal-solicitud" data-backdrop="static" data-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Capture la justificaci&oacute;n de su incidencia</h2>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body" >
                <div class="col-md-12 row">
                    <div class="col-md-10 row">
                        <input class="form-control numero col-md-5 md-2" id="folio_solicitud_asociada" placeholder="Folio de solicitud" type="text" value="">
                        <input class="form-control numero col-md-5 md-2" maxlength="4" style="margin-left:2%;" id="anio_solicitud_asociada" placeholder="A&ntilde;o de solicitud" type="text" value="">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary" onclick="getSolicitudAsociada();">Buscar</button>
                    </div>
                </div>
                <div id="SolicitudAsociada" class="card col-md-8 offset-2" style="margin-top: 5%;">
                    <div class="col-md-12">
                        <div id="divSolicitudAsoc">
                        </div>
                        <div id="divPartesAsoc">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right row">
                    <a class="btn btn-white" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</a>
                </div>
                <div class="text-right row">
                    <button class="btn btn-primary btn-sm m-l-5" data-dismiss="modal" aria-hidden="true" onclick="seleccionarSolicitud();"><i class="fa fa-save"></i> Aceptar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Fin Modal de Preview-->


@endsection

@push('scripts')
    <script>
        function limpiar(){
            $("#divSolicitudMod").html("");
            $("#divSolicitantesMod").html("");
            $("#divCitadosMod").html("");
            $("#audiencia_id").val("");
            $("#solicitud_id").val("");
            $("#solicitante_id").val("");
            $("#solicitado_id").val("");
            $("#divAudienciaConsulta").html("");
            limpiarSolicitudAsociada();
        }
        
        function consultarSolicitud(){
            limpiar();
            $("#divSolicitudMod").html("");
            $("#divSolicitantesMod").html("");
            $("#divCitadosMod").html("");
            getSolicitudFromBD();
            $("#SolicitudInfo").show();
        }
        
        function nuevaIncidencia(){
            $("#lista-incidencias").hide();
            $("#nueva-incidencia").show();
        }
        function cancelarCaptura(){
            $("#lista-incidencias").show();
            limpiar();
            $("#nueva-incidencia").hide();
        }

        function getSolicitudFromBD(){
            arraySolicitados = []; //Lista de citados
            arraySolicitantes = []; //Lista de solicitantes
            arrayDomiciliosSolicitante = []; // Array de domicilios para el solicitante
            arrayDomiciliosSolicitado = []; // Array de domicilios para el citado
            arrayObjetoSolicitudes = []; // Array de objeto_solicitude para el citado
            solicitudObj = {}; // Array de objeto_solicitude para el citado
            ratifican = false;; // Array de solicitante excepción
            $.ajax({
                url:'/solicitudes/folio',
                type:"POST",
                dataType:"json",
                async:false,
                data:{
                    folio:$("#folio_solicitud").val(),
                    anio: $("#anio_solicitud").val(),
                    _token:$("input[name=_token]").val()
                },
                success:function(json){
                    try{
                        if(json.success){
                            var data = json.data;
                            $("#datosIdentificacionSolicitado").show();
                            arraySolicitados = Object.values(data.solicitados);
                            arraySolicitantes = Object.values(data.solicitantes); 
                            solicitudObj.estatus_solicitud = data.estatusSolicitud.nombre;
                            solicitudObj.fecha_ratificacion = dateFormat(data.fecha_ratificacion,2);
                            solicitudObj.fecha_recepcion = dateFormat(data.fecha_recepcion,2);
                            solicitudObj.fecha_conflicto = dateFormat(data.fecha_conflicto,4);
                            solicitudObj.folio = data.folio;
                            solicitudObj.anio = data.anio;
                            solicitudObj.centro = data.centro.nombre;
                            solicitudObj.tipoSolicitud = data.tipoSolicitud.nombre;
                            var htmlSolicitud = formatoSolicitud();
                            var htmlSolicitantes = formarSolicitantes();
                            var htmlCitados = formarCitados();
                            $("#solicitud_id").val(data.id)
                            $("#divSolicitudMod").html(htmlSolicitud);
                            $("#divSolicitantesMod").html(htmlSolicitantes);
                            $("#divCitadosMod").html(htmlCitados);
                            $("#divBtnSol").show();
                        }else{
                            swal({
                                title: 'Advertencia',
                                text: ' No se encontro la solicitud: '+$("#folio_solicitud").val()+"/"+$("#anio_solicitud").val(),
                                icon: 'warning'
                            });
                        }
                    }catch(error){
                        console.log(error);
                    }
                }
            });
        }

    function getSolicitudAsociada(){
        
        $.ajax({
            url:'/solicitudes/folio',
            type:"POST",
            dataType:"json",
            async:false,
            data:{
                folio:$("#folio_solicitud_asociada").val(),
                anio: $("#anio_solicitud_asociada").val(),
                _token:$("input[name=_token]").val()
            },
            success:function(json){
                try{
                    $("#solicitud_id_aux").val("")
                    $("#divSolicitudAsoc").html("");
                    $("#divPartesAsoc").html("");
                    if(json.success){
                        var data = json.data;
                        var htmlSolicitud = "<div><strong>Folio:</strong> "+data.folio+"/"+data.anio+" <br> <strong>Centro:</strong> "+data.centro.nombre+" <br> <strong>Estatus:</strong> "+data.estatusSolicitud.nombre+" </div>";
                        var htmlPartes = "<div>";
                        htmlPartes+="<h4>";
                        htmlPartes+="Solicitantes";
                        htmlPartes+="</h4>";
                        $.each(data.solicitantes,function(key, value){
                            if(value.tipo_persona_id == 1){
                                htmlPartes+=' - '+value.nombre + " " + value.primer_apellido+" "+(value.segundo_apellido|| "")+ "<br>";
                            }else{
                                htmlPartes+=' - '+value.nombre_comercial+ "<br>";
                            }
                        });
                        
                        htmlPartes+="<h4>";
                        htmlPartes+="Citados";
                        htmlPartes+="</h4>";
                        $.each(data.solicitados,function(key, value){
                            if(value.tipo_persona_id == 1){
                                htmlPartes+=' - '+value.nombre + " " + value.primer_apellido+" "+(value.segundo_apellido|| "")+ "<br>";
                            }else{
                                htmlPartes+=' - '+value.nombre_comercial+ "<br>";
                            }
                        });
                        htmlPartes += "</div>";
                        $("#solicitud_id_aux").val(data.id)
                        $("#divSolicitudAsoc").html(htmlSolicitud);
                        $("#divPartesAsoc").html(htmlPartes);
                        $("#quitar_relacion").show();
                    }else{
                        swal({
                            title: 'Advertencia',
                            text: ' No se encontro la solicitud: '+$("#folio_solicitud_asociado").val()+"/"+$("#anio_solicitud_asociado").val(),
                            icon: 'warning'
                        });
                    }
                }catch(error){
                    console.log(error);
                }
            }
        });
    }
        
    function formarSolicitantes(){
        var html = "";
        html += "<hr class='red'><div><div><h2>Solicitantes</h2></div>";
        var selected = false;
        if(arraySolicitantes.length == 1){
            $("#solicitante_id").val(arraySolicitantes[0].id);
            selected = true;
        }
        $.each(arraySolicitantes,function(key, value){
            html += "<div class='col-md-12' style='margin:1%;' >";
                html+='<div >';
                html+='<h4>';
                        if(value.tipo_persona_id == 1){
                            html+=' - '+value.nombre + " " + value.primer_apellido+" "+(value.segundo_apellido|| "");
                        }else{
                            html+=' - '+value.nombre_comercial;
                        }
                    html+='</h4>';
                html+='</div>';
                    html+='<div ';
                        html+='<div >';
                            html+='<div >';
                                html+='<div>';
                                    if(value.curp){
                                        html+='<div>';
                                            html+='<label ><b>CURP:</b>'+value.curp+'</label>';
                                        html+='</div>';
                                    }
                                    if(value.rfc){
                                        html+='<div>';
                                            html+='<label ><b>RFC:</b>'+value.rfc+'</label>';
                                        html+='</div>';
                                    }
                                    if(value.domicilios && value.domicilios.length > 0){
                                        html+='<div>';
                                            html+="<label ><b>Dirección:</b><br> &nbsp;&nbsp;&nbsp;&nbsp;"+value.domicilios[0].tipo_vialidad+" "+value.domicilios[0].vialidad+", "+value.domicilios[0].asentamiento+", "+value.domicilios[0].municipio+", "+value.domicilios[0].estado.toUpperCase()+'</label>';
                                        html+='</div>';
                                    }
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
    function seleccionarSolicitud(){
        var folio = $("#folio_solicitud_asociada").val();
        var anio = $("#anio_solicitud_asociada").val();
        var solicitud_id = $("#solicitud_id_aux").val();
        $("#solicitud_asociada_id").val(solicitud_id);
        $("#folio_solicitud_incidencia").html("Solicitud asociada: "+folio+"/"+anio);
        $("#folio_solicitud_asociar").html("Solicitud asociada: "+folio+"/"+anio);
    }
    function limpiarSolicitudAsociada(){
        $("#solicitud_id_aux").val("");
        $("#solicitud_asociada_id").val("");
        $("#quitar_relacion").hide();
        $("#folio_solicitud_incidencia").html("");
        $("#folio_solicitud_asociar").html("");
    }
    function formarCitados(){
        var html = "";
        html += "<hr class='red'><div><div><h2>Citados</h2></div>";
        var selected = false;
        if(arraySolicitados.length == 1){
            $("#solicitado_id").val(arraySolicitados[0].id);
            selected = true;
        }
        $.each(arraySolicitados,function(key, value){
            html += "<div class='col-md-12' style='margin:1%;' >";
            html += "<div >";
                html+='<h4>';
                    if(value.tipo_persona_id == 1){
                        html+=' - '+value.nombre + " " + value.primer_apellido+" "+(value.segundo_apellido|| "");
                    }else{
                        html+=' - '+value.nombre_comercial;
                    }
                html+='</h4>';
            html+='</div>';
                html+='<div >';
                    html+='<div>';
                        html+='<div >';
                            html+='<div>';
                                if(value.curp){
                                    html+='<div>';
                                        html+='<label ><b>CURP:</b>'+value.curp+'</label>';
                                    html+='</div>';
                                }
                                if(value.rfc){
                                    html+='<div>';
                                        html+='<label ><b>RFC:</b>'+value.rfc+'</label>';
                                    html+='</div>';
                                }
                                if(value.domicilios && value.domicilios.length > 0){
                                    html+='<div>';
                                        html+="<label ><b>Direccion:</b><br> &nbsp;&nbsp;&nbsp;&nbsp;"+value.domicilios[0].tipo_vialidad+" "+value.domicilios[0].vialidad+", "+value.domicilios[0].asentamiento+", "+value.domicilios[0].municipio+", "+value.domicilios[0].estado.toUpperCase()+"</label>";
                                    html+='</div>';
                                }
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
    function formatoSolicitud(){
        var html = "";
        html += "<hr class='red'><div><div><h2>Solicitud</h2></div>";
        html += "<div class='col-md-12'>";
            html += "<div class='col-md-12 row'>";
                html += "<div class='col-md-12'>";
                    html += "<h4><b>Tipo de solicitud:</b> "+solicitudObj.tipoSolicitud+ "<br></h4>";
                html += "</div>";
                html += "<div class='col-md-12 text-right'>";
                    html += "<label><b>Estatus de la solicitud:</b> "+solicitudObj.estatus_solicitud+ "<br></label>";
                html += "</div>";
                if(solicitudObj.ratificada == true){
                    html += "<div class='col-md-6'>";
                        html += "<label ><b>Expediente:</b> "+solicitudObj.expediente+ "</label ><br>";
                    html += "</div>";
                    html += "<div class='col-md-6'>";
                        html += "<b>Centro:</b> "+solicitudObj.centro+ "<br>";
                    html += "</div>";
                }
                if(solicitudObj.ratificada == true){
                    html += "<div class='col-md-3'>";
                        html += "<label ><b>Fecha de confirmaci&oacute;n:</b> "+solicitudObj.fecha_ratificacion+ "</label ><br>";
                    html += "</div>";
                }
                html += "<div class='col-md-4'>";
                    html += "<label ><b>Folio de la solicitud:</b> "+solicitudObj.folio + "/" + solicitudObj.anio + "</label ><br>";
                html += "</div>";
                html += "<div class='col-md-4'>";
                    html += "<label ><b>Fecha de recepci&oacute;n:</b> "+solicitudObj.fecha_recepcion+ "</label ><br>";
                html += "</div>";
                html += "<div class='col-md-4'>";
                    html += "<label ><b>Fecha de conflicto:</b> "+solicitudObj.fecha_conflicto+ "</label ><br>";
                html += "</div>";
            html += "</div>";
        html += "</div>";
        
        return html;
    }
    function formatoAudiencia(){
        var html = "";
        html += "<hr class='red'><div><h2>Audiencias</h2></div>";
        if(ArrAudiencias.length > 1){
            
        }
        var selected = false;
        if(ArrAudiencias.length == 1){
            $("#audiencia_id").val(ArrAudiencias[0].id);
            selected = true;
        }
        $.each(ArrAudiencias,function(key, audiencia){
            html += "<div class='col-md-12' style='margin:1%;' >";
                html += "<div class='col-md-12 row'>";
                    html += "<div class='col-md-12 text-right'>";
                    if(audiencia.finalizada){
                        html += "<label><b>Estatus de la audiencia:</b> Concluida <br></label>";
                    }else{
                        html += "<label><b>Estatus de la audiencia:</b> Pendiente <br></label>";
                    }
                    html += "</div>";
                    html += "<div class='col-md-3'>";
                        html += "<label ><b>Folio de la Audiencia:</b> "+audiencia.folio + "/" + audiencia.anio + "</label ><br>";
                    html += "</div>";
                    html += "<div class='col-md-3'>";
                        html += "<label ><b>Fecha de audiencia:</b> "+audiencia.fecha_audiencia+ " "+ audiencia.hora_inicio + " "+ audiencia.hora_fin + " </label ><br>";
                    html += "</div>";
                    html += "<div class='col-md-3'>";
                    var conciliador = audiencia.conciliador.persona;
                        html += "<label ><b>Conciliador:</b> "+conciliador.nombre+" "+conciliador.primer_apellido+" "+conciliador.segundo_apellido+ "</label ><br>";
                    html += "</div>";
                html += "</div>";
            html += "</div>";
        });
        
        return html;
    }
    function marcarIncidencia(){
        if($("#solicitud_id").val() != "" && $("#justificacion_incidencia").val() != "" && $("#tipo_incidencia_solicitud_id").val() != ""){
            $.ajax({
                url:'/guardar_incidencia',
                type:"POST",
                dataType:"json",
                async:false,
                data:{
                    solicitud_id:$("#solicitud_id").val(),
                    justificacion_incidencia: $("#justificacion_incidencia").val(),
                    tipo_incidencia_solicitud_id: $("#tipo_incidencia_solicitud_id").val(),
                    solicitud_asociada_id: $("#solicitud_asociada_id").val(),
                    _token:$("input[name=_token]").val()
                },
                success:function(json){
                    try{
                        swal({
                            title: 'Correcto',
                            text: ' Se agrego incidencia en la solicitud: '+$("#folio_solicitud").val()+"/"+$("#anio_solicitud").val(),
                            icon: 'success'
                        });
                        window.location.reload();
                    }catch(error){
                        console.log(error);
                    }
                },
                error:function(){
                    swal({
                        title: 'Error',
                        text: ' No se pudo agregar la incidencia de la solicitud: '+$("#folio_solicitud").val()+"/"+$("#anio_solicitud").val(),
                        icon: 'warning'
                    });
                }
            });
        }else{
            swal({
                title: 'Error',
                text: ' Es necesario seleccionar una justificación, tipo de error y la solicitud para agregar la incidencia ',
                icon: 'warning'
            });
        }
    }
    function borrarIncidencia(solicitud_id){
        swal({
            title: '¿Estas seguro que quieres borrar esta incidencia?',
            text: '',
            icon: 'warning',
            buttons: {
                cancel: {
                    text: 'Cancelar',
                    value: null,
                    visible: true,
                    className: 'btn btn-primary',
                    closeModal: true,
                },
                confirm: {
                    text: "Aceptar",
                    value: true,
                    visible: true,
                    className: 'btn btn-primary',
                    closeModal: true
                }
            }
        }).then(function(isConfirm){
            if(isConfirm){
                $.ajax({
                    url:'/borrar_incidencia',
                    type:"POST",
                    dataType:"json",
                    async:false,
                    data:{
                        solicitud_id:solicitud_id,
                        _token:$("input[name=_token]").val()
                    },
                    success:function(json){
                        try{
                            swal({
                                title: 'Correcto',
                                text: ' Se elimino incidencia',
                                icon: 'success'
                            });
                            window.location.reload();
                        }catch(error){
                            console.log(error);
                        }
                    },
                    error:function(){
                        swal({
                            title: 'Error',
                            text: ' No se pudo eliminar la incidencia de la solicitud: ',
                            icon: 'warning'
                        });
                    }
                });
            }
        });
    }
    function getIncidencia(solicitud_id){
        $.ajax({
            url:'/solicitudes/'+solicitud_id,
            type:"GET",
            dataType:"json",
            async:false,
            data:{
                solicitud_id:solicitud_id,
                _token:$("input[name=_token]").val()
            },
            success:function(json){
                try{
                   console.log(json);
                   $("#folio_consulta").html(json.folio+"/"+json.anio);
                   $("#razon_incidencia").html(json.tipoIncidenciaSolicitud.nombre);
                   $("#justificacion_consulta").html(json.justificacion_incidencia);
                   $("#modal-consulta-incidencia").modal('show');
                }catch(error){
                    console.log(error);
                }
            },
            error:function(){
                swal({
                    title: 'Error',
                    text: ' No se pudo eliminar la incidencia de la solicitud: ',
                    icon: 'warning'
                });
            }
        });
    }
    $("#data-table-default").DataTable({language: {url: "/assets/plugins/datatables.net/dataTable.es.json"}});
    </script>
@endpush
