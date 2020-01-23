@extends('layouts.default', ['paceTop' => true])

@section('title', 'Centros')

@include('includes.component.datatables')

@section('content')
<button class="btn btn-primary" onclick="location.href='{{ route('centros.index')  }}'" >Regresar</button>
<div class="panel panel-inverse">
    <div class="panel panel-heading ui-sortable-handle">
        <h4 class="panel-title">Registro de Centros</h4>
    </div>
    <div class="panel-body">
        <form action="{{url('api/centro')}}" method="POST">
            {{ csrf_field() }}
            @include('centros.centros._form')
            {{ Form::submit('Guardar', array('class' => 'btn btn-primary')) }}
            
        </form>
    </div>
</div>
@endsection
