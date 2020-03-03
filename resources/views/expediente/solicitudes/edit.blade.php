@extends('layouts.default', ['paceTop' => true])

@section('title', 'Solicitud')

@include('includes.component.datatables')
@include('includes.component.pickers')

@section('content')
<button class="btn btn-info" onclick="location.href='{{ route('solicitudes.index')  }}'" ><i class="fa fa-arrow-alt-circle-left"></i> Regresar</button>
<div class="panel panel-inverse">
    <div class="panel panel-heading ui-sortable-handle">
        <h4 class="panel-title">Editar</h4>
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
