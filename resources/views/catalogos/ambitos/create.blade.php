@extends('layouts.default', ['paceTop' => true])

@section('title', 'Ámbitos')

@include('includes.component.datatables')

@section('content')
<!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item"><a href="{!! route('ambitos.index') !!}">Ambitos</a></li>
        <li class="breadcrumb-item active"><a href="javascript:;">Registro</a></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Administración de catalogos <small>Registro de ámbitos</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
    {!! Form::open([ 'route' => 'ambitos.store' ]) !!}
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Nuevo ámbito</h4>
            <div class="panel-heading-btn">
                <a href="{!! route('ambitos.index') !!}" class="btn btn-primary btn-sm"><i class="fa fa-arrow-alt-circle-left"></i> Regresar</a>
            </div>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            @include('catalogos.ambitos._form')
        </div>
        <!-- end panel-body -->
        <!-- begin panel-footer -->
        <div class="panel-footer text-right">
            <a href="{!! route('ambitos.index') !!}" class="btn btn-white btn-sm"><i class="fa fa-times"></i> Cancelar</a>
            <button class="btn btn-primary btn-sm m-l-5" id='btnGuardar'><i class="fa fa-save"></i> Guardar</button>
        </div>
        <!-- end panel-footer -->
    </div>
    {!! Form::close() !!}
@endsection
