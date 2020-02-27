<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">
    <thead>
    <tr>
        <th width="1%"></th>
        <th class="text-nowrap">Fecha Recepcion</th>
        <th class="text-nowrap">Objeto</th>
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
</table>
