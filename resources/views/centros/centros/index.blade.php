@extends('layouts.default', ['paceTop' => true])

@section('title', 'Centros de conciliación')

@include('includes.component.datatables')
@include('includes.component.pickers')

@section('content')

    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item active"><a href="javascript:;">Centros</a></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Administrar centros de conciliación <small>Listado de centros</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Listado de centros</h4>
            <div class="panel-heading-btn">
                <a href="{!! route('centros.create') !!}" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Nuevo centro</a>
            </div>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            @include('centros.centros._list')
        </div>
    </div>


    <!-- inicio Modal de disponibilidad-->
    <div class="modal" id="modal-disponinbilidad" aria-hidden="true" style="display:none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Disponibilidad de <span id='nombreCentro'></span></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-muted">
                        - Activa el día que será laborable para el centro.<br>
                        - Coloca la hora de inicio y fin de servicios en el centro.<br>
                        - Da clik en guardar para registrar los cambios
                    </div>
                    <form id="formDisponibilidad">
                        <div class="col-md-12 row">
                            <div class="col-md-2">
                                <span class="text-muted m-l-5 m-r-20" for='switch1'>Lunes</span>
                            </div>
                            <div class="col-md-2">
                                <input type="hidden" class="hddDisponibilidad"/>
                                <input type="checkbox" value="1" data-id="" data-render="switchery" data-theme="default" data-change="switchDia" id="switch1" name='switch1'/>
                            </div>
                            <div class="col-md-4">
                                <label for="hora_inicioLunes" class="control-label">Hora de inicio</label>
                                <input type="text" class="form-control horas" id="hora_inicioLunes"/>
                            </div>
                            <div class="col-md-4">
                                <label for="hora_finLunes" class="control-label">Hora fin</label>
                                <input type="text" class="form-control horas" id="hora_finLunes"/>
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-12 row">
                            <div class="col-md-2">
                                <span class="text-muted m-l-5 m-r-20" for='switch2'>Martes</span>
                            </div>
                            <div class="col-md-2">
                                <input type="hidden" class="hddDisponibilidad"/>
                                <input type="checkbox" value="2" data-id="" data-render="switchery" data-theme="default" data-change="switchDia" id="switch2" name='switch2'/>
                            </div>
                            <div class="col-md-4">
                                <label for="hora_inicioMartes" class="control-label">Hora de inicio</label>
                                <input type="text" class="form-control horas" id="hora_inicioMartes"/>
                            </div>
                            <div class="col-md-4">
                                <label for="hora_finMartes" class="control-label">Hora fin</label>
                                <input type="text" class="form-control horas" id="hora_finMartes"/>
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-12 row">
                            <div class="col-md-2">
                                <span class="text-muted m-l-5 m-r-20" for='switch3'>Miercoles</span>
                            </div>
                            <div class="col-md-2">
                                <input type="hidden" class="hddDisponibilidad"/>
                                <input type="checkbox" value="3" data-id="" data-render="switchery" data-theme="default" data-change="switchDia" id="switch3" name='switch3'/>
                            </div>
                            <div class="col-md-4">
                                <label for="hora_inicioMartes" class="control-label">Hora de inicio</label>
                                <input type="text" class="form-control horas" id="hora_inicioMiercoles"/>
                            </div>
                            <div class="col-md-4">
                                <label for="hora_finMiercoles" class="control-label">Hora fin</label>
                                <input type="text" class="form-control horas" id="hora_finMiercoles"/>
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-12 row">
                            <div class="col-md-2">
                                <span class="text-muted m-l-5 m-r-20" for='switch4'>Jueves</span>
                            </div>
                            <div class="col-md-2">
                                <input type="hidden" class="hddDisponibilidad"/>
                                <input type="checkbox" value="4" data-id="" data-render="switchery" data-theme="default" data-change="switchDia" id="switch4" name='switch4'/>
                            </div>
                            <div class="col-md-4">
                                <label for="hora_inicioJueves" class="control-label">Hora de inicio</label>
                                <input type="text" class="form-control horas" id="hora_inicioJueves"/>
                            </div>
                            <div class="col-md-4">
                                <label for="hora_finJueves" class="control-label">Hora fin</label>
                                <input type="text" class="form-control horas" id="hora_finJueves"/>
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-12 row">
                            <div class="col-md-2">
                                <span class="text-muted m-l-5 m-r-20" for='switch5'>Viernes</span>
                            </div>
                            <div class="col-md-2">
                                <input type="hidden" class="hddDisponibilidad"/>
                                <input type="checkbox" value="5" data-id="" data-render="switchery" data-theme="default" data-change="switchDia" id="switch5" name='switch15'/>
                            </div>
                            <div class="col-md-4">
                                <label for="hora_inicioViernes" class="control-label">Hora de inicio</label>
                                <input type="text" class="form-control horas" id="hora_inicioViernes"/>
                            </div>
                            <div class="col-md-4">
                                <label for="hora_finViernes" class="control-label">Hora fin</label>
                                <input type="text" class="form-control horas" id="hora_finViernes"/>
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-12 row">
                            <div class="col-md-2">
                                <span class="text-muted m-l-5 m-r-20" for='switch6'>Sabado</span>
                            </div>
                            <div class="col-md-2">
                                <input type="hidden" class="hddDisponibilidad"/>
                                <input type="checkbox" value="6" data-id="" data-render="switchery" data-theme="default" data-change="switchDia" id="switch6" name='switch6'/>
                            </div>
                            <div class="col-md-4">
                                <label for="hora_inicioSabado" class="control-label">Hora de inicio</label>
                                <input type="text" class="form-control horas" id="hora_inicioSabado"/>
                            </div>
                            <div class="col-md-4">
                                <label for="hora_finSabado" class="control-label">Hora fin</label>
                                <input type="text" class="form-control horas" id="hora_finSabado"/>
                            </div>
                        </div>
                    </form>
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
    <!-- Fin Modal de disponibilidad-->

    <!-- inicio Modal de incidencias-->
    <div class="modal" id="modal-incidencias" aria-hidden="true" style="display:none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Incidencias de <span id='nombreCentroIncidencia'></span></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div id="divConsultaIncidencias">
                        <div id="table_incidencias"></div>
                    </div>
                    <div id="divRegistroIncidencias">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Justificación</label>
                                <input type="text" id="justificacion" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-12 row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha y hora de inicio</label>
                                    <input type="text" id="fecha_inicio" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha y hora fin</label>
                                    <input type="text" id="fecha_fin" class="form-control" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="text-right">
                        <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-sign-out"></i> Cerrar</a>
                        <button class="btn btn-primary btn-sm m-l-5" id="btnNuevaIncidencia"><i class="fa fa-plus-circle"></i> Nueva incidencia</button>
                        <button class="btn btn-primary btn-sm m-l-5" id="btnRegresarIncidencia"><i class="fa fa-arrow-left"></i> Regresar</button>
                        <button class="btn btn-primary btn-sm m-l-5" id="btnGuardarIncidencia"><i class="fa fa-save"></i> Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin Modal de disponibilidad-->
    <input type="hidden" id="id" name="id">
    <input type="hidden" id="incidencia_id" name="incidencia_id">

