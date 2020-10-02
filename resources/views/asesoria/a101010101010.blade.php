@extends('layouts.default')

@section('content')
    <style>
        .video-container{
            outline: none;
        }
    </style>

    <div class="embed-responsive embed-responsive-21by9">
        <video controls id="vid" class="video-container">
            <source src="/assets/img/asesoria/nueva-ley.mp4?autoplay=1">
        </video>
    </div>

    <a href="/solicitudes/create-public/?solicitud=1" class="btn btn-primary btn-lg m-10 float-right" type="button">Ir a solicitud de conciliación</a>

@endsection
