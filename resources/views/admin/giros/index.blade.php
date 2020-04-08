@extends('layouts.default')

@include('includes.component.treetables')

@section('content')


    <!-- begin breadcrumb -->
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="/">Inicio</a></li>
        <li class="breadcrumb-item"><a href="javascript:;">Giros Comerciales</a></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Administrar giros comerciales <small>Listado</small></h1>
    <!-- end page-header -->
    <!-- begin panel -->
    <div class="panel panel-default">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Listado de giros comerciales</h4>
            <div class="panel-heading-btn">
            </div>
        </div>
        <!-- end panel-heading -->
        <!-- begin panel-body -->
        <div class="panel-body">
            @include('admin.giros._list')
        </div>
    </div>


@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

                var moverNodo = function (moverId, aId) {
                    console.log("Se movio el nodo %d al padre: %d", moverId, aId);
                    $("#lista-ccostos").treetable("move", moverId, aId);

                    var url = $('#action-mover').val();
                    var token = $('meta[name="csrf-token"]').attr('content');
                    var datos = {mover_id: moverId, a_id: aId, _token: token};
                    console.log(datos);
                    $.post(url, datos).done(function (res) {
                        console.log(res);
                    });
                    return true;
                };

                //Genera la tabla arbol de centros de costos
                $("#lista-ccostos").treetable({
                    expandable: true,
                    clickableNodeNames: true,
                    initialState: 'collapsed',
                    stringCollapse: 'Cerrar',
                    stringExpand: 'Abrir'
                });

                // Drag & Drop
                $("#lista-ccostos .file, #lista-ccostos .folder").draggable({
                    helper: "clone",
                    opacity: .9,
                    refreshPositions: true,
                    revert: "invalid",
                    revertDuration: 300,
                    scroll: true
                });

                $("#lista-ccostos .folder").each(function () {
                    $(this).parents("#lista-ccostos tr").droppable({
                        accept: ".file, .folder",
                        drop: function (e, ui) {
                            var droppedEl = ui.draggable.parents("tr");
                            droppedEl.addClass('droppedEl');
                            setTimeout(function() {
                                droppedEl.removeClass('droppedEl');
                            }, 2000)
                            return moverNodo(droppedEl.data("ttId"), $(this).data("ttId"));
                        },
                        hoverClass: "accept",
                        over: function (e, ui) {
                            var droppedEl = ui.draggable.parents("tr");

                            if (this != droppedEl[0] && !$(this).is(".expanded")) {
                                $("#lista-ccostos").treetable("expandNode", $(this).data("ttId"));
                            }
                        }
                    });
                });

        });
    </script>
@endpush


