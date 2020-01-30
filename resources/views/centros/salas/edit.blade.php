@extends('layouts.default', ['paceTop' => true])

@section('title', 'Salas')

@include('includes.component.datatables')

@section('content')
<h2>Editar  {{ $sala->sala }}</h2>

{{ Form::model($sala, array('route' => array('salas.update', $sala->id), 'method' => 'PUT')) }}
    @include('centros.salas._form')


  <div class="form-group">
    <!-- {{ Form::submit('Editar', array('class' => 'btn btn-primary')) }} -->


    <div class="panel-footer text-right">
        <a href="{!! route('salas.index') !!}" class="btn btn-white btn-sm"><i class="fa fa-times"></i> Cancelar</a>
        <button class="btn btn-primary btn-sm m-l-5"><i class="fa fa-save"></i> Guardar</button>
    </div>

  </div>
{{ Form::close() }}
@endsection
