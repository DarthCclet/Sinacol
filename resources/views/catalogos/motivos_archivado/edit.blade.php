@extends('layouts.default', ['paceTop' => true])

@section('title', 'Motivos de archivado')

@include('includes.component.datatables')

@section('content')
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item"><a href="{!! route('motivos_archivado.index') !!}">MotivoArchivadoController</a></li>
        <li class="breadcrumb-item active"><a href="javascript:;">Editar</a></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Administrar motivos de archivado <small>Editar {{$motivo->descripcion}}</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
{{ Form::model($motivo, array('route' => array('motivos_archivado.update', $motivo->id), 'method' => 'PUT')) }}
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Nuevo motivo de archivado</h4>
            <div class="panel-heading-btn">
                <a href="{!! route('motivos_archivado.index') !!}" class="btn btn-primary btn-sm"><i class="fa fa-arrow-alt-circle-left"></i> Regresar</a>
            </div>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            @include('catalogos.motivos_archivado._form')
        </div>
        <!-- end panel-body -->
        <!-- begin panel-footer -->
        <div class="panel-footer text-right">
            <a href="{!! route('motivos_archivado.index') !!}" class="btn btn-white btn-sm"><i class="fa fa-times"></i> Cancelar</a>
            <button class="btn btn-primary btn-sm m-l-5" id="btnGuardar"><i class="fa fa-save"></i> Modificar</button>
        </div>
        <!-- end panel-footer -->
    </div>
{{ Form::close() }}
<input type="hidden" id="id">
@endsection
