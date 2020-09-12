@extends('layouts.default', ['paceTop' => true])

@section('title', 'Solicitudes')

@include('includes.component.datatables')
@include('includes.component.pickers')

@section('content')

    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{!! route("solicitudes.index") !!}">Solicitudes</a></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="h2">Administrar solicitudes <small>Listado de solicitudes</small></h1>
    <hr class="red">
    <!-- end page-header -->
    <!-- begin panel -->
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="">
            <div class="panel-heading-btn">
                <button class="btn btn-primary pull-right" onclick="location.href='{{ route('solicitudes.create')  }}'" > <i class="fa fa-plus-circle"></i> Nueva solicitud</button>
            </div>
        </div>
        <div id="divFilters" class="col-md-12 row" style="display: none">
            <div class="col-md-4">
                <input class="form-control filtros" id="curp" placeholder="CURP" type="text" value="">
                <p class="help-block needed">CURP</p>
            </div>
            <div class="col-md-4">
                <input class="form-control filtros" id="nombre" placeholder="Nombre completo" type="text" value="">
                <p class="help-block needed">Nombre</p>
            </div>
            <div class="col-md-4">
                <input class="form-control filtros" id="folio" placeholder="Folio" type="text" value="">
                <p class="help-block needed">Folio</p>
            </div>
            <div class="col-md-4">
                <input class="form-control filtros" id="Expediente" placeholder="Folio del Expediente" type="text" value="">
                <p class="help-block needed">Expediente</p>
            </div>
            <div class="col-md-4">
                <input class="form-control filtros" id="anio" placeholder="A&ntilde;o" type="text" value="">
                <p class="help-block needed">A&ntilde;o</p>
            </div>
            <div class="col-md-4">
                <input class="form-control date filtros" id="fechaRatificacion" placeholder="Fecha de ratificacion" type="text" value="">
                <p class="help-block needed">Fecha de ratificaci&oacute;n</p>
            </div>
            <div class="col-md-4">
                <input class="form-control date filtros" id="fechaRecepcion" placeholder="Fecha de recepcion" type="text" value="">
                <p class="help-block needed">Fecha de recepci&oacute;n</p>
            </div>
            <div class="col-md-4">
                <input class="form-control date filtros" id="fechaConflicto" placeholder="Fecha de conflicto" type="text" value="">
                <p class="help-block needed">Fecha de conflicto</p>
            </div>
            <div class="col-md-4">
                {!! Form::select('estatus_solicitud_id', isset($estatus_solicitudes) ? $estatus_solicitudes : [] , null, ['id'=>'estatus_solicitud_id','placeholder' => 'Seleccione una opción', 'class' => 'form-control catSelect filtros']);  !!}
                {!! $errors->first('estatus_solicitud_id', '<span class=text-danger>:message</span>') !!}
                <p class="help-block needed">Estatus de la solicitud</p>
            </div>
            <div class="col-md-4">
                <button class="btn btn-danger" type="button" id="limpiarFiltros" > <i class="fa fa-eraser"></i> Limpiar filtros</button>
            </div>
        
        </div>
        <div>
            
        </div>
        <div style="float: left;">
            <label class="col-md-12"> Filtros</label>
            <button class="btn btn-primary pull-right m-2" onclick="filtros()">Mas filtros</button>
            <button class="btn btn-primary pull-right m-2" onclick="$('#estatus_solicitud_id').val(3).trigger('change');" >Terminadas</button>
            <button class="btn btn-primary pull-right m-2" onclick="$('#estatus_solicitud_id').val(2).trigger('change');">Ratificadas</button>
            <button class="btn btn-primary pull-right m-2" onclick="$('#estatus_solicitud_id').val(1).trigger('change');">Sin Ratificar</button>
            <button class="btn btn-primary pull-right m-2" onclick="$('#estatus_solicitud_id').val('').trigger('change');">Todas</button>
        </div>
        <br>
        <br>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            @include('expediente.solicitudes._list')
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(".date").datetimepicker({format:"DD/MM/YYYY",locale:'es'});
//        $.datetimepicker.setLocale('es');
        $('#solicitantefechaNacimiento').datetimepicker({useCurrent: false,format:'DD/MM/YYYY HH:mm'});
        $(document).ready(function() {


                $('#data-table-default').DataTable({
                    responsive: true
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


        });
        function filtros(){
            $("#divFilters").toggle();
        }
    </script>
@endpush
