

<input type="hidden" id="ruta" value="{!! route("solicitudes.edit",1) !!}">
<table id="tabla-detalle" style="width:100%;" class="table display">
    <thead>
      <tr><th>Id</th><th>Estatus</th><th>Folio</th><th>Año</th><th>Fecha de ratificación</th><th>Fecha de recepción</th><th>Fecha de conflicto</th><th>Partes</th><th>Expediente</th><th>Días para expiraci&oacute;n</th><th>Acción</th></tr>
    </thead>

</table>
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
                    "url": '/solicitudes',
                    "dataSrc": function(json){
                        var array = new Array();
                        this.recordsTotal = json.total;
                        $.each(json.data,function(key, value){
                            array.push(Object.values(value));
                        });
                        return array;
                    },
                    "data": function (d) {
                        d.fechaRatificacion = dateFormat($("#fechaRatificacion").val(),1),
                        d.fechaRecepcion = dateFormat($("#fechaRecepcion").val(),1),
                        d.fechaConflicto = dateFormat($("#fechaConflicto").val(),1),
                        d.folio = $("#folio").val(),
                        d.Expediente = $("#Expediente").val(),
                        d.anio = $("#anio").val(),
                        d.estatus_solicitud_id = $("#estatus_solicitud_id").val(),
                        d.curp = $("#curp").val(),
                        d.nombre = $("#nombre").val(),
                        d.IsDatatableScroll = true,
                        d.loadPartes = true
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
                    {"targets": [2]},
                    {"targets": [3]},
                    {
                        "targets": [4],
                        "render": function (data, type, row) {
                                console.log(data);
                            if (data != null) {
                                return  dateFormat(data,2);
                            } else {
                                return "";
                            }
                        }
                    },
                    {
                        "targets": [5],
                        "render": function (data, type, row) {
                            if (data != null) {
                                return  dateFormat(data,2);
                            } else {
                                return "";
                            }
                        }
                    },
                   
                    {
                        "targets": [6],
                        "render": function (data, type, row) {
                            if (data != null) {
                                return  dateFormat(data);
                            } else {
                                return "";
                            }
                        }
                    },
                    {
                        "targets": [7],
                        "render": function (data, type, row) {
                            var html = "";
                            var solicitantes = "";
                            var solicitados = "";
                            $.each(row[7],function(key, value){
                                var nombre = "";
                                if(value.tipo_persona_id == 1){
                                        nombre = value.nombre + " " + value.primer_apellido + " " + (value.segundo_apellido || "")
                                }else{
                                    nombre = value.nombre_comercial;
                                }
                                if(value.tipo_parte_id == 2){
                                    solicitantes += "<p> -"+nombre+"</p>";
                                }else if(value.tipo_parte_id == 1){
                                    solicitados += "<p> - "+nombre+"</p>";
                                }
                            });
                            html += "<div>";
                            html += "<h5>Solicitantes</h5>";
                            html += solicitados;
                            html += "<h5>Citados</h5>";
                            html += solicitantes;
                            html += "</div>";
                            return  html;
                        }
                    },
                    {
                        "targets": [8],
                        "render": function (data, type, row) {
                            console.log(row[8]);
                            html = "N/A";
                            if(row[8] != null){
                            html = ""+row[8].folio;
                            }
                            return  html;
                        }
                    },
                    {
                        "targets": -2,
                        "render": function (data, type, row) {
                                // console.log(row[0]);
                                if(row[1] == "2" && row[4] != null){
                                    var d = new Date();
                                    var dateToday = d.getFullYear()+"-"+String(d.getMonth() + 1).padStart(2, '0')+"-"+String(d.getDate()).padStart(2, '0');
                                    var date1 = new Date(row[4].split(" ")[0]);
                                    var date2 = new Date(dateToday);
                                    var dias = date2 - date1;
                                    dias = (dias/ (1000 * 3600 * 24));
                                    diasExpira = 45;
                                    resultado = diasExpira - dias;
                                    if(resultado > 0){
                                        return resultado +" d&iacute;as";
                                    }
                                    expiro  = resultado * -1;
                                    return  "<p style='color:red;'>Expir&oacute; hace: " + expiro+ " d&iacute;as </p>";
                                }else{
                                    return '';
                                }
                            }
                        // "defaultContent": '<div style="display: inline-block;"><a href="{{route("solicitudes.edit",['+row[0]+'])}}" class="btn btn-xs btn-primary"><i class="fa fa-pencil-alt"></i></a>&nbsp;<button class="btn btn-xs btn-danger btn-borrar"><i class="fa fa-trash btn-borrar"></i></button></div>',
                    },
                    {
                        "targets": -1,
                        "render": function (data, type, row) {
                                return '<div style="display: inline-block;"><a href="'+ruta.replace('/1/',"/"+row[0]+"/")+'#step-3" class="btn btn-xs btn-primary"><i class="fa fa-pencil-alt"></i></a></div>';
                            }
                        // "defaultContent": '<div style="display: inline-block;"><a href="{{route("solicitudes.edit",['+row[0]+'])}}" class="btn btn-xs btn-primary"><i class="fa fa-pencil-alt"></i></a>&nbsp;<button class="btn btn-xs btn-danger btn-borrar"><i class="fa fa-trash btn-borrar"></i></button></div>',
                    }
                ],
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
                console.log('tratando de poner')
                if(filtrado){
                //dt.scroller().scrollToRow(0);
                filtrado = false;
                }
            });

            $('.filtros').on( 'dp.change change clear', function () {
                dt.clear();
                dt.ajax.reload(function(){}, true);
                filtrado = true;
            });
            $(".catSelect").select2({width: '100%'});
            $(".date").datetimepicker({useCurrent: false,language: "es",format:'DD/MM/YYYY'});
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
                $(".catSelect").trigger('change');
                dt.clear();
                dt.ajax.reload(function(){}, true);
                filtrado = true;
            });
       });
  </script>
  @endpush
