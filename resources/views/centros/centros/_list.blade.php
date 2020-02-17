<table id="data-table-default" class="table table-striped table-bordered table-condensed table-td-valign-middle">
    <thead>
    <tr>
        <th class="text-nowrap"></th>
        <th class="text-nowrap">Centro de Conciliacion</th>
        <th class="text-nowrap">Duración de Audiencias(promedio)</th>
        <th class="text-nowrap">Acciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($centros as $centro)
        <tr class="odd gradeX">
            <td width="1%" class="f-s-600 text-inverse">{{$centro->id}}</td>
            <td>{{$centro->nombre}}</td>
            <td>{{$centro->duracionAudiencia}}</td>
            <td>
                {!! Form::open(['action' => ['CentroController@destroy', $centro->id], 'method'=>'DELETE']) !!}
                <div style="display: inline-block;">
                    <a href="{{route('centros.edit',[$centro])}}" class="btn btn-xs btn-info" title="Editar">
                        <i class="fa fa-pencil-alt"></i>
                    </a>
                    <a class="btn btn-xs btn-info disponibilidad" onclick="getCentroDisponibilidad({{$centro->id}})" title="Días disponibles">
                        <i class="fa fa-calendar text-light"></i>
                    </a>
                    <a class="btn btn-xs btn-info incidencia" onclick="getCentroIncidencias({{$centro->id}})" title="Fechas no disponibles">
                        <i class="fa fa-calendar-times text-light"></i>
                    </a>
                    <button class="btn btn-xs btn-warning btn-borrar" title="Eliminar centro">
                        <i class="fa fa-trash btn-borrar"></i>
                    </button>
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach

    </tbody>
</table>
