@extends('layouts.defaultBuzon')
@include('includes.component.datatables')
@include('includes.component.pickers')
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
                        <li>Ratificacion:
                            <table class="table table-striped table-bordered table-td-valign-middle">
                                <tr>
                                    <td class="text-nowrap"><strong>Fecha de Solicitud:</strong> {{\Carbon\Carbon::parse($solicitud->fecha_solicitud)->format('d/m/Y')}}</td>
                                    <td class="text-nowrap"><strong>Fecha de Conflicto:</strong> {{\Carbon\Carbon::parse($solicitud->fecha_conflicto)->format('d/m/Y')}}</td>
                                    <td class="text-nowrap"><strong>Objeto de la solicitud:</strong> {{$solicitud->objeto_solicitudes[0]->nombre}}</td>
                                    <td class="text-nowrap"><strong>Fecha de ratificación:</strong> {{\Carbon\Carbon::parse($solicitud->fecha_ratificacion)->format('d/m/Y')}}</td>
                                    <td class="text-nowrap"><strong>Centro:</strong> {{$solicitud->centro->nombre}}</td>
                                </tr>
                                <tr>
                                    <td class="text-nowrap" colspan="5" align="center"><strong>Partes citadas</strong></td>
                                </tr>
                                @foreach($solicitud->partes as $parte)
                                @if($parte->tipo_parte_id == 2)
                                <tr>
                                    <td class="text-nowrap" colspan="3">
                                        <br>
                                                @if($parte->tipo_persona_id == 1)
                                                - {{$parte->nombre}} {{$parte->primer_apellido}} {{$parte->segundo_apellido}}
                                                @else
                                                - {{$parte->nombre_comercial}}
                                                @endif
                                    </td>
                                    <td class="text-nowrap" colspan="2">
                                        <button class="btn btn-primary" onclick="cambiarDomicilio({{$parte->id}})">Verificar domicilio</button>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                                <tr>
                                    <td class="text-nowrap" colspan="5">
                                        Documentos:<br>
                                        <ul>
                                        @if($solicitud->expediente->audiencia != null)
                                        @foreach($solicitud->expediente->audiencia as $key => $audiencia)
                                            @if($key == 0)
                                                @foreach($audiencia->documentos as $documento)
                                                @if($documento->clasificacion_archivo_id == 14 || $documento->clasificacion_archivo_id == 18 || $documento->clasificacion_archivo_id == 19)
                                                    <li><a href="/api/documentos/getFile/{{$documento->uuid}}" target="_blank">{{$documento->clasificacionArchivo->nombre}}</a></li>
                                                @endif
                                                @endforeach
                                            @endif
                                        @endforeach
                                        @endif
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        @if($solicitud->expediente->audiencia != null)
                        @foreach($solicitud->expediente->audiencia as $key => $audiencia)
                        <li>Audiencia: {{$audiencia->folio}}/{{$audiencia->anio}}
                            @if($key == 0)
                                @if($solicitud->expediente != null)
                                    @if(count($solicitud->expediente->audiencia) > 0)
                                        @if(!$solicitud->expediente->audiencia[0]->solicitud_cancelacion && !$solicitud->expediente->audiencia[0]->finalizada)
                                        <button class="btn btn-primary btn-small pull-right" onclick="reprogramarAudiencia({{$solicitud->expediente->audiencia[0]->id}})">Reprogramar audiencia </button>
                                        @endif
                                    @endif
                                @endif
                            @endif
                            <br>
                            <table class="table table-striped table-bordered table-td-valign-middle">
                                <tr>
                                    <td class="text-nowrap">
                                        Fecha de audiencia: {{\Carbon\Carbon::parse($audiencia->fecha_audiencia)->format('d/m/Y')}}
                                    </td>
                                    <td class="text-nowrap">Hora de inicio: {{$audiencia->hora_inicio}}</td>
                                    <td class="text-nowrap">Hora de t&eacute;rmino: {{$audiencia->hora_fin}}</td>
                                </tr>
                                <tr>
                                    @foreach($audiencia->audienciaParte as $parte)
                                        @if($parte->parte_id == $solicitud->parte->id)
                                            @if(!$audiencia->multiple)
                                                <td class="text-nowrap">Sala: {{ $audiencia->salasAudiencias[0]->sala->sala }}</td>
                                                <td class="text-nowrap">Conciliador: {{ $audiencia->conciliadoresAudiencias[0]->conciliador->persona->nombre }} {{ $audiencia->conciliadoresAudiencias[0]->conciliador->persona->primer_apellido }} {{ $audiencia->conciliadoresAudiencias[0]->conciliador->persona->segundo_apellido }}</td>
                                            @elseif($audiencia->multiple && $audiencia->multiple != null)
                                                @foreach($audiencia->salas as $sala)
                                                    @if($sala->solicitante and $parte->tipo_parte_id == 1)
                                                        <td class="text-nowrap">Sala: {{ $sala->sala->sala }}</td>
                                                    @elseif(!$sala->solicitante and $parte->tipo_parte_id != 1)
                                                        <td class="text-nowrap">Sala: {{ $sala->sala->sala }}</td>
                                                    @endif
                                                @endforeach
                                                @foreach($audiencia->conciliadoresAudiencias as $conciliador)
                                                    @if($conciliador->solicitante && $parte->tipoParte->id == 1)
                                                        <td class="text-nowrap">Conciliador: {{ $conciliador->conciliador->persona->nombre }} {{ $conciliador->conciliador->persona->primer_apellido }} {{ $conciliador->conciliador->persona->segundo_apellido }}</td>
                                                    @elseif(!$conciliador->solicitante and $parte->tipo_parte_id != 1)
                                                        <td class="text-nowrap">Conciliador: {{ $conciliador->conciliador->persona->nombre }} {{ $conciliador->conciliador->persona->primer_apellido }} {{ $conciliador->conciliador->persona->segundo_apellido }}</td>
                                                    @endif
                                                @endforeach
                                            @else
                                                <td class="text-nowrap">No asignado</td>
                                                <td class="text-nowrap">No asignado
                                            @endif
                                        @endif
                                    @endforeach
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
                                                {{$etapas->etapaResolucion->nombre}} (Fecha: {{\Carbon\Carbon::parse($etapas->created_at)->format('d/m/Y')}})
                                                @if($etapas->etapa_resolucion_id == 6)
                                                <ul>
                                                    @foreach($audiencia->documentos as $key => $documento)
                                                    @if($documento->clasificacion_archivo_id == 15 || $documento->clasificacion_archivo_id == 16 || $documento->clasificacion_archivo_id == 17)
                                                    <li><a href="/api/documentos/getFile/{{$documento->uuid}}" target="_blank">{{$documento->clasificacionArchivo->nombre}}</a></li>
                                                    @endif
                                                    @endforeach
                                                </ul>
                                                @endif
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
<div class="modal" id="modal-cancelar" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Reagendar audiencia</h2>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form id="fileupload" action="/api/buzon/uploadJustificante" method="POST" enctype="multipart/form-data">
                @csrf
            <div class="modal-body" id="domicilio-form">
                <div class="row">
                    <div class="col-md-12">
                        <h6>Solicitud de nueva fecha para audiencia</h6>
                        <hr class="red">
                    </div>
                    <div class="alert alert-muted">
                        Esta opción está habilitada para solicitar el reagendado de la audiencia por causa justificada, en conformidad con al Artículo 684-E fracción IX de la LFT.<br><br>

                        <strong>Nota!</strong> la solicitud de reagendar será validada por el conciliador y una vez aprobada se le avisará nueva fecha por este buzón
                    </div>
                    <div class="col-md-2">
                    </div>
                    <div class="col-md-10 form-group" >
                    @if($solicitudes[0]->expediente != null)
                    @if(count($solicitudes[0]->expediente->audiencia) > 0)
                        <input type="hidden" name="audiencia_id" value="{{$solicitud->expediente->audiencia[0]->id}}">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="justificante" class="control-label">Justificante</label>
                                <input type="file" id="justificante" name="justificante" class="form-control" required>
                                <p class="help-block">Selecciona el documento que servirá para evaluar la cancelación</p>
                            </div>
                        </div>
                    @endif
                    @endif
                    </div>
                    <div class="col-md-2">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</a>
                    <button type="submit" class="btn btn-primary btn-sm m-l-5" ><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
 <div class="modal" id="modal-domicilio" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Domicilio</h2>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body" id="domicilio-form">
                <input type="hidden" id="domicilio_edit">
                @include('includes.component.map',['identificador' => 'solicitado','needsMaps'=>"false", 'instancia' => 2])
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal" onclick="domicilioObj2.limpiarDomicilios()"><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5" onclick="guardarDomicilio()"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="parte_idHDD">
@endsection
@push('scripts')
<script type="text/javascript">
        function reprogramarAudiencia(id){
            $("#modal-cancelar").modal('show');
        }
        function DatosLaborales(parte_id){
            $("#parte_id").val(parte_id);
            $.ajax({
                url:"/partes/datoLaboral/"+parte_id,
                type:"GET",
                dataType:"json",
                success:function(data){
                    try{

                        if(data != null && data != ""){
                            $("#dato_laboral_id").val(data.id);
                            // getGiroEditar("solicitante");
                            
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
                    }catch(error){
                        console.log(error);
                    }
                }
            });
        }
        function AgregarRepresentante(parte_id,tipoRepresentante){
        $.ajax({
            url:"/partes/representante/"+parte_id,
            type:"GET",
            dataType:"json",
            success:function(data){
                try{
                    if(data != null && data != ""){
                        data = data[0];
                        $("#tieneRepresentante"+parte_id).html("<i class='fa fa-check'></i> ");
                        $("#btnaddRep"+parte_id).html("Ver Representante");
                        $("#curpRep").val(data.curp);
                        $("#nombreRep").val(data.nombre);
                        $("#primer_apellidoRep").val(data.primer_apellido);
                        $("#segundo_apellidoRep").val((data.segundo_apellido|| ""));
                        $("#fecha_nacimientoRep").val(dateFormat(data.fecha_nacimiento,4));
                        $("#genero_idRep").val(data.genero_id).trigger("change");
                        $("#clasificacion_archivo_id_representante").val(data.clasificacion_archivo_id).trigger('change');
                        $("#feha_instrumento").val(dateFormat(data.feha_instrumento,4));
                        $("#detalle_instrumento").val(data.detalle_instrumento);
                        $("#parte_id").val(data.id);
                        listaContactos = data.contactos;
                        if(data.documentos && data.documentos.length > 0){
                            $.each(data.documentos,function(index,doc){
                                if(doc.tipo_archivo == 1){
                                    $("#labelIdentifRepresentante").html("<b>Identificado con:</b> "+doc.descripcion);
                                    $("#tipo_documento_id").val(doc.clasificacion_archivo_id).trigger('change');
                                }else{
                                    $("#labelInstrumentoRepresentante").html("<b>Identificado con:</b> "+doc.descripcion);
                                    $("#clasificacion_archivo_id_representante").val(doc.clasificacion_archivo_id).trigger('change');
                                }
                            });
                            
                        }else{
                            $("#tipo_documento_id").val("").trigger("change");
                            $("#labelIdentifRepresentante").html("");
                            $("#clasificacion_archivo_id_representante").val("").trigger('change');
                            $("#labelInstrumentoRepresentante").html("");
                        }
                    }else{
                        $("#curpRep").val("");
                        $("#nombreRep").val("");
                        $("#primer_apellidoRep").val("");
                        $("#segundo_apellidoRep").val("");
                        $("#fecha_nacimientoRep").val("");
                        $("#genero_idRep").val("").trigger("change");
                        $("#clasificacion_archivo_id_representante").val("").trigger('change');
                        $("#feha_instrumento").val("");
                        $("#detalle_instrumento").val("");
                        $("#parte_id").val("");
                        $("#tipo_documento_id").val("").trigger("change");
                        $("#labelIdentifRepresentante").html("");
                        listaContactos = [];
                    }
                    $("#tipo_contacto_id").val("").trigger("change");
                    $("#contacto").val("");
                    $("#parte_representada_id").val(parte_id);
                    if(tipoRepresentante == 1){
                        $("#menorAlert").show();
                        $("#representanteMoral").hide();
                    }else{
                        $("#menorAlert").hide();
                        $("#representanteMoral").show();
                    }
                    cargarContactos();
                    $("#modal-representante").modal("show");
                }catch(error){
                    console.log(error);
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
                    try{
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
                    }catch(error){
                        console.log(error);
                    }
                }
            });
        }
        function cambiarDomicilio(id){
            $.ajax({
                url:"/api/getDomicilioParte/"+id,
                type:"GET",
                global:false,
                dataType:"json",
                success:function(data){
                    if(data != null && data != ""){
                        domicilioObj2.cargarDomicilio(data);
                        $("#parte_idHDD").val(id);
                        $("#modal-domicilio").modal("show");
                    }
                }
            });
        }
        function guardarDomicilio(id){
            if($("#estado_idsolicitado").val() != "" && $("#municipiosolicitado").val() != "" && $("#cpsolicitado").val() != "" && $("#tipo_asentamiento_idsolicitado").val() != "" && $("#asentamientosolicitado").val() != "" && $("#tipo_vialidad_idsolicitado").val() != "" && $("#vialidadsolicitado").val() != "" && $("#num_extsolicitado").val() != ""){
                $.ajax({
                    url:"/api/cambiarDomicilioParte",
                    type:"POST",
                    dataType:"json",
                    data:{
                        domicilio:domicilioObj2.getDomicilio(),
                    },
                    success:function(data){
                        try{

                            if(data != null && data != ""){
                                $('#modal-domicilio').modal('hide');
                                domicilioObj2.limpiarDomicilios();
                                swal({
                                    title: 'Éxito',
                                    text: 'Se cambio el domicilio',
                                    icon: 'success'
                                });
                            }
                        }catch(error){
                            console.log(error);
                        }
                    }
                });
            }else{
                swal({
                    title: 'Error',
                    text: 'Es necesario llenar los campos obligatorios',
                    icon: 'error'
                });
            }
        }
    </script>
@endpush

