@extends('layouts.default', ['paceTop' => true])

@section('title', 'Salas')

@include('includes.component.datatables')

@section('content')


{!! Form::open([ 'route' => 'salas.store' ]) !!}


    @include('centros.salas._form')

    <div class="form-group">
      {{ Form::submit('Guardar', array('class' => 'btn btn-primary')) }}
    </div>
{!! Form::close() !!}
@endsection
