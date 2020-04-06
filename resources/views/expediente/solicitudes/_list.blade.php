
{{-- <table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">Fecha Recepcion</th>
        <!--<th class="text-nowrap">Objeto</th>-->
        <th class="text-nowrap">Estatus</th>
        <th class="text-nowrap">Fecha Ratificacion</th>
        <th class="text-nowrap">Acciones</th>
        <!-- <th >Editar</th> -->
    </tr>
    </thead>
    <tbody>
    @foreach($solicitud as $solicitud)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$solicitud->id}}</td>
            <td>{{$solicitud->fecha_recepcion}}</td>
            <td>{{$solicitud->estatusSolicitud->nombre}}</td>
            <td>{{$solicitud->fecha_ratificacion}}</td>
            <td class="all">
                {!! Form::open(['action' => ['SolicitudController@destroy', $solicitud->id], 'method'=>'DELETE']) !!}
                <div style="display: inline-block;">
                    <a href="{{route('solicitudes.edit',[$solicitud])}}" class="btn btn-xs btn-info">
                        <i class="fa fa-pencil-alt"></i>
                    </a>
                    <button class="btn btn-xs btn-warning btn-borrar">
                        <i class="fa fa-trash btn-borrar"></i>
                    </button>
                </div>
                {!! Form::close() !!}
            </td>

        </tr>
    @endforeach

    </tbody>
</table> --}}
<input type="hidden" id="ruta" value="{!! route("solicitudes.edit",1) !!}">
<table id="tabla-detalle" style="width:100%;" class="table display">
    <thead>
      <tr><th>Id</th><th>Estatus</th><th>Folio</th><th>Anio</th><th>Centro</th><th>user</th><th>ratificada</th><th>excepcion</th><th>Fecha Ratificacion</th><th>Fecha Recepcion</th><th>Observaciones</th><th>deleted_at</th><th>created_at</th><th>updated_at</th><th>Fecha Conflicto</th><th>Accion</th></tr>
    </thead>

</table>
<div id="divFilters" class="col-md-12 row" >
    <div class="col-md-4">
        <input class="form-control filtros" id="folio" placeholder="Folio" type="text" value="">
        <p class="help-block needed">Folio</p>
    </div>
    <div class="col-md-4">
        <input class="form-control filtros" id="anio" placeholder="A&ntilde;o" type="text" value="">
        <p class="help-block needed">A&ntilde;o</p>
    </div>
    <div class="col-md-4">
        <input class="form-control date filtros" id="fechaRatificacion" placeholder="Fecha de ratificacion" type="text" value="">
        <p class="help-block needed">Fecha de Ratificaci&oacute;n</p>
    </div>
    <div class="col-md-4">
        <input class="form-control date filtros" id="fechaRecepcion" placeholder="Fecha de recepcion" type="text" value="">
        <p class="help-block needed">Fecha de Recepci&oacute;n</p>
    </div>
    <div class="col-md-4">
        <input class="form-control date filtros" id="fechaConflicto" placeholder="Fecha de conflicto" type="text" value="">
        <p class="help-block needed">Fecha de Conflicto</p>
    </div>
    <div class="col-md-4">
        {!! Form::select('estatus_solicitud_id', isset($estatus_solicitudes) ? $estatus_solicitudes : [] , null, ['id'=>'estatus_solicitud_id','placeholder' => 'Seleccione una opcion', 'class' => 'form-control catSelect filtros']);  !!}
        {!! $errors->first('estatus_solicitud_id', '<span class=text-danger>:message</span>') !!}
        <p class="help-block needed">Estatus de la solicitud</p>
    </div>
    <div class="col-md-4">
        <button class="btn btn-info" type="button" id="limpiarFiltros" > <i class="fa fa-eraser"></i> Limpiar filtros</button>
    </div>
    
