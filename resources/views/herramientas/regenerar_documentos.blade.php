@extends('layouts.default', ['paceTop' => true])

@section('title', 'expedientes')

@include('includes.component.datatables')
@include('includes.component.pickers')

@section('content')

    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:;">Audiencias</a></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Regenerar Documentos</h1>
    <!-- end page-header -->
    <!-- begin panel -->
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Selecciona el documento que quieres regenerar</h4>
            <div class="panel-heading-btn">
            </div>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            <div class="col-md-6">
                {!! Form::select('plantilla_id', isset($plantillas) ? $plantillas : [] , null, ['id'=>'plantilla_id','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                {!! $errors->first('plantilla_id', '<span class=text-danger>:message</span>') !!}
                <p class="help-block needed">Documento a regenerar</p>
            </div>
            <input type="hidden" id="audiencia_id" value="" />
            <input type="hidden" id="solicitud_id" value="" />
            <input type="hidden" id="plantilla_id" value="" />
            <input type="hidden" id="solicitante_id" value="" />
            <input type="hidden" id="solicitado_id" value="" />
            <div>
                <div id="divAudiencia" style="display: none; style='margin:5%;'">
                    <div class="col-md-12 row">
                        <div>
                            <div class="col-md-12 row">
                                <input class="form-control numero col-md-5 md-2" id="folio_audiencia" placeholder="Folio de solicitud" type="text" value="">
                                <input class="form-control numero col-md-5 md-2" style="margin-left:2%;" maxlength="4" id="anio_audiencia" placeholder="A&ntilde;o de audiencia" type="text" value="">
                            </div>
                            <p class="help-block needed">Folio de solicitud</p>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary" onclick="consultarAudiencia()">Buscar</button>
                        </div>
                    </div>
                    <div id="AudienciaInfo" class="card col-md-8 offset-2" style="margin-top: 5%;">
                        <div class="col-md-12" style="margin:5%;">
                            <div id="divSolicitudAudiencia">
                            </div>
                            <div id="divSolicitantesAudiencia">
                            </div>
                            <div id="divCitadosAudiencia">
                            </div>
                            <div id="divAudienciaConsulta">
                            </div>
                        </div>
                        <div style="display: none" class="col-md-4" id="divBtnAud">
                            <button class="btn btn-primary" onclick="previewDocumento()">Pre-visualizar</button>
                        </div>
                    </div>
                </div>
                <div id="divSolicitud" style="display: none;">
                    <div class="col-md-12 row">
                        <div>
                            <div class="col-md-12 row">
                                <input class="form-control numero col-md-5 md-2" id="folio_solicitud" placeholder="Folio de solicitud" type="text" value="">
                                <input class="form-control numero col-md-5 md-2" maxlength="4" style="margin-left:2%;" id="anio_solicitud" placeholder="A&ntilde;o de solicitud" type="text" value="">
                            </div>
                            <label class="help-block neweded">Folio de solicitud</label>
                        </div>
                        <div class="col-md-2">
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
                        </div>
                        <div style="display: none" class="col-md-4" id="divBtnSol">
                            <button class="btn btn-primary" onclick="previewDocumento()">Pre-visualizar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- inicio Modal Preview-->
<div class="modal" id="modal-preview" data-backdrop="static" data-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-body" >
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h5>Valide los datos del documento.</h5>
                <div id="documentoPreviewHtml" style="margin:0 5% 0 5%; max-height:600px; border:1px solid black; overflow: scroll; padding:2%;">

                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right row">
                    <a class="btn btn-white btn-sm" class="close" data-dismiss="modal" aria-hidden="true" ><i class="fa fa-times"></i> cancelar</a>
                    <div>
                        <label for="centro[sedes_multiples]" class="control-label">Validar</label>
                        <input type="checkbox" value="true" data-render="switchery" data-theme="default" id="validar" name='validar'/>
                    </div>
                    <a class="btn btn-primary btn-sm" onclick="generarDocumento()"  > Aceptar</a>
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
            $("#divSolicitudAudiencia").html("");
            $("#divSolicitantesAudiencia").html("");
            $("#divCitadosAudiencia").html("");
        }
        $("#plantilla_id").change(function(){
            if($(this).val() == 6){
                $("#divAudiencia").hide();
                $("#divSolicitud").show();
            }else{
                $("#divSolicitud").hide();
                $("#divAudiencia").show();
            }
        });
        function consultarSolicitud(){
            limpiar();
            $("#divSolicitudMod").html("");
            $("#divSolicitantesMod").html("");
            $("#divCitadosMod").html("");
            getSolicitudFromBD();
            $("#SolicitudInfo").show();
        }
        function consultarAudiencia(){
            limpiar();
            $("#divAudienciaConsulta").html("");
            $("#divSolicitudAudiencia").html("");
            $("#divSolicitantesAudiencia").html("");
            $("#divCitadosAudiencia").html("");
            getAudienciafromBD();
            $("#AudienciaInfo").show();
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
                    _token:$("input[name=_token]").val(),
                    validate: true,
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
                                text: json.message+': '+$("#folio_solicitud").val()+"/"+$("#anio_solicitud").val(),
                                icon: 'warning'
                            });
                        }
                    }catch(error){
                        console.log(error);
                    }
                }
            });
        }

        function getAudienciafromBD(){
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
                    folio:$("#folio_audiencia").val(),
                    anio: $("#anio_audiencia").val(),
                    _token:$("input[name=_token]").val(),
                    validate: true,
                },
                success:function(json){
                    try{
                        if(json.success){
                            var data = json.data;
                            console.log(data);
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
                            $("#solicitud_id").val(data.id);
                            var htmlSolicitud = formatoSolicitud();
                            var htmlSolicitantes = formarSolicitantes();
                            var htmlCitados = formarCitados();
                            if(data.audiencias){
                                ArrAudiencias = data.audiencias;
                                var htmlAudiencia = formatoAudiencia();
                                $("#divAudienciaConsulta").html(htmlAudiencia);
                            }
                            $("#divSolicitudAudiencia").html(htmlSolicitud);
                            $("#divSolicitantesAudiencia").html(htmlSolicitantes);
                            $("#divCitadosAudiencia").html(htmlCitados);
                            $("#divBtnAud").show();
                        }else{
                            swal({
                                title: 'Advertencia',
                                text: json.message+': '+$("#folio_audiencia").val()+"/"+$("#anio_audiencia").val(),
                                icon: 'warning'
                            });
                        }
                    }catch(error){
                        console.log(error);
                        swal({
                                title: 'Advertencia',
                                text: ' Los datos de la audiencia no son correctos favor de revisar ',
                                icon: 'warning'
                            });
                    }
                }
            });
        }
        
    function formarSolicitantes(){
        var html = "";
        html += "<hr class='red'><div><div><h2>Solicitantes</h2></div>";
        var selected = false;
        // if(arraySolicitantes.length == 1){
        //     $("#solicitante_id").val(arraySolicitantes[0].id);
        //     selected = true;
        // }
        $.each(arraySolicitantes,function(key, value){
            html += "<div class='col-md-10 card' style='margin:1%;' >";
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
                    if(!selected){
                        html += "<div class='col-md-12 text-right'>";
                            html += "<input type='radio' name='solicitante_id' id='solicitante"+value.id+"' onclick='seleccionarSolicitante("+value.id+")' />";
                            html += " <label for='solicitante"+value.id+"'>Seleccionar Solicitante<label />";
                        html+='</div>';
                    }else{
                        html += "<div class='col-md-12 text-right'>";
                            html += "<input type='radio' name='solicitante_id' checked='true' id='solicitante"+value.id+"' onclick='seleccionarSolicitante("+value.id+")' />";
                            html += " <label for='solicitante"+value.id+"'>Seleccionar Solicitante<label />";
                        html+='</div>';
                    }
                    html+='</div>';
                html+='</div>';
            html+='</div>';
        });
        html += '</div>';
        return html;
    }
    function formarCitados(){
        var html = "";
        html += "<hr class='red'><div><div><h2>Citados</h2></div>";
        var selected = false;
        // if(arraySolicitados.length == 1){
        //     $("#solicitado_id").val(arraySolicitados[0].id);
        //     selected = true;
        // }
        $.each(arraySolicitados,function(key, value){
            html += "<div class='col-md-10 card' style='margin:1%;' >";
            html += "<div >";
                html+='<h4>';
                    if(value.tipo_persona_id == 1){
                        html+=' '+value.nombre + " " + value.primer_apellido+" "+(value.segundo_apellido|| "");
                    }else{
                        html+=' '+value.nombre_comercial;
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
                if(!selected){
                    html += "<div class='col-md-12 text-right'>";
                        html += "<input type='radio' name='solicitado_id' id='citado"+value.id+"' onclick='seleccionarCitado("+value.id+")' />";
                        html += " <label for='citado"+value.id+"'>Seleccionar Citado<label />";
                    html+='</div>';
                }else{
                    html += "<div class='col-md-12 text-right'>";
                        html += "<input type='radio' name='solicitado_id' checked='true' id='citado"+value.id+"' onclick='seleccionarCitado("+value.id+")' />";
                        html += " <label for='citado"+value.id+"'>Seleccionar Citado<label />";
                    html+='</div>';
                }
            html+='</div>';
            html+='</div>';
        });
        html += '</div>';
        return html;
    }
    function formatoSolicitud(){
        var html = "";
        html += "<hr class='red'><div><div><h2>Solicitud</h2></div>";
        html += "<div class='col-md-10'>";
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
                html += "<div class='col-md-3'>";
                    html += "<label ><b>Folio de la solicitud:</b> "+solicitudObj.folio + "/" + solicitudObj.anio + "</label ><br>";
                html += "</div>";
                html += "<div class='col-md-3'>";
                    html += "<label ><b>Fecha de recepci&oacute;n:</b> "+solicitudObj.fecha_recepcion+ "</label ><br>";
                html += "</div>";
                html += "<div class='col-md-3'>";
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
                    var conciliador = audiencia.conciliador.persona;
                        html += "<label ><b>Conciliador:</b> "+conciliador.nombre+" "+conciliador.primer_apellido+" "+conciliador.segundo_apellido+ "</label ><br>";
                    html += "</div>";
                html += "</div>";
                if(!selected){
                    html += "<div class='col-md-12 text-right'>";
                        html += "<input type='radio' class='custom-control custom-radio' name='audiencia_id' id='audiencia"+audiencia.id+"' onclick='seleccionarAudiencia("+audiencia.id+")' />";
                        html += " <label for='audiencia"+audiencia.id+"'>Seleccionar Audiencia<label />";
                    html += "</div>";
                }else{
                    html += "<div class='col-md-12 text-right'>";
                        html += "<input type='radio' checked='true' name='audiencia_id' id='audiencia"+audiencia.id+"' onclick='seleccionarAudiencia("+audiencia.id+")' />";
                        html += " <label for='audiencia"+audiencia.id+"'>Seleccionar Audiencia<label />";
                    html += "</div>";
                }
            html += "</div>";
        });
        
        return html;
    }
    function validaCampos(){
        var plantilla = $("#plantilla_id").val();
        var response = {};
        response.success = true;
        response.msj = "Ok";
        switch(plantilla){
            case "6":
                if($("#solicitud_id").val() == "" || $("#solicitud_id").val() == undefined ){
                    response.success = false;
                    response.msj = "No se ha seleccionado la solicitud";
                }
            break;
            case "7":
                if($("#solicitud_id").val() == "" || $("#audiencia_id").val() == "" || $("#solicitado_id").val() == ""  ){
                    response.success = false;
                    response.msj = " Es necesario seleccionar Solicitud, Audiencia y un citado para continuar";
                }
            break;
            case "1":
                if($("#solicitud_id").val() == "" || $("#audiencia_id").val() == "" || $("#solicitado_id").val() == "" || $("#solicitante_id").val() == "" ){
                    response.success = false;
                    response.msj = " Es necesario seleccionar Solicitud, Audiencia, un solicitante y un citado para continuar";
                }
            break;
            case "2":
                if($("#solicitud_id").val() == "" || $("#audiencia_id").val() == "" || $("#solicitante_id").val() == "" ){
                    response.success = false;
                    response.msj = " Es necesario seleccionar Solicitud, Audiencia y un solicitante continuar";
                }else{
                    $("#solicitado_id").val('');
                }
            break;
            case "3":
                if($("#solicitud_id").val() == "" || $("#audiencia_id").val() == "" ){
                    response.success = false;
                    response.msj = " Es necesario seleccionar Solicitud y Audiencia para continuar";
                }
            break;
            case "4":
                if($("#solicitud_id").val() == "" || $("#audiencia_id").val() == "" || $("#solicitado_id").val() == ""  ){
                    response.success = false;
                    response.msj = " Es necesario seleccionar Solicitud, Audiencia y un citado para continuar";
                }
            break;
            case "10":
                if($("#solicitud_id").val() == ""  || $("#solicitante_id").val() == ""  ){
                    response.success = false;
                    response.msj = " Es necesario seleccionar Solicitud, Audiencia y un solicitante para continuar";
                }else{
                    
                }
            break;
            case "11":
                if($("#solicitud_id").val() == "" || $("#audiencia_id").val() == ""  ){
                    response.success = false;
                    response.msj = " Es necesario seleccionar Solicitud, Audiencia";
                }
            break;
            case "8":
                if($("#solicitud_id").val() == "" || $("#audiencia_id").val() == "" || $("#solicitado_id").val() == "" || $("#solicitante_id").val() == "" ){
                    response.success = false;
                    response.msj = " Es necesario seleccionar Solicitud, Audiencia, un solicitante y un citado para continuar";
                }
            break;
            case "9":
                if($("#solicitud_id").val() == "" || $("#audiencia_id").val() == "" || $("#solicitante_id").val() == "" ){
                    response.success = false;
                    response.msj = " Es necesario seleccionar Solicitud, Audiencia y un solicitante para continuar";
                }
            break;
            case "12":
                if($("#solicitud_id").val() == "" || $("#audiencia_id").val() == "" || $("#solicitante_id").val() == "" ){
                    response.success = false;
                    response.msj = " Es necesario seleccionar Solicitud, Audiencia y un solicitante para continuar";
                }
            break;
            case "13":
                if($("#solicitud_id").val() == "" || $("#audiencia_id").val() == "" || $("#solicitante_id").val() == "" ){
                    response.success = false;
                    response.msj = " Es necesario seleccionar Solicitud, Audiencia y un solicitante para continuar";
                }
            break;
            case "14":
                if($("#solicitud_id").val() == "" || $("#audiencia_id").val() == "" || $("#solicitado_id").val() == "" || $("#solicitante_id").val() == "" ){
                    response.success = false;
                    response.msj = " Es necesario seleccionar Solicitud, Audiencia, un solicitante y un citado para continuar";
                }
            break;
            case "15":
                if($("#solicitud_id").val() == "" || $("#audiencia_id").val() == "" || $("#solicitado_id").val() == "" || $("#solicitante_id").val() == "" ){
                    response.success = false;
                    response.msj = " Es necesario seleccionar Solicitud, Audiencia, un solicitante y un citado para continuar";
                }
            break;
            case "18":
                if($("#solicitud_id").val() == "" || $("#audiencia_id").val() == "" || $("#solicitado_id").val() == ""  ){
                    response.success = false;
                    response.msj = " Es necesario seleccionar Solicitud, Audiencia y un citado para continuar";
                }
            break;
            case "29":
                if($("#solicitud_id").val() == "" || $("#audiencia_id").val() == "" || $("#solicitante_id").val() == "" ){
                    response.success = false;
                    response.msj = " Es necesario seleccionar Solicitud, Audiencia, un solicitante para continuar";
                }
            break;
            case "24":
                if($("#solicitud_id").val() == "" || $("#audiencia_id").val() == "" ){
                    response.success = false;
                    response.msj = " Es necesario seleccionar Solicitud y Audiencia para continuar";
                }
            break;
            case "19":
                if($("#solicitud_id").val() == "" || $("#audiencia_id").val() == "" ){
                    response.success = false;
                    response.msj = " Es necesario seleccionar Solicitud y Audiencia para continuar";
                }
            break;
            case "31":
                if($("#solicitud_id").val() == "" || $("#audiencia_id").val() == "" || $("#solicitado_id").val() == "" ){
                    response.success = false;
                    response.msj = " Es necesario seleccionar Solicitud, Audiencia y un citado para continuar";
                }
            break;
            case "30":
                if($("#solicitud_id").val() == "" || $("#audiencia_id").val() == "" || $("#solicitante_id").val() == "" ){
                    response.success = false;
                    response.msj = " Es necesario seleccionar Solicitud, Audiencia y un solicitante para continuar";
                }
            break;
            default:
                response.success = false;
                response.msj = " No se seleccionó el tipo de documento a generar";
            break;
        }
        return response;
    }

    function previewDocumento(){
        var valido = validaCampos();
        if(valido.success){
            $.ajax({
                url:'/preview',
                type:"POST",
                dataType:"json",
                async:false,
                data:{
                    audiencia_id:$("#audiencia_id").val(),
                    solicitud_id: $("#solicitud_id").val(),
                    plantilla_id: $("#plantilla_id").val(),
                    solicitante_id: $("#solicitante_id").val(),
                    solicitado_id: $("#solicitado_id").val(),
                    _token:$("input[name=_token]").val()
                },
                success:function(data){
                    try{
                        $("#documentoPreviewHtml").html(data.data);
                        $("#modal-preview").modal("show");
                    }catch(error){
                        console.log(error);
                    }
                }
            });
        }else{
            swal({
                title: 'Advertencia',
                text: valido.msj,
                icon: 'warning'
            });
        }
    }
    function seleccionarAudiencia(audiencia_id){
        $('#audiencia_id').val(audiencia_id);
    }
    function seleccionarCitado(solicitado_id){
        $('#solicitado_id').val(solicitado_id);
    }
    function seleccionarSolicitante(solicitante_id){
        $('#solicitante_id').val(solicitante_id);
    }
    function generarDocumento(){
        if($("#validar").is(":checked")){
            $.ajax({
                url:'/store_regenerar_documento',
                type:"POST",
                dataType:"json",
                async:false,
                data:{
                    audiencia_id:$("#audiencia_id").val(),
                    solicitud_id: $("#solicitud_id").val(),
                    plantilla_id: $("#plantilla_id").val(),
                    solicitante_id: $("#solicitante_id").val(),
                    solicitado_id: $("#solicitado_id").val(),
                    _token:$("input[name=_token]").val()
                },
                success:function(data){
                    try{
                        if(data.success){
                            $("#documentoPreviewHtml").html(data.data);
                            swal({
                                title: 'Listo',
                                text: ' Documento generado correctamente ',
                                icon: 'success'
                            });
                            limpiar();
                            $("#AudienciaInfo").hide();
                            $("#folio_audiencia").val("");
                            $("#folio_solicitud").val("");
                            $("#anio_audiencia").val("");
                            $("#anio_solicitud").val("");
                            $("#modal-preview").modal("hide");
                        }else{
                            swal({
                                title: 'Advertencia',
                                text: ' No se pudo generar el documento ',
                                icon: 'warning'
                            });
                        }
                    }catch(error){
                        console.log(error);
                    }
                }
            });
        }else{
            swal({
                title: 'Advertencia',
                text: ' Es necesario validar la plantilla para generar el documento ',
                icon: 'warning'
            });
        }
    }
    </script>
@endpush
