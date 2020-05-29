@extends('layouts.default', ['paceTop' => true])

@section('title', 'Centros')

@include('includes.component.datatables')

@section('content')
<button class="btn btn-primary" onclick="location.href='{{ route('jornadas.index')  }}'" > <i class="fa fa-arrow-alt-circle-left"></i> Regresar</button>
<div class="panel panel-inverse">
    <div class="panel panel-heading ui-sortable-handle">
        <h4 class="panel-title">Registro de Jornadas</h4>
    </div>
    <div class="panel-body">
        <form action="{{url('jornadas')}}" method="POST">
            {{ csrf_field() }}
            @include('catalogos.jornadas._form')
             <button class="btn btn-primary btn-sm m-l-5"><i class="fa fa-save"></i> Guardar</button>

        </form>
    </div>
</div>
@endsection
