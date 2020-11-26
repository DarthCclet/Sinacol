@extends('layouts.default', ['paceTop' => true])

@section('title', 'Contadores')

@include('includes.component.datatables')
@include('includes.component.pickers')

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item active"><a href="javascript:;">Contadores</a></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Administrar contadores <small>Nuevo Contador</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Nuevo contador</h4>
            <div class="panel-heading-btn">
                <a href="{!! route('contadores.index') !!}" class="btn btn-primary btn-sm"><i class="fa fa-arrow-alt-circle-left"></i> Regresar</a>
            </div>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            @include('contadores._form')
        </div>
        <!-- end panel-body -->
        <!-- begin panel-footer -->
        <div class="panel-footer text-right">
            <a href="{!! route('contadores.index') !!}" class="btn btn-white btn-sm"><i class="fa fa-times"></i> Cancelar</a>
            <button class="btn btn-primary btn-sm m-l-5" id='btnGuardar'><i class="fa fa-save"></i> Guardar</button>
        </div>
        <!-- end panel-footer -->
    </div>
    <input type="hidden" id='id'>
@endsection
@push('scripts')
    <script>
        $(document).ready(function(){
            $('#anio').datetimepicker({useCurrent: false,format:'YYYY'}).limitKeyPress("0123456789");
            $("#centro_id").select2({width: '100%'});
            $("#tipo_contador_id").select2({width: '100%'});
            $.ajax({
                url:"/api/centro",
                type:"GET",
                global:false,
                dataType:"json",
                success:function(data){
                    if(data.data.data != null && data.data.data != ""){
                        $("#centro_id").html("<option value=''>-- Selecciona un centro</option>");
                        $.each(data.data.data,function(index,element){
                            $("#centro_id").append("<option value='"+element.id+"'>"+element.nombre+"</option>");
                        });
                    }else{
                        $("#centro_id").html("<option value=''>-- Selecciona un centro</option>");
                    }
                    $("#centro_id").trigger('change');
                }
            });
            $.ajax({
                url:"/api/tipo_contadores",
                type:"GET",
                global:false,
                dataType:"json",
                success:function(data){
                    if(data != null && data != ""){
                        $("#tipo_contador_id").html("<option value=''>-- Selecciona un tipo de contador</option>");
                        $.each(data,function(index,element){
                            $("#tipo_contador_id").append("<option value='"+element.id+"'>"+element.nombre+"</option>");
                        });
                    }else{
                        $("#tipo_contador_id").html("<option value=''>-- Selecciona un tipo de contador</option>");
                    }
                    $("#tipo_contador_id").trigger('change');
                }
            });
        });
        $("#btnGuardar").on("click",function(){
            var validar = validarContador();
            if(!validar){
                $.ajax({
                    url:"/api/contadores",
                    type:"POST",
                    dataType:"json",
                    global:false,
                    data:{
                        id:"",
                        centro_id:$("#centro_id").val(),
                        tipo_contador_id:$("#tipo_contador_id").val(),
                        anio:$("#anio").val(),
                        contador:$("#contador").val()
                    },
                    success:function(data){
                        if(data != null && data != ""){
                            window.location.href = "{{ route('contadores.index')}}";
                        }else{
                            swal({
                                title: 'Algo salió mal',
                                text: 'No se guardo el registro',
                                icon: 'warning'
                            });
                        }
                    }
                });
            }else{
                swal({
                    title: 'Algo salió mal',
                    text: 'Llena los cambios requeridos',
                    icon: 'warning'
                });
            }
        });
        function validarContador(){
            $(".select2-selection").css("border-color","");
            $(".labelConsecutivo").css("border-color","");
            var error=false;
            var msgError="";
            if($("#centro_id").val() == ""){
                $(".select2-selection[aria-labelledby='select2-centro_id-container']").css("border-color","red");
                error = true;
            }
            if($("#tipo_contador_id").val() == ""){
                $(".select2-selection[aria-labelledby='select2-tipo_contador_id-container']").css("border-color","red");
                error = true;
            }
            if($("#anio").val() == ""){
                $("#anio").css("border-color","red");
                error = true;
            }
            if($("#contador").val() == ""){
                $("#contador").css("border-color","red");
                error = true;
            }
            return error;
        }
        (function (a) {
            a.fn.limitKeyPress = function (b) {
                a(this).on({keypress: function (a) {
                        var c = a.which, d = a.keyCode, e = String.fromCharCode(c).toLowerCase(), f = b;
                        (-1 != f.indexOf(e) || 9 == d || 37 != c && 37 == d || 39 == d && 39 != c || 8 == d || 46 == d && 46 != c) && 161 != c || a.preventDefault()
                    }})
            }
        })(jQuery);
    </script>
@endpush
