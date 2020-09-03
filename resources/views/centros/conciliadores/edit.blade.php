@extends('layouts.default', ['paceTop' => true])

@section('title', 'Conciliadores')

@include('includes.component.datatables')
@include('includes.component.pickers')

@section('content')
<!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item"><a href="{!! route('centros.index') !!}">Centros</a></li>
        <li class="breadcrumb-item active"><a href="javascript:;">Conciliadores</a></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Administrar conciliadores <small>Editar {{$conciliador->persona->nombre}} {{$conciliador->persona->primer_apellido}} {{$conciliador->persona->segundo_apellido}}</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
{{ Form::model($conciliador, array('route' => array('conciliadores.update', $conciliador->id), 'method' => 'PUT')) }}
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Nuevo conciliador</h4>
            <div class="panel-heading-btn">
                <a href="{!! route('conciliadores.index') !!}" class="btn btn-primary btn-sm"><i class="fa fa-arrow-alt-circle-left"></i> Regresar</a>
            </div>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            @include('centros.conciliadores._form')
        </div>
        <!-- end panel-body -->
        <!-- begin panel-footer -->
        <div class="panel-footer text-right">
            <a href="{!! route('centros.index') !!}" class="btn btn-white btn-sm"><i class="fa fa-times"></i> Cancelar</a>
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
            $("#id").val('{{ $conciliador->id }}');
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
                    $("#centro_id").val('{{ $conciliador->centro_id }}').select2();
                }
            });
            $.ajax({
                url:"/api/persona",
                type:"GET",
                dataType:"json",
                success:function(data){
                    if(data != null && data != ""){
                        $("#persona_id").html("<option value=''>-- Selecciona una persona</option>");
                        $.each(data,function(index,element){
                            $("#persona_id").append("<option value='"+element.id+"'>"+element.nombre+" "+element.primer_apellido+" "+(element.segundo_apellido || "")+"</option>");
                        });
                    }else{
                        $("#persona_id").html("<option value=''>-- Selecciona una persona</option>");
                    }
                    $("#persona_id").val('{{ $conciliador->persona_id }}').select2().prop('disabled',true);
                }
            });
            $.ajax({
                url:"/api/rol-conciliador",
                type:"GET",
                dataType:"json",
                success:function(data){
                    console.log(data);
                    if(data.data.data != null && data.data.data != ""){
                        $("#rol_conciliador_id").html("<option value=''>-- Selecciona un rol</option>");
                        $.each(data.data.data,function(index,element){
                            $("#rol_conciliador_id").append("<option value='"+element.id+"'>"+element.nombre+"</option>");
                        });
                    }else{
                        $("#rol_conciliador_id").html("<option value=''>-- Selecciona un rol</option>");
                    }
                    $("#rol_conciliador_id").val('{{ $conciliador->rol_conciliador_id }}').select2();
                }
            });
        });
        $("#btnGuardar").on("click",function(){
            var validar = validarConciliador();
            if(!validar){
                $.ajax({
                    url:"/conciliadores",
                    type:"POST",
                    dataType:"json",
                    data:{
                        id:$("#id").val(),
                        rol_conciliador_id:$("#rol_conciliador_id").val(),
                        persona_id:$("#persona_id").val(),
                        centro_id:$("#centro_id").val(),
                        _token:"{{ csrf_token() }}"
                    },
                    success:function(data){
                        if(data != null && data != ""){
                            window.location.href = "{{ route('conciliadores.index')}}";
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
        function validarConciliador(){
            $(".select2-selection").css("border-color","");
            var error=false;
            var msgError="";
            if($("#rol_conciliador_id").val() == ""){
                $("#rol_conciliador_id").css("border-color","red");
                error = true;
            }
            if($("#centro_id").val() == ""){
                $("#select2-selection").css("border-color","red");
                error = true;
            }
            if($("#persona_id").val() == ""){
                $("#persona_id").css("border-color","red");
                error = true;
            }
            return error;
        }
    </script>
@endpush
