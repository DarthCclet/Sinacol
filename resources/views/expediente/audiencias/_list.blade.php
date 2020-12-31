<input type="hidden" id="ruta" value="{!! route("audiencias.edit",1) !!}">
<table id="tabla-detalle" style="width:100%;" class="table display">
    <thead>
      <tr>
          <th>Folio de audiencia</th>
          <th>Fecha de audiencia</th>
          <th>Hora inicio</th>
          <th>Hora fin</th>
          <th>Conciliador</th>
          <th>Estatus</th>
          <th>Acciones</th></tr>
    </thead>

</table>
@if(isset($audiencias))
    <input type="hidden"  id="expediente_id" value="{{$audiencias[0]->expediente_id}}"  />
@else
    <input type="hidden"  id="expediente_id" value=""  />
@endif
@if(!isset($audiencias))
<div id="divFilters" class="col-md-12 row" >
    <div class="col-md-4">
        <input class="form-control filtros" id="NoAudiencia" placeholder=" No. Audiencia" type="text" value="">
        <p class="help-block needed">No. Audiencia</p>
    </div>
    <div class="col-md-4">
        <input class="form-control date filtros" id="fechaAudiencia" placeholder="Fecha de audiencia" type="text" value="">
        <p class="help-block needed">Fecha de audiencia</p>
    </div>
    <div>
        <input type="hidden" class="filtros"  id="estatus_audiencia" value=""  />
    </div>
    <div class="col-md-4">
        <button class="btn btn-danger" type="button" id="limpiarFiltros" > <i class="fa fa-eraser"></i> Limpiar filtros</button>
    </div>
</div>
@endif

@push('scripts')
  <script>
        var filtrado=false;
        $(document).ready(function() {
            var ruta = $("#ruta").val();
            var dt = $('#tabla-detalle').DataTable({
                "deferRender": true,
                "ajax": {
                    "url": '/audiencias',
                    "dataSrc": function(json){
                        var array = new Array();
                        this.recordsTotal = json.total;
                        $.each(json.data,function(key, value){
                            array.push(Object.values(value));
                        });
                        return array;
                    },
                    "data": function (d) {
                        d.fechaAudiencia = dateFormat($("#fechaAudiencia").val(),1),
                        d.NoAudiencia = $("#NoAudiencia").val(),
                        d.estatus_audiencia = $("#estatus_audiencia").val(),
                        d.expediente_id = $("#expediente_id").val(),
                        d.IsDatatableScroll = true,
                        d.loadPartes = true
                        // d.objeto_solicitud_id = $("#objeto_solicitud_id").val()
                    }
                },
                "columnDefs": [
                    {
                        "targets": [0],
                        "render": function (data, type, row) {
                            return  row[1]+"/"+row[2];
                        }
                    },
                    {
                        "targets": [1],
                        "render": function (data, type, row) {
                            if (data != null) {
                                return  dateFormat(row[3],4);
                            } else {
                                return "";
                            }
                        }
                    },
                    {
                        "targets": [2],
                        "render": function (data, type, row) {
                            return  row[4];
                        }
                    },
                    {
                        "targets": [3],
                        "render": function (data, type, row) {
                            return  row[5];
                        }
                    },
                    
                    {
                        "targets": [4],
                        "render": function (data, type, row) {
                            var html = "";

                            if(row[11] != null){
                                html = ""+row[11].persona.nombre + " "+ row[11].persona.primer_apellido + " " + (row[11].persona.segundo_apellido|| "");

                            }
                            return  html;
                        }
                    },
                    {
                        "targets": [5],
                        "render": function (data, type, row) {
                            var html = "";
                            if(row[7] == true){
                                html = "Concluida";
                            }else{
                                html = "Pendiente";
                            }
                            return  html;
                        }
                    },
                    {
                        "targets": -1,
                        "render": function (data, type, row) {
                                // console.log(row[0]);
                                var guia = "";
                                if(row[7] == false){
                                    if(row[12].solicitud.tipo_solicitud_id == 1){
                                        guia = '<div style="display: inline-block;" class="m-2"><a title="Iniciar proceso de audiencia" href="'+ruta.replace('/audiencias/1/edit',"/guiaAudiencia/"+row[0]+"")+'" class="btn btn-xs btn-primary"><i class="fa fa-tasks"></i></a></div>';
                                    }else{
                                        guia = '<div style="display: inline-block;" class="m-2"><a title="Iniciar proceso de audiencia" href="'+ruta.replace('/audiencias/1/edit',"/resolucionColectiva/"+row[0]+"")+'" class="btn btn-xs btn-primary"><i class="fa fa-tasks"></i></a></div>';
                                        
                                    }
                                }
                                return '<div style="display: inline-block;" class="m-2"><a title="Detalle" href="'+ruta.replace('/1/',"/"+row[0]+"/")+'" class="btn btn-xs btn-primary"><i class="fa fa-search"></i></a></div>'+guia;
                            }
                        // "defaultContent": '<div style="display: inline-block;"><a href="{{route("solicitudes.edit",['+row[0]+'])}}" class="btn btn-xs btn-primary"><i class="fa fa-pencil-alt"></i></a>&nbsp;<button class="btn btn-xs btn-danger btn-borrar"><i class="fa fa-trash btn-borrar"></i></button></div>',
                    }
                ],
                "rowCallback": function( row, data ) {
                    if(data[9] && !data[10]){
                        $(row).css("background-color","rgb(157 36 73 / 0.28)");
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
                },
                "dom": "tiS", // UI layout
            });
            dt.on( 'draw', function () {
                if(filtrado){
                filtrado = false;
                }
            });
            $('.filtros').on( 'dp.change change clear', function () {
                dt.clear();
                dt.ajax.reload(function(){}, true);
                filtrado = true;
            });
            $(".date").datetimepicker({useCurrent: false,format:'DD/MM/YYYY'});
            $("#limpiarFiltros").click(function(){
                $(".filtros").val("");
                dt.clear();
                dt.ajax.reload(function(){}, true);
                filtrado = true;
            });

       });
  </script>
  @endpush