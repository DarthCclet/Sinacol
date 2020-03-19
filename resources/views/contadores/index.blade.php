@extends('layouts.default', ['paceTop' => true])

@section('title', 'Contadores')

@include('includes.component.datatables')
@include('includes.component.pickers')

@section('content')

    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item active"><a href="javascript:;">Contadores</a></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Administrar folio <small>Listado de contadores</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Listado de contadores</h4>
            <div class="panel-heading-btn">
                <a href="{!! route('contadores.create') !!}" class="btn btn-info"><i class="fa fa-plus-circle"></i> Nuevo</a>
            </div>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            @include('contadores._list')
        </div>
    </div>

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
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#data-table-default').DataTable({
                responsive: true
            });
            limpiarModal();
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
        function getContador(id){
            $.ajax({
                url:"/api/salas/disponibilidades",
                type:"POST",
                dataType:"json",
                async:false,
                data:{
                    id:id
                },
                success:function(data){
                    console.log(data);
                    if(data != null){
                        $("#id").val(data.id);
                        $("#nombreSala").text(data.nombre);
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
                }
            });
        }
        $("#btnGuardar").on("click",function(){
            var validar = validarCampos();
            if(!validar.error){
                $.ajax({
                    url:"/api/salas/disponibilidad",
                    type:"POST",
                    dataType:"json",
                    data:{
                        id:$("#id").val(),
                        datos:validar.datos
                    },
                    success:function(data){
                        $("#modal-disponinbilidad").modal("hide");
                        swal({
                            title: 'Exito',
                            text: 'Se guardarón los datos de la disponibilidad',
                            icon: 'success'
                        });
                    }
                });
            }else{
                swal({
                    title: 'Algo salio mal',
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
    </script>
@endpush
