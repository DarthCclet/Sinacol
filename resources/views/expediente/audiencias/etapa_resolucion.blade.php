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
    <li class="breadcrumb-item"><a href="javascript:;">Audiencias</a></li>
    <li class="breadcrumb-item active">Guia Audiencia</li>
</ol>
<!-- end breadcrumb -->
<!-- begin page-header -->
<h1 class="page-header">Gu&iacute;a Resoluci&oacute;n <small>pasos para cumplir la audiencia</small></h1>
<!-- end page-header -->
<input type="hidden" id="audiencia_id" name="audiencia_id" value="{{$audiencia->id}}" />
<!-- begin timeline -->
<ul class="timeline">
    @foreach($etapa_resolucion as $etapa)
        @if($etapa->id == 1)
        <li style="" id="step{{$etapa->id}}">
        @else
        <li style="display:none;" id="step{{$etapa->id}}">
        @endif
            <!-- begin timeline-time -->
            <div class="timeline-time">
                <span class="date"></span>
            <span class="time">{{$etapa->id}}.  {{$etapa->nombre}}</span>
            </div>
            <!-- end timeline-time -->
            <!-- begin timeline-icon -->
            <div class="timeline-icon">
            <a href="javascript:;" id="icon{{$etapa->id}}">&nbsp;</a>
            </div>
            <!-- end timeline-icon -->
            <!-- begin timeline-body -->
        <div class="timeline-body" style="border: 1px solid black;">
                <div class="timeline-header">
                <span class="username"><a href="javascript:;">{{$etapa->descripcion}}</a> <small></small></span>
                </div>
            <div class="timeline-content" id="contentStep{{$etapa->id}}">
                    <p>
                        @switch($etapa->id)
                            @case(1)
                                <p>Comparecientes</p>
                                <div class="col-md-offset-3 col-md-12 ">
                                    <table class="table table-striped table-bordered table-td-valign-middle">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap">Tipo Parte</th>
                                                <th class="text-nowrap">Nombre de la parte</th>
                                                <th class="text-nowrap" style="width: 10%;">Representante Legal</th>
                                                <th class="text-nowrap" style="width: 10%;">Datos Laborales</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($audiencia->partes as $parte)
                                            @if($parte->tipo_parte_id != 3)
                                                <tr>
                                                    <td class="text-nowrap">{{ $parte->tipoParte->nombre }}</td>
                                                    @if($parte->tipo_persona_id == 1)
                                                        <td class="text-nowrap">{{ $parte->nombre }} {{ $parte->primer_apellido }} {{ $parte->segundo_apellido }}</td>
                                                    @else
                                                        <td class="text-nowrap">{{ $parte->nombre_comercial }}</td>
                                                    @endif
                                                    <td>
                                                        @if(($parte->tipo_persona_id == 2) || ($parte->tipo_parte_id == 2 && $parte->tipo_persona_id == 1))
                                                        <div style="display: inline-block;">
                                                            <button onclick="AgregarRepresentante({{$parte->id}})" class="btn btn-xs btn-primary btnAgregarRepresentante" title="Agregar">
                                                                <i class="fa fa-plus"></i>
                                                            </button>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($parte->tipo_parte_id == 1)
                                                        <div style="display: inline-block;">
                                                            <button onclick="DatosLaborales({{$parte->id}})" class="btn btn-xs btn-primary btnAgregarRepresentante" title="Datos Laborales">
                                                                <i class="fa fa-briefcase"></i>
                                                            </button>
                                                        </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            
                            
                            
                                <!--<input type="text" id="evidencia{{$etapa->id}}" />-->
                                <button class="btn btn-primary" align="center" id="btnCargarComparecientes">Continuar </button>
                                @break
                            @case(2)
                                <input type="hidden" id="evidencia{{$etapa->id}}" value="true" />
                                <button class="btn btn-primary" onclick="nextStep({{$etapa->id}})">Continuar </button>
                            @break
                            @case(3)
                                <input type="text" id="evidencia{{$etapa->id}}" />
                                <button class="btn btn-primary" onclick="nextStep({{$etapa->id}})">Continuar </button>
                            @break
                            @case(4)
                                <input type="text" id="evidencia{{$etapa->id}}" />
                                <button class="btn btn-primary" onclick="nextStep({{$etapa->id}})">Continuar </button>
                            @break
                            @case(5)
                                <input type="text" id="evidencia{{$etapa->id}}" />
                                <button class="btn btn-primary" onclick="nextStep({{$etapa->id}})">Continuar </button>
                            @break
                            @case(6)
                                <div class="col-md-offset-3 col-md-6 ">
                                    <div class="form-group">
                                        <label for="resolucion_id" class="col-sm-6 control-label">Resolución</label>
                                        <div class="col-sm-10">
                                            {!! Form::select('resolucion_id', isset($resoluciones) ? $resoluciones : [] , null, ['id'=>'resolucion_id', 'required','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect']);  !!}
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-primary" onclick="nextStep({{$etapa->id}})">Finalizar </button>
                            @break
                            @default
                                
                        @endswitch
                    </p>
                </div>
                <div class="timeline-footer">
                </div>
            </div>
            <!-- end timeline-body -->
        </li>
    @endforeach
