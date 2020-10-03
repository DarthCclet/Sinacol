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

    <a href="/solicitudes/create-public/?solicitud=1" id="btn-siguiente" class="btn-siguiente d-none btn btn-primary btn-lg m-10 float-right" type="button">Ir a solicitud de conciliación</a>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            document.getElementById('vid').addEventListener('ended', function( evt ) {
                console.log('Video finalizado');
                document.getElementById('btn-calculo').click();
            });
            setTimeout(function(){ $('video').trigger('play'); }, 2000);
            setTimeout(function(){ $('.btn-siguiente').removeClass('d-none'); }, 4000);
        });
    </script>
@endpush

