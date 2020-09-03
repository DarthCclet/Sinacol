@extends('layouts.default')

@section('content')

    <br>
    <br>
    <br>
    <H1>¿Qué tipo de conflicto tienes?</H1>
    <hr class="red">
    <br>
    <br>
    <ol class="h4">
        <li class="mb-4"><a href="/asesoria/1010" data-toggle="modal" data-target="#confirmar" class="text-black h4 mb-4">Me despidieron de manera injusta.</a></li>
        <li class="mb-4"><a href="/asesoria/1020" data-toggle="modal" data-target="#confirmar" class="text-black h4 mb-4">Renuncié y no me pagaron el finiquito correcto.</a></li>
        <li class="mb-4"><a href="/asesoria/1030" data-toggle="modal" data-target="#confirmar" class="text-black h4 mb-4">Sigo laborando para mi patrón pero tengo un conflicto con él.</a></li>
        <li class="mb-4"><a href="/asesoria/1040" data-toggle="modal" data-target="#confirmar" class="text-black h4 mb-4">Quiero dejar de trabajar con mi patrón y quiero una compensación.</a></li>
        <li class="mb-4"><a href="/asesoria/1050" data-toggle="modal" data-target="#confirmar" class="text-black h4 mb-4">Quiero reclamar mi derecho de preferencia, antigüedad o ascenso bajo el </a><a href="http://www.diputados.gob.mx/LeyesBiblio/pdf/125_020719.pdf#page=50" target="_blank">Título IV Capítulo IV de la Ley Federal del Trabajo.</a></li>
    </ol>

    <!-- Modal -->
    <div class="modal fade" id="confirmar" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLongTitle">
                        A continuación le brindaremos orientación sobre su conflicto y sobre el procedimiento de conciliación.
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
            var button = $(event.relatedTarget) // Button that triggered the modal
            ref = button.attr('href');
        });
        $('#ir-orientacion').on('click', function (event) {
            window.location.href = ref;
        });
        $('#ir-solicitud').on('click', function (event) {
            window.location.href = '/solicitudes/create-public/?solicitud=1';
        });
    </script>

@endpush