</ul>
<!-- end timeline -->

<!--inicio modal para representante legal-->
<div class="modal" id="modal-representante" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Representante legal</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h5>Datos del Representante legal</h5>
                <div class="col-md-12 row">
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <label for="curp" class="control-label">CURP</label>
                            <input type="text" id="curp" maxlength="18" onblur="validaCURP(this.value);" class="form-control" placeholder="CURP del representante legal">
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <label for="nombre" class="control-label">Nombre</label>
                            <input type="text" id="nombre" class="form-control" placeholder="Nombre del representante legal">
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <label for="primer_apellido" class="control-label">Primer apellido</label>
                            <input type="text" id="primer_apellido" class="form-control" placeholder="Primer apellido del representante">
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <label for="segundo_apellido" class="control-label">Segundo apellido</label>
                            <input type="text" id="segundo_apellido" class="form-control" placeholder="Segundo apellido representante">
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <label for="fecha_nacimiento" class="control-label">Fecha de nacimiento</label>
                            <input type="text" id="fecha_nacimiento" class="form-control fecha" placeholder="Fecha de nacimiento del representante">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="genero_id" class="col-sm-6 control-label">Genero</label>
                        <select id="genero_id" class="form-control select-element">
                            <option value="">-- Selecciona un genero</option>
                        </select>
                    </div>
                </div>
                <hr>
                <h5>Datos de comprobante como representante legal</h5>
                <div class="col-md-12 row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="instrumento" class="control-label">Instrumento</label>
                            <input type="text" id="instrumento" class="form-control" placeholder="Instrumento que acredita la representatividad">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="feha_instrumento" class="control-label">Fecha de instrumento</label>
                            <input type="text" id="feha_instrumento" class="form-control fecha" placeholder="Fecha en que se extiende el instrumento">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="numero_notaria" class="control-label">Número</label>
                            <input type="text" id="numero_notaria" class="form-control" placeholder="Número de la notaría">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre_notario" class="control-label">Nombre del Notario</label>
                            <input type="text" id="nombre_notario" class="form-control" placeholder="Nombre del notario que acredita">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="localidad_notaria" class="control-label">Localidad</label>
                            <input type="text" id="localidad_notaria" class="form-control" placeholder="Localidad de la notaría">
                        </div>
                    </div>
                </div>
                <hr>
                <h5>Datos de contacto</h5>
                <div class="col-md-12 row">
                    <div class="col-md-5">
                        <label for="tipo_contacto_id" class="col-sm-6 control-label">Tipo de contacto</label>
                        <select id="tipo_contacto_id" class="form-control select-element">
                            <option value="">-- Selecciona un genero</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="contacto" class="control-label">Contacto</label>
                            <input type="text" id="contacto" class="form-control" placeholder="Información de contacto">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary" type="button" id="btnAgregarContacto">
                            <i class="fa fa-plus-circle"></i> Agregar
                        </button>
                    </div>
                </div>
                <div class="col-md-12">
                    <table class="table table-bordered" >
                        <thead>
                            <tr>
                                <th style="width:80%;">Tipo</th>
                                <th style="width:80%;">Contacto</th>
                                <th style="width:20%; text-align: center;">Accion</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyContacto">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5" id="btnGuardarRepresentante"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Fin de modal de representante legal-->
