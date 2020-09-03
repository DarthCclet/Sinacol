@extends('layouts.default')

@section('content')

    <div class="align-middle" >
        <div class="btn-group center offset-3" role="group" aria-label="...">
            <p>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <a href="/asesoria/201010" data-toggle="modal" data-target="#confirmar" class="btn btn-primary btn-lg m-10" type="button">CONFLICTO INDIVIDUAL</a>
                <a href="/solicitudes/create-public/?solicitud=3" class="btn btn-primary btn-lg m-10" type="button">CONFLICTO COLECTIVO</a>
            </p>
        </div>

    </div>
    <div class="modal fade" id="confirmar" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLongTitle">
                        A continuación le brindaremos orientación sobre la rescisión de la relación de trabajo, con base en el Artículo 47 de la Ley Federal del Trabajo
                    </h4>
                </div>
                <div class="modal-body text-center">
                    <button type="button" id="ir-orientacion" class="btn btn-lg btn-primary m-10 center-block" data-dismiss="modal">Seguir a la orientación</button>
                    <button type="button" id="ir-solicitud" class="btn btn-lg btn-primary m-10">Ya he visto la orientación, llévame directamente a la solicitud de conciliación</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        var ref = '';
        $('#confirmar').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            ref = button.attr('href');
        });
        $('#ir-orientacion').on('click', function (event) {
            window.location.href = ref;
        });
        $('#ir-solicitud').on('click', function (event) {
            window.location.href = '/solicitudes/create-public/?solicitud=2';
        });
    </script>
@endpush
