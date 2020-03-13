@extends('layouts.default', ['paceTop' => true])

@section('title', 'Solicitud')

@include('includes.component.datatables')
@include('includes.component.pickers')
@include('includes.component.calendar')

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">Editar</h4>
        <div class="panel-heading-btn">
            <a href="{!! route('solicitudes.index') !!}" class="btn btn-info btn-sm"><i class="fa fa-arrow-alt-circle-left"></i> Regresar</a>
        </div>
    </div>
    <div class="panel-body">
        {{-- {{ Form::model($solicitud, array('route' => array('solicitudes.update', $solicitud->id), 'method' => 'PUT')) }} --}}
          @include('expediente.solicitudes._form')
          <div class="form-group">
            <button class="btn btn-info btn-sm m-l-5" onclick="guardarSolicitud()"><i class="fa fa-save"></i> Modificar</button>
          </div>
        {{-- {{ Form::close() }} --}}
    </div>
</div>
<script>
  var edit = true;
</script>

@endsection