<!--inicio modal para representante legal-->
<div class="modal" id="modal-dato-laboral" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Datos Laborales</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12 row">
                    <input type="hidden" id="dato_laboral_id">
                    <input type="hidden" id="resolucion_dato_laboral">
                    <div class="col-md-12">
                        <input class="form-control datoLaboral" id="nombre_jefe_directo" placeholder="Nombre del jefe directo" type="text" value="">
                        <p class="help-block">Nombre del Jefe directo</p>
                    </div>
                    <div class="col-md-12 form-group row">
                        <input type="hidden" id="term">
                        <div class="col-md-12 ">
                            <select name="giro_comercial_solicitante " placeholder="Seleccione" id="giro_comercial_solicitante" class="form-control datoLaboral"></select>
                        </div>
                        <div class="col-md-12">
                            <p class="help-block needed">Giro comercial</p>
                        <label id="giro_solicitante"></label>
                        </div>
                    </div>
                    {!! Form::select('giro_comercial_hidden', isset($giros_comerciales) ? $giros_comerciales : [] , null, ['id'=>'giro_comercial_hidden','placeholder' => 'Seleccione una opcion','style'=>'display:none;']);  !!}
                    <div class="col-md-12 row">
                        <div class="col-md-4">
                            {!! Form::select('ocupacion_id', isset($ocupaciones) ? $ocupaciones : [] , null, ['id'=>'ocupacion_id', 'required','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect datoLaboral']);  !!}
                            {!! $errors->first('ocupacion_id', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block needed">Categoria/Puesto</p>
                        </div>
                        <div class="col-md-4">
                            <input class="form-control numero datoLaboral" data-parsley-type='integer' id="nss" placeholder="No. IMSS"  type="text" value="">
                            <p class="help-block ">No. IMSS</p>
                        </div>
                        <div class="col-md-4">
                            <input class="form-control numero datoLaboral" data-parsley-type='integer' id="no_issste" placeholder="No. ISSSTE"  type="text" value="">
                            <p class="help-block">No. ISSSTE</p>
                        </div>
                    </div>
                    <div class="col-md-12 row">
                        <div class="col-md-4">
                            <input class="form-control numero "datoLaboral required data-parsley-type='number' id="remuneracion" max="99999999" placeholder="Remuneraci&oacute;n (pago)" type="text" value="">
                            <p class="help-block needed">Remuneraci&oacute;n (pago)</p>
                        </div>
                        <div class="col-md-4">
                            {!! Form::select('periodicidad_id', isset($periodicidades) ? $periodicidades : [] , null, ['id'=>'periodicidad_id','placeholder' => 'Seleccione una opcion','required', 'class' => 'form-control catSelect datoLaboral']);  !!}
                            {!! $errors->first('periodicidad_id', '<span class=text-danger>:message</span>') !!}
                            <p class="help-block needed">Periodicidad</p>
                        </div>
                        <div class="col-md-4">
                            <input class="form-control numero datoLaboral" required data-parsley-type='integer' id="horas_semanales" placeholder="Horas semanales" type="text" value="">
                            <p class="help-block needed">Horas semanales</p>
                        </div>
                    </div>
                    <div class="col-md-12 row">

                        <div class="col-md-2">
                            <span class="text-muted m-l-5 m-r-20" for='switch1'>Labora actualmente</span>
                        </div>
                        <div class="col-md-2">
                            <input type="hidden" />
                            <input type="checkbox" value="1" data-render="switchery" data-theme="default" id="labora_actualmente" name='labora_actualmente'/>
                        </div>
                        <div class="col-md-4">
                            <input class="form-control date datoLaboral" required id="fecha_ingreso" placeholder="Fecha de ingreso" type="text" value="">
                            <p class="help-block needed">Fecha de ingreso</p>
                        </div>
                        <div class="col-md-4" id="divFechaSalida">
                            <input class="form-control date datoLaboral" id="fecha_salida" placeholder="Fecha salida" type="text" value="">
                            <p class="help-block needed">Fecha salida</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        {!! Form::select('jornada_id', isset($jornadas) ? $jornadas : [] , null, ['id'=>'jornada_id','placeholder' => 'Seleccione una opcion','required', 'class' => 'form-control catSelect datoLaboral']);  !!}
                        {!! $errors->first('jornada_id', '<span class=text-danger>:message</span>') !!}
                        <p class="help-block needed">Jornada</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5" id="btnGuardarDatoLaboral"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Fin de modal de representante legal-->
<!-- Inicio Modal de comparecientes y resolución individual-->
<div class="modal" id="modal-comparecientes" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Comparecientes</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered" >
                        <thead>
                            <tr>
                                <th>Tipo Parte</th>
                                <th>Nombre</th>
                                <th>Primer Apellido</th>
                                <th>Segundo Apellido</th>
                                <th>Comparecio</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyPartesFisicas">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <button class="btn btn-danger btn-borrar" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary" id="btnGuardarComparecientes">Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Fin Modal de comparecientes y resolución individual-->
<input type="hidden" id="parte_id">
<input type="hidden" id="parte_representada_id">
@endsection
@push('scripts')
<script>
    var listaContactos = [];
    $(document).ready(function(){
        $(".tipo_documento,.select-element,.catSelect").select2();
        $(".fecha").datetimepicker({format:"DD/MM/YYYY"});
        cargarGeneros();
        getEtapasAudiencia();
        cargarTipoContactos();
    });
    function nextStep(pasoActual){
        var siguiente = pasoActual+1;
        $("#icon"+pasoActual).css("background","lightgreen");
        $("#contentStep"+pasoActual).hide();
        $("#step"+siguiente).show();
        guardarEvidenciaEtapa(pasoActual);
    }

    function guardarEvidenciaEtapa(etapa){
        $.ajax({
            url:'/api/etapa_resolucion_audiencia',
            type:"POST",
            dataType:"json",
            async:false,
            data:{
                etapa_resolucion_id:etapa,
                audiencia_id:$("#audiencia_id").val(),
                evidencia: $("#evidencia"+etapa).val(),
            },
            success:function(data){
                try{
                    
                }catch(error){
                    console.log(error);
                }
            }
        });
    }
    function getEtapasAudiencia(){
        $.ajax({
            url:'/api/etapa_resolucion_audiencia/audiencia/'+$("#audiencia_id").val(),
            type:"GET",
            dataType:"json",
            async:false,
            data:{
            },
            success:function(data){
                try{
                    setPasosAudiencia(data)
                }catch(error){
                    console.log(error);
                }
            }
        });
    }
    function setPasosAudiencia(etapas){
        $.each(etapas, function (key, value) {
            var pasoActual = value.etapa_resolucion_id;
            var siguiente = pasoActual+1;
            if(pasoActual == 1){
                cargarComparecientes();
            }else{
                $("#evidencia"+pasoActual).val(value.evidencia)
            }
            $("#icon"+pasoActual).css("background","lightgreen");
            // $("#contentStep"+pasoActual).hide();
            $("#step"+siguiente).show();
        });
    }
        
    /*
     * Aqui inician las funciones para administrar el paso 1
     * 
     */
    $("#btnCargarComparecientes").on("click",function(){
        $.ajax({
            url:"/audiencia/validar_partes/{{ $audiencia->id }}",
            type:"GET",
            dataType:"json",
            success:function(data){
                console.log(data.pasa);
                if(data.pasa){
                    getPersonasComparecer();
                }else{
                    swal({title: 'Error',text: 'Debes agregar el representante legal de todas las personas Morales',icon: 'error'});
                }
            }
        });
    });
    $("#btnGuardarComparecientes").on("click",function(){
        var validacion = validarResolucionComparecientes();
        if(!validacion.error){
            $.ajax({
                url:"/audiencia/comparecientes",
                type:"POST",
                dataType:"json",
                async:true,
                data:{
                    audiencia_id:'{{ $audiencia->id }}',
                    comparecientes:validacion.comparecientes,
                    _token:"{{ csrf_token() }}"
                },
                success:function(data){
                    $("#modal-comparecientes").modal("hide");
                    swal({
                        title: 'Éxito',
                        text: 'Se han registrado los comparecientes',
                        icon: 'success'
                    });
                    nextStep(1);
                },
                error:function(data){
                    swal({
                        title: 'Algo salio mal',
                        text: 'No se guardo el registro',
                        icon: 'warning'
                    });
                    
                }
            });
        }
    });
    function cargarComparecientes(){
        $.ajax({
            url:"/audiencia/comparecientes/{{ $audiencia->id }}",
            type:"GET",
            dataType:"json",
            success:function(data){
                var html="<table class='table table-bordered table-striped table-hover'>";
                html +='<tr>';
                html +='    <th>Tipo de parte</th>';
                html +='    <th>Nombre</th>';
                html +='    <th>Curp</th>';
                html +='    <th>Es representante</th>';
                html +='</tr>';
                $.each(data,function(index,element){
                    html +='<tr>';
                    html +='    <td>'+element.parte.tipoParte+'</td>';
                    html +='    <td>'+element.parte.nombre+' '+element.parte.primer_apellido+' '+element.parte.segundo_apellido+'</td>';
                    html +='    <td>'+element.parte.curp+'</td>';
                    if(element.parte.tipo_parte_id == 3 && element.parte.parte_representada_id != null){
                        if(element.parte.parteRepresentada.tipo_persona_id == 1){
                            html +='<td>Si ('+element.parte.parteRepresentadanombre+' '+element.parte.parteRepresentada.primer_apellido+' '+element.parte.parteRepresentada.segundo_apellido+')</td>';
                        }else{
                            html +='<td>Si ('+element.parte.parteRepresentada.nombre_comercial+')</td>';
                        }
                    }else{
                        html +='<td>No</td>';
                    }
                    html +='</tr>';
                });
                html +='</table>';
                $("#contentStep1").html(html);
            }
        });
    }
    function validarResolucionComparecientes(){
        var listaComparecientes = [];
        $(".checkCompareciente").each(function(index){
            if($(this).is(":checked")){
                listaComparecientes.push($(this).data("parte_id"));
            }
        });
        if(listaComparecientes.length > 0){
            return {error:false,comparecientes:listaComparecientes};
        }else{
            swal({title: 'Error',text: 'No has agregado comparecientes',icon: 'warning'});
            return {error:true,comparecientes:[]};
        }
    }
    // Funciones para representante legal(Etapa 1)
    function getPersonasComparecer(){
        $.ajax({
            url:"/audiencia/fisicas/{{ $audiencia->id }}",
            type:"GET",
            dataType:"json",
            success:function(data){
                var table = "";
                $.each(data, function(index,element){
                    table +='<tr>';
                    table +='   <td>'+element.tipo_parte.nombre+'</td>';
                    table +='   <td>'+element.nombre+'</td>';
                    table +='   <td>'+element.primer_apellido+'</td>';
                    table +='   <td>'+element.segundo_apellido+'</td>';
                    table +='   <td>';
                    table +='       <div class="col-md-2">';
                    table +='           <input type="checkbox" value="1" data-parte_id="'+element.id+'" class="checkCompareciente" name="switch1"/>';
                    table +='       </div>';
                    table +='   </td>';
                    table +='</tr>';
                });
                $("#tbodyPartesFisicas").html(table);
                $("#resolucionVarias").hide();
                $("#btnCancelarVarias").hide();
                $("#btnGuardarResolucionMuchas").hide();
                $("#btnConfigurarResoluciones").show();
                $("#btnGuardarResolucionUna").show();
                $("#modal-comparecientes").modal("show");
            }
        });
    }
    function AgregarRepresentante(parte_id){
        $.ajax({
            url:"/partes/representante/"+parte_id,
            type:"GET",
            dataType:"json",
            success:function(data){
                if(data != null && data != ""){
                    data = data[0];
                    $("#curp").val(data.curp);
                    $("#nombre").val(data.nombre);
                    $("#primer_apellido").val(data.primer_apellido);
                    $("#segundo_apellido").val(data.segundo_apellido);
                    $("#fecha_nacimiento").val(dateFormat(data.fecha_nacimiento,4));
                    $("#genero_id").val(data.genero_id).trigger("change");
                    $("#instrumento").val(data.instrumento);
                    $("#feha_instrumento").val(dateFormat(data.feha_instrumento,4));
                    $("#numero_notaria").val(data.numero_notaria);
                    $("#nombre_notario").val(data.nombre_notario);
                    $("#localidad_notaria").val(data.localidad_notaria);
                    $("#parte_id").val(data.id);
                    listaContactos = data.contactos;
                }else{
                    $("#curp").val("");
                    $("#nombre").val("");
                    $("#primer_apellido").val("");
                    $("#segundo_apellido").val("");
                    $("#fecha_nacimiento").val("");
                    $("#genero_id").val("").trigger("change");
                    $("#instrumento").val("");
                    $("#feha_instrumento").val("");
                    $("#numero_notaria").val("");
                    $("#nombre_notario").val("");
                    $("#localidad_notaria").val("");
                    $("#parte_id").val("");
                    listaContactos = [];
                }
                $("#tipo_contacto_id").val("").trigger("change");
                $("#contacto").val("");
                $("#parte_representada_id").val(parte_id);
                cargarContactos();
                $("#modal-representante").modal("show");
            }
        });
    }
    function cargarContactos(){
        var table = "";
        $.each(listaContactos, function(index,element){
            table +='<tr>';
            table +='   <td>'+element.tipo_contacto.nombre+'</td>';
            table +='   <td>'+element.contacto+'</td>';
            table +='   <td style="text-align: center;">';
            table +='       <a class="btn btn-xs btn-warning" onclick="eliminarContacto('+index+')">'
            table +='           <i class="fa fa-trash" style="color:white;"></i>';
            table +='       </a>';
            table +='   </td>';
            table +='<tr>';
        });
        $("#tbodyContacto").html(table);
    }
    function cargarGeneros(){
        $.ajax({
            url:"/generos",
            type:"GET",
            dataType:"json",
            success:function(data){
                $("#genero_id").html("<option value=''>-- Selecciona un genero</option>");
                if(data.data.length > 0){
                    $.each(data.data,function(index,element){
                        $("#genero_id").append("<option value='"+element.id+"'>"+element.nombre+"</option>");
                    });
                }
                $("#genero_id").trigger("change");
            }
        });
    }
    function cargarTipoContactos(){
        $.ajax({
            url:"/tipos_contactos",
            type:"GET",
            dataType:"json",
            success:function(data){
                if(data.data.total > 0){
                    $("#tipo_contacto_id").html("<option value=''>-- Selecciona un tipo de contacto</option>");
                    $.each(data.data.data,function(index,element){
                        $("#tipo_contacto_id").append("<option value='"+element.id+"'>"+element.nombre+"</option>");
                    });
                }else{
                    $("#tipo_contacto_id").html("<option value=''>-- Selecciona un tipo de contacto</option>");
                }
                $("#tipo_contacto_id").trigger("change");
            }
        });
    }
    $("#btnAgregarContacto").on("click",function(){
        if($("#parte_id").val() != ""){
            $.ajax({
                url:"/partes/representante/contacto",
                type:"POST",
                dataType:"json",
                data:{
                    tipo_contacto_id:$("#tipo_contacto_id").val(),
                    contacto:$("#contacto").val(),
                    parte_id:$("#parte_id").val(),
                    _token:"{{ csrf_token() }}"
                },
                success:function(data){
                    if(data != null && data != ""){
                        listaContactos = data;
                        cargarContactos();
                    }else{
                        swal({title: 'Error',text: 'Algo salio mal',icon: 'warning'});
                    }
                }
            });
        }else{
            listaContactos.push({
                tipo_contacto_id:$("#tipo_contacto_id").val(),
                contacto:$("#contacto").val(),
                id:null,
                tipo_contacto:{
                    nombre:$("#tipo_contacto_id option:selected").text()
                }
            });
        }
        cargarContactos();
    });
    $("#btnGuardarRepresentante").on("click",function(){
        if(!validarRepresentante()){
            $.ajax({
                url:"/partes/representante",
                type:"POST",
                dataType:"json",
                data:{
                    curp:$("#curp").val(),
                    nombre:$("#nombre").val(),
                    primer_apellido:$("#primer_apellido").val(),
                    segundo_apellido:$("#segundo_apellido").val(),
                    fecha_nacimiento:dateFormat($("#fecha_nacimiento").val()),
                    genero_id:$("#genero_id").val(),
                    instrumento:$("#instrumento").val(),
                    feha_instrumento:dateFormat($("#feha_instrumento").val()),
                    numero_notaria:$("#numero_notaria").val(),
                    nombre_notario:$("#nombre_notario").val(),
                    localidad_notaria:$("#localidad_notaria").val(),
                    parte_id:$("#parte_id").val(),
                    parte_representada_id:$("#parte_representada_id").val(),
                    audiencia_id:$("#audiencia_id").val(),
                    listaContactos:listaContactos,
                    _token:"{{ csrf_token() }}"
                },
                success:function(data){
                    if(data != null && data != ""){
                        swal({title: 'Exito',text: 'Se agrego el representante',icon: 'success'});
                        $("#modal-representante").modal("hide");
                    }else{
                        swal({title: 'Error',text: 'Algo salio mal',icon: 'warning'});
                    }
                }
            });
        }else{
            swal({title: 'Error',text: 'Llena todos los campos',icon: 'warning'});
        }
    });
    function validarRepresentante(){
        var error=false;
        $(".control-label").css("color","");
        if($("#curp").val() == ""){
            $("#curp").prev().css("color","red");
            error = true;
        }
        if($("#nombre").val() == ""){
            $("#nombre").prev().css("color","red");
            error = true;
        }
        if($("#primer_apellido").val() == ""){
            $("#primer_apellido").prev().css("color","red");
            error = true;
        }
        if($("#segundo_apellido").val() == ""){
            $("#segundo_apellido").prev().css("color","red");
            error = true;
        }
        if($("#fecha_nacimiento").val() == ""){
            $("#fecha_nacimiento").prev().css("color","red");
            error = true;
        }
        if($("#genero_id").val() == ""){
            $("#genero_id").prev().css("color","red");
            error = true;
        }
        if($("#instrumento").val() == ""){
            $("#instrumento").prev().css("color","red");
            error = true;
        }
        if($("#feha_instrumento").val() == ""){
            $("#feha_instrumento").prev().css("color","red");
            error = true;
        }
        if($("#numero_notaria").val() == ""){
            $("#numero_notaria").prev().css("color","red");
            error = true;
        }
        if($("#nombre_notario").val() == ""){
            $("#nombre_notario").prev().css("color","red");
            error = true;
        }
        if($("#localidad_notaria").val() == ""){
            $("#localidad_notaria").prev().css("color","red");
            error = true;
        }
        console.log(listaContactos.length);
        if(listaContactos.length == 0){
            $("#contacto").prev().css("color","red");
            $("#tipo_contacto_id").prev().css("color","red");
            error = true;
            error = true;
        }
        return error;
    }
    
    // Funciones para Datos laborales(Etapa 1)
    function DatosLaborales(parte_id){
        $("#parte_id").val(parte_id);
        $.ajax({
            url:"/partes/datoLaboral/"+parte_id,
            type:"GET",
            dataType:"json",
            success:function(data){
                if(data != null && data != ""){
                    $("#dato_laboral_id").val(data.id);
                    // $("#giro_comercial_solicitante").val(data.giro_comercial_id).trigger("change");
                    $("#giro_comercial_hidden").val(data.giro_comercial_id)
                    $("#giro_solicitante").html("<b> *"+$("#giro_comercial_hidden :selected").text() + "</b>");
                    // getGiroEditar("solicitante");
                    $("#nombre_jefe_directo").val(data.nombre_jefe_directo);
                    $("#ocupacion_id").val(data.ocupacion_id);
                    $("#nss").val(data.nss);
                    $("#no_issste").val(data.no_issste);
                    $("#remuneracion").val(data.remuneracion);
                    $("#periodicidad_id").val(data.periodicidad_id);
                    if(data.labora_actualmente != $("#labora_actualmente").is(":checked")){
                        $("#labora_actualmente").click();
                        $("#labora_actualmente").trigger("change");
                    }
                    $("#fecha_ingreso").val(dateFormat(data.fecha_ingreso,4));
                    $("#fecha_salida").val(dateFormat(data.fecha_salida,4));
                    console.log(data.jornada_id);
                    $("#jornada_id").val(data.jornada_id);
                    $("#horas_semanales").val(data.horas_semanales);                            
                    $("#resolucion_dato_laboral").val(data.resolucion);   
                    $(".catSelect").trigger('change')
                }
                $("#modal-dato-laboral").modal("show");
            }
        });
    }
    function highlightText(string){
        return string.replace($("#term").val().trim(),'<span class="highlighted">'+$("#term").val().trim()+"</span>");
    }
    $("#giro_comercial_solicitante").select2({
        ajax: {
            url: '/giros_comerciales/filtrarGirosComerciales',
            type:"POST",
            dataType:"json",
            delay: 400,
            async:false,
            data:function (params) {
                $("#term").val(params.term);
                var data = {
                    nombre: params.term,
                    _token:"{{ csrf_token() }}"
                }
                return data;
            },
            processResults:function(json){
                $.each(json.data, function (key, node) {
                    var html = '';
                    html += '<table>';
                    var ancestors = node.ancestors.reverse();
                    html += '<tr><th colspan="2"><h5>* '+highlightText(node.nombre)+'</h5><th></tr>';
                    $.each(ancestors, function (index, ancestor) {
                        if(ancestor.id != 1){
                            var tab = '&nbsp;&nbsp;&nbsp;&nbsp;'.repeat(index);
                            html += '<tr><td ><b>'+ancestor.codigo+'</b></td>'+' <td style="border-left:1px solid;">'+tab+highlightText(ancestor.nombre)+'</td></tr>';
                        }
                    });
                    var tab = '&nbsp;&nbsp;&nbsp;&nbsp;'.repeat(node.ancestors.length);
                    html += '<tr><td><b>'+node.codigo+'</b></td>'+'<td style="border-left:1px solid;"> '+ tab+highlightText(node.nombre)+'</td></tr>';
                    html += '</table>';
                    json.data[key].html = html;
                });
                return {
                    results: json.data
                };
            }
            // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
        },
        escapeMarkup: function(markup) {
            return markup;
        },
        templateResult: function(data) {
            return data.html;
        },templateSelection: function(data) {
            console.log(data);
            if(data.id != ""){
                return "<b>"+data.codigo+"</b>&nbsp;&nbsp;"+data.nombre;
            }
            return data.text;
        },
        placeholder:'Seleccione una opcion',
        minimumInputLength:4,
        allowClear: true,
        language: "es"
    });
    $("#giro_comercial_solicitante").change(function(){
        $("#giro_comercial_hidden").val($(this).val());
    });
    function validarDatosLaborales(){
        var error=false;
        $(".datoLaboral").each(function(){
            if($(this).val() == ""){
                $(this).prev().css("color","red");
                error = true;
            }
        });
        return error;
    }
    $("#btnGuardarDatoLaboral").on("click",function(){
        if(!validarDatosLaborales()){
            $.ajax({
                url:"/partes/datoLaboral",
                type:"POST",
                dataType:"json",
                data:{
                    id : $("#dato_laboral_id").val(),
                    nombre_jefe_directo : $("#nombre_jefe_directo").val(),
                    ocupacion_id : $("#ocupacion_id").val(),
                    nss : $("#nss").val(),
                    no_issste : $("#no_issste").val(),
                    remuneracion : $("#remuneracion").val(),
                    periodicidad_id : $("#periodicidad_id").val(),
                    labora_actualmente : $("#labora_actualmente").is(":checked"),
                    fecha_ingreso : dateFormat($("#fecha_ingreso").val()),
                    fecha_salida : dateFormat($("#fecha_salida").val()),
                    jornada_id : $("#jornada_id").val(),
                    horas_semanales : $("#horas_semanales").val(),
                    giro_comercial_id : $("#giro_comercial_hidden").val(),
                    parte_id:$("#parte_id").val(),
                    resolucion:$("#resolucion_dato_laboral").val(),
                    _token:"{{ csrf_token() }}"
                },
                success:function(data){
                    if(data != null && data != ""){
                        swal({title: 'Exito',text: 'Se modificaron los datos laborales correctamente',icon: 'success'});
                        $("#modal-dato-laboral").modal("hide");
                    }else{
                        swal({title: 'Error',text: 'Algo salio mal',icon: 'warning'});
                    }
                },error:function(data){
                    console.log(data);
                    var mensajes = "";
                    $.each(data.responseJSON.errors, function (key, value) {
                        console.log(key.split("."));
                        console.log(value);
                        var origen = key.split(".");

                        mensajes += "- "+value[0]+ " del "+origen[0].slice(0,-1)+" "+(parseInt(origen[1])+1)+" \n";
                    });
                    swal({
                        title: 'Error',
                        text: 'Es necesario validar los siguientes campos \n'+mensajes,
                        icon: 'error'
                    });
                }
            });
        }else{
            swal({title: 'Error',text: 'Llena todos los campos',icon: 'warning'});
        }
    });
    $("#labora_actualmente").change(function(){
        if($("#labora_actualmente").is(":checked")){
            $("#divFechaSalida").hide();
            $("#fecha_salida").removeAttr("required");
        }else{
            $("#fecha_salida").attr("required","");
            $("#divFechaSalida").show();
        }
    });
</script>
<script src="/assets/js/demo/timeline.demo.js"></script>
@endpush