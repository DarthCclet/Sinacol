@extends('layouts.default', ['paceTop' => true])

@section('title', 'Jornadas')

@include('includes.component.datatables')

@section('content')
<button class="btn btn-info" onclick="location.href='{{ route('jornadas.index')  }}'" ><i class="fa fa-arrow-alt-circle-left"></i> Regresar</button>
<div class="panel panel-inverse">
    <div class="panel panel-heading ui-sortable-handle">
        <h4 class="panel-title">Editar {{ $jornada->nombre }}</h4>
    </div>
    <div class="panel-body">
        {{ Form::model($jornada, array('route' => array('jornadas.update', $jornada->id), 'method' => 'PUT')) }}
            @include('catalogos.jornadas._form')
          <div class="form-group">
            <button class="btn btn-info btn-sm m-l-5"><i class="fa fa-save"></i> Modificar</button>
          </div>
        {{ Form::close() }}
    </div>
</div>

@endsection
