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
                        <li>Ratificacion:
                            <button class="btn btn-primary pull-right" style="display:hidden;">Cancelar </button>
                            <table class="table table-striped table-bordered table-td-valign-middle">
                                <tr>
                                    <td class="text-nowrap"><strong>Fecha de Solicitud:</strong> {{\Carbon\Carbon::parse($solicitud->fecha_solicitud)->format('d/m/Y')}}</td>
                                    <td class="text-nowrap"><strong>Fecha de Conflicto:</strong> {{\Carbon\Carbon::parse($solicitud->fecha_conflicto)->format('d/m/Y')}}</td>
                                    <td class="text-nowrap"><strong>Objeto de la solicitud:</strong> {{$solicitud->objeto_solicitudes[0]->nombre}}</td>
                                    <td class="text-nowrap"><strong>Fecha de ratificación:</strong> {{\Carbon\Carbon::parse($solicitud->fecha_ratificacion)->format('d/m/Y')}}</td>
                                    <td class="text-nowrap"><strong>Centro:</strong> {{$solicitud->centro->nombre}}</td>
                                </tr>
                                <tr>
                                    <td class="text-nowrap" colspan="5" align="center"><strong>Partes solicitadas</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-nowrap" colspan="3">
                                        <br>
                                        @foreach($solicitud->partes as $parte)
                                            @if($parte->tipo_parte_id == 2)
                                                @if($parte->tipo_persona_id == 1)
                                                - {{$parte->nombre}} {{$parte->primer_apellido}} {{$parte->segundo_apellido}}
                                                @else
                                                - {{$parte->nombre_comercial}}
                                                @endif
                                            @endif
                                        @endforeach
                                    </td>
                                    <td class="text-nowrap" colspan="2">
                                        <button class="btn btn-primary">Verificar Ubicación</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-nowrap" colspan="5">
                                        Documentos:<br>
                                        <ul>
                                            <li><a href="#">Citatorio</a></li>
                                        @if($solicitud->documentosRatificacion != null)
                                        @foreach($solicitud->documentosRatificacion as $documento)
                                            <li><a href="#">{{$documento->clasificacionArchivo->nombre}}</a></li>
                                        @endforeach
                                        @endif
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        @if($solicitud->expediente->audiencia != null)
                        @foreach($solicitud->expediente->audiencia as $audiencia)
                        <li>Audiencia: {{$audiencia->folio}}/{{$audiencia->anio}}<br>
                            <table class="table table-striped table-bordered table-td-valign-middle">
                                <tr>
                                    <td class="text-nowrap">
                                        Fecha de audiencia: {{\Carbon\Carbon::parse($audiencia->fecha_audiencia)->format('d/m/Y')}} 
                                    </td>
                                    <td class="text-nowrap">Hora de inicio: {{$audiencia->hora_inicio}}</td>
                                    <td class="text-nowrap">Hora de termino: {{$audiencia->hora_fin}}</td>
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
                                                <ul>
                                                    <li>
                                                        <a href="http://conciliacion.test/">Documento</a>
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

