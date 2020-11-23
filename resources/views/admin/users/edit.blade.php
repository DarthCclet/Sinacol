@extends('layouts.default', ['paceTop' => true])

@section('title', 'Usuarios')

@include('includes.component.datatables')
@include('includes.component.pickers')

@section('content')

    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item"><a href="{!! route('users.index') !!}">Administración</a></li>
        <li class="breadcrumb-item active">Usuarios</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Administrar usuarios <small>Editar usuario</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
    <a href="{!! route('users.index') !!}" class="btn btn-primary btn-sm pull-right"><i class="fa fa-arrow-alt-circle-left"></i> Regresar</a>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a href="#default-tab-1" data-toggle="tab" class="nav-link active">
                <span class="d-sm-none">Us</span>
                <span class="d-sm-block d-none">Usuario</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#default-tab-2" data-toggle="tab" class="nav-link">
                <span class="d-sm-none">Rol</span>
                <span class="d-sm-block d-none">Roles</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#default-tab-3" data-toggle="tab" class="nav-link">
                <span class="d-sm-none">Cen</span>
                <span class="d-sm-block d-none">Centros</span>
            </a>
        </li>
    </ul>
    <div class="tab-content" style="background: #f2f3f4 !important;">
        <div class="tab-pane fade active show" id="default-tab-1">
            {!! Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'put'] ) !!}
                @include('admin.users._form')
                <div class="panel-footer text-right">
                    <a href="{!! route('users.index') !!}" class="btn btn-white btn-sm"><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5"><i class="fa fa-save"></i> Modificar</button>
                </div>
            {!! Form::close() !!}
        </div>
        <div class="tab-pane fade show " id="default-tab-2">
            <fieldset>
                <div class="row" id="form">
                    <div class="col-xl-10 offset-xl-1">
                        <div class="form-group row">
                            <div class="col-md-10">
                                <label for="apepat" class="control-label">Rol</label>
                                <select class="selectRol form-control" name="rol" id="rol">
                                    <option value="">-- Seleccione el rol</option>
                                    @foreach($roles as $rol)
                                    <option value="{{$rol->name}}">{{$rol->name}}</option>
                                    @endforeach
                                </select>
                                {!! $errors->first('rol', '<span class=text-danger>:message</span>') !!}
                                <p class="help-block">Rol que tendrá el usuario en el sistema</p>
                            </div>
                            <div class="col-md-2 d-flex align-items-center" >
                                <button class="btn btn-primary btn-sm pull-left" id="btnAgregarRol"><i class="fa fa-plus"></i> Agregar</button>
                            </div>
                            <div class="col-md-12">
                                <table class="table table-border table-striped">
                                    <thead>
                                        <tr>
                                            <th>Permiso</th>
                                            <th>Descripción</th>
                                            <th>Eliminar</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyRoles"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="tab-pane fade show" id="default-tab-3">
            <fieldset>
            <div class="row">
                <div class="col-xl-10 offset-xl-1">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="centro_user_id" class="control-label">Centro</label>
                                <div class="col-sm-10">
                                    {!! Form::select('centro_user_id', isset($centros) ? $centros : [] , null , ['id'=>'centro_user_id', 'required','placeholder' => 'Seleccione una opción', 'class' => 'form-control']);  !!}
                                    {!! $errors->first('centro_user_id', '<span class=text-danger>:message</span>') !!}
                                    <p class="help-block">Selecciona el centro Que se podrá asignar</p>
                                </div>
                                <div class="col-sm-2">
                                    <div class="panel-footer text-right">
                                        <button class="btn btn-primary btn-sm m-l-5" id="btnGuardarCentro"><i class="fa fa-plus"></i> Agregar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <table class="table table-bordered table-striped" id="table-centros" style="width: 100%">
                                <thead>
                                <th>Centro</th>
                                <th>Accion</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <input type="hidden" id="user_id">
