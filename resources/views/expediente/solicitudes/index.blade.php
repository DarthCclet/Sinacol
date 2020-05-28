@extends('layouts.default', ['paceTop' => true])

@section('title', 'solicitudes')

@include('includes.component.datatables')
@include('includes.component.pickers')

@section('content')
    
    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="">Home</a></li>
        <li class="breadcrumb-item"><a href="{!! route("solicitudes.index") !!}">Solicitudes</a></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Administrar solicitudes <small>Listado de solicitudes</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Listado de solicitudes</h4>
            <div class="panel-heading-btn">
                <button class="btn btn-info" onclick="location.href='{{ route('solicitudes.create')  }}'" > <i class="fa fa-plus-circle"></i> Nuevo</button>
            </div>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            @include('expediente.solicitudes._list')
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(".date").datetimepicker({format:"DD/MM/YYYY"});
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
    </script>
@endpush
