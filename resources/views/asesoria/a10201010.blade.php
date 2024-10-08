@extends('layouts.default')

@section('content')
    <style>
        .video-container{
            outline: none;
        }
    </style>

    <div class="embed-responsive embed-responsive-21by9">
        <video controls id="vid" class="video-container">
            <source src="/assets/img/asesoria/prestaciones.mp4?autoplay=1">
        </video>
    </div>

    <a href="/asesoria/{{$accion}}?from={{$paso_next}}&source={{$accion}}&origen={{$origen}}" id="btn-siguiente" class="btn-siguiente d-none btn btn-primary btn-lg m-10 float-right d-none btn-siguiente" type="button">Siguiente</a>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            document.getElementById('vid').addEventListener('ended', function( evt ) {
                console.log('Video finalizado');
                document.getElementById('btn-siguiente').click();
            });

            setTimeout(function(){ $('video').trigger('play'); }, 4000);
            setTimeout(function(){ $('.btn-siguiente').removeClass('d-none'); }, 6000);
        });
    </script>
@endpush



