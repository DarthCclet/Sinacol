@extends('layouts.default', ['paceTop' => true])

@section('title', 'Salas')

@include('includes.component.datatables')

@section('content')
<h2>Editar  {{ $sala->sala }}</h2>

{{ Form::model($sala, array('route' => array('salas.update', $sala->id), 'method' => 'PUT')) }}
    @include('centros.salas._form')


  <div class="form-group">
    {{ Form::submit('Edit', array('class' => 'btn btn-primary')) }}
  </div>
{{ Form::close() }}
@endsection
