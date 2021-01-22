@extends('layouts.default', ['paceTop' => true])

@section('title', 'Centros')

@include('includes.component.datatables')
@include('includes.component.pickers')

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item active"><a href="{{route("centros.index")}}">Centros</a></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="h2">Administrar centros de conciliación <small>Editar centro</small></h1>
    <hr class="red">
    <!-- end page-header -->
    <!-- begin panel -->
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a href="#default-tab-1" data-toggle="tab" class="nav-link active">
                <span class="d-sm-none">Cen</span>
                <span class="d-sm-block d-none">Centro</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#default-tab-2" data-toggle="tab" class="nav-link">
                <span class="d-sm-none">Con</span>
                <span class="d-sm-block d-none">Contactos</span>
            </a>
        </li>
    </ul>
    <div class="tab-content" style="background: #f2f3f4 !important;">
        <!-- begin tab-pane -->
        <div class="tab-pane fade active show" id="default-tab-1">
            {!! Form::model($centro, ['route' => ['centros.update', $centro->id], 'method' => 'put'] ) !!}        
                @include('centros.centros._form')
                <div class="text-right">
                    <a href="{!! route('centros.index') !!}" class="btn btn-white btn-sm"><i class="fa fa-times"></i> Cancelar</a>
                    <button class="btn btn-primary btn-sm m-l-5"><i class="fa fa-save"></i> Modificar</button>
                </div>
            {!! Form::close() !!}
        </div>
        <div class="tab-pane fade" id="default-tab-2">
            <div class="col-md-12 row">
                <div class="col-md-5">
                    <label for="tipo_contacto_id" class="col-sm-6 control-label">Tipo de contacto</label>
                    {!! Form::select('contacto[contacto]', isset($tipo_contactos) ? $tipo_contactos  : [] , null, ['id'=>'tipo_contacto_id','required','placeholder' => 'Seleccione una opción', 'class' => 'form-control']);  !!}
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="contacto" class="control-label">Contacto</label>
                        <input type="text" id="contacto" class="form-control" placeholder="Información de contacto">
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary" type="button" id="btnAgregarContacto">
                        <i class="fa fa-plus-circle"></i> Agregar
                    </button>
                </div>
            </div>
            <div class="col-md-12">
                <table class="table table-bordered" >
                    <thead>
                        <tr>
                            <th style="width:80%;">Tipo</th>
                            <th style="width:80%;">Contacto</th>
                            <th style="width:20%; text-align: center;">Acci&oacute;n</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyContacto">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $("#tipo_contacto_id").select2();
            $("#duracionAudiencia").datetimepicker({format:"HH:mm"});
            obtenerContactos();
        });
        $("#btnAgregarContacto").on("click",function(){
            if($("#tipo_contacto_id").val() != "" && $("#contacto").val() != ""){
                $.ajax({
                    url:"/centros/agregar_contacto",
                    type:"POST",
                    dataType:"json",
                    data:{
                        centro_id:'{{$centro->id}}',
                        tipo_contacto_id:$("#tipo_contacto_id").val(),
                        contacto:$("#contacto").val(),
                        _token:"{{ csrf_token() }}"
                    },
                    async:true,
                    success:function(data){
                        try{
                            obtenerContactos();
                        }catch(error){

                        }
                    }
                });
            }else{
                swal({
                    title: 'Error',
                    text: 'Llena todos los campos',
                    icon: 'error',

                });
            }
        });
        function obtenerContactos(){
            $.ajax({
                url:"/centros/contactos",
                type:"POST",
                dataType:"json",
                data:{
                    centro_id:'{{$centro->id}}',
                    _token:"{{ csrf_token() }}"
                },
                async:true,
                success:function(data){
                    try{
                        var table = "";
                        $.each(data, function(index,element){
                            table +='<tr>';
                            table +='   <td>'+element.tipo_contacto.nombre+'</td>';
                            table +='   <td>'+element.contacto+'</td>';
                            table +='   <td><button onclick="eliminarContacto('+element.id+')" class="btn btn-xs btn-warning" title="Eliminar"><i class="fa fa-trash"></i></button></td>';
                            table +='</tr>';
                        });
                        $("#tbodyContacto").html(table);
                    }catch(error){
                    
                    }
                }
            });
        }
        function eliminarContacto(id){
            $.ajax({
                url:"/centros/contactos/eliminar",
                type:"POST",
                dataType:"json",
                data:{
                    id:id,
                    _token:"{{ csrf_token() }}"
                },
                async:true,
                success:function(data){
                    try{
                        obtenerContactos();
                    }catch(error){
                    
                    }
                }
            });
        }
    </script>
@endpush
