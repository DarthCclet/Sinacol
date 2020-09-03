@extends('layouts.default')

@section('content')

    <div class="align-middle">
        <a href="/asesoria/{{$accion}}?from={{$paso_next}}&source={{$accion}}" class="text-black h4 mb-4">
            <img width="1024" class="mx-auto d-block" src="/assets/img/asesoria/201010/{{$asset_paso}}" alt="">
        </a>

    </div>

@endsection
