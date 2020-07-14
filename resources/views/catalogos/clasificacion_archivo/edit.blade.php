@extends('layouts.default', ['paceTop' => true])

@section('title', 'Clasificación de archivos')

@include('includes.component.datatables')

@section('content')
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item"><a href="{!! route('clasificacion_archivos.index') !!}">Clasificación de archivos</a></li>
        <li class="breadcrumb-item active"><a href="javascript:;">Edición</a></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Administrar centros de conciliación <small>Editar {{$clasificacion->nombre}}</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
{{ Form::model($clasificacion, array('route' => array('clasificacion_archivos.update', $clasificacion->id), 'method' => 'PUT')) }}
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Nueva clasificación</h4>
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
            <button class="btn btn-primary btn-sm m-l-5" id="btnGuardar"><i class="fa fa-save"></i> Modificar</button>
        </div>
        <!-- end panel-footer -->
    </div>
{{ Form::close() }}
<input type="hidden" id="id">
@endsection
