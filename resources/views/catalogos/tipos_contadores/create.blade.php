@extends('layouts.default', ['paceTop' => true])

@section('title', 'Tipos de contadores')

@include('includes.component.datatables')

@section('content')
<!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item"><a href="{!! route('tipos_contadores.index') !!}">Tipos de contadores</a></li>
        <li class="breadcrumb-item active"><a href="javascript:;">Registro</a></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Administración de catalogos <small>Registro de Tipos de contadores</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
    {!! Form::open([ 'route' => 'tipos_contadores.store' ]) !!}
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Nuevo tipo de contador</h4>
            <div class="panel-heading-btn">
                <a href="{!! route('tipos_contadores.index') !!}" class="btn btn-primary btn-sm"><i class="fa fa-arrow-alt-circle-left"></i> Regresar</a>
            </div>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            @include('catalogos.tipos_contadores._form')
        </div>
        <!-- end panel-body -->
        <!-- begin panel-footer -->
        <div class="panel-footer text-right">
            <a href="{!! route('tipos_contadores.index') !!}" class="btn btn-white btn-sm"><i class="fa fa-times"></i> Cancelar</a>
            <button class="btn btn-primary btn-sm m-l-5" id='btnGuardar'><i class="fa fa-save"></i> Guardar</button>
        </div>
        <!-- end panel-footer -->
    </div>
    {!! Form::close() !!}
@endsection
