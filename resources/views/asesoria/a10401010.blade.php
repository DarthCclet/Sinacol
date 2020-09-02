@extends('layouts.default')

@section('content')
    <a href="/asesoria/{{$accion}}?from={{$paso_next}}&source={{$accion}}&origen={{$origen}}" class="text-black h4 mb-4">
        <img width="1024" class="mx-auto d-block" src="/assets/img/asesoria/10401010/{{$asset_paso}}" alt="">
    </a>

@endsection



