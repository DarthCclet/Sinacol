@extends('layouts.default', ['paceTop' => true])

@section('title', 'Audiencias')

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
    <h1 class="page-header">Administrar Audiencias <small>Resolución de Audiencias</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">Resolucion de Audiencia</h4>
                <div class="panel-heading-btn">
                    <a href="{!! route('audiencias.index') !!}" class="btn btn-info btn-sm"><i class="fa fa-arrow-alt-circle-left"></i> Regresar</a>
                </div>
            </div>
            <!-- begin panel-body -->
            <div class="panel-body">
                @include('expediente.audiencias._form')
            </div>
            <!-- end panel-body -->
            <!-- begin panel-footer -->
            <div class="panel-footer text-right">
                <a href="{!! route('audiencias.index') !!}" class="btn btn-white btn-sm"><i class="fa fa-times"></i> Cancelar</a>
                <button class="btn btn-primary btn-sm m-l-5" id='btnGuardar'><i class="fa fa-save"></i> Guardar resolución</button>
            </div>
            <!-- end panel-footer -->
        </div>

@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $("#duracionAudiencia").datetimepicker({format:"HH:mm"});
            $('#convenio').wysihtml5(); 
            $('#desahogo').wysihtml5(); 
            $.ajax({
                url:"/api/resoluciones",
                type:"GET",
                dataType:"json",
                success:function(data){
                    if(data.data.data != null && data.data.data != ""){
                        $("#resolucion_id").html("<option value=''>-- Selecciona un centro</option>");
                        $.each(data.data.data,function(index,element){
                            $("#resolucion_id").append("<option value='"+element.id+"'>"+element.nombre+"</option>");
                        });
                    }else{
                        $("#resolucion_id").html("<option value=''>-- Selecciona un centro</option>");
                    }
                    $("#resolucion_id").val('{{ $audiencia->resolucion_id }}').select2();
                }
            });
        });
        $("#btnGuardar").on("click",function(){
            var validar = validarResolucion();
            if(!validar){
                $.ajax({
                    url:"/api/audiencia/resolucion",
                    type:"POST",
                    dataType:"json",
                    data:{
                        audiencia_id:'{{ $audiencia->id }}',
                        convenio:$("#convenio").val(),
                        desahogo:$("#desahogo").val(),
                        resolucion_id:$("#resolucion_id").val()
                    },
                    success:function(data){
                        if(data != null && data != ""){
                            window.location.href = "{{ route('audiencias.index')}}";
                        }else{
                            swal({
                                title: 'Algo salio mal',
                                text: 'No se guardo el registro',
                                icon: 'warning'
                            });
                        }
                    }
                });
            }
        });
        function validarResolucion(){
            if($("#convenio").val() == ""){
                swal({title: 'Error',text: 'Describe el convenio',icon: 'warning'});
                return true;
            }
            if($("#desahogo").val() == ""){
                swal({title: 'Error',text: 'Describe el desahogo',icon: 'warning'});
                return true;
            }
            if($("#resolucion_id").val() == ""){
                swal({title: 'Error',text: 'Selecciona una resolucion',icon: 'warning'});
                return true;
            }
            return false;
        }
    </script>
@endpush