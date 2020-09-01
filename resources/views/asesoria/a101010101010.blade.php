@extends('layouts.default')

@section('content')

    <a href="/asesoria/{{$accion}}?from={{$paso_next}}&source={{$accion}}" class="text-black h4 mb-4">
        <img width="1024" class="mx-auto d-block" src="/assets/img/asesoria/101010101010/{{$asset_paso}}" alt="">
    </a>
    <a href="/solicitudes/create-public" class="btn btn-primary btn-lg m-10 float-right" type="button">Ir a solicitud de conciliación</a>

@endsection
