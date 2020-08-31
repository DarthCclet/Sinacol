@extends('layouts.default')

@section('content')
    @if($origen == '10101010')
    <div class="embed-responsive embed-responsive-21by9">
        <iframe class="embed-responsive-item" id="vid" src="/assets/img/asesoria/Asesoria-1080p-200828.mp4?autoplay=1"></iframe>
    </div>
    <a href="/asesoria/1010101010/?origen={{$origen}}" class="btn btn-primary btn-lg m-10 float-right" type="button">Siguiente</a>
    @elseif($origen == '10201010')
        <h2>Video: Prestaciones laborales</h2>
        <a href="/asesoria/1010101010/?origen={{$origen}}" class="btn btn-primary btn-lg m-10 float-right" type="button">Siguiente</a>
    @elseif($origen == '10301010')
        <h2>Video: Prestaciones laborales</h2>
        <a href="/asesoria/1010101010/?origen={{$origen}}" class="btn btn-primary btn-lg m-10 float-right" type="button">Siguiente</a>
    @elseif($origen == '10401010')
        <h2>Presentación: Recisión por parte del trabajador</h2>
        <a href="/asesoria/1010101010/?origen={{$origen}}" class="btn btn-primary btn-lg m-10 float-right" type="button">Siguiente</a>
    @elseif($origen == '10501010')
        <h2>Presentación: Derechos de preferencia, antigüedad y ascenso</h2>
        <a href="/solicitudes/create-public/?origen={{$origen}}" class="btn btn-primary btn-lg m-10 float-right" type="button">Siguiente</a>
    @endif

@endsection
