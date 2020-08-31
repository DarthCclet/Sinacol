    <!-- begin vertical-box -->
    <div class="vertical-box">
    <!-- end event-list -->
    <!-- begin calendar -->
    <div id="calendar" class="vertical-box-column calendar"></div>
    <!-- end calendar -->
    </div>
    <!-- end vertical-box -->
<!-- inicio Modal de disponibilidad-->
<div class="modal" id="modal-asignar" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Asignar audiencia</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-muted">
                    - Selecciona el conciliador y la sala donde se celebrará la audiencia<br>
                    - La fecha limite para notificar será 5 días habiles previo a la fecha de audiencia (<span id="lableFechaInicio"></span>>)
                </div>
                <div id="divAsignarUno">
                    <div class="col-md-12 row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="conciliador_id" class="col-sm-6 control-label">Conciliador</label>
                                <div class="col-sm-10">
                                    <select id="conciliador_id" class="form-control">
                                        <option value="">-- Selecciona un conciliador</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sala_id" class="col-sm-6 control-label">Sala</label>
                                <div class="col-sm-10">
                                    <select id="sala_id" class="form-control">
                                        <option value="">-- Selecciona una sala</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="divAsignarDos">
                    <div class="col-md-12 row">
                        <div class="col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Solicitante</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="conciliador_solicitante_id" class="col-sm-6 control-label">Conciliador</label>
                                            <div class="col-sm-10">
                                                <select id="conciliador_solicitante_id" class="form-control">
                                                    <option value="">-- Selecciona un conciliador</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="sala_solicitante_id" class="col-sm-6 control-label">Sala</label>
                                            <div class="col-sm-10">
                                                <select id="sala_solicitante_id" class="form-control">
                                                    <option value="">-- Selecciona una sala</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Citado</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="conciliador_solicitado_id" class="col-sm-6 control-label">Conciliador</label>
                                            <div class="col-sm-10">
                                                <select id="conciliador_solicitado_id" class="form-control">
                                                    <option value="">-- Selecciona un conciliador</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="sala_solicitado_id" class="col-sm-6 control-label">Sala</label>
                                            <div class="col-sm-10">
                                                <select id="sala_solicitado_id" class="form-control">
                                                    <option value="">-- Selecciona una sala</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <h1 class="h2">Notificaciones <small>Notificación de los citados</small></h1>
                <hr class="red">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <td>Citado</td>
                                <td>Dirección</td>
                                <td>Mapa</td>
                                <td>Tipo de notificación</td>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($partes))
                            @foreach($partes as $parte)
                            <tr>
                                @if($parte->tipo_parte_id == 2)
                                    @if($parte->tipo_persona_id == 1)
                                    <td>{{$parte->nombre}} {{$parte->primer_apellido}} {{$parte->segundo_apellido}}</td>
                                    @else
                                    <td>{{$parte->nombre_comercial}}</td>
                                    @endif
                                    <td>{{$parte->domicilios->vialidad}} {{$parte->domicilios->num_ext}}, {{$parte->domicilios->asentamiento}} {{$parte->domicilios->municipio}}, {{$parte->domicilios->estado}}</td>
                                    <td>
                                        <input type="hidden" id="parte_id{{$parte->id}}" class="hddParte_id" value="{{$parte->id}}">
                                        @if($parte->domicilios->latitud != "" && $parte->domicilios->longitud != "")
                                        <a href="https://maps.google.com/?q={{$parte->domicilios->latitud}},{{$parte->domicilios->longitud}}" target="_blank" class="btn btn-xs btn-primary"><i class="fa fa-map"></i></a>
                                        @else
                                        <legend>Sin datos</legend>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="radioNotificacionA{{$parte->id}}" value="1" name="radioNotificacion{{$parte->id}}" class="custom-control-input">
                                            <label class="custom-control-label" for="radioNotificacionA{{$parte->id}}">A) El solicitante entrega citatorio al citado(s)</label>
                                        </div>
                                        @if($parte->domicilios->latitud != "" && $parte->domicilios->longitud != "")
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="radioNotificacionB{{$parte->id}}" value="2" name="radioNotificacion{{$parte->id}}" class="custom-control-input">
                                            <label class="custom-control-label" for="radioNotificacionB{{$parte->id}}">B) Un notificador del centro entrega citatorio al citado(s)</label>
                                        </div>
                                        @else
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="radioNotificacionB{{$parte->id}}" value="3" name="radioNotificacion{{$parte->id}}" class="custom-control-input">
                                            <label class="custom-control-label" for="radioNotificacionB{{$parte->id}}">B) Agendar cita con el notificador para entrega de citatorio</label>
                                        </div>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                            @endforeach
                            @endif
                        <tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5" id="btnGuardarAudiencia"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="fecha_audiencia">
<input type="hidden" id="hora_inicio">
<input type="hidden" id="hora_fin">
<input type="hidden" id="tipoAsignacion">
<!-- Fin Modal de disponibilidad-->