@endsection
@push('scripts')
    <script>
        $("#user_id").val('{{ $user->id }}');
        $(document).ready(function() {
            $(".selectRol").select2({ placeholder: "Selecciona un rol" });
            $("#centro_user_id").select2({ placeholder: "Selecciona un centro" });
            getRoles();
            cargarCentros($("#user_id").val());
        });
        $("#btnAgregarRol").on("click",function(){
            if($("#rol").val() != ""){
                $.ajax({
                    url:"/usuario/roles",
                    type:"POST",
                    dataType:"json",
                    async:true,
                    data:{
                        _token: "{{ csrf_token() }}",
                        user_id:$("#user_id").val(),
                        rol:$("#rol").val()
                    },
                    success:function(data){
                        if(data != null){
                            CrearTabla(data);
                        }
                    }
                });
            }else{
                swal({title: 'Error',text: 'Deberás seleccionar un rol de la lista',icon: 'error'});
            }
        });
        function getRoles(){
            $.ajax({
                url:"/usuario/roles/"+$("#user_id").val(),
                type:"GET",
                dataType:"json",
                async:true,
                success:function(data){
                    if(data != null){
                        CrearTabla(data);
                    }
                }
            });
        }
        function CrearTabla(data){
            var table="";
            $.each(data,function(index,element){
                table +='<tr>';
                table +='   <td>'+element.name+'</td>';
                table +='   <td>'+element.description+'</td>';
                table +='   <td>';
                table +='       <a class="btn btn-xs btn-primary" onclick="EliminarRol(\''+element.name+'\')">';
                table +='           <i class="fa fa-trash"></i>';
                table +='       </a>';
                table +='   </td>';
                table +='</tr>';
            });
            $("#tbodyRoles").html(table);
        }
        function EliminarRol(rol){
            swal({
                title: 'Advertencia',
                text: 'Al oprimir aceptar se eliminará el registro, ¿Esta seguro de continuar?',
                icon: 'warning',
                buttons: {
                    cancel: {
                        text: 'Cancelar',
                        value: null,
                        visible: true,
                        className: 'btn btn-default',
                        closeModal: true
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
                        url:"/usuario/roles/delete",
                        type:"POST",
                        dataType:"json",
                        async:true,
                        data:{
                            _token: "{{ csrf_token() }}",
                            user_id:$("#user_id").val(),
                            rol:rol
                        },
                        success:function(data){
                            if(data != null){
                                CrearTabla(data);
                            }
                        }
                    });
                }
            });
        }
        $("#btnGuardarCentro").on("click",function(){
            if($("#centro_user_id").val() != "" && $("#centro_user_id").val() != null){
                $.ajax({
                    url:"/usuario/centros",
                    type:"POST",
                    dataType:"json",
                    async:true,
                    data:{
                        _token: "{{ csrf_token() }}",
                        user_id:$("#user_id").val(),
                        centro_id:$("#centro_user_id").val()
                    },
                    success:function(data){
                        if(data != null){
                            cargarCentros($("#user_id").val());
                        }
                    }
                });
            }else{
                swal({title: 'Error',text: 'Deberás seleccionar un centro de la lista',icon: 'error'});
            }
        });
        function cargarCentros(user_id){
            $.ajax({
                url:"/usuario/centros/"+user_id,
                type:"GET",
                dataType:"json",
                async:true,
                success:function(data){
                    var table = "";
                    $.each(data, function(index,element){
                        table +='<tr>';
                        table +='   <td>'+element.centro.nombre+'</td>';
                        table +='   <td>';
                        table +='       <a class="btn btn-xs btn-primary" onclick="EliminarCentro('+element.id+')">';
                        table +='           <i class="fa fa-trash"></i>';
                        table +='       </a>';
                        table +='   </td>';
                        table +='</tr>';
                    });
                    $("#table-centros tbody").html(table)
                }
            });
        }
        function EliminarCentro(id){
        swal({
                title: 'Advertencia',
                text: 'Al oprimir aceptar se eliminará el centro, ¿Esta seguro de continuar?',
                icon: 'warning',
                buttons: {
                    cancel: {
                        text: 'Cancelar',
                        value: null,
                        visible: true,
                        className: 'btn btn-default',
                        closeModal: true
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
                        url:"/usuario/centros/delete",
                        type:"POST",
                        dataType:"json",
                        async:true,
                        data:{
                            _token: "{{ csrf_token() }}",
                            id:id,
                        },
                        success:function(data){
                            if(data != null){
                                cargarCentros($("#user_id").val());
                            }
                        }
                    });
                }
            });
        }
    </script>
@endpush
