@extends('layouts.default', ['paceTop' => true])

@section('title', 'Salas')

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
    <h1 class="page-header">Administrar centros de conciliación <small>Editar {{$sala->sala}}</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
{{ Form::model($sala, array('route' => array('salas.update', $sala->id), 'method' => 'PUT')) }}
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Nuevo centro</h4>
            <div class="panel-heading-btn">
                <a href="{!! route('salas.index') !!}" class="btn btn-primary btn-sm"><i class="fa fa-arrow-alt-circle-left"></i> Regresar</a>
            </div>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            @include('centros.salas._form')
        </div>
        <!-- end panel-body -->
        <!-- begin panel-footer -->
        <div class="panel-footer text-right">
            <a href="{!! route('salas.index') !!}" class="btn btn-white btn-sm"><i class="fa fa-times"></i> Cancelar</a>
            <a class="btn btn-primary btn-sm m-l-5" id="btnGuardar"><i class="fa fa-save"></i> Modificar</a>
        </div>
        <!-- end panel-footer -->
    </div>
{{ Form::close() }}
<input type="hidden" id="id">
@endsection
@push('scripts')
    <script>
        $(document).ready(function(){
            $("#id").val('{{ $sala->id }}');
            $.ajax({
                url:"/centros?all=true",
                type:"GET",
                dataType:"json",
                success:function(data){
                    if(data.data != null && data.data != ""){
                        $("#centro_id").html("<option value=''>-- Selecciona un centro</option>");
                        $.each(data.data,function(index,element){
                            $("#centro_id").append("<option value='"+element.id+"'>"+element.nombre+"</option>");
                        });
                    }else{
                        $("#centro_id").html("<option value=''>-- Selecciona un centro</option>");
                    }
                    $("#centro_id").val('{{ $sala->centro_id }}').select2();
                }
            });
        });
        $("#btnGuardar").on("click",function(){
            var validar = validarSala();
            if(!validar){
                $.ajax({
                    url:"/salas",
                    type:"POST",
                    dataType:"json",
                    data:{
                        id:$("#id").val(),
                        sala:$("#sala").val(),
                        centro_id:$("#centro_id").val(),
                        _token:"{{ csrf_token() }}"
                    },
                    success:function(data){
                        if(data != null && data != ""){
                            window.location.href = "{{ route('salas.index')}}";
                        }else{
                            swal({
                                title: 'Algo salio mal',
                                text: 'No se guardo el registro',
                                icon: 'warning'
                            });
                        }
                    }
                });
            }else{
                swal({
                    title: 'Algo salio mal',
                    text: 'Llena los cambios requeridos',
                    icon: 'warning'
                });
            }
        });
        function validarSala(){
            $("#sala").css("border-color","");
            $(".select2-selection").css("border-color","");
            var error=false;
            var msgError="";
            if($("#sala").val() == ""){
                $("#sala").css("border-color","red");
                error = true;
            }
            if($("#centro_id").val() == ""){
                $(".select2-selection").css("border-color","red");
                error = true;
            }
            return error;
        }
    </script>
@endpush
