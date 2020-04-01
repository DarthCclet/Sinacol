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
<table id="tabla-detalle" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
      <tr><th>Id</th><th>Estatus</th><th>Folio</th><th>Anio</th><th>Centro</th><th>user</th><th>ratificada</th><th>excepcion</th><th>Fecha Ratificacion</th><th>Fecha Recepcion</th><th>Observaciones</th><th></th><th></th><th></th><th></th><th></th></tr>
    </thead>
  </table>
  @push('scripts')
  <script>
       $(document).ready(function() {
       var dt = $('#tabla-detalle').DataTable({
            "deferRender": true,
            "ajax": {
              "type": "GET",
              "async": true,
              "url": '/api/solicitudes',
              "data": function (d) {
              }
            },
            "columnDefs": [
              {"targets": [0], "visible": false},
            {
              "targets": -1,
              "defaultContent": '<button class="btn-warning btn-editar"><i class="fa fa-pencil btn-editar"></i></button>&nbsp;&nbsp;<button class="btn-danger btn-helpkit"><i class="fa fa-medkit btn-helpkit"></i></button>',
            }],
            "aoColumns": [
              null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null
              ],
            "processing": true,
            select: true,
            "ordering": false,
            "searching": false,
            "scrollX": true,
            "scrollY": $(window).height() - $('#header').height()-200,
            "scrollColapse": false,
            "scroller": {
              "serverWait": 200,
              "loadingIndicator": true
            },
            "serverSide": true,
            "pageLength": 20,
            "responsive": true,
            "stateSaveParams": function (settings, data) {
              //data.search.search = "";
              console.log(data);
            }
          });
       });
  </script>
  @endpush
  