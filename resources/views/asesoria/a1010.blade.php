@extends('layouts.default')

@section('content')

    <style>
        .video-container{
            outline: none;
        }
    </style>

    <div class="embed-responsive embed-responsive-21by9">
        <video controls id="vid" class="video-container">
            <source src="/assets/img/asesoria/excepciones-conciliacion.mp4?autoplay=1">
        </video>
    </div>

    <a href="/asesoria/{{$accion}}?from={{$paso_next}}&source={{$accion}}" class="btn btn-primary btn-lg m-10 float-right" type="button">Siguiente</a>
@endsection
