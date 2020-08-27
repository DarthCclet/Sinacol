@extends('layouts.default', ['paceTop' => true])

@section('title', 'Clasificación de archivos')

@include('includes.component.datatables')
@include('includes.component.pickers')

@section('content')
<!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item"><a href="{!! route('clasificacion_archivos.index') !!}">Clasificación de archivos</a></li>
        <li class="breadcrumb-item active"><a href="javascript:;">Registro</a></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Administración de catalogos <small>Registro de clasificación de archivos</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
    {!! Form::open([ 'route' => 'clasificacion_archivos.store']) !!}
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Nueva clasificación de archivos</h4>
            <div class="panel-heading-btn">
                <a href="{!! route('clasificacion_archivos.index') !!}" class="btn btn-primary btn-sm"><i class="fa fa-arrow-alt-circle-left"></i> Regresar</a>
            </div>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            @include('catalogos.clasificacion_archivo._form')
        </div>
        <!-- end panel-body -->
        <!-- begin panel-footer -->
        <div class="panel-footer text-right">
            <a href="{!! route('clasificacion_archivos.index') !!}" class="btn btn-white btn-sm"><i class="fa fa-times"></i> Cancelar</a>
            <button class="btn btn-primary btn-sm m-l-5" id='btnGuardar'><i class="fa fa-save"></i> Guardar</button>
        </div>
        <!-- end panel-footer -->
    </div>
    {!! Form::close() !!}
@endsection
@push('scripts')
<script>
    $('#btnGuardar').on('click', function (e) {
        let that = this;
        console.log('boton clic');
        e.preventDefault();
        if($("#nombre").val() != "" && $("#tipo_archivo_id").val() != ""  && $("#entidad_emisora_id").val() != "" ){
            $(that).closest('form').submit();
        }else{
            swal({
                title: 'Error',
                text: 'Llena todos los campos',
                icon: 'warning'
            });
        }
        return false;
    });
</script>
@endpush
