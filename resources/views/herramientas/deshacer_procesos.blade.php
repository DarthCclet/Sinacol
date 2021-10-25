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
    <h1 class="page-header">Deshacer Procesos</h1>
    <!-- end page-header -->
    <!-- begin panel -->
    <div class="panel panel-default" id="nueva-incidencia" >
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Selecciona el la solicitud relacionada al proceso</h4>
            <div class="panel-heading-btn">
            </div>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            <input type="hidden" id="solicitud_id" value="" />
            <input type="hidden" id="audiencia_id" value="" />
            <input type="hidden" id="tipoRollback" value="" />
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
                            <div id="divAudienciaConsulta">
                            </div>
                            <h4 style="color: {{config('colores.btn-primary-color')}};" id="folio_solicitud_asociar"></h4>
                        </div>
                        <div>
                            <h2></h2>
                        </div>
                        <div style="display: none; margin: 2%;" class="col-md-12 row" id="divBtnSol" >
                            <h1 > Proceso a realizar: <span style="color: {{config('colores.btn-primary-color')}};" id="labeltipoRollback"></span></h1>
                            <div class="col-md-12 row">
                                <button class="btn btn-primary" style="margin-left: 1%;" id="btnEjecutar" onclick="rollback()">Ejecutar proceso</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>


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
                        <label id="justificacion_consulta" style="border: 1px solid black; padding:2%; max-height:250px; overflow:scroll;"></label>
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

@endsection

@push('scripts')
    <script>
        function limpiar(){
            $("#divSolicitudMod").html("");
            $("#divSolicitantesMod").html("");
            $("#divCitadosMod").html("");
            $("#audiencia_id").val("");
            $("#tipoRollback").val("");
            $("#labeltipoRollback").html("");
            $("#solicitud_id").val("");
            $("#solicitante_id").val("");
            $("#solicitado_id").val("");
            $("#divAudienciaConsulta").html("");
            limpiarSolicitudAsociada();
        }
        
        function consultarSolicitud(){
            limpiar();
            $("#divAudienciaConsulta").html("");
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

        function getSolicitudFromBD(){
            arraySolicitados = []; //Lista de citados
            arraySolicitantes = []; //Lista de solicitantes
            arrayDomiciliosSolicitante = []; // Array de domicilios para el solicitante
            arrayDomiciliosSolicitado = []; // Array de domicilios para el citado
            arrayObjetoSolicitudes = []; // Array de objeto_solicitude para el citado
            solicitudObj = {}; // Array de objeto_solicitude para el citado
            ArrAudiencias = {}; // Array de objeto_solicitude para el citado
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
                            solicitudObj.estatus_solicitud = data.estatus_solicitud.nombre;
                            solicitudObj.fecha_ratificacion = dateFormat(data.fecha_ratificacion,2);
                            solicitudObj.fecha_recepcion = dateFormat(data.fecha_recepcion,2);
                            solicitudObj.fecha_conflicto = dateFormat(data.fecha_conflicto,4);
                            solicitudObj.folio = data.folio;
                            solicitudObj.anio = data.anio;
                            solicitudObj.centro = data.centro.nombre;
                            solicitudObj.tipoSolicitud = data.tipo_solicitud.nombre;
                            solicitudObj.ratificada = data.ratificada;
                            solicitudObj.incidencia = data.incidencia;
                            solicitudObj.expediente = data.expediente.folio;
                            if(!solicitudObj.ratificada){
                                $("#labeltipoRollback").html("La solicitud no esta confirmada, no se puede hacer ningun proceso");
                            }
                            if(solicitudObj.incidencia && solicitudObj.ratificada){
                                $("#labeltipoRollback").html("Quitar incidencia");
                                $("#tipoRollback").val(4);
                            }
                            if(data.audiencias && data.audiencias.length > 0){
                                ArrAudiencias = data.audiencias;
                                var htmlAudiencia = formatoAudiencia();
                                $("#divAudienciaConsulta").html(htmlAudiencia);
                            }
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
        var audiencia;
        var last = ArrAudiencias.length -1;
        var html = "";
        html += "<hr class='red'><div><h2>Audiencias</h2></div>";
        audiencia = ArrAudiencias[last];
        if(audiencia.finalizada){
            $("#tipoRollback").val(1);
            $("#labeltipoRollback").html("Quitar terminaci&oacute;n");
        }else if(audiencia.iniciada){
            $("#tipoRollback").val(2);
            $("#labeltipoRollback").html("Reiniciar Audiencia");
        }else{
            if(ArrAudiencias.length == 1){
                $("#tipoRollback").val(3);
                $("#labeltipoRollback").html("Quitar Confirmaci&oacute;n");
            }else{
                swal({
                    title: 'Advertencia',
                    text: ' No se puede realizar ningun procedimiento sobre esta solicitud, por que tiene mas de una audiencia ',
                    icon: 'warning'
                });
                $("#tipoRollback").val("");
                $("#labeltipoRollback").html("La solicitud tiene mas de una audiencia, por lo tanto no puede quitarse la confirmaci&oacute;n de esta");
            }
        }
        $("#audiencia_id").val(ArrAudiencias[last].id);
        // $.each(ArrAudiencias,function(key, audiencia){
        html += "<div class='col-md-10 card' style='margin:1%;' >";
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
                if(audiencia.conciliador){
                    var conciliador = audiencia.conciliador.persona;
                    html += "<label ><b>Conciliador:</b> "+conciliador.nombre+" "+conciliador.primer_apellido+" "+conciliador.segundo_apellido+ "</label ><br>";
                }
                html += "</div>";
            html += "</div>";
            
        html += "</div>";
        // });
        
        return html;
    }
    function rollback(){
        if($("#solicitud_id").val() != "" && $("#tipoRollback").val() != "" && ($("#audiencia_id").val() != "" || $("#tipoRollback").val() == "4")){
        swal({
            title: '¿Estas seguro de hacer este proceso?',
            text: '',
            icon: '',
            buttons: {
                cancel: {
                    text: 'No',
                    value: null,
                    visible: true,
                    className: 'btn btn-primary',
                    closeModal: true,
                },
                confirm: {
                    text: "Si",
                    value: true,
                    visible: true,
                    className: 'btn btn-primary',
                    closeModal: true
                }
            }
        }).then(function(isConfirm){
            if(isConfirm){
                $.ajax({
                    url:'/rollback_proceso',
                    type:"POST",
                    dataType:"json",
                    async:false,
                    data:{
                        solicitud_id:$("#solicitud_id").val(),
                        audiencia_id: $("#audiencia_id").val(),
                        tipoRollback: $("#tipoRollback").val(),
                        _token:$("input[name=_token]").val()
                    },
                    success:function(json){
                        try{
                            swal({
                                title: 'Correcto',
                                text: ' Se realizo el proceso sobre la solicitud: '+$("#folio_solicitud").val()+"/"+$("#anio_solicitud").val(),
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
                            text: ' No se pudo realizar el proceso de la solicitud: '+$("#folio_solicitud").val()+"/"+$("#anio_solicitud").val(),
                            icon: 'warning'
                        });
                    }
                });
            }
        });
        }else{
            swal({
                title: 'Error',
                text: ' Es necesario seleccionar solicitud valida para este proceso ',
                icon: 'warning'
            });
        }
    }
    </script>
@endpush