@push('scripts')
        <script>
            $(document).ready(function(){
                $.ajax({
                    url:"/audiencia/getCalendario",
                    type:"POST",
                    data:{
                        centro_id:1,
                        _token:"{{ csrf_token() }}"
                    },
                    dataType:"json",
                    success:function(data){
                        construirCalendario(data);
                    }
                });
            });
            function construirCalendario(arregloGeneral){
                $('#external-events .fc-event').each(function() {
                    // store data so the calendar knows to render an event upon drop
                    $(this).data('event', {
                            title: $.trim($(this).text()), // use the element's text as the event title
                            stick: true // maintain when user navigates (see docs on the renderEvent method)
                    });
                });
                $('#calendar').fullCalendar({
                    header: {
                        left: 'month,agendaWeek',
                        center: 'title',
                        right: 'prev,today,next '
                    },
                    selectable: true,
                    selectHelper: true,
//                    slotDuration:arregloGeneral.duracionPromedio,
//                    slotDuration:"01:00:00",
                    select: function(start, end,a,b) {
                        var ahora = new Date();
                        end=moment(end).add(1, 'hours').add(30,'minutes').format('Y-MM-DD HH:mm:ss');
                        console.log(end);
                        start=moment(start).format('Y-MM-DD HH:mm:ss');
                        var startVal = new Date(start);
                        if(startVal > ahora){ //validar si la fecha es mayor que hoy
                            if(b.type == "month"){ // si es la vista de mes, abrir la vista de semana
                                $('#calendar').fullCalendar("gotoDate",start);
                                $(".fc-agendaWeek-button").click();
                                $("#fecha_audiencia").val(start);
                            }else{
                                SolicitarAudiencia(start,end);
                            }
                        }else{
                            swal({
                                title: 'Error',
                                text: 'No puedes seleccionar una fecha previa',
                                icon: 'warning'
                            });
                        }
                        $('#calendar').fullCalendar('unselect');
                    },
                    selectOverlap: function(event) {
                        return event.rendering !== 'background';
                    },
                    editable: false,
                    allDaySlot:false,
                    eventLimit: false,
                    businessHours: arregloGeneral.laboresCentro,
                    events: arregloGeneral.incidenciasCentro,
                    eventConstraint: "businessHours"
                });
            }
            function SolicitarAudiencia(inicio,fin){
                swal({
                    title: '¿Las partes concilian en la misma sala?',
                    text: 'Al oprimir aceptar se asignará solo un consiliador y una sola sala para solicitante y citado',
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: 'Separados',
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
                    if(isConfirm){
                        CargarModal(1,inicio,fin);
                    }else{
                        CargarModal(2,inicio,fin);
                    }
                });
            }
            function CargarModal(aux,inicio,fin){
//                console.log(inicio);
//                console.log(fin);
                if(aux == 1){
                    $("#divAsignarUno").show();
                    $("#divAsignarDos").hide();
                    $("#tipoAsignacion").val(1);
                }else{
                    $("#divAsignarUno").hide();
                    $("#divAsignarDos").show();
                    $("#tipoAsignacion").val(2);
                }
                getConciliadores(inicio,fin);
                getSalas(inicio,fin);
                $("#hora_inicio").val(inicio);
                $("#hora_fin").val(fin);
                $("#lableFechaInicio").html(inicio.substring(1, 10));
                $("#modal-asignar").modal("show");
            }
            getConciliadores = function(fechaInicio,fechaFin){
                $.ajax({
                    url:"/audiencia/ConciliadoresDisponibles",
                    type:"POST",
                    data:{
                        fechaInicio:fechaInicio,
                        fechaFin:fechaFin,
                        centro_id:1,
                        _token:"{{ csrf_token() }}"
                    },
                    dataType:"json",
                    success:function(data){
                        if(data != null && data != ""){
                            $("#conciliador_id,#conciliador_solicitado_id,#conciliador_solicitante_id").html("<option value=''>-- Selecciona un conciliador</option>");
                            $.each(data,function(index,element){
                                $("#conciliador_id,#conciliador_solicitado_id,#conciliador_solicitante_id").append("<option value='"+element.id+"'>"+element.persona.nombre+" "+element.persona.primer_apellido+" "+element.persona.segundo_apellido+"</option>");
                            });
                        }else{
                            $("#conciliador_id,#conciliador_solicitado_id,#conciliador_solicitante_id").html("<option value=''>-- Selecciona un conciliador</option>");
                        }
                        $("#conciliador_id,#conciliador_solicitado_id,#conciliador_solicitante_id").select2();
                    }
                });
            };
            function getSalas(fechaInicio,fechaFin){
                $.ajax({
                    url:"/audiencia/SalasDisponibles",
                    type:"POST",
                    data:{
                        fechaInicio:fechaInicio,
                        fechaFin:fechaFin,
                        centro_id:1,
                        _token:"{{ csrf_token() }}"
                    },
                    dataType:"json",
                    success:function(data){
                        $("#sala_id,#sala_solicitado_id,#sala_solicitante_id").html("<option value=''>-- Selecciona una sala</option>");
                        if(data != null && data != ""){
                            $.each(data,function(index,element){
                                $("#sala_id,#sala_solicitado_id,#sala_solicitante_id").append("<option value='"+element.id+"'>"+element.sala+"</option>");
                            });
                        }
                        $("#sala_id,#sala_solicitado_id,#sala_solicitante_id").select2();
                    }
                });
            }
            $("#btnGuardarAudiencia").on("click",function(){
                var validacion = validarAsignacion();
                if(!validacion.error){
                    var listaRelaciones = [];
                    if(origen == 'audiencias'){
                        var pasa =true;
                        $(".switchPartes").each(function(index){
                            if($(this).is(":checked")){
                                listaRelaciones.push({
                                    parte_solicitante_id:$(this).data("parte_solicitante_id"),
                                    parte_solicitada_id:$(this).data("parte_solicitada_id")
                                });
                            }
                        });
                        if(listaRelaciones.length == 0){
                            swal({title: 'Error',text: 'Selecciona una relación al menos',icon: 'warning'});
                            return false;
                        }
                        var url = '/audiencia/nuevaAudiencia';
                    }else{
                        var url = '/audiencia/calendarizar';
                    }
                    $.ajax({
                        url:url,
                        type:"POST",
                        data:{
                            fecha_audiencia:new Date($("#fecha_audiencia").val()).toISOString(),
                            hora_inicio:$("#hora_inicio").val(),
                            hora_fin:$("#hora_fin").val(),
                            tipoAsignacion:$("#tipoAsignacion").val(),
                            expediente_id:$("#expediente_id").val(),
                            asignacion:validacion.arrayEnvio,
                            listaNotificaciones:validacion.listaNotificaciones,
                            nuevaCalendarizacion:'S',
                            listaRelaciones:listaRelaciones,
                            audiencia_id:$("#audiencia_id").val(),
                            _token:"{{ csrf_token() }}"
                        },
                        dataType:"json",
                        success:function(data){
                            console.log(data);
                            if(data != null && data != ""){
                                if(origen == 'audiencias'){
                                    window.location.href = "audiencias/"+data.id+"/edit";
                                }else{
                                    window.location.href = "/audiencias/"+data.id+"/edit";
                                    // window.location.href = "{{ route('audiencias.index')}}";
                                }
                            }else{
                                swal({
                                    title: 'Algo salio mal',
                                    text: 'No se registro la audiencia',
                                    icon: 'warning'
                                });
                            }
                        }
                    });
                }else{
                    swal({
                        title: 'Algo salio mal',
                        text: validacion.msg,
                        icon: 'warning'
                    });
                }
            });
            function validarAsignacion(){
                var error = false;
                var msg="";
                var arrayEnvio = new Array();
                if($("#tipoAsignacion").val() == 1){
                    if($("#sala_id").val() == ""){
                        error = true;
                        msg = "Selecciona una sala";
                    }
                    if($("#conciliador_id").val() == ""){
                        error = true;
                        msg = "Selecciona un conciliador";
                    }
                    if(!error){
                        arrayEnvio.push({sala:$("#sala_id").val(),conciliador:$("#conciliador_id").val(),resolucion:true});
                    }
                }else{
                    if(($("#sala_solicitante_id").val() == "" || $("#sala_solicitado_id").val() == "") || ($("#sala_solicitante_id").val() == $("#sala_solicitado_id").val())){
                        error = true;
                        msg = "Asegurate de seleccionar las salas y que no sea la misma";
                    }
                    if(($("#conciliador_solicitante_id").val() == "" || $("#conciliador_solicitado_id").val() == "") || ($("#conciliador_solicitante_id").val() == $("#conciliador_solicitado_id").val())){
                        error = true;
                        msg = "Asegurate de seleccionar los conciliadores y que no sean el mismo";
                    }
                    if(!error){
                        arrayEnvio.push({sala:$("#sala_solicitante_id").val(),conciliador:$("#conciliador_solicitante_id").val(),resolucion:true});
                        arrayEnvio.push({sala:$("#sala_solicitado_id").val(),conciliador:$("#conciliador_solicitado_id").val(),resolucion:false});
                    }
                }
                var listaNotificaciones = [];
                if(!error){
                    $(".hddParte_id").each(function(element){
                        var parte_id = $(this).val();
                        if($("#radioNotificacionA"+parte_id).is(":checked")){
                            listaNotificaciones.push({
                                parte_id:parte_id,
                                tipo_notificacion_id:1
                            });
                        }else if($("#radioNotificacionB"+parte_id).is(":checked")){
                            listaNotificaciones.push({
                                parte_id:parte_id,
                                tipo_notificacion_id:2
                            });
                        }else{
                            msg = "Indica el tipo de notificación para los citados";
                            error = true;
                        }
                        console.log(listaNotificaciones);
                    });
                }
                var array = [];
                array.error=error;
                array.msg=msg;
                array.arrayEnvio=arrayEnvio;
                array.listaNotificaciones=listaNotificaciones;
                console.log(array);
                return array;
            }
        </script>
@endpush
