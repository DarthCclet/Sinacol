@extends('layouts.default')

@section('content')

    <div class="align-middle">
        @if($last)
            <a href="/solicitudes/create-public?solicitud=2" class="text-black h4 mb-4">
        @else
            <a href="/asesoria/{{$accion}}?from={{$paso_next}}&source={{$accion}}" class="text-black h4 mb-4">
        @endif
            <img width="1024" class="mx-auto d-block" src="/assets/img/asesoria/201010/{{$asset_paso}}" alt="">
        </a>
    </div>
    @if($last)
        <a href="/solicitudes/create-public?solicitud=2" class="btn btn-primary btn-lg m-10 float-right" type="button">Ir a solicitud de conciliación</a>
    @endif

@endsection