@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#data-table-default').DataTable({paging: false,"info":false,responsive: true,language: {url: "/assets/plugins/datatables.net/dataTable.es.json"}});
            /**
             * Funcion para permitir llenado de los dias disponibles
             */
            $(document).on("change",'[data-change="switchDia"]',function(){
                if($(this).is(":checked")){
                    $(this).parent().next().children().next().prop("disabled",false);
                    $(this).parent().next().next().children().next().prop("disabled",false);
                }else{
                    $(this).parent().next().children().next().prop("disabled",true);
                    $(this).parent().next().children().next().val("");
                    $(this).parent().next().next().children().next().prop("disabled",true);
                    $(this).parent().next().next().children().next().val("");
                }
            });
            $(".horas").datetimepicker({format:"HH:mm"});
            $('#fecha_fin,#fecha_inicio').datetimepicker({useCurrent: false,format:'DD/MM/YYYY'});
            $("#fecha_inicio").on("dp.change", function (e) {
                $('#fecha_fin').data("DateTimePicker").minDate(e.date);
            });
            $("#fecha_fin").on("dp.change", function (e) {
                $('#fecha_inicio').data("DateTimePicker").maxDate(e.date);
            });
            $('.btn-borrar').on('click', function (e) {
                let that = this;
                console.log('boton clic');
                e.preventDefault();
                swal({
                    title: '¿Está seguro?',
                    text: 'Al oprimir el botón de aceptar se eliminará el registro',
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
                            className: 'btn btn-warning',
                            closeModal: true
                        }
                    }
                }).then(function(isConfirm){
                    if(isConfirm){
                        $(that).closest('form').submit();
                    }
                });
                return false;
            });
            limpiarModal();
            $(".catSelect").select2({width: '100%'});
            $("#municipio").select2({width: '100%', tags: true});
        });
        function limpiarModal(){
            $("input[data-change='switchDia']").each(function() {
                if($(this).is(":checked")){
                    $(this).trigger('click');
                }
            });
            $(".hddDisponibilidad").val("");
            $(".horas").val("");
            $(".horas").prop("disabled",true);
            $(".horas").css("border-color","");
        }
        function limpiarModalIncidencia(){
            $("#incidencia_id").val("");
            $("#justificacion").val("").css("border-color","");
            $("#fecha_inicio").val("").css("border-color","");
            $("#fecha_fin").val("").css("border-color","");
        }
        function getCentroDisponibilidad(id){
            $.ajax({
                url:"/centros/disponibilidades",
                type:"POST",
                dataType:"json",
                async:false,
                data:{
                    id:id,
                    _token:"{{ csrf_token() }}"
                },
                success:function(data){
                    try{

                        console.log(data);
                        if(data != null){
                            $("#id").val(data.id);
                            $("#nombreCentro").text(data.nombre);
                            limpiarModal();
                            $.each(data.disponibilidades,function(index,data){
                                var elm = $("#switch"+data.dia);
                                $(elm).trigger('click');
                                $(elm).prev().val(data.id);
                                $(elm).parent().next().children().next().val(data.hora_inicio);
                                $(elm).parent().next().next().children().next().val(data.hora_fin);
                            });
                            $("#modal-disponinbilidad").modal("show");
                        }
                    }catch(error){
                        console.log(error);
                    }
                }
            });
        }
        function getCentroIncidencias(id){
            $.ajax({
                url:"/centros/disponibilidades",
                type:"POST",
                dataType:"json",
                async:false,
                data:{
                    id:id,
                    _token:"{{ csrf_token() }}"
                },
                success:function(data){
                    try{
                        console.log(data);
                        if(data != null){
                            $("#id").val(data.id);
                            $("#nombreCentroIncidencia").text(data.nombre);
                            limpiarModalIncidencia();
                            var table = `
                                <table id="data-table-incidencias" class="table table-striped table-bordered table-condensed table-td-valign-middle">
                                    <thead>
                                    <tr>
                                        <th class="text-nowrap">Justificación</th>
                                        <th class="text-nowrap">Fecha y hora de inicio</th>
                                        <th class="text-nowrap">Fecha y hora fin</th>
                                        <th class="text-nowrap all">Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                            `;
                            $.each(data.incidencias,function(index,data){
                                table +='<tr>';
                                table +='   <td>'+data.justificacion+'</td>';
                                table +='   <td>'+data.fecha_inicio+'</td>';
                                table +='   <td>'+data.fecha_fin+'</td>';
                                table +='   <td>';
                                table +='       <a class="btn btn-xs btn-primary incidencia" onclick="cargarIncidencia('+data.id+')">';
                                table +='           <i class="fa fa-edit"></i>';
                                table +='       </a>';
                                table +='       <a class="btn btn-xs btn-warning incidencia" onclick="eliminarIncidencia('+data.id+')">';
                                table +='           <i class="fa fa-trash"></i>';
                                table +='       </a>';
                                table +='   </td>';
                                table +='</tr>';
                            });
                            table +='</tbody>';
                            table +='</table>';
                            $("#table_incidencias").html(table);
                            $('#data-table-incidencias').DataTable();
                            cambiarDivIncidencias(1);
                            $("#modal-incidencias").modal("show");
                        }
                    }catch(error){
                        console.log(error);
                    }
                }
            });
        }
        $("#btnGuardar").on("click",function(){
            var validar = validarCampos();
            if(!validar.error){
                $.ajax({
                    url:"/centros/disponibilidad",
                    type:"POST",
                    dataType:"json",
                    data:{
                        id:$("#id").val(),
                        datos:validar.datos,
                        _token:"{{ csrf_token() }}"
                    },
                    success:function(data){
                        $("#modal-disponinbilidad").modal("hide");
                        swal({
                            title: 'Éxito',
                            text: 'Se guardarón los datos de la disponibilidad',
                            icon: 'success'
                        });
                    }
                });
            }else{
                swal({
                    title: 'Algo salió mal',
                    text: validar.errorMsg,
                    icon: 'warning'
                });
            }
        });
        function validarCampos(){
            var error=false;
            var errorMsg="";
            var count = 0;
            var datos = new Array();
            $(".horas").css("border-color","");
            $("input[data-change='switchDia']").each(function() {
                var hora_inicio = $(this).parent().next().children().next();
                var hora_fin = $(this).parent().next().next().children().next();
                if($(this).is(":checked")){
                    datos.push({
                        dia:$(this).val(),
                        disponibilidad_id:$(this).prev().val(),
                        hora_inicio:$(hora_inicio).val(),
                        hora_fin:$(hora_fin).val(),
                        borrar:false
                    });
                    count++;
                    if($(hora_inicio).val() == ""){
                        error=true;
                        errorMsg = "Indica la hora inicio";
                        $(hora_inicio).css("border-color","red");
                    }else{
                        if($(hora_fin).val() == ""){
                            error=true;
                            errorMsg = "Indica la hora fin";
                            $(hora_fin).css("border-color","red");
                        }else{
                            if($(hora_inicio).val() >= $(hora_fin).val()){
                                error=true;
                                errorMsg = "La hora de inicio no puede ser mayor o igual que la hora fin";
                                $(hora_inicio).css("border-color","red");
                                $(hora_fin).css("border-color","red");
                            }
                        }
                    }
                }else{
                    if($(this).prev().val() != ""){
                        count++;
                        datos.push({
                            disponibilidad_id:$(this).prev().val(),
                            borrar:true
                        });
                    }
                }
            });
            console.log(datos);
            if(count == 0){
                error=true;
            }
            var arreglo = new Array();
            arreglo.datos=datos;
            arreglo.error=error;
            arreglo.errorMsg=errorMsg;
            return arreglo;
        }
        function cambiarDivIncidencias(aux){
            if(aux == 1){
                $("#btnGuardarIncidencia").hide();
                $("#btnRegresarIncidencia").hide();
                $("#divRegistroIncidencias").hide();
                $("#btnNuevaIncidencia").show();
                $("#divConsultaIncidencias").show();
            }else{
                $("#btnGuardarIncidencia").show();
                $("#btnRegresarIncidencia").show();
                $("#divRegistroIncidencias").show();
                $("#btnNuevaIncidencia").hide();
                $("#divConsultaIncidencias").hide();

            }
        }
        $("#btnNuevaIncidencia").on("click",function(){
            limpiarModalIncidencia();
            cambiarDivIncidencias(2);
        });
        $("#btnRegresarIncidencia").on("click",function(){
            cambiarDivIncidencias(1);
        });
        $("#btnGuardarIncidencia").on("click",function(){
            var validacion = validarCamposIncidencia();
            console.log(validacion);
            if(!validacion.error){
                $.ajax({
                    url:"/centros/incidencias",
                    type:"POST",
                    dataType:"json",
                    data:{
                        id:$("#id").val(),
                        incidencia_id:$("#incidencia_id").val(),
                        justificacion:$("#justificacion").val(),
                        fecha_inicio:dateFormat($("#fecha_inicio").val()),
                        fecha_fin:dateFormat($("#fecha_fin").val()),
                        _token:"{{ csrf_token() }}"
                    },
                    success:function(data){
                        try{

                            getCentroIncidencias($("#id").val());
                            swal({
                                title: 'Éxito',
                                text: 'Se guardarón los datos de la disponibilidad',
                                icon: 'success'
                            });
                        }catch(error){
                            console.log(error);
                        }
                    }
                });
            }else{
                swal({
                    title: 'Algo salió mal',
                    text: validacion.msgError,
                    icon: 'warning'
                });
            }
        });
        function validarCamposIncidencia(){
            var error=false;
            var msgError='';
            if($("#justificacion").val() == ""){
                $("#justificacion").css("border-color","red");
                error = true;
                msgError = "Agrega una justificación";
            }
            if($("#fecha_inicio").val() == ""){
                $("#fecha_inicio").css("border-color","red");
                error = true;
                msgError = "Agrega una fecha y hora de inicio";
            }
            if($("#fecha_fin").val() == ""){
                $("#fecha_fin").css("border-color","red");
                error = true;
                msgError = "Agrega una fecha y hora fin";
            }
            return {error:error,msgError:msgError};
        }
        function cargarIncidencia(id){
            $.ajax({
                url:"/incidencia/"+id,
                type:"GET",
                dataType:"json",
                async:true,
                success:function(data){
                    try{
                        if(data != null && data != ""){
                            limpiarModalIncidencia();
                            $("#incidencia_id").val(data.id);
                            $("#justificacion").val(data.justificacion);
                            $("#fecha_inicio").val(data.fecha_inicio);
                            $("#fecha_fin").val(data.fecha_fin);
                            cambiarDivIncidencias(2);
                        }else{
                            swal({
                                title: 'Algo salió mal',
                                text: 'No pudimos traer la información',
                                icon: 'warning'
                            });
                        }
                    }catch(error){
                        console.log(error);
                    }
                }
            });
        }
        function eliminarIncidencia(id){
            swal({
                title: '¿Está seguro?',
                text: 'Al oprimir el botón de aceptar se eliminará el registro',
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
                        className: 'btn btn-warning',
                        closeModal: true
                    }
                }
            }).then(function(isConfirm){
                if(isConfirm){
                    $.ajax({
                        url:"/incidencia/"+id,
                        type:"DELETE",
                        dataType:"json",
                        async:true,
                        data:{
                            _token:"{{ csrf_token() }}"
                        },
                        success:function(data){
                            try{

                                if(data != null && data != ""){
                                    getCentroIncidencias($("#id").val());
                                }else{
                                    swal({
                                        title: 'Algo salió mal',
                                        text: 'No pudimos traer la información',
                                        icon: 'warning'
                                    });
                                }
                            }catch(error){
                                console.log(error);
                            }
                        }
                    });
                }
            });
        }
    </script>
@endpush
