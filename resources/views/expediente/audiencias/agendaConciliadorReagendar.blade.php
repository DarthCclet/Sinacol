    <!-- begin vertical-box -->
    <div class="vertical-box">
    <!-- end event-list -->
    <!-- begin calendar -->
    <div id="calendario" class="vertical-box-column calendar"></div>
    <!-- end calendar -->
    </div>
    <!-- end vertical-box -->
<!-- inicio Modal de disponibilidad-->

<input type="hidden" id="fecha_audiencia">
<input type="hidden" id="hora_inicio">
<input type="hidden" id="hora_fin">
<input type="hidden" id="tipoAsignacion">
<!-- Fin Modal de disponibilidad-->
@push('scripts')
        <script>
            $(document).ready(function(){
                $.ajax({
                    url:"/getAudienciaConciliador",
                    type:"GET",
                    dataType:"json",
                    success:function(data){
                        construirCalendario(data);
                    }
                });
            });
            function construirCalendario(arregloGeneral){
                console.log(arregloGeneral);
                $('#external-events .fc-event').each(function() {
                    // store data so the calendar knows to render an event upon drop
                    $(this).data('event', {
                            title: $.trim($(this).text()), // use the element's text as the event title
                            stick: true // maintain when user navigates (see docs on the renderEvent method)
                    });
                });
                $('#calendario').fullCalendar({
                    header: {
                        left: 'month,agendaWeek',
                        center: 'title',
                        right: 'prev,today,next '
                    },
                    selectable: true,
                    selectHelper: true,
                    select: function(start, end,a,b) {
                        var ahora = new Date();
                        end=moment(end).add(1, 'hours').add(30,'minutes').format('Y-MM-DD HH:mm:ss');
                        console.log(end);
                        start=moment(start).format('Y-MM-DD HH:mm:ss');
                        var startVal = new Date(start);
                        if(startVal > ahora){ //validar si la fecha es mayor que hoy
                            if(b.type == "month"){ // si es la vista de mes, abrir la vista de semana
                                $('#calendario').fullCalendar("gotoDate",start);
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
                        $('#calendario').fullCalendar('unselect');
                    },
                    selectOverlap: function(event) {
                        return event.rendering !== 'background';
                    },
                    editable: false,
                    allDaySlot:false,
                    eventLimit: false,
                    events: arregloGeneral,
                    eventConstraint: "businessHours"
                });
            }
            function SolicitarAudiencia(inicio,fin){
                swal({
                    title: 'Advertencia',
                    text: 'Al oprimir aceptar se reagendará la audiencia y se creará un nuevo citatorio. ¿Estas seguro?',
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: 'No',
                            value: null,
                            visible: true,
                            className: 'btn btn-default',
                            closeModal: true,
                        },
                        confirm: {
                            text: 'Si',
                            value: true,
                            visible: true,
                            className: 'btn btn-warning',
                            closeModal: true
                        }
                    }
                }).then(function(isConfirm){
                    if(isConfirm){
                        CargarModal(1,inicio,fin);
                    }
                });
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
                getSalas(inicio,fin);
                $("#hora_inicio").val(inicio);
                $("#hora_fin").val(fin);
                $("#lableFechaInicio").html(inicio.substring(1, 10));
                $("#modal-reprogramacion").modal("hide");
                $("#modal-asignarAudiencia").modal("show");
            }
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
                        $("#sala_cambio_id").html("<option value=''>-- Selecciona una sala</option>");
                        if(data != null && data != ""){
                            $.each(data,function(index,element){
                                $("#sala_cambio_id").append("<option value='"+element.id+"'>"+element.sala+"</option>");
                            });
                        }
                        $("#sala_cambio_id").select2();
                    }
                });
            }
            $("#btnGuardarNuevaFecha").on("click",function(){
                if($("#sala_cambio_id option:selected").attr("value") != ""){
                    var listaRelaciones = [];
                    $.ajax({
                        url:'/audiencias/cambiar_fecha',
                        type:"POST",
                        data:{
                            fecha_audiencia:$("#hora_inicio").val().substring(0, 10),
                            hora_inicio:$("#hora_inicio").val(),
                            hora_fin:$("#hora_fin").val(),
                            audiencia_id:$("#audiencia_id").val(),
                            _token:"{{ csrf_token() }}"
                        },
                        dataType:"json",
                        success:function(data){
                            if(data != null && data != ""){
                                swal({
                                    title: 'Correcto',
                                    text: 'Se cambio la fecha de audiencia',
                                    icon: 'success'
                                });
                                window.location.reload();
                            }else{
                                swal({
                                    title: 'Algo salio mal',
                                    text: 'No se registro la audiencia',
                                    icon: 'warning'
                                });
                            }
                        },error: function(){
                            swal({
                                title: 'Error',
                                text: 'Algo salio mal al tratar de reagendar',
                                icon: 'warning'
                            });
                        }
                    });
                }else{
                    swal({
                        title: 'Algo salio mal',
                        text: 'Selecciona la sala dónde se realizará la audiencia',
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
                            msg = "Indica el tipo de notificación para los solicitados";
                            error = true;
                        }
                    });
                }
                var array = [];
                array.error=error;
                array.msg=msg;
                array.arrayEnvio=arrayEnvio;
                array.listaNotificaciones=listaNotificaciones;
                return array;
            }
        </script>
@endpush
