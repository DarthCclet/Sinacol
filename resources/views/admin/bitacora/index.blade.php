@extends('layouts.default')
@section('title', 'Bitácora')
@include('includes.component.datatables')
@include('includes.component.datatables')
@include('includes.component.pickers')
@section('content')
<ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="/">Inicio</a></li>
    <li class="breadcrumb-item"><a href="javascript:;">Bitácora</a></li>
</ol>
<!-- end breadcrumb -->
<!-- begin page-header -->
<h1 class="page-header">Acciones en el sistema <small>Listado</small></h1>
<!-- end page-header -->
<!-- begin panel -->
<div class="panel panel-default">
    <!-- begin panel-heading -->
    <div class="panel-heading">
        <h4 class="panel-title">Listado de movimientos a la base</h4>
        <div class="panel-heading-btn">
        </div>
    </div>
    <!-- end panel-heading -->
    <!-- begin panel-body -->
    <div class="panel-body">
        <div class="col-md-12">
            <table id="data-table-scroller" class="table table-striped table-bordered  table-td-valign-middle" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Modelo</th>
                        <th>Movimiento</th>
                        <th>Fecha</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="col-md-12 row" id="divFilters">
            <div class="col-sm-offset-3 col-md-6">
                <div class="form-group">
                    <select id="event" class="form-control select filtros">
                        <option value="">-- Selecciona un movimiento</option>
                        <option value="Inserción">Inserción</option>
                        <option value="Modificación">Modificación</option>
                        <option value="Eliminación">Eliminación</option>
                        <option value="Logged In">Login</option>
                        <option value="Logged Out">Logout</option>
                    </select>
                    <p class="help-block needed">Movimiento</p>
                </div>
            </div>
            <div class="col-sm-offset-3 col-md-6">
                <div class="form-group">
                    <select id="user_id" class="form-control select filtros">
                        <option value="">-- Selecciona un usuario</option>
                        @foreach($users as $user)
                        <option value="{{$user->id}}">{{$user->persona->nombre}} {{$user->persona->primer_apellido}} {{$user->persona->segundo_apellido}}</option>
                        @endforeach
                    </select>
                    <p class="help-block needed">Usuario</p>
                </div>
            </div>
            <div class="col-md-6 col-sm-offset-3">
                <input class="form-control fecha filtros" id="fecha_inicio" placeholder="Fecha inicio de busqueda" type="text">
                <p class="help-block needed">Fecha inicio</p>
            </div>
            <div class="col-md-6 col-sm-offset-3">
                <input class="form-control fecha filtros" id="fecha_fin" placeholder="Fecha fin de busqueda" type="text">
                <p class="help-block needed">Fecha fin</p>
            </div>
            <div class="col-md-12" align="center">
                <button class="btn btn-danger" type="button" id="limpiarFiltros" align="center"> <i class="fa fa-eraser"></i> Limpiar filtros</button>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
    <script>       
        $(document).ready(function(){
            var limpiar = false;
            $(".select").select2();
            var dt = $('#data-table-scroller').DataTable({
                "deferRender": true,
                "ajax": {
                    "url": '/bitacora',
                    "dataSrc": function(json){
                        var array = new Array();
                        this.recordsTotal = json.total;
                        $.each(json.data,function(key, value){
                            array.push(Object.values(value));
                        });
                        return array;
                    },
                    "data": function (d) {
                        d.IsDatatableScroll = true,
                        d.loadPartes = true,
                        d.event = $("#event").val(),
                        d.user_id = $("#user_id").val(),
                        d.fecha_inicio = dateFormat($("#fecha_inicio").val(),1),
                        d.fecha_fin = dateFormat($("#fecha_fin").val(),1)
                    }
                },
                
                "serverSide": true,
                "processing": true,
                select: true,
                "ordering": false,
                "searching": false,
                "pageLength": 20,
                "recordsTotal":20,
                "recordsFiltered":20,
                "lengthChange": false,
                "scrollX": true,
                "scrollY": $(window).height() - $('#header').height()-200,
                "scrollColapse": false,
                "scroller": {
                    "serverWait": 200,
                    "loadingIndicator": true,
                },
                "responsive": false,
                "language": {
                    "url": "/assets/plugins/datatables.net/dataTable.es.json"
                },
                "stateSaveParams": function (settings, data) {
                //data.search.search = "";
                  console.log(data);
                },
                "dom": "tiS", // UI layout
            });
            dt.on( 'draw', function () {
                console.log('tratando de poner');
            });
            $('.filtros').on( 'dp.change change clear', function () {
                if(!limpiar){
                    dt.clear();
                    dt.ajax.reload(function(){}, true);
                }
            });
            $(".fecha").datetimepicker({useCurrent: false,format:'DD/MM/YYYY'});
            $("#limpiarFiltros").click(function(){
                limpiar = true;
                $(".filtros").val("");
                $(".select").trigger("change");
                dt.clear();
                dt.ajax.reload(function(){
                    limpiar = false;
                }, true);
            });
        });
    </script>
@endpush