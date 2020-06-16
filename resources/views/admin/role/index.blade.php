@extends('layouts.default', ['paceTop' => true])

@section('title', 'Permisos')

@include('includes.component.datatables')

@section('content')

    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item active">Roles</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Administrar roles <small>Listado de permisos</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Listado de roles</h4>
            <div class="panel-heading-btn">
                <a href="{!! route('roles.create') !!}" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Nuevo rol</a>
            </div>
        </div>

        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            @include('admin.role._list')
        </div>
    </div>
<div class="modal" id="modalPermisos" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Permisos de <span id='nombre_rol'></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <table class="table table-hover table-borderless" id="table-roles" style="text-align: center">
                    <thead>
                        <tr>
                            <th class="with-checkbox">Permiso</th>
                            <th>Descripción</th>
                            <th>Eliminar</th>
                        </tr>
                    </thead>
                    <tbody id='tbodyPermisos'>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id='role_id'>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#data-table-default').DataTable({responsive: true,language: {url: "/assets/plugins/datatables.net/dataTable.es.json"}});
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
        function getPermisos(id,nombre){
            $.ajax({
                url:"/roles/permisos/"+id,
                type:"GET",
                dataType:"json",
                async:true,
                success:function(data){
                    if(data != null){
                        var table="";
                        $.each(data,function(index,element){
                            table +='<tr>';
                            table +='   <td>'+element.name+'</td>';
                            table +='   <td>'+element.description+'</td>';
                            table +='   <td>';
                            table +='       <a class="btn btn-xs btn-primary" onclick="eliminarPermiso(\''+element.name+'\')">';
                            table +='           <i class="fa fa-edit text-light"></i>';
                            table +='       </a>';
                            table +='   </td>';
                            table +='</tr>';
                        });
                        $("#role_id").val(id);
                        $("#nombre_rol").text(nombre);
                        $("#tbodyPermisos").html(table);
                        $("#modalPermisos").modal("show");
                    }
                }
            });
        }
        function eliminarPermiso(name){
            swal({
                title: '¿Está seguro?',
                text: 'Al oprimir el botón de aceptar se eliminará el permiso',
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
                        url:"/roles/permisos/"+$("#role_id").val(),
                        type:"PUT",
                        dataType:"json",
                        async:true,
                        data:{
                            _token: "{{ csrf_token() }}",
                            permiso:name
                        },
                        success:function(data){
                            if(data != null){
                                var table="";
                                $.each(data,function(index,element){
                                    table +='<tr>';
                                    table +='   <td>'+element.name+'</td>';
                                    table +='   <td>'+element.description+'</td>';
                                    table +='   <td>';
                                    table +='       <a class="btn btn-xs btn-primary" onclick="eliminarPermiso('+element.name+')">';
                                    table +='           <i class="fa fa-edit text-light"></i>';
                                    table +='       </a>';
                                    table +='   </td>';
                                    table +='</tr>';
                                });
//                                $("#nombre_rol").text(nombre);
                                $("#tbodyPermisos").html(table);
//                                $("#modalPermisos").modal("show");
                            }
                        }
                    });
                }
            });
            return false;
        }
    </script>
@endpush
