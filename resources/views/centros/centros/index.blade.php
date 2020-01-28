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
                <a href="{!! route('centros.create') !!}" class="btn btn-info"><i class="fa fa-plus-circle"></i> Nuevo centro</a>
            </div>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            @include('centros.centros._list')
        </div>
    </div>

@endsection
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
                    <input type="hidden" id="id" name="id">
                    <div class="col-md-12 row">
                        <div class="col-md-2">
                            <span class="text-muted m-l-5 m-r-20" for='switch1'>Lunes</span>
                        </div>
                        <div class="col-md-2">
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
                            <!--<input type="hidden" id=""/>-->
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
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#data-table-default').DataTable({
                responsive: true
            });
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
        });
        function limpiarModal(){
            $("input[data-change='switchDia']").each(function() {
                if($(this).is(":checked")){
                    $(this).trigger('click');
                    $(this).attr('data-id',"");
                }
            });
            $(".horas").val("");
            $(".horas").prop("disabled",true);
        }
        function getCentroDisponibilidad(id){
            $.ajax({
                url:"/api/centros/disponibilidades",
                type:"POST",
                dataType:"json",
                data:{
                    id:id
                },
                success:function(data){
                    console.log(data);
                    if(data != null){
                        $("#id").val(data.id);
                        $("#nombreCentro").text(data.nombre);
                        limpiarModal();
                        $.each(data.disponibilidades,function(index,data){
                            var elm = $("#switch"+data.dia);
                            $(elm).trigger('click');
                            $(elm).attr('data-id',data.id);
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
                    url:"/api/centros/disponibilidad",
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
                    title: 'Error',
                    text: 'Verifica que los campos requeridos sean correctos y que al menos hay un día laborable',
                    icon: 'error'
                });
            }
        });
        function validarCampos(){
            var error=false;
            var count = 0;
            var datos = new Array();
            $("input[data-change='switchDia']").each(function() {
                if($(this).is(":checked")){
                    datos.push({
                        dia:$(this).val(),
                        disponibilidad_id:$(this).data('id'),
                        hora_inicio:$(this).parent().next().children().next().val(),
                        hora_fin:$(this).parent().next().next().children().next().val()
                    });
                    count++;
                    if($(this).parent().next().children().next().val() == ""){
                        error=true;
                    }
                    if($(this).parent().next().next().children().next().val() == ""){
                        error=true;
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
            return arreglo;
        }
    </script>
@endpush
