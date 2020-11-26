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
                    - La fecha límite para notificar será 5 días hábiles previo a la fecha de audiencia (<span id="lableFechaInicio"></span>>)
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
                    url:"/audiencia/getCalendarioCentral",
                    type:"POST",
                    data:{
                        _token:"{{ csrf_token() }}"
                    },
                    dataType:"json",
                    success:function(data){
                        try{
                            console.log(data.minTime);
                            console.log(data.maxtime);
                            if(data.minTime == "23:59:59" && data.maxtime == "00:00:00"){
                                swal({
                                    title: 'Error',
                                    text: 'No está configurada la disponibilidad del centro',
                                    icon: 'warning'
                                });
                            }
                            construirCalendario(data);
                        }catch(error){
                            console.log(error);
                        }
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
                    minTime: arregloGeneral.minTime,
                    maxTime: arregloGeneral.maxtime,
                    select: function(start, end,a,b) {
                        var validarRatificacion = RatificacionValidar();
                        if(!validarRatificacion.error){
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
                                    $("#fecha_audiencia").val(start);
                                    SolicitarAudiencia(start,end);
                                }
                            }else{
                                swal({
                                    title: 'Error',
                                    text: 'No puedes seleccionar una fecha previa',
                                    icon: 'warning'
                                });
                            }
                        }else{
                            swal({
                                title: 'Error',
                                text: validarRatificacion.msg,
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
                $.ajax({
                    url:'/solicitud/correos/'+$("#solicitud_id").val(),
                    type:'GET',
                    dataType:"json",
                    async:true,
                    success:function(data){
                        try{
                            if(data == null || data == ""){
                                swal({
                                    title: '¿Estas seguro?',
                                    text: 'Al oprimir aceptar se creará un expediente y se podrán agendar audiencias para conciliación',
                                    icon: 'warning',
                                    buttons: {
                                        cancel: {
                                            text: 'Cancelar',
                                            value: null,
                                            visible: true,
                                            className: 'btn btn-default',
                                            closeModal: true,
                                        },
                                        confirm: {
                                            text: 'Aceptar',
                                            value: true,
                                            visible: true,
                                            className: 'btn btn-danger',
                                            closeModal: true
                                        }
                                    }
                                }).then(function(isConfirm){
                                    if(isConfirm){
                                        swal({
                                            title: '¿Las partes concilian en la misma sala?',
                                            text: 'Selecciona el tipo de conciliación que se llevará a cabo',
                                            icon: 'warning',
                                            buttons: {
                                                cancel: {
                                                    text: 'cancelar',
                                                    value: null,
                                                    visible: true,
                                                    className: 'btn btn-default',
                                                    closeModal: true,
                                                },
                                                roll: {
                                                    text: "Separados",
                                                    value: 2,
                                                    className: 'btn btn-warning',
                                                    visible: true,
                                                    closeModal: true
                                                },
                                                confirm: {
                                                    text: 'Juntos',
                                                    value: 1,
                                                    visible: true,
                                                    className: 'btn btn-warning',
                                                    closeModal: true
                                                }
                                            }
                                        }).then(function(tipo){
                                            if(tipo == 1 || tipo == 2){
                                                if(tipo == 1){
                                                    CargarModal(1,inicio,fin);
                                                }else{
                                                    CargarModal(2,inicio,fin);
                                                }
                                            }
                                        });
                                    }
                                });
                            }else{
                                var tableSolicitantes = '';
                                $.each(data, function(index,element){
                                    tableSolicitantes +='<tr>';
                                    if(element.tipo_persona_id == 1){
                                        tableSolicitantes +='<td>'+element.nombre+' '+element.primer_apellido+' '+(element.segundo_apellido|| "")+'</td>';
                                    }else{
                                        tableSolicitantes +='<td>'+element.nombre_comercial+'</td>';
                                    }
                                    tableSolicitantes += '  <td>';
                                    tableSolicitantes += '      <div class="col-md-12">';
                                    tableSolicitantes += '          <span class="text-muted m-l-5 m-r-20" for="checkCorreo'+element.id+'">Proporcionar accesos</span>';
                                    tableSolicitantes += '          <input type="checkbox" class="checkCorreo" data-id="'+element.id+'" checked="checked" id="checkCorreo'+element.id+'" name="checkCorreo'+element.id+'" onclick="checkCorreo('+element.id+')"/>';
                                    tableSolicitantes += '      </div>';
                                    tableSolicitantes += '  </td>';
                                    tableSolicitantes += '  <td>';
                                    tableSolicitantes += '      <input type="text" class="form-control" disabled="disabled" id="correoValidar'+element.id+'">';
                                    tableSolicitantes += '  </td>';
                                    tableSolicitantes +='</tr>';
                                });
                                $("#tableSolicitantesCorreo tbody").html(tableSolicitantes);
                                $("#modal-registro-correos").modal("show");
                            }
                        }catch(error){
                            console.log(error);
                        }
                    }
                });
                
                
                
//                swal({
//                    title: '¿Las partes concilian en la misma sala?',
//                    text: 'Al oprimir aceptar se asignará solo un conciliador y una sola sala para solicitante y citado',
//                    icon: 'warning',
//                    buttons: {
//                        cancel: {
//                            text: 'Separados',
//                            value: null,
//                            visible: true,
//                            className: 'btn btn-default',
//                            closeModal: true,
//                        },
//                        confirm: {
//                            text: 'Aceptar',
//                            value: true,
//                            visible: true,
//                            className: 'btn btn-warning',
//                            closeModal: true
//                        }
//                    }
//                }).then(function(isConfirm){
//                    if(isConfirm){
//                        CargarModal(1,inicio,fin);
//                    }else{
//                        CargarModal(2,inicio,fin);
//                    }
//                });
            }
            function CargarModal(aux,inicio,fin){
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
//                $("#lableFechaInicio").html(inicio);
                $("#modal-asignar").modal("show");
            }
            getConciliadores = function(fechaInicio,fechaFin){
                $.ajax({
                    url:"/audiencia/ConciliadoresDisponiblesCentral",
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
                    url:"/audiencia/SalasDisponiblesCentral",
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
                    $.ajax({
                        url:"/audiencia/calendarizarCentral",
                        type:"POST",
                        data:{
                            fecha_audiencia:$("#fecha_audiencia").val(),
                            hora_inicio:$("#hora_inicio").val(),
                            hora_fin:$("#hora_fin").val(),
                            tipoAsignacion:$("#tipoAsignacion").val(),
                            asignacion:validacion.arrayEnvio,
                            tipo_notificacion_id:validacion.tipo_notificacion_id,
                            solicitud_id:$("#solicitud_id").val(),
                            _token:"{{ csrf_token() }}"
                        },
                        dataType:"json",
                        success:function(data){
                            try{
                                console.log(data);
                                if(data != null && data != ""){
                                    $("#modal-asignar").modal("hide");
                                    $("#spanFolio").text(data.folio+"/"+data.anio);
                                    $("#spanFechaAudiencia").text(dateFormat(data.fecha_audiencia,4));
                                    $("#spanHoraInicio").text(data.hora_inicio);
                                    $("#spanHoraFin").text(data.hora_fin);
                                    var table="";
                                    if(data.multiple){
                                        $.each(data.conciliadores_audiencias,function(index,element){
                                            table +='<tr>';
                                            if(element.solicitante){
                                                table +='   <td>Solicitante(s)</td>';
                                            }else{
                                                table +='   <td>Citado(s)</td>';
                                            }
                                            table +='   <td>'+element.conciliador.persona.nombre+' '+element.conciliador.persona.primer_apellido+' '+element.conciliador.persona.segundo_apellido+'</td>';
                                            $.each(data.salas_audiencias,function(index2,element2){
                                                if(element2.solicitante == element.solicitante){
                                                    table +='<td>'+element2.sala.sala+'</td>';
                                                }
                                            });
                                            table +='</tr>';
                                        });
                                    }else{
                                        table +='<tr>';
                                        table +='   <td>Solicitante(s) y citado(s)</td>';
                                        table +='   <td>'+data.conciliadores_audiencias[0].conciliador.persona.nombre+' '+data.conciliadores_audiencias[0].conciliador.persona.primer_apellido+' '+data.conciliadores_audiencias[0].conciliador.persona.segundo_apellido+'</td>';
                                        table +='   <td>'+data.salas_audiencias[0].sala.sala+'</td>';
                                        table +='</tr>';
                                    }
                                    $("#tableAudienciaSuccess tbody").html(table);
                                    $("#modalRatificacion").modal("hide");
                                    $("#modal-ratificacion-success").modal({backdrop: 'static', keyboard: false});
                                    swal({
                                        title: 'Correcto',
                                        text: 'Solicitud ratificada correctamente',
                                        icon: 'success'
                                    });
                                }else{
                                    swal({
                                        title: 'Algo salió mal',
                                        text: 'No se pudo ratificar la audiencia',
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
                        title: 'Algo salió mal',
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
                if($("#aradioNotificacionA1").is(":checked")){
                    var tipo_notificacion_id=1;
                }else if($("#aradioNotificacionB1").is(":checked")){
                    var tipo_notificacion_id=2;
                }else if($("#aradioNotificacionB2").is(":checked")){
                   var tipo_notificacion_id=3;
                }else{
                    var tipo_notificacion_id = null;
                    msg = "Indica el tipo de notificación para los citados";
                    error = true;
                }
                var array = [];
                array.error=error;
                array.msg=msg;
                array.arrayEnvio=arrayEnvio;
                array.tipo_notificacion_id=tipo_notificacion_id;
                return array;
            }
        </script>
@endpush
