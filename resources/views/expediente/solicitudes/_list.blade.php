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
<table id="tabla-detalle" style="width:100%;" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
      <tr><th>Id</th><th>Estatus</th><th>Folio</th><th>Anio</th><th>Centro</th><th>user</th><th>ratificada</th><th>excepcion</th><th>Fecha Ratificacion</th><th>Fecha Recepcion</th><th>Observaciones</th><th></th><th></th><th></th><th></th><th></th></tr>
    </thead>

</table>
  @push('scripts')
  <script>
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
              }
            },
            "columnDefs": [
              {"targets": [1], "visible": false},
              {"targets": [3], "visible": false},
              {"targets": [4], "visible": false},
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
                "loadingIndicator": true
            },
            "responsive": false,
            "language": {
              "url": "assets/javascripts/dataTables.es.json"
            },
            "stateSaveParams": function (settings, data) {
              //data.search.search = "";
            //   console.log(data);
            }
          });
       });
  </script>
  @endpush
  