@extends('layouts.default')
@section('title', 'Giros Comerciales')
@include('includes.component.treetables')
@include('includes.component.pickers')
@include('includes.component.datatables')

@section('content')
@push('css')
	<link href="/assets/plugins/x-editable-bs4/dist/bootstrap4-editable/css/bootstrap-editable.css" rel="stylesheet" />
@endpush

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


    <!-- inicio Modal de cambio de nombre-->
    <div class="modal" id="modal-cambio" aria-hidden="true" style="display:none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nombre de Giro</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div id="divRegistroIncidencias">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Nombre del giro</label>
                                <input type="text" id="nombre" class="form-control" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="text-right">
                        <a class="btn btn-white btn-sm" data-dismiss="modal"><i class="fa fa-sign-out"></i> Cancelar</a>
                        <button class="btn btn-primary btn-sm m-l-5" id="btnGuardarGiro"><i class="fa fa-save"></i> Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin Modal de cambio de nombre-->
    <input type="hidden" id="id">
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

                var moverNodo = function (moverId, aId) {

                    //TODO: eliminar return para hacer funcional, se agrega para que no puedan modificar los ayudantes de Joyce
                    //pero si requieren hacer búsquedas (2020-05-21)
                    //return ;

                    console.log("Se movio el nodo %d al padre: %d", moverId, aId);
                    $("#lista-ccostos").treetable("move", moverId, aId);

//                    var url = $('#action-mover').val();
                    var url = "api/giros_comerciales/cambiar_padre";
                    var token = $('meta[name="csrf-token"]').attr('content');
                    var datos = {mover_id: moverId, a_id: aId, _token: token};
                    console.log(datos);
                    $.post(url, datos).done(function (res) {
                        console.log(res);
                        if(res.status == "success"){

                        }else{
                            swal({
                                title: 'Error',
                                text: res.mensaje,
                                icon: 'error'
                            });
                        }
                    }).fail(function(res){
                        swal({
                            title: 'Error',
                            text: 'No puedes Colocar este nodo aquí',
                            icon: 'error'
                        });
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
                            console.log("e",e);
                            console.log("ui",ui);
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
        //TODO: modificar para hacer funcional, se agrega para que no puedan modificar los ayudantes de Joyce
        //pero si requieren hacer búsquedas (2020-05-21)
        //$(".spanAmbito-disabled").on("click",function(){
        $(".spanAmbito").on("click",function(){
            var id = $(this).data("id");
            var ambito_id = $(this).data("ambito_id");
            $.ajax({
                url:"/api/giros_comerciales/cambiar_ambito",
                type:"POST",
                dataType:"json",
                data:{
                    id:id,
                    ambito_id:ambito_id
                },
                success:function(data){
                    $.each(data, function(index,element){
                        $("#spanAmbito"+element.id).text(element.nombre);
                        $("#spanAmbito"+element.id).data("ambito_id",element.ambito_id);
                    });
                }
            });
        });
        function CargarGiro(id){
            //TODO: eliminar return para hacer funcional, se agrega para que no puedan modificar los ayudantes de Joyce
            //pero si requieren hacer búsquedas (2020-05-21)
            //return;

            var url="api/giros_comerciales/"+id;
            $.get(url).done(function(res){
                if(res != null){
                    $("#nombre").val(res.nombre);
                    $("#id").val(res.id);
                    $("#modal-cambio").modal("show");
                }else{
                    swal({
                        title: 'Error',
                        text: 'No se pudo obtener el Giro',
                        icon: 'error'
                    });
                }
            });
        }
        $("#btnGuardarGiro").on("click",function(){
            if($("#nombre").val() != ""){
                $.ajax({
                    url:"/api/giros_comerciales/"+$("#id").val(),
                    type:"PUT",
                    dataType:"json",
                    data:{
                        nombre:$("#nombre").val()
                    },
                    success:function(data){
                        if(data != "" && data != null){
                            $("#spanNombre"+$("#id").val()).text(data.nombre);
                            $("#modal-cambio").modal("hide");
                        }else{
                            swal({
                                title: 'Error',
                                text: 'No se logro cambiar el nombre del giro',
                                icon: 'error'
                            });
                        }
                    }
                });
            }else{
                swal({
                    title: 'Error',
                    text: 'Agrega un nombre para el giro',
                    icon: 'error'
                });
            }
        });
    </script>
@endpush


