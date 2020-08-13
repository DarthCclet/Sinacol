@extends('layouts.default')

@section('content')
    <div class="align-middle">

        <br>
        <br>


        <p class="text-justify h2">El Centro Federal de Conciliación y Registro Laboral tiene la obligación de proporcionarte
            asesoría gratuita sobre tus derechos (Artículo 684-E III). <br><br>Una orientación correcta te podrá guiar
            para solucionar tu conflicto de la mejor manera.</p>

        <div class="btn-group center offset-4 text-center" role="group" aria-label="...">
            <p>
                <br>
                <br>

                <br>
                <br>
                <br>
                <br>
                <br>
                <a href="/asesoria/trabajador-tipo-asesoria" class="btn btn-primary h2 btn-lg m-10" type="button">Sí
                    quiero la asesoría antes de proceder a la solicitud</a>
                <br>
                <a href="/solicitudes/create-public" class="btn btn-primary btn-lg m-10 h2" type="button">No quiero la
                    asesoría</a>

            </p>
        </div>

    </div>

@endsection
