@extends('layouts.default', ['paceTop' => true])

@section('title', 'Centros de conciliación')

@include('includes.component.datatables')
@push('css')
<link href="/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.css" rel="stylesheet" />
	<link href="/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css" rel="stylesheet" />
	<link href="/assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" />
	<link href="/assets/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet" />
	<link href="/assets/plugins/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet" />

	<link href="/assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
	<link href="/assets/plugins/abpetkov-powerange/dist/powerange.min.css" rel="stylesheet" />
@endpush

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
                <div class="col-md-12 row">
                    <div class="col-md-2">
                        <span class="text-muted m-l-5 m-r-20" for='switch1'>Lunes</span>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" data-render="switchery" data-theme="default" data-change="switchDia" id="switch1" name='switch1'/>
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
                        <input type="checkbox" data-render="switchery" data-theme="default" data-change="switchDia" id="switch2" name='switch2'/>
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
                        <input type="checkbox" data-render="switchery" data-theme="default" data-change="switchDia" id="switch3" name='switch3'/>
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
                        <input type="checkbox" data-render="switchery" data-theme="default" data-change="switchDia" id="switch4" name='switch4'/>
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
                        <input type="checkbox" data-render="switchery" data-theme="default" data-change="switchDia" id="switch5" name='switch15'/>
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
                        <input type="checkbox" data-render="switchery" data-theme="default" data-change="switchDia" id="switch6" name='switch6'/>
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
                <hr>
            </div>
            <div class="modal-footer">
                <a class="btn btn-white" data-dismiss="modal">Close</a>
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
            $(".disponibilidad").on("click",function(){
                $("#modal-disponinbilidad").modal("show");
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
            $(".horas").val("");
            $(".horas").prop("disabled",true);
        }
    </script>
@endpush
@push('scripts')
	<script src="/assets/plugins/moment/moment.js"></script>
	<script src="/assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.js"></script>
	<script src="/assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
	<script src="/assets/plugins/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
        
        
	<script src="/assets/plugins/switchery/switchery.min.js"></script>
	<script src="/assets/plugins/abpetkov-powerange/dist/powerange.min.js"></script>
	<script src="/assets/js/demo/form-slider-switcher.demo.js"></script>
@endpush
