@extends('layouts.default', ['paceTop' => true])

@section('title', 'Solicitud')

@include('includes.component.datatables')
@include('includes.component.pickers')
@include('includes.component.calendar')
@include('includes.component.dropzone')

@section('content')
<!-- begin breadcrumb -->
<ol class="breadcrumb float-xl-right">
  <li class="breadcrumb-item"><a href="">Home</a></li>
  <li class="breadcrumb-item"><a href="{!! route("solicitudes.index") !!}">Solicitudes</a></li>
  <li class="breadcrumb-item"><a href="javascript:;">Editar Solicitud</a></li>
</ol>
<!-- end breadcrumb -->

<div class="panel panel-inverse">
    <div class="panel-body">
        {{-- {{ Form::model($solicitud, array('route' => array('solicitudes.update', $solicitud->id), 'method' => 'PUT')) }} --}}

          @include('expediente.solicitudes._form')
        <br>
          <div class="form-group">
            <button class="btn btn-primary pull-right btn-sm m-l-5 solicitudTerminada" onclick="guardarSolicitud()"><i class="fa fa-save"></i> Modificar</button>
          </div>
        {{-- {{ Form::close() }} --}}
    </div>
</div>
<input type="hidden" id="audiencia_id">

@endsection
@push('scripts')
<script>
  var edit = true;
  var origen = 'solicitudes';
  var listaResolucionesIndividuales = [];
</script>
@endpush