</div>
  @push('scripts')
  <script>
      var estatus_solicitudes = [];
      @foreach($estatus_solicitudes as $key => $node)
        estatus_solicitudes['{{$key}}'] = '{{$node}}';
      @endforeach
      
        var filtrado=false;
        $(document).ready(function() {
            var ruta = $("#ruta").val();
            var dt = $('#tabla-detalle').DataTable({
                "deferRender": true,
                "ajax": {
                    "type": "GET",
                    "async": true,
                    "url": '/api/solicitudes',
                    "dataSrc": function(json){
                        var array = new Array();
                        this.recordsTotal = json.total;
                        $.each(json.data,function(key, value){
                            array.push(Object.values(value)); 
                        });
                        return array;
                    },
                    "dataFilter": function(data){
                        var json = jQuery.parseJSON( data );
                        json.recordsTotal = json.total;
                        json.recordsFiltered = json.total;
                        return JSON.stringify(json);
                    },
                    "data": function (d) {
                        d.fechaRatificacion = dateFormat($("#fechaRatificacion").val(),1),
                        d.fechaRecepcion = dateFormat($("#fechaRecepcion").val(),1),
                        d.fechaConflicto = dateFormat($("#fechaConflicto").val(),1),
                        d.folio = $("#folio").val()
                        d.anio = $("#anio").val()
                        d.estatus_solicitud_id = $("#estatus_solicitud_id").val()
                        // d.objeto_solicitud_id = $("#objeto_solicitud_id").val()
                    }
                },
                "columnDefs": [
                    {"targets": [0], "visible": false},
                    {
                        "targets": [1], 
                        "render": function (data, type, row) {
                            if (data != null) {
                                return  estatus_solicitudes[data];
                            } else {
                                return "";
                            }
                        }
                    },
                    {"targets": [4], "visible": false},
                    {"targets": [5], "visible": false},
                    {"targets": [6], "visible": false},
                    {"targets": [7], "visible": false},
                    {
                        "targets": [8],
                        "render": function (data, type, row) {
                            if (data != null) {
                                return  dateFormat(data,2);
                            } else {
                                return "";
                            }
                        }
                    },
                    {
                        "targets": [9],
                        "render": function (data, type, row) {
                            if (data != null) {
                                return  dateFormat(data,2);
                            } else {
                                return "";
                            }
                        }
                    },
                    {
                        "targets": [14],
                        "render": function (data, type, row) {
                            if (data != null) {
                                return  dateFormat(data);
                            } else {
                                return "";
                            }
                        }
                    },
                    {"targets": [11], "visible": false},
                    {"targets": [12], "visible": false},
                    {"targets": [13], "visible": false},
                {
                    "targets": -1,
                    "render": function (data, type, row) {
                            // console.log(row[0]);
                            
                            return '<div style="display: inline-block;"><a href="'+ruta.replace('/1/',"/"+row[0]+"/")+'" class="btn btn-xs btn-info"><i class="fa fa-pencil-alt"></i></a><button class="btn btn-xs btn-warning btn-borrar"><i class="fa fa-trash btn-borrar"></i></button></div>';          
                        }
                    // "defaultContent": '<div style="display: inline-block;"><a href="{{route("solicitudes.edit",['+row[0]+'])}}" class="btn btn-xs btn-info"><i class="fa fa-pencil-alt"></i></a><button class="btn btn-xs btn-warning btn-borrar"><i class="fa fa-trash btn-borrar"></i></button></div>',
                }
                ],
                "serverSide": true, 
                select: true,
                "ordering": false,
                "searching": false,
                "pageLength": 20,   
                "lengthChange": false,
                "scrollX": true,
                "scrollY": $(window).height() - $('#header').height()-400,
                "scrollColapse": false,
                "scroller": {
                    "serverWait": 200,
                    "loadingIndicator": true,   
                },
                "dom": "frtiS",
                "responsive": false,
                "language": {
                "url": "/assets/plugins/datatables.net/dataTable.es.json"
                },
                "stateSaveParams": function (settings, data) {
                //data.search.search = "";
                //   console.log(data);
                }
            });
            dt.on( 'draw', function () {
                console.log('tratando de poner')
                if(filtrado){
                //dt.scroller().scrollToRow(0);
                filtrado = false;
                }
            });
        
            $('.filtros').on( 'blur change clear', function () {
                dt.clear();
                dt.ajax.reload(function(){}, true);
                filtrado = true;
            });
            $(".catSelect").select2({width: '100%'});
            $(".date").datetimepicker({useCurrent: false,format:'DD/MM/YYYY'});
            $(".date").keypress(function(event){event.preventDefault();});
            function dateFormat(fecha,tipo){
                if(fecha != ""){
                    if(tipo == 1){
                        var vecFecha = fecha.split("/");
                        var formatedDate = vecFecha[2] + "-" + vecFecha[1] + "-" + vecFecha[0];
                        return formatedDate;
                    }else if(tipo == 2){
                        var vecFechaHora = fecha.split(" ");
                        var vecFecha = vecFechaHora[0].split("-");
                        var formatedDate = vecFecha[2] + "/" + vecFecha[1] + "/" + vecFecha[0] + " " + vecFechaHora[1];
                        return formatedDate;
                    }else if(tipo == 3){
                        var vecFechaHora = fecha.split(" ");
                        var vecFecha = vecFechaHora[0].split("/");
                        var formatedDate = vecFecha[2] + "-" + vecFecha[1] + "-" + vecFecha[0] + " " + vecFechaHora[1];
                        return formatedDate;
                    }else{
                        var vecFecha = fecha.split("-");
                        var formatedDate = vecFecha[2] + "/" + vecFecha[1] + "/" + vecFecha[0];
                        return formatedDate;
                    }
                }
            }
            $("#limpiarFiltros").click(function(){
                $(".filtros").val("");
                // $(".filtros").trigger('change');
                dt.clear();
                dt.ajax.reload(function(){}, true);
                filtrado = true;
            });
       });
  </script>
  @endpush
  