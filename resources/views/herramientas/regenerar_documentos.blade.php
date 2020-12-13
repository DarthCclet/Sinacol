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
                {!! Form::select('clasificacion_archivo_id', isset($clasificacion_archivos) ? $clasificacion_archivos : [] , null, ['id'=>'clasificacion_archivo_id','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect']);  !!}
                {!! $errors->first('clasificacion_archivo_id', '<span class=text-danger>:message</span>') !!}
                <p class="help-block needed">Documento a regenerar</p>
            </div>
            <div>
                <div id="divAudiencia" style="display: none;">
                    <div class="col-md-12 row">
                        <div class="col-md-4">
                            <input class="form-control upper" id="folio_audiencia" required placeholder="Folio de audienica" type="text" value="">
                            <p class="help-block needed">Folio de audiencia</p>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary">Buscar</button>
                        </div>
                    </div>
                    <div id="SolicitudInfo">

                    </div>
                </div>
                <div id="divSolicitud" style="display: none;">
                    <div class="col-md-12 row">
                        <div class="col-md-4">
                            <input class="form-control upper" id="folio_solicitud" required placeholder="Folio de solicitud" type="text" value="">
                            <p class="help-block needed">Folio de solicitud</p>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary">Buscar</button>
                        </div>
                    </div>
                    <div id="SolicitudInfo">

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $("#clasificacion_archivo_id").change(function(){
            if($(this).val() == 40){
                $("#divAudiencia").hide();
                $("#divSolicitud").show();
            }else{
                $("#divSolicitud").hide();
                $("#divAudiencia").show();
            }
        });
        getSolicitudFromBD();
        function getSolicitudFromBD(solicitud){
            arraySolicitados = []; //Lista de citados
            arraySolicitantes = []; //Lista de solicitantes
            arrayDomiciliosSolicitante = []; // Array de domicilios para el solicitante
            arrayDomiciliosSolicitado = []; // Array de domicilios para el citado
            arrayObjetoSolicitudes = []; // Array de objeto_solicitude para el citado
            solicitudObj = {}; // Array de objeto_solicitude para el citado
            ratifican = false;; // Array de solicitante excepción
            $.ajax({
                url:'/solicitudes/'+solicitud,
                type:"GET",
                dataType:"json",
                async:false,
                data:{},
                success:function(data){
                    try{
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
                        
                        
                    }catch(error){
                        console.log(error);
                    }
                }
            });
        }
        function formarSolicitantes(){
        var html = "";
        html += '<div class="accordion col-md-10 offset-1" id="accordionExample">';
        $.each(arraySolicitantes,function(key, value){
            html+='<div class="card">';
                html+='<div class="card-header" id="headingOne">';
                    html+='<h2 class="mb-0">';
                        html+='<button id="collSol'+value.id+'" class="btn btn-link card-header " i type="button" data-toggle="collapse" data-target="#collapseSol'+value.id+'" aria-expanded="true" aria-controls="collapseOne"  ><i style="font-size: large;" class="fa fa-angle-down"></i> ';
                        if(value.tipo_persona_id == 1){
                            html+=' '+value.nombre + " " + value.primer_apellido+" "+(value.segundo_apellido|| "");
                        }else{
                            html+=' '+value.nombre_comercial;
                        }
                        html+=' </button>';
                    html+='</h2>';
                html+='</div>';

                html+='<div id="collapseSol'+value.id+'" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">';
                    html+='<div class="card-body">';
                        html+='<div >';
                            html+='<div>';
                                if(value.curp){
                                    html+='<div>';
                                        html+='<label><b>CURP:</b>'+value.curp+'</label>';
                                    html+='</div>';
                                }
                                
                                if(value.rfc){
                                    html+='<div>';
                                        html+='<label><b>RFC:</b>'+value.rfc+'</label>';
                                    html+='</div>';
                                }
                                html+='<div>';
                                    html+="<b>Dirección:</b><br> &nbsp;&nbsp;&nbsp;&nbsp;"+value.domicilios[0].tipo_vialidad+" "+value.domicilios[0].vialidad+", "+value.domicilios[0].asentamiento+", "+value.domicilios[0].municipio+", "+value.domicilios[0].estado.toUpperCase();
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
    function formarCitados(){
        var html = "";
        html += '<div class="accordion col-md-10 offset-1" id="accordionCitados">';
        $.each(arraySolicitados,function(key, value){
            html+='<div class="card">';
                html+='<div class="card-header" id="headingTwo">';
                    html+='<h2 class="mb-0">';
                        html+='<button id="collCit'+value.id+'" class="btn btn-link card-header " i type="button" data-toggle="collapse" data-target="#collapseCit'+value.id+'" aria-expanded="true" aria-controls="collapseOne"  ><i style="font-size: large;" class="fa fa-angle-down"></i> ';
                        if(value.tipo_persona_id == 1){
                            html+=' '+value.nombre + " " + value.primer_apellido+" "+(value.segundo_apellido|| "");
                        }else{
                            html+=' '+value.nombre_comercial;
                        }
                        html+=' </button>';
                    html+='</h2>';
                html+='</div>';

                html+='<div id="collapseCit'+value.id+'" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionCitados">';
                    html+='<div class="card-body">';
                        html+='<div >';
                            html+='<div>';
                                if(value.curp){
                                    html+='<div>';
                                        html+='<label><b>CURP:</b>'+value.curp+'</label>';
                                    html+='</div>';
                                }
                                if(value.rfc){
                                    html+='<div>';
                                        html+='<label><b>RFC:</b>'+value.rfc+'</label>';
                                    html+='</div>';
                                }
                                html+='<div>';
                                    html+="<b>Direccion:</b><br> &nbsp;&nbsp;&nbsp;&nbsp;"+value.domicilios[0].tipo_vialidad+" "+value.domicilios[0].vialidad+", "+value.domicilios[0].asentamiento+", "+value.domicilios[0].municipio+", "+value.domicilios[0].estado.toUpperCase();
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
    </script>
@endpush
