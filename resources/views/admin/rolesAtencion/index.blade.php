@extends('layouts.default', ['paceTop' => true])

@section('title', 'Roles Conciliadores')

@include('includes.component.datatables')

@section('content')

    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item active">Roles para conciliadores</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Administrar roles de conciliadores <small>Listado de roles</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Listado de roles</h4>
            <div class="panel-heading-btn">
                <a href="{!! route('roles-atencion.create') !!}" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Nuevo rol</a>
            </div>
        </div>

        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            @include('admin.rolesAtencion._list')
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#data-table-default').DataTable({paging: false,"info":false,responsive: true,language: {url: "/assets/plugins/datatables.net/dataTable.es.json"}});
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
