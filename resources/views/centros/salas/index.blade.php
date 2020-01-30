@extends('layouts.default', ['paceTop' => true])

@section('title', 'Salas de Audiencias')

@include('includes.component.datatables')

@section('content')

    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:;">Tables</a></li>
        <li class="breadcrumb-item active">Managed Tables</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Administrar salas de audiencias <small>Listado de salas</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Listado de salas</h4>
            <div class="panel-heading-btn">
                <a href="{!! route('salas.create') !!}" class="btn btn-info"><i class="fa fa-plus-circle"></i> Nuevo</a>
            </div>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            @include('centros.salas._list')
        </div>
    </div>

@endsection

@push('scripts')
    <script>
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
