@extends('layouts.defaultBuzon')
@include('includes.component.datatables')
@section('content')

<div class="col-xl-12">
    <!-- begin #accordion -->
    <div id="accordion" class="accordion">
        <!-- begin card -->
        @foreach($solicitudes as $solicitud)
        @if($solicitud->expediente != null)
        <div class="card">
            <div class="card-header  pointer-cursor d-flex align-items-center" data-toggle="collapse" data-target="#collapse{{$solicitud->id}}">
                <div style="width: 100%">
                    <i class="fa fa-circle fa-fw text-gold mr-2 f-s-8"></i> <strong>Expediente:</strong> {{$solicitud->expediente->folio}}/{{$solicitud->expediente->anio}}
                </div>
            </div>
            <div id="collapse{{$solicitud->id}}" class="collapse" data-parent="#accordion">
                <div class="card-body">
                    <ul>
                        @if($solicitud->expediente->audiencias != null)
                        @foreach($solicitud->expediente->audiencias as $audiencia)
                        <li>Audiencia: {{$audiencia->folio}} {{$audiencia->anio}}<br>
                            <table class="table table-striped table-bordered table-td-valign-middle">
                                <tr>
                                    <td class="text-nowrap">Fecha de audiencia: {{$audiencia->fecha_audiencia}}</td>
                                    <td class="text-nowrap">Hora de inicio: {{$audiencia->hora_inicio}}</td>
                                    <td class="text-nowrap">Hora de termino: {{$audiencia->hora_fin}}</td>
                                </tr>
                                <tr>
                                    <td class="text-nowrap">Sala: {{$audiencia->fecha_audiencia}}</td>
                                    <td class="text-nowrap">Conciliador: {{$audiencia->fecha_audiencia}}</td>
                                    @if($audiencia->resolucion_id != null)
                                    <td class="text-nowrap">Resolución: {{$audiencia->resolucion->nombre}}</td>
                                    @else
                                    <td class="text-nowrap">Resolución: Audiencia no celebrada</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td class="text-nowrap" colspan="3">
                                        Movimientos:
                                        <ul>
                                            @foreach($audiencia->etapasResolucionAudiencia as $etapas)
                                            <li>
                                                <ul>
                                                    <li>
                                                        {{$etapas->etapaResolucion->nombre}} (Fecha: {{\Carbon\Carbon::parse($etapas->created_at)->diffForHumans()}})
                                                        <ul>
                                                            <li>
                                                                <a href="http://conciliacion.test/">Documento</a>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </li>
                        @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        @endif
        @endforeach
    </div>
</div>
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
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="modal-documentos" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Documentos derivados de la audiencia</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered table-td-valign-middle" id="table_documentos">
                    <thead>
                        <tr>
                            <th class="text-nowrap">Nombre</th>
                            <th class="text-nowrap">Tipo de documento</th>
                            <th class="text-nowrap">Fecha de creación</th>
                            <th class="text-nowrap">Ver</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
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
                        $("#modal-dato-laboral").modal("show");
                    }else{
                        swal({
                            title: 'Aviso',
                            text: 'No hay datos laborales registrados',
                            icon: 'info'
                        });
                    }
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
                        var table = "";
                        $.each(data.contactos, function(index,element){
                            table +='<tr>';
                            table +='   <td>'+element.tipo_contacto.nombre+'</td>';
                            table +='   <td>'+element.contacto+'</td>';
                            table +='<tr>';
                        });
                        $("#tbodyContacto").html(table);
                        $("#modal-representante").modal("show");
                    }else{
                        swal({
                            title: 'Aviso',
                            text: 'No hay representante legal',
                            icon: 'info'
                        });
                    }
                }
            });
        }
        function cargarDocumentos(audiencia_id){
            $.ajax({
                url:"/audiencia/documentos/"+audiencia_id,
                type:"GET",
                dataType:"json",
                async:true,
                success:function(data){
                    if(data != null && data != ""){
                        var table = "";
                        var div = "";
                        $.each(data, function(index,element){
                            table +='<tr>';
                            table +='   <td>'+element.nombre_original+'</td>';
                            table +='   <td>'+element.clasificacionArchivo.nombre+'</td>';
                            table +='   <td>'+element.created_at+'</td>';
                            table +='   <td>';
                            table +='       <button onclick="" class="btn btn-xs btn-primary btnAgregarRepresentante" title="Ver documento">';
                            table +='        <i class="fa fa-file"></i>';
                            table +='    </button>';
                            table +='   </td>';
                            table +='</tr>';
                        });
                        $("#table_documentos tbody").html(table);
                        $("#modal-documentos").modal("show");
                    }else{
                        swal({
                            title: 'Aviso',
                            text: 'No hay datos generados para la audiencia',
                            icon: 'info'
                        });
                    }
                }
            });
        }
    </script>
@endpush

