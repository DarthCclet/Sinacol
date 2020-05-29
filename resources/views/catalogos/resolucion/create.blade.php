@extends('layouts.default', ['paceTop' => true])

@section('title', 'Resolución Audiencia')

@include('includes.component.datatables')

@section('content')
  <!-- begin breadcrumb -->
  <ol class="breadcrumb float-xl-right">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item"><a href="{!! route('roles-conciliadores.index') !!}">Administración</a></li>
      <li class="breadcrumb-item active">Usuarios</li>
  </ol>
  <!-- end breadcrumb -->
  <!-- begin page-header -->
  <h1 class="page-header">Administrar resoluciones de audiencia <small>Nuevo </small></h1>
  <!-- end page-header -->


{!! Form::open([ 'route' => 'resolucion-audiencia.store' ]) !!}

  <div class="panel panel-default">
      <!-- begin panel-heading -->
      <div class="panel-heading">
          <h4 class="panel-title">Nueva resolución de Audiencia</h4>
          <div class="panel-heading-btn">
              <a href="{!! route('resolucion-audiencia.index') !!}" class="btn btn-primary btn-sm"><i class="fa fa-arrow-alt-circle-left"></i> Regresar</a>
          </div>
      </div>
      <div class="panel-body">
        @include('catalogos.resolucion._form')
      </div>
      <div class="panel-footer text-right">
          <a href="{!! route('resolucion-audiencia.index') !!}" class="btn btn-white btn-sm"><i class="fa fa-times"></i> Cancelar</a>
          <button class="btn btn-primary btn-sm m-l-5"><i class="fa fa-save"></i> Guardar</button>
      </div>
  </div>
{!! Form::close() !!}
@endsection
