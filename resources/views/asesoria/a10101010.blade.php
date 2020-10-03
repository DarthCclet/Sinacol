@extends('layouts.default')

@section('content')
    <style>
        .video-container{
            outline: none;
        }
    </style>
    @if($origen == '10101010')
    <div class="embed-responsive embed-responsive-21by9">
        <video controls id="vid" class="video-container">
            <source src="/assets/img/asesoria/Asesoria-1080p-200828.mp4?autoplay=1">
        </video>
    </div>

    <a href="/asesoria/1010101010/?origen={{$origen}}" id="btn-calculo" class="btn-siguiente d-none btn btn-primary btn-lg m-10 float-right" type="button">Siguiente</a>
    @elseif($origen == '10201010')
        <h2>Video: Prestaciones laborales</h2>
        <a href="/asesoria/1010101010/?origen={{$origen}}" id="btn-calculo" class="btn-siguiente d-none btn btn-primary btn-lg m-10 float-right" type="button">Siguiente</a>
    @elseif($origen == '10301010')
        <h2>Video: Prestaciones laborales</h2>
        <a href="/asesoria/1010101010/?origen={{$origen}}" id="btn-calculo" class="btn-siguiente d-none btn btn-primary btn-lg m-10 float-right" type="button">Siguiente</a>
    @elseif($origen == '10401010')
        <h2>Presentación: Recisión por parte del trabajador</h2>
        <a href="/asesoria/1010101010/?origen={{$origen}}" id="btn-calculo" class="btn-siguiente d-none btn btn-primary btn-lg m-10 float-right" type="button">Siguiente</a>
    @elseif($origen == '10501010')
        <h2>Presentación: Derechos de preferencia, antigüedad y ascenso</h2>
        <a href="/asesoria/101010101010/?origen={{$origen}}" id="btn-calculo" class="btn-siguiente d-none btn btn-primary btn-lg m-10 float-right" type="button">Siguiente</a>
    @endif
@endsection


@push('scripts')
    <script>
        $(document).ready(function() {
            document.getElementById('vid').addEventListener('ended', function( evt ) {
                console.log('Video finalizado');
                document.getElementById('btn-calculo').click();
            });
            setTimeout(function(){ $('video').trigger('play'); }, 4000);
            setTimeout(function(){ $('.btn-siguiente').removeClass('d-none'); }, 2000);
        });
    </script>
@endpush

