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
                    <i class="fa fa-circle fa-fw text-gold mr-2 f-s-8"></i> <strong>Expediente:</strong> {{$solicitud->expediente->folio}}
                </div>
            </div>
            <div id="collapse{{$solicitud->id}}" class="collapse" data-parent="#accordion">
                <div class="card-body">
                    <ul>
                        <li><strong>Confirmaci&oacute;n:</strong>
                            <table class="table table-striped table-bordered table-td-valign-middle">
                                <tr>
                                    <td class="text-nowrap"><strong>Fecha de Solicitud:</strong> {{\Carbon\Carbon::parse($solicitud->fecha_solicitud)->format('d/m/Y')}}</td>
                                    <td class="text-nowrap"><strong>Fecha de Conflicto:</strong> {{\Carbon\Carbon::parse($solicitud->fecha_conflicto)->format('d/m/Y')}}</td>
                                    <td class="text-nowrap"><strong>Objeto de la solicitud:</strong> {{$solicitud->objeto_solicitudes[0]->nombre}}</td>
                                    <td class="text-nowrap"><strong>Fecha de confirmaci&oacute;n:</strong> {{\Carbon\Carbon::parse($solicitud->fecha_ratificacion)->format('d/m/Y')}}</td>
                                    <td class="text-nowrap"><strong>Centro:</strong> {{$solicitud->centro->nombre}}</td>
                                </tr>
                                <tr>
                                    <td class="text-nowrap" colspan="5" align="center"><strong>Partes citadas</strong></td>
                                </tr>
                                @foreach($solicitud->partes as $parte)
                                @if($parte->tipo_parte_id == 2)
                                <tr>
                                    <td class="text-nowrap" colspan="5">
                                        <br>
                                                @if($parte->tipo_persona_id == 1)
                                                - {{$parte->nombre}} {{$parte->primer_apellido}} {{$parte->segundo_apellido}}
                                                @else
                                                - {{$parte->nombre_comercial}}
                                                @endif
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                                <tr>
                                    <td class="text-nowrap" colspan="5">
                                        <strong>Documentos:</strong><br>
                                        <ul>
                                        @foreach($solicitud->documentos as $doc_sol)
                                            @if($doc_sol->clasificacion_archivo_id == 13 || $doc_sol->clasificacion_archivo_id == 62 || $doc_sol->clasificacion_archivo_id == 59 || $doc_sol->clasificacion_archivo_id == 61)
                                            <li><a href="/api/documentos/getFile/{{$doc_sol->uuid}}" target="_blank">{{ isset($doc_sol->clasificacionArchivo->nombre)?$doc_sol->clasificacionArchivo->nombre: "N/A"}}</a></li>
                                            @endif
                                        @endforeach
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        @if($solicitud->expediente->audiencia != null)
                        @foreach($solicitud->expediente->audiencia as $key => $audiencia)
                        <li><strong>Audiencia:</strong> {{$audiencia->folio}}/{{$audiencia->anio}}
                            <br>
                            <table class="table table-striped table-bordered table-td-valign-middle">
                                <tr>
                                    <td class="text-nowrap">
                                        <strong>Fecha de audiencia:</strong> {{\Carbon\Carbon::parse($audiencia->fecha_audiencia)->format('d/m/Y')}}
                                    </td>
                                    <td class="text-nowrap"><strong>Hora de inicio:</strong> {{$audiencia->hora_inicio}}</td>
                                    <td class="text-nowrap"><strong>Hora de t&eacute;rmino:</strong> {{$audiencia->hora_fin}}</td>
                                </tr>
                                <tr>
                                    @foreach($audiencia->audienciaParte as $parte)
                                        @if($parte->parte_id == $solicitud->parte->id)
                                            @if(!$audiencia->multiple)
                                                <td class="text-nowrap"><strong>Sala:</strong> {{ $audiencia->salasAudiencias[0]->sala->sala }}</td>
                                                <td class="text-nowrap"><strong>Conciliador:</strong> {{ $audiencia->conciliadoresAudiencias[0]->conciliador->persona->nombre }} {{ $audiencia->conciliadoresAudiencias[0]->conciliador->persona->primer_apellido }} {{ $audiencia->conciliadoresAudiencias[0]->conciliador->persona->segundo_apellido }}</td>
                                            @elseif($audiencia->multiple && $audiencia->multiple != null)
                                                @foreach($audiencia->salas as $sala)
                                                    @if($sala->solicitante and $parte->tipo_parte_id == 1)
                                                        <td class="text-nowrap"><strong>Sala:</strong> {{ $sala->sala->sala }}</td>
                                                    @elseif(!$sala->solicitante and $parte->tipo_parte_id != 1)
                                                        <td class="text-nowrap"><strong>Sala:</strong> {{ $sala->sala->sala }}</td>
                                                    @endif
                                                @endforeach
                                                @foreach($audiencia->conciliadoresAudiencias as $conciliador)
                                                    @if($conciliador->solicitante && $parte->tipoParte->id == 1)
                                                        <td class="text-nowrap"><strong>Conciliador:</strong> {{ $conciliador->conciliador->persona->nombre }} {{ $conciliador->conciliador->persona->primer_apellido }} {{ $conciliador->conciliador->persona->segundo_apellido }}</td>
                                                    @elseif(!$conciliador->solicitante and $parte->tipo_parte_id != 1)
                                                        <td class="text-nowrap"><strong>Conciliador:</strong> {{ $conciliador->conciliador->persona->nombre }} {{ $conciliador->conciliador->persona->primer_apellido }} {{ $conciliador->conciliador->persona->segundo_apellido }}</td>
                                                    @endif
                                                @endforeach
                                            @else
                                                <td class="text-nowrap">No asignado</td>
                                                <td class="text-nowrap">No asignado
                                            @endif
                                        @endif
                                    @endforeach
                                    @if($audiencia->resolucion_id != null)
                                        <td class="text-nowrap"><strong>Resolución:</strong> {{$audiencia->resolucion->nombre}}</td>
                                    @else
                                        <td class="text-nowrap"><strong>Resolución:</strong> Audiencia no celebrada</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td class="text-nowrap" colspan="2">
                                        <strong>Movimientos:</strong>
                                        <ul>
                                            @foreach($audiencia->etapasResolucionAudiencia as $etapas)
                                            <li>
                                                {{$etapas->etapaResolucion->nombre}} (Fecha: {{\Carbon\Carbon::parse($etapas->created_at)->format('d/m/Y')}})
                                                @if($etapas->etapa_resolucion_id == 6)
                                                <ul>
                                                    @foreach($audiencia->documentos as $key => $documento)
                                                    @if($documento->clasificacion_archivo_id == 15 || $documento->clasificacion_archivo_id == 16 || $documento->clasificacion_archivo_id == 17)
                                                    <li><a href="/api/documentos/getFile/{{$documento->uuid}}" target="_blank">{{ isset($documento->clasificacionArchivo->nombre)?$documento->clasificacionArchivo->nombre: "N/A"}}</a></li>
                                                    @endif
                                                    @endforeach
                                                </ul>
                                                @endif
                                            </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="text-nowrap">
                                        <strong>Documentos por firmar:</strong>
                                        <ul>
                                            @if($audiencia->documentos_firmar)
                                                @foreach($audiencia->documentos_firmar as $doc)
                                                @if($doc->firma == "" && $doc->firma == null)
                                                @if(isset($doc->documento->clasificacionArchivo) && $doc->documento->clasificacionArchivo->nombre != "Citatorio")
                                                <li>
                                                    <a href="#" onclick="validarFirma({{$doc->id}},'{{$doc->firma}}','{{$doc->documento->uuid}}')">{{$doc->documento->clasificacionArchivo->nombre}}</a>
                                                </li>
                                                @endif
                                                @endif
                                                @endif
                                                @endforeach
                                            @endif
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <strong>Documentos por firmar:</strong>
                                        <ul>
                                            @if($audiencia->documentos != null)
                                                @foreach($audiencia->documentos as $doc_audiencia)
                                                @if($doc_audiencia->clasificacion_archivo_id == 14 || $doc_audiencia->clasificacion_archivo_id == 18 || $doc_audiencia->clasificacion_archivo_id == 41)
                                                    <li><a href="/api/documentos/getFile/{{$doc_audiencia->uuid}}" target="_blank">{{ isset($doc_audiencia->clasificacionArchivo->nombre)?$doc_audiencia->clasificacionArchivo->nombre: "N/A"}}</a></li>
                                                @endif
                                                @endforeach
                                            @endif
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </li>
                        
                        @endforeach
                        @endif
                    </ul>
                    <div>
                            <button class="btn btn-primary" onclick="getBitacoraBuzon({{$solicitud->parte->id}})">Consultar Bitacora</button>
                    </div>
                    
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
            <form id="fileupload" action="/buzon/uploadJustificante" method="POST" enctype="multipart/form-data">
                @csrf
            <div class="modal-body" id="domicilio-form">
                <div class="row">
                    <div class="col-md-12">
                        <h6>Solicitud de nueva fecha para audiencia</h6>
                        <hr class="red">
                    </div>
                    <div class="alert alert-muted">
                        Esta función está habilitada para el cambio de la fecha de la celebración de la audiencia de conciliación en el caso de que las partes o alguna de ellas no pueda compadecer por una causa justificada, de conformidad con la fracción IX del artículo 684-E de la Ley Federal del Trabajo y numeral 22 de los Lineamientos para el Procedimiento de Conciliación Individual Prejudicial. <br><br>
                        <br>
                        1. Esta petición se encuentra sujeta a aprobación del conciliador y de la disponibilidad del calendario de la Oficina Estatal correspondiente del CFCRL. <br>
                        2. Usted podrá sugerir la fecha en la que desea llevar la audiencia. En caso de que se deba notificar a alguno de los citados por medio de notificador, la nueva fecha deberá ser después de 15 días a partir de la fecha de hoy; en caso contrario, podrá solicitar que la nueva fecha sea después de 6 días a partir de la fecha de hoy.<br>
                        3. El justificante que cargue deberá estar firmado por usted y en formato .pdf o .jpg. En él deberá explicarla razón por la que no podrá asistir a la audiencia en la fecha programada.<br>
                    </div>
                    <div class="col-md-2">
                    </div>
                    <div class="col-md-10 form-group" >
                        <input type="hidden" name="audiencia_id" id="audiencia_reprogramacion_id">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="justificante" class="control-label">Justificante</label>
                                <input type="file" accept=".pdf,.jpg" id="justificante" name="justificante" class="form-control" required>
                                <p class="help-block">Selecciona el documento que servirá para evaluar la cancelación</p>
                            </div>
                        </div>
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
<div class="modal" id="modal-firma" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Firma de documento</h2>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body" id="domicilio-form">
                <form enctype="multipart/form-data" id="formFirmar" method="post">
                    <input type="hidden" name="persona_id" id="persona_id">
                    <input type="hidden" name="tipo_persona" id="tipo_persona">
                    <input type="hidden" name="audiencia_id" id="audiencia_id">
                    <input type="hidden" name="solicitud_id" id="solicitud_id">
                    <input type="hidden" name="plantilla_id" id="plantilla_id">
                    <input type="hidden" name="documento_id" id="documento_id">
                    <input type="hidden" name="solicitante_id" id="solicitante_id">
                    <input type="hidden" name="solicitado_id" id="solicitado_id">
                    <input type="hidden" name="firma_documento_id" id="firma_documento_id">
                    <input type="hidden" name="tipo_firma" id="tipo_firma">
                    <input type="hidden" name="encoding_firmas" id="encoding_firmas">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="cert" class="col-sm-6 control-label">Archivo .cer</label>
                            <div class="col-sm-10">
                                <input type="file" name="cert" id='cert' accept=".cer">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="key" class="col-sm-6 control-label">Archivo .key</label>
                            <div class="col-sm-10">
                                <input type="file" name="key" id='key' accept=".key">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="password" class="col-sm-6 control-label">Contraseña</label>
                            <div class="col-sm-10">
                                <input type="password" name="password" id='password' class="form-control">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <button class="btn btn-primary btn-sm" data-dismiss="guardar" type="submit" form="formFirmar"><i class="fa fa-save"></i> Guardar</button>
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="parte_idHDD">
@include('buzon.modal_bitacora',["interno"=>false])
@endsection
@push('scripts')
<script type="text/javascript">
        function reprogramarAudiencia(id,acepto_buzon){
            console.log(acepto_buzon);
            if(acepto_buzon == "si"){
                $("#audiencia_reprogramacion_id").val(id);
                $("#modal-cancelar").modal('show');
            }else{
                swal("Error","Si desea hacer uso de esta herramienta deberá aceptar que las notificaciones personales de esta solicitud se hagan por medio del buzón electrónico. Para que usted pueda aceptar las notificaciones por buzón electrónico deberá comunicarse a la Oficina Estatal que le corresponda del CFCRL.","error");
            }
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
        function validarFirma(id,firma,uuid){
            console.log(id);
            console.log(firma);
            console.log(uuid);
            if(firma == "" || firma == null){
                swal({
                    title: 'Documento no firmado',
                    text: '¿Que acción deseas realizar?',
                    icon: 'info',
                    buttons: {
                        cancel: {
                            text: 'cancelar',
                            value: null,
                            visible: true,
                            className: 'btn btn-default',
                            closeModal: true,
                        },
                        roll: {
                            text: "Ver documento",
                            value: 2,
                            className: 'btn btn-warning',
                            visible: true,
                            closeModal: true
                        },
                        confirm: {
                            text: 'Firmar',
                            value: 1,
                            visible: true,
                            className: 'btn btn-warning',
                            closeModal: true
                        }
                    }
                }).then(function(tipo){
                    if(tipo == 1 || tipo == 2){
                        if(tipo == 1){
                            obtenerInformacionDocumento(id);
                        }else{
                            var archivo = document.createElement('a');
                            archivo.href = '/api/documentos/getFile/'+uuid;
                            archivo.taget = '_blank';
                            archivo.click();
                        }
                    }
                });
            }else{
                var archivo = document.createElement('a');
                archivo.href = '/api/documentos/getFile/'+uuid;
                archivo.taget = '_blank';
                archivo.click();
            }
        }
        function obtenerInformacionDocumento(firma_documento_id){
            $.ajax({
                url:"/documentos/firmado/obtener/"+firma_documento_id,
                type:"GET",
                dataType:"json",
                async:true,
                success:function(data){
                    try{
                        console.log(data);
                        $("#persona_id").val(data.persona_id);
                        $("#tipo_persona").val(data.tipo_persona);
                        $("#solicitante_id").val(data.solicitante_id);
                        $("#solicitado_id").val(data.solicitado_id);
                        $("#audiencia_id").val(data.audiencia_id);
                        $("#solicitud_id").val(data.solicitud_id);
                        $("#plantilla_id").val(data.plantilla_id);
                        $("#documento_id").val(data.documento_id);
                        $("#tipo_firma").val(data.tipo_firma);
                        $("#encoding_firmas").val(data.encoding_firmas);
                        $("#firma_documento_id").val(data.firma_documento_id);
                        $("#modal-firma").modal("show");
                    }catch(error){
                        console.log(error);
                    }
                }
            });
        }
        $("#formFirmar").on("submit",function(e){
            e.preventDefault();
            if($("#cer").val() != "" && $("#key").val() != "" && $("#password").val() != ""){
                var formData = new FormData(document.getElementById("formFirmar"));
                formData.append("_token","{{ csrf_token() }}");
                $.ajax({
                    url: "/documentos/firmado",
                    type: "POST",
                    dataType: "json",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false
                }).done(function(res){
                    if(res.success){
                        $("#modal-firma").modal("hide");
                        swal({
                            title: 'Correcto',
                            text: 'Documento firmado correctamente',
                            icon: 'success',
                        });
                        setTimeout('', 5000);
                        location.reload();
                    }else{
                        swal({
                            title: 'Error',
                            text: res.message,
                            icon: 'error',
                        });
                    }
                });
            }else{
                swal({
                    title: 'Error',
                    text: 'Llena todos los campos',
                    icon: 'error',
                });
            }
        });
    </script>
@endpush

