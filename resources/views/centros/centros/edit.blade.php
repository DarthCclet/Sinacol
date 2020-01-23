@extends('layouts.default', ['paceTop' => true])

@section('title', 'Centros')

@include('includes.component.datatables')

@section('content')
<button class="btn btn-primary" onclick="location.href='{{ route('centros.index')  }}'" >Regresar</button>
<div class="panel panel-inverse">
    <div class="panel panel-heading ui-sortable-handle">
        <h4 class="panel-title">Editar {{ $centro->nombre }}</h4>
    </div>
    <div class="panel-body">
        {{ Form::model($centro, array('route' => array('centro.update', $centro->id), 'method' => 'PUT')) }}
            @include('centros.centros._form')
          <div class="form-group">
            {{ Form::submit('Guardar', array('class' => 'btn btn-primary')) }}
          </div>
        {{ Form::close() }}
    </div>
</div>

@endsection
