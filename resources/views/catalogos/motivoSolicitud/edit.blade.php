@extends('layouts.default', ['paceTop' => true])

@section('title', 'Motivo Solicitud')

@include('includes.component.datatables')

@section('content')

<!-- begin breadcrumb -->
<ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
    <li class="breadcrumb-item active"><a href="javascript:;">Roles</a></li>
</ol>
<!-- end breadcrumb -->
<!-- begin page-header -->
<h1 class="page-header">Administrar motivos de solicitudes <small>Editar</small></h1>
<!-- end page-header -->

<!-- begin panel -->
{!! Form::model($motivoSolicitud, ['route' => ['motivos-solicitud.update', $motivoSolicitud->id], 'method' => 'PUT'] ) !!}
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Nuevo</h4>
            <div class="panel-heading-btn">
                <a href="{!! route('motivos-solicitud.index') !!}" class="btn btn-info btn-sm"><i class="fa fa-arrow-alt-circle-left"></i> Regresar</a>
            </div>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
          @include('catalogos.motivoSolicitud._form')
        </div>
        <!-- end panel-body -->
        <!-- begin panel-footer -->
        <div class="panel-footer text-right">
            <a href="{!! route('motivos-solicitud.index') !!}" class="btn btn-white btn-sm"><i class="fa fa-times"></i> Cancelar</a>
            <button class="btn btn-primary btn-sm m-l-5"><i class="fa fa-save"></i> Modificar</button>
        </div>
        <!-- end panel-footer -->
    </div>
{{ Form::close() }}
@endsection
