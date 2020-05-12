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
                    - Selecciona el conciliador y la sala donde se celebrará la audiencia
                </div>
                <div id="divAsignarUno">
                    <div class="col-md-12 row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="conciliador_id" class="col-sm-6 control-label">Conciliador</label>
                                <div class="col-sm-10">
                                    <select id="conciliador_id" class="form-control">
                                        <option value="">-- Selecciona un centro</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sala_id" class="col-sm-6 control-label">Sala</label>
                                <div class="col-sm-10">
                                    <select id="sala_id" class="form-control">
                                        <option value="">-- Selecciona un centro</option>
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
                                                    <option value="">-- Selecciona un centro</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="sala_solicitante_id" class="col-sm-6 control-label">Sala</label>
                                            <div class="col-sm-10">
                                                <select id="sala_solicitante_id" class="form-control">
                                                    <option value="">-- Selecciona un centro</option>
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
                                    <h4 class="panel-title">Solicitado</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="conciliador_solicitado_id" class="col-sm-6 control-label">Conciliador</label>
                                            <div class="col-sm-10">
                                                <select id="conciliador_solicitado_id" class="form-control">
                                                    <option value="">-- Selecciona un centro</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="sala_solicitado_id" class="col-sm-6 control-label">Sala</label>
                                            <div class="col-sm-10">
                                                <select id="sala_solicitado_id" class="form-control">
                                                    <option value="">-- Selecciona un centro</option>
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
                    <button class="btn btn-primary btn-sm m-l-5" id="btnGuardar"><i class="fa fa-save"></i> Guardar</button>
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
                    url:"/api/audiencia/getCalendario",
                    type:"POST",
                    data:{
                        centro_id:1
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
                    text: 'Al oprimir aceptar se asignará solo un consiliador y una sola sala para solicitante y solicitado',
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
                $("#modal-asignar").modal("show");
            }
            getConciliadores = function(fechaInicio,fechaFin){
                $.ajax({
                    url:"/api/audiencia/ConciliadoresDisponibles",
                    type:"POST",
                    data:{
                        fechaInicio:fechaInicio,
                        fechaFin:fechaFin,
                        centro_id:1
                    },
                    dataType:"json",
                    success:function(data){
                        if(data != null && data != ""){
                            $("#conciliador_id,#conciliador_solicitado_id,#conciliador_solicitante_id").html("<option value=''>-- Selecciona un centro</option>");
                            $.each(data,function(index,element){
                                $("#conciliador_id,#conciliador_solicitado_id,#conciliador_solicitante_id").append("<option value='"+element.id+"'>"+element.persona.nombre+" "+element.persona.primer_apellido+" "+element.persona.segundo_apellido+"</option>");
                            });
                        }else{
                            $("#conciliador_id,#conciliador_solicitado_id,#conciliador_solicitante_id").html("<option value=''>-- Selecciona un centro</option>");
                        }
                        $("#conciliador_id,#conciliador_solicitado_id,#conciliador_solicitante_id").select2();
                    }
                });
            };
            function getSalas(fechaInicio,fechaFin){
                $.ajax({
                    url:"/api/audiencia/SalasDisponibles",
                    type:"POST",
                    data:{
                        fechaInicio:fechaInicio,
                        fechaFin:fechaFin,
                        centro_id:1
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
            $("#btnGuardar").on("click",function(){
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
                        var url = '/api/audiencia/nuevaAudiencia';
                    }else{
                        var url = '/api/audiencia/calendarizar';
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
                            nuevaCalendarizacion:'S',
                            listaRelaciones:listaRelaciones,
                            audiencia_id:$("#audiencia_id").val()
                        },
                        dataType:"json",
                        success:function(data){
                            console.log(data);
                            if(data != null && data != ""){
                                if(origen == 'audiencias'){
                                    window.location.href = "audiencias/"+data.id+"/edit";
                                }else{
                                    window.location.href = "{{ route('audiencias.index')}}";
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
                var array = [];
                array.error=error;
                array.msg=msg;
                array.arrayEnvio=arrayEnvio;
                return array;
            }
        </script>
@endpush
