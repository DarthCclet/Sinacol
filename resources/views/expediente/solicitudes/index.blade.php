@extends('layouts.default', ['paceTop' => true])

@section('title', 'Solicitudes')

@include('includes.component.datatables')
@include('includes.component.pickers')
@include('includes.component.calendar')
@include('includes.component.dropzone')

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
                <button class="btn btn-primary pull-right" onclick="nuevaSolicitud()" > <i class="fa fa-plus-circle"></i> Nueva solicitud</button>
            </div>
        </div>
        @if(auth()->user()->hasRole('Personal conciliador'))
        <div class="col-md-12">
            <a href="#" onclick="filtrarMisSolicitudes()" id="btnMisSol" class="btn btn-primary badge-pill btn-sm mb-2" title="Solo mostrar mis solicitudes asignadas">
                <span class="fa fa-unlink"></span> &nbsp; Mis solicitudes &nbsp;
                <span class="badge badge-pill btn-light" id="spanMisSol">0</span>
            </a>    
        </div>
        @endif
        <div id="divFilters" class="col-md-12 row" style="display: none">
            <input type="hidden" value="false" class="filtros" id="mis_solicitudes">
            <div class="col-md-4">
                <input class="form-control filtros" id="curp" placeholder="CURP" type="text" value="">
                <p class="help-block needed">CURP</p>
            </div>
            <div class="col-md-4">
                <input class="form-control filtros" id="nombre" placeholder="Nombre completo" type="text" value="">
                <p class="help-block needed">Nombre</p>
            </div>
            <div class="col-md-4">
                <input class="form-control filtros numero" id="folio" placeholder="Folio" type="text" value="">
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
            <button class="btn btn-primary pull-right m-2 estatus" id="estatus3" onclick="$('#estatus_solicitud_id').val(3).trigger('change');" >Concluidas</button>
            <button class="btn btn-primary pull-right m-2 estatus" id="estatus2" onclick="$('#estatus_solicitud_id').val(2).trigger('change');">Ratificadas</button>
            <button class="btn btn-primary pull-right m-2 estatus" id="estatus1" onclick="$('#estatus_solicitud_id').val(1).trigger('change');">Sin Ratificar</button>
            <button class="btn btn-primary pull-right m-2 estatus selectedButton" id="estatus" onclick="$('#estatus_solicitud_id').val('').trigger('change');">Todas</button>
        </div>
        <br>
        <br>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            @include('expediente.solicitudes._list')
        </div>
    </div>
    <div class="modal" id="modal-crear-solicitud" aria-hidden="true" style="display:none;">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Crear nueva Solicitud</h2>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <h4>Selecciona el tipo de solicitud que deseas capturar</h4>
                    <div class="col-md-12">
                        @role("Orientador Central")
                            <div class="col-md-8 offset-2" style="margin:1%;">
                                <a class="btn btn-primary btn-lg col-md-12" onclick="capturarSolicitud(3)" data-dismiss="modal" ><i class="fa fa-plus"></i> Patronal colectiva</a>
                            </div>
                            <div class="col-md-8 offset-2" style="margin:1%;">
                                <a class="btn btn-primary btn-lg col-md-12" onclick="capturarSolicitud(4)" data-dismiss="modal" ><i class="fa fa-plus"></i> Sindicato</a>
                            </div>
                        @else
                            <div class="col-md-8 offset-2" style="margin:1%;">
                                <a class="btn btn-primary btn-lg col-md-12 " onclick="capturarSolicitud(1)" data-dismiss="modal" ><i class="fa fa-plus"></i> Solicitud individual</a>
                            </div>
                            <div class="col-md-8 offset-2" style="margin:1%;">
                                <a class="btn btn-primary btn-lg col-md-12" onclick="capturarSolicitud(2)" data-dismiss="modal" ><i class="fa fa-plus"></i> Patronal individual</a>
                            </div>
                            <div class="col-md-8 offset-2" style="margin:1%;">
                                <a class="btn btn-primary btn-lg col-md-12" onclick="capturarSolicitud(3)" data-dismiss="modal" ><i class="fa fa-plus"></i> Patronal colectiva</a>
                            </div>
                            <div class="col-md-8 offset-2" style="margin:1%;">
                                <a class="btn btn-primary btn-lg col-md-12" onclick="capturarSolicitud(4)" data-dismiss="modal" ><i class="fa fa-plus"></i> Sindicato</a>
                            </div>
                        @endrole
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="text-right">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var mis_solicitudes = false;
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

        function nuevaSolicitud(){
            $("#modal-crear-solicitud").modal('show');
        }

        function capturarSolicitud(id){
            location.href='{{ route('solicitudes.create')  }}?solicitud='+id;
        }
    </script>
@endpush